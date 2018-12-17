<?php
/**
 * Miscellaneous Functions.
 *
 * @package   Give
 * @copyright Copyright (c) 2018, WordImpress
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