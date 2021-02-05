<?php
/**
 * Admin Settings
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
 * Proceed only, if class Give_Moneris_Admin_Settings not exists.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Give_Moneris_Admin_Settings' ) ) {

	/**
	 * Class Give_Moneris_Admin_Settings
	 *
	 * @since 1.0.0
	 */
	class Give_Moneris_Admin_Settings {


		/**
		 * Give_Moneris_Admin_Settings constructor.
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function __construct() {

			add_filter( 'give_get_sections_gateways', array( $this, 'register_sections' ) );
			add_action( 'give_get_settings_gateways', array( $this, 'register_settings' ) );
		}


		/**
		 * Register Admin Settings.
		 *
		 * @param array $settings List of admin settings.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		function register_settings( $settings ) {

			switch ( give_get_current_setting_section() ) {

				case 'moneris-settings':

					$settings = array(
						array(
							'id'   => 'give_title_moneris',
							'type' => 'title',
						),
						array(
							'name' => __( 'Access Token', 'give-moneris' ),
							'desc' => __( 'Enter the Access Token provided by Moneris for a specific country environment. Please confirm the base country you selected.', 'give-moneris' ),
							'id'   => 'give_moneris_access_token',
							'type' => 'api_key',
						),
						array(
							'name' => __( 'Store ID', 'give-moneris' ),
							'desc' => __( 'Enter the Store ID for which you want to accept the donations.', 'give-moneris' ),
							'id'   => 'give_moneris_store_id',
							'type' => 'text',
						),
						array(
							'name'    => __( 'Statement Descriptor', 'give-moneris' ),
							'desc'    => __( 'This is the text that appears on your donor\'s bank statements. Statement descriptors are limited to 22 characters including <code>/</code>, cannot use the special characters <code><</code>, <code>></code>, <code>\'</code>, or <code>"</code>, and must not consist solely of numbers. This is typically the name of your website or organization.', 'give-moneris' ),
							'id'      => 'give_moneris_statement_descriptor',
							'type'    => 'text',
							'default' => give_moneris_get_default_statement_descriptor(),
						),
						array(
							'name'    => __( 'CVD Validation', 'give-moneris' ),
							'desc'    => __( 'The Card Validation Digits (CVD) value refers to the numbers appearing on the back of the credit card rather than the numbers imprinted on the front1. It is an optional fraud prevention tool that enables merchants to verify data provided by the cardholder at transaction time. This data is submitted along with the transaction to the issuing bank, which provides a response indicating whether the data is a match.', 'give-moneris' ),
							'id'      => 'give_moneris_cvd_validation',
							'type'    => 'radio_inline',
							'options' => [
								'enabled'  => __( 'Enabled', 'give' ),
								'disabled' => __( 'Disabled', 'give' ),
							],
							'default' => 'enabled',
						),
						array(
							'name' => __( 'Collect Billing Details', 'give-moneris' ),
							'desc' => __( 'This option will enable the billing details section for Moneris which requires the donor\'s address to complete the donation. These fields are not required by Moneris to process the transaction, but you may have the need to collect the data.', 'give-moneris' ),
							'id'   => 'give_moneris_collect_billing_details',
							'type' => 'checkbox',
						),
						array(
							'name'  => __( 'Give Moneris Settings Docs Link', 'give-moneris' ),
							'id'    => 'give_moneris_settings_docs_link',
							'url'   => esc_url( 'http://docs.givewp.com/addon-moneris' ),
							'title' => __( 'Moneris Payment Gateway', 'give-moneris' ),
							'type'  => 'give_docs_link',
						),
						array(
							'id'   => 'give_title_moneris',
							'type' => 'sectionend',
						),
					);

					break;

			}// End switch().

			return $settings;
		}


		/**
		 * Register Section for Gateway Settings.
		 *
		 * @param array $sections List of sections.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return mixed
		 */
		public function register_sections( $sections ) {

			$sections['moneris-settings'] = __( 'Moneris', 'give-moneris' );

			return $sections;
		}


	}
}

new Give_Moneris_Admin_Settings();
