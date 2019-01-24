<?php
/**
 * Admin Filters
 *
 * @package   Give-Moneris
 * @copyright Copyright (c) 2018, GiveWP
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

/**
 * Plugin row meta links
 *
 * @param array $plugin_meta An array of the plugin's metadata.
 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
 *
 * @since 1.0.0
 *
 * @return array
 */
function give_moneris_plugin_row_meta( $plugin_meta, $plugin_file ) {

	if ( $plugin_file !== GIVE_MONERIS_PLUGIN_BASENAME ) {
		return $plugin_meta;
	}

	$new_meta_links = array(
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( add_query_arg( array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-row',
					'utm_campaign' => 'admin',
				), 'http://docs.givewp.com/addon-moneris' )
			),
			__( 'Documentation', 'give-moneris' )
		),
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( add_query_arg( array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-row',
					'utm_campaign' => 'admin',
				), 'https://givewp.com/addons/' )
			),
			__( 'Add-ons', 'give-moneris' )
		),
	);

	return array_merge( $plugin_meta, $new_meta_links );
}

add_filter( 'plugin_row_meta', 'give_moneris_plugin_row_meta', 10, 2 );

