<?php
/**
 * Give Moneris Gateway
 *
 * @package   Give
 * @copyright Copyright (c) 2018, GiveWP
 * @license   https://opensource.org/licenses/gpl-license GNU Public License
 * @since     1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_Moneris_Gateway
 *
 * @since 1.0.0
 */
class Give_Moneris_Gateway {

	/**
	 * Default Gateway ID.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Access Token
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $access_token = '';

	/**
	 * Store ID
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $store_id = '';

	/**
	 * Give_Moneris_Gateway constructor.
	 *
	 * @return void
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function __construct() {

		$this->id           = 'moneris';
		$this->access_token = give_get_option( 'give_moneris_access_token' );
		$this->store_id     = give_get_option( 'give_moneris_store_id' );

		// Bailout, if gateway is not active.
		if ( ! give_is_gateway_active( $this->id ) ) {
			return;
		}

		add_action( "give_{$this->id}_cc_form", array( $this, 'display_billing_details' ), 10, 1 );
		add_action( "give_gateway_{$this->id}", array( $this, 'process_donation' ) );
	}

	/**
	 * This function will be used to do all the heavy lifting for processing a donation payment.
	 *
	 * @param array $donation_data List of donation data.
	 *
	 * @return void
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function process_donation( $donation_data ) {

		// Bailout, if the current gateway and the posted gateway mismatched.
		if ( $this->id !== $donation_data['gateway'] ) {
			return;
		}

		// Validate gateway nonce.
		give_validate_nonce( $donation_data['gateway_nonce'], 'give-gateway' );

		// Make sure we don't have any left over errors present.
		give_clear_errors();

		// Validate fields here.

		// Any errors?
		$errors = give_get_errors();

		// No errors, proceed.
		if ( ! $errors ) {

			$donation_amount = give_format_amount( $donation_data['price'] );

			$args = array(
				'price'           => $donation_amount,
				'give_form_title' => $donation_data['post_data']['give-form-title'],
				'give_form_id'    => intval( $donation_data['post_data']['give-form-id'] ),
				'give_price_id'   => isset( $donation_data['post_data']['give-price-id'] ) ? $donation_data['post_data']['give-price-id'] : '',
				'date'            => $donation_data['date'],
				'user_email'      => $donation_data['user_email'],
				'purchase_key'    => $donation_data['purchase_key'],
				'currency'        => give_get_currency( $donation_data['post_data']['give-form-id'], $donation_data ),
				'user_info'       => $donation_data['user_info'],
				'status'          => 'pending'
			);

			// Create a pending donation.
			$donation_id = give_insert_payment( $args );
			
			$exp_month   = sprintf( '%02d', $donation_data['card_info']['card_exp_month'] );
			$exp_year    = substr( $donation_data['card_info']['card_exp_year'], 2, 2 );
			$expiry_date = "{$exp_year}{$exp_month}";

			$payment_object = array(
				'type'               => 'purchase',
				'order_id'           => give_moneris_get_unique_donation_id( $donation_id ),
				'cust_id'            => give_get_payment_donor_id( $donation_id ),
				'amount'             => give_format_decimal( array( 'amount' => $donation_data['price'] ) ),
				'pan'                => $donation_data['card_info']['card_number'],
				'expdate'            => $expiry_date,
				'crypt_type'         => 7, // @todo provide a filter to change the crypt type.
				'dynamic_descriptor' => give_moneris_get_statement_descriptor(),
			);

			$transaction_object = new Give_Moneris\mpgTransaction( $payment_object );
			$request_object     = new Give_Moneris\mpgRequest( $transaction_object );
			$request_object->setProcCountryCode( give_get_option( 'base_country' ) );
			$request_object->setTestMode( give_is_test_mode() );

			$https_post_object = new Give_Moneris\mpgHttpsPost( $this->store_id, $this->access_token, $request_object );
			$response          = $https_post_object->getMpgResponse();

			// Prepare Response Variables.
			$response_code       = (int) $response->getResponseCode();
			$is_payment_complete = (bool) $response->getComplete();

			if ( $is_payment_complete & $response_code !== null ) {

				switch ( $response_code ) {

					case $response_code <= 29:

						// Save Transaction ID to Donation.
						$transaction_id = $response->getTxnNumber();
						give_set_payment_transaction_id( $donation_id, $transaction_id );
						give_insert_payment_note( $donation_id, "Transaction ID: {$transaction_id}" );
						give_insert_payment_note( $donation_id, "Approval Code: {$response->getAuthCode()}" );

						if ( ! empty( $transaction_id ) ) {

							// Set status to completed.
							give_update_payment_status( $donation_id );

							// All done. Send to success page.
							give_send_to_success_page();
						}

						break;

					case $response_code >= 50 && $response_code <= 99:

						// Something went wrong outside of Moneris.
						give_record_gateway_error(
							__( 'Moneris Error', 'give-moneris' ),
							sprintf(
							/* translators: %s Exception error message. */
								__( 'The Moneris Gateway declined the donation with an error. Details: %s', 'give-moneris' ),
								$response->getMessage()
							)
						);

						// Set Error to notify donor.
						give_set_error( 'give_moneris_gateway_error', __( 'Payment Declined. Please try again.', 'give-moneris' ) );

						// Set status to failed.
						give_update_payment_status( $donation_id, 'failed' );

						// Send user back to checkout.
						give_send_back_to_checkout( '?payment-mode=moneris' );
						break;

					default:

						// Something went wrong outside of Moneris.
						give_record_gateway_error(
							__( 'Moneris Error', 'give-moneris' ),
							sprintf(
							/* translators: %s Exception error message. */
								__( 'The Moneris Gateway declined the donation with an error. Details: %s', 'give-moneris' ),
								$response->getMessage()
							)
						);

						// Set Error to notify donor.
						give_set_error( 'give_moneris_gateway_error', __( 'Payment Declined. Please try again.', 'give-moneris' ) );

						// Set status to failed.
						give_update_payment_status( $donation_id, 'failed' );

						// Send user back to checkout.
						give_send_back_to_checkout( '?payment-mode=moneris' );
						break;

				}

			} else {

				// Something went wrong outside of Moneris.
				give_record_gateway_error(
					__( 'Moneris Error', 'give-moneris' ),
					sprintf(
					/* translators: %s Exception error message. */
						__( 'The Moneris Gateway returned an error while processing a donation. Details: %s', 'give-moneris' ),
						$response->getMessage()
					)
				);

				// Set Error to notify donor.
				give_set_error( 'give_moneris_gateway_error', __( 'Incomplete Payment Recorded. Please try again.', 'give-moneris' ) );

				// Set status to failed.
				give_update_payment_status( $donation_id, 'failed' );

				// Send user back to checkout.
				give_send_back_to_checkout( '?payment-mode=moneris' );
			}
		}

	}

	/**
	 * This function is used to display billing details only when enabled.
	 *
	 * @param $form_id
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function display_billing_details( $form_id ) {

		// Remove Address Fields if user has option enabled.
		if ( ! give_get_option( 'give_moneris_collect_billing_details' ) ) {
			remove_action( 'give_after_cc_fields', 'give_default_cc_address_fields' );
		}

		// Ensure CC field is in place properly.
		do_action( 'give_cc_form', $form_id );

	}

}

new Give_Moneris_Gateway();
