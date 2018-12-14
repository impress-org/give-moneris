<?php
/**
 * Admin Filters
 *
 * @package   Give-Moneris
 * @copyright Copyright (c) 2018, WordImpress
 * @license   https://opensource.org/licenses/gpl-license GNU Public License
 * @since     1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	 exit;
}

/**
 * Register the Moneris payment gateway.
 *
 * @access public
 * @since  1.0.0
 *
 * @param array $gateways List of registered gateways.
 *
 * @return array
 */
function give_moneris_register_gateway( $gateways ) {

	$gateways['moneris'] = array(
		'admin_label'    => __( 'Moneris - Credit Card', 'give-moneris' ),
		'checkout_label' => __( 'Credit Card', 'give-moneris' ),
	);

	return $gateways;

}

add_filter( 'give_payment_gateways', 'give_moneris_register_gateway' );
