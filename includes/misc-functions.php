<?php
/**
 * Miscellaneous Functions.
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
 * This function will return default statement descriptor.
 *
 * @since 1.0.0
 *
 * @return string
 */
function give_moneris_get_default_statement_descriptor() {
	
	return sprintf(
		/* translators: 1. Site name, 2. Text */
		'%1$s/%2$s',
		get_bloginfo( 'name' ),
		__( 'Donation', 'give-moneris' )
	);
	
}


/**
 * This function will return the dynamic statement descriptor.
 *
 * @since 1.0.0
 *
 * @return string
 */
function give_moneris_get_statement_descriptor() {
	
	return give_get_option( 'give_moneris_statement_descriptor', give_moneris_get_default_statement_descriptor() );
	
}

/**
 * This function will return formatted donation amount.
 *
 * @param int $donation_id Donation ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function give_moneris_get_formatted_donation_amount( $donation_id ) {
	
	return give_format_amount(
		give_get_meta( $donation_id, '_give_payment_total', true ),
		array(
			'sanitize' => false
		)
	);
}

/**
 * This function is used to create a unique donation id to avoid payment processing error due to duplicate order id.
 *
 * @param int $donation_id Donation ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function give_moneris_get_unique_donation_id( $donation_id ) {
	
	$sequential_donation_id = Give()->seq_donation_number->get_serial_number( $donation_id );
	
	return "Give_{$donation_id}_{$sequential_donation_id}";
}
