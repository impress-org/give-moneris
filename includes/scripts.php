<?php
/**
 * Enqueue and Dequeue Scripts
 *
 * @package   Give-Moneris
 * @copyright Copyright (c) 2019, GiveWP
 * @license   https://opensource.org/licenses/gpl-license GNU Public License
 * @since     1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Scripts to Admin.
 *
 * @since 1.0.0
 */
function give_moneris_add_admin_scripts() {
	
	wp_register_script( 'give-moneris-admin', GIVE_MONERIS_PLUGIN_URL . 'assets/dist/js/give-moneris-admin.js', '', GIVE_MONERIS_VERSION );
	wp_enqueue_script( 'give-moneris-admin' );
	
}

add_action( 'admin_enqueue_scripts', 'give_moneris_add_admin_scripts' );
