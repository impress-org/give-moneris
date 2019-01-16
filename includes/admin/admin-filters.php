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

/**
 * Plugins row action links
 *
 * @since 1.0.0
 *
 * @param array $actions An array of plugin action links.
 *
 * @return array An array of updated action links.
 */
function give_moneris_plugin_action_links( $actions ) {
	$new_actions = array(
		'settings' => sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=moneris-settings' ),
			__( 'Settings', 'give-moneris' )
		),
	);

	return array_merge( $new_actions, $actions );
}

add_filter( 'plugin_action_links_' . GIVE_MONERIS_PLUGIN_BASENAME, 'give_moneris_plugin_action_links' );

