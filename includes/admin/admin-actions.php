<?php
/**
 * Admin Actions
 *
 * @package   Give-Moneris
 * @copyright Copyright (c) 2019, WordImpress
 * @license   https://opensource.org/licenses/gpl-license GNU Public License
 * @since     1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process refund in Moneris.
 *
 * @access public
 * @since  1.0.0
 *
 * @param string $donation_id Payment ID.
 * @param string $new_status  New Donation Status.
 * @param string $old_status  Old Donation Status.
 *
 * @return      void
 */
function give_moneris_process_refund( $donation_id, $new_status, $old_status ) {

    // Only move forward if refund requested.
	$opt_refund = filter_input( INPUT_POST, 'give_moneris_opt_refund' );
	$can_refund = ! empty( $opt_refund ) ? give_clean( $opt_refund ) : false;

	// Bailout, if can't refund.
	if ( ! $can_refund ) {
		return;
	}
	
	// Verify statuses.
	$can_process_refund = 'publish' !== $old_status ? false : true;
	$can_process_refund = apply_filters( 'give_moneris_can_process_refund', $can_process_refund, $donation_id, $new_status, $old_status );

	// Bail out, if processing refund is not allowed.
	if ( false === $can_process_refund ) {
	    return;
	}
	
	// Bail out, if already refunded.
	if ( 'refunded' !== $new_status ) {
		return;
	}
	
	$transaction_id = give_get_payment_transaction_id( $donation_id );

	// Bail out, if no transaction ID was found.
	if ( empty( $transaction_id ) ) {
		return;
	}
	
	try {
		
		$store_id       = give_get_option( 'give_moneris_store_id' );
		$access_token   = give_get_option( 'give_moneris_access_token' );
		$payment_object = array(
			'type'               => 'refund',
			'txn_number'         => give_get_payment_transaction_id( $donation_id ),
			'order_id'           => give_moneris_get_unique_donation_id( $donation_id ),
			'cust_id'            => give_get_payment_donor_id( $donation_id ),
			'amount'             => give_moneris_get_formatted_donation_amount( $donation_id ),
			'crypt_type'         => 7, // @todo provide a filter to change the crypt type.
			'dynamic_descriptor' => give_moneris_get_statement_descriptor(),
		);

		$transaction_object = new Give_Moneris\mpgTransaction( $payment_object );
		$request_object     = new Give_Moneris\mpgRequest( $transaction_object );
		$request_object->setProcCountryCode( give_get_option( 'base_country' ) );
		$request_object->setTestMode( give_is_test_mode() );
		
		$https_post_object  = new Give_Moneris\mpgHttpsPost( $store_id, $access_token, $request_object );
		$response           = $https_post_object->getMpgResponse();
		$transaction_id     = $response->getTxnNumber();

		if ( $transaction_id ) {
		    
		    // Add donation note for admin reference of the refund procedure to link with Moneris.
			give_insert_payment_note(
				$donation_id,
				sprintf(
					/* translators: 1. Refund ID */
					esc_html__( 'Payment refunded in Moneris with transaction id: %s', 'give-moneris' ),
					$transaction_id
				)
			);
		}
	} catch ( Exception $e ) {
		
		// Log it with DB.
		give_record_gateway_error(
			__( 'Moneris Error', 'give-moneris' ),
			sprintf(
				/* translators: 1. Error Message, 2. Exception Message, 3. Code Text, 4. Code Message. */
				'%1$s %2$s %3$s %4$s',
				__( 'The Moneris payment gateway returned an error while refunding a donation. Message:', 'give-moneris' ),
				$e->getMessage(),
				__( 'Code:', 'give-moneris' ),
				$e->getCode()
			)
		);
		
	}
}

add_action( 'give_update_payment_status', 'give_moneris_process_refund', 200, 3 );

/**
 * This function will display field to opt for refund in Moneris.
 *
 * @param int $donation_id Donation ID.
 *
 * @since 1.0.0
 *
 * @return void
 */
function give_moneris_opt_refund_field( $donation_id ) {
	
	$processed_gateway = Give()->payment_meta->get_meta( $donation_id, '_give_payment_gateway', true );
	
	// Bail out, if the donation is not processed with Moneris payment gateway.
	if ( 'moneris' !== $processed_gateway ) {
		return;
	}
	?>
	<div id="give-moneris-opt-refund-wrap" class="give-moneris-opt-refund give-admin-box-inside give-hidden">
		<p>
			<input type="checkbox" id="give-moneris-opt-refund" name="give_moneris_opt_refund" value="1"/>
			<label for="give-moneris-opt-refund">
				<?php esc_html_e( 'Refund Charge in Moneris?', 'give-moneris' ); ?>
			</label>
		</p>
	</div>
	
	<?php
}

add_action( 'give_view_donation_details_totals_after', 'give_moneris_opt_refund_field', 10, 1 );

