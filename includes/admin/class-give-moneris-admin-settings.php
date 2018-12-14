<?php
/**
 * Admin Settings
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
							'name'          => __( 'Access Token', 'give-moneris' ),
							'desc'          => __( 'Enter the Access Token provided by Moneris for a specific country environment. Please confirm the base country you selected.', 'give-moneris' ),
							'id'            => 'give_moneris_access_token',
							'type'          => 'api_key',
						),
						array(
							'name'          => __( 'Store ID', 'give-moneris' ),
							'desc'          => __( 'Enter the Store ID for which you want to accept the donations.', 'give-moneris' ),
							'id'            => 'give_moneris_store_id',
							'type'          => 'text',
						),
						array(
							'name'          => __( 'Collect Billing Details', 'give-moneris' ),
							'desc'          => __( 'This option will enable the billing details section for Moneris which requires the donor\'s address to complete the donation. These fields are not required by Moneris to process the transaction, but you may have the need to collect the data.', 'give-moneris' ),
							'id'            => 'give_moneris_collect_billing_details',
							'type'          => 'checkbox',
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
			
			$sections['moneris-settings'] = __( 'Moneris Settings', 'give-square' );
			
			return $sections;
		}
		
		
	}
}

new Give_Moneris_Admin_Settings();