<?php
/**
 * Plugin Name: Give - Moneris
 * Plugin URI:  https://givewp.com/addons/moneris-gateway/
 * Description: Adds support to accept donations via the Moneris Payment gateway.
 * Version:     1.0.1
 * Author:      GiveWP
 * Author URI:  https://givewp.com
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: give-moneris
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Give_Moneris' ) ) :

/**
 * Class Give_Moneris
 *
 * @since 1.0.0
 */
final class Give_Moneris {
	/**
	 * Instance.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @var Give_Moneris
	 */
	private static $instance;

	/**
	 * Notices (array)
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Get instance.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return Give_Moneris
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup Give Moneris.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function setup() {

		// Setup constants.
		$this->setup_constants();

		// Give init hook.
		add_action( 'give_init', array( $this, 'init' ), 10 );
		add_action( 'admin_init', array( $this, 'check_environment' ), 999 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
	}

	/**
	 * Setup constants
	 *
	 * Defines useful constants to use throughout the add-on.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function setup_constants() {

		if ( ! defined( 'GIVE_MONERIS_VERSION' ) ) {
			define( 'GIVE_MONERIS_VERSION', '1.0.1' );
		}

		if ( ! defined( 'GIVE_MONERIS_MIN_GIVE_VERSION' ) ) {
			define( 'GIVE_MONERIS_MIN_GIVE_VERSION', '2.3.0' );
		}

		if ( ! defined( 'GIVE_MONERIS_MIN_PHP_VERSION' ) ) {
			define( 'GIVE_MONERIS_MIN_PHP_VERSION', '5.4' );
		}

		if ( ! defined( 'GIVE_MONERIS_PLUGIN_FILE' ) ) {
			define( 'GIVE_MONERIS_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'GIVE_MONERIS_PLUGIN_DIR' ) ) {
			define( 'GIVE_MONERIS_PLUGIN_DIR', plugin_dir_path( GIVE_MONERIS_PLUGIN_FILE ) );
		}

		if ( ! defined( 'GIVE_MONERIS_PLUGIN_URL' ) ) {
			define( 'GIVE_MONERIS_PLUGIN_URL', plugin_dir_url( GIVE_MONERIS_PLUGIN_FILE ) );
		}

		if ( ! defined( 'GIVE_MONERIS_PLUGIN_BASENAME' ) ) {
			define( 'GIVE_MONERIS_PLUGIN_BASENAME', plugin_basename( GIVE_MONERIS_PLUGIN_FILE ) );
		}
	}

	/**
	 * Initialize Plugin after plugins loaded.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Load Text Domain for Give - Moneris Integration.
		load_plugin_textdomain( 'give-moneris', false, GIVE_MONERIS_PLUGIN_BASENAME . '/languages' );
		$this->licensing();

		if ( ! $this->get_environment_warning() ) {
			return;
		}

		$this->includes();
		$this->activation_banner();

	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/admin/class-give-moneris-admin-settings.php';
		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/admin/admin-filters.php';
		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/admin/admin-actions.php';
		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/scripts.php';
		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/misc-functions.php';
		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/class-moneris-api.php';
		require_once GIVE_MONERIS_PLUGIN_DIR . '/includes/class-give-moneris-gateway.php';

	}

	/**
	 * Plugin Licensing.
	 *
	 * @since 1.0.0
	 */
	public function licensing() {
		if ( class_exists( 'Give_License' ) ) {
			new Give_License( GIVE_MONERIS_PLUGIN_FILE, 'Moneris Gateway', GIVE_MONERIS_VERSION, 'WordImpress', 'moneris_license_key' );
		}
	}

	/**
	 * Check plugin environment.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function check_environment() {

		// Flag to check whether plugin file is loaded or not.
		$is_working = true;

		// Load plugin helper functions.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// Check if Give Core plugin is active or not.
		$is_give_active = defined( 'GIVE_PLUGIN_BASENAME' ) ? is_plugin_active( GIVE_PLUGIN_BASENAME ) : false;

		if ( empty( $is_give_active ) ) {

			// Show admin notice.
			$this->add_admin_notice(
				'prompt_give_activate',
				'error',
				sprintf(
					/* translators: 1. Strong Text, 2. Intro Text, 3. URL, 4. Plugin Name, 5. End Text. */
					'<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s',
					__( 'Activation Error:', 'give-moneris' ),
					__( 'You must have the', 'give-moneris' ),
					esc_url_raw( 'https://givewp.com' ),
					__( 'Give', 'give-moneris' ),
					__( 'plugin installed and activated for Give - Moneris to activate.', 'give-moneris' )
				)
			);
			$is_working = false;
		}

		return $is_working;
	}

	/**
	 * Check plugin for Give environment.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function get_environment_warning() {

		// Flag to check whether plugin file is loaded or not.
		$is_working = true;

		// Verify dependency cases.
		if (
			defined( 'GIVE_VERSION' )
			&& version_compare( GIVE_VERSION, GIVE_MONERIS_MIN_GIVE_VERSION, '<' )
		) {

			// Display notice when minimum Give Core plugin version is not found.
			$this->add_admin_notice(
				'prompt_give_incompatible',
				'error',
				sprintf(
					/* translators: 1. Strong Text, 2. Intro Text, 3. URL, 4. Plugin Name, 5. Plugin Text, 6. Plugin Version, 7. End Text */
					'<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s %6$s %7$s',
					__( 'Activation Error:', 'give-moneris' ),
					__( 'You must have the', 'give-moneris' ),
					esc_url_raw( 'https://givewp.com' ),
					__( 'Give', 'give-moneris' ),
					__( 'core version', 'give-moneris' ),
					GIVE_MONERIS_MIN_GIVE_VERSION,
					__( 'for the Give - Moneris add-on to activate.', 'give-moneris' )
				)
			);

			$is_working = false;
		}


		if ( version_compare( phpversion(), GIVE_MONERIS_MIN_PHP_VERSION, '<' ) ) {
			$this->add_admin_notice(
				'prompt_give_incompatible',
				'error',
				sprintf(
					/* translators: 1. Strong Text, 2. Intro Text, 3. URL, 4. PHP text, 5. Min Plugin Version, 6. End Text */
					'<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s %6$s',
					__( 'Activation Error:', 'give-moneris' ),
					__( 'You must have the', 'give-moneris' ),
					esc_url_raw( 'https://givewp.com/documentation/core/requirements/' ),
					__( 'PHP version', 'give-moneris' ),
					GIVE_MONERIS_MIN_PHP_VERSION,
					__( 'or above for the Give - Moneris gateway add-on to activate.', 'give-moneris' )
				)
			);

			$is_working = false;
		}

		return $is_working;
	}

	/**
	 * Allow this class and other classes to add notices.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug    Admin Notice Slug.
	 * @param string $class   Admin Notice Type.
	 * @param string $message Admin Notice Message.
	 */
	public function add_admin_notice( $slug, $class, $message ) {
		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message,
		);
	}

	/**
	 * Display admin notices.
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {

		$allowed_tags = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
				'class' => array(),
				'id'    => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'span'   => array(
				'class' => array(),
			),
			'strong' => array(),
		);

		foreach ( (array) $this->notices as $notice_key => $notice ) {
			echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
			echo wp_kses( $notice['message'], $allowed_tags );
			echo '</p></div>';
		}

	}

	/**
	 * Show activation banner for this add-on.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function activation_banner() {

		// Check for activation banner inclusion.
		if (
			! class_exists( 'Give_Addon_Activation_Banner' )
			&& file_exists( GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php' )
		) {
			include GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php';
		}

		// Initialize activation welcome banner.
		if ( class_exists( 'Give_Addon_Activation_Banner' ) ) {

			// Only runs on admin.
			$args = array(
				'file'              => GIVE_MONERIS_PLUGIN_FILE,
				'name'              => __( 'Moneris Gateway', 'give-moneris' ),
				'version'           => GIVE_MONERIS_VERSION,
				'settings_url'      => admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=moneris-settings' ),
				'documentation_url' => 'http://docs.givewp.com/addon-moneris',
				'support_url'       => 'https://givewp.com/support/',
				'testing'           => false,
			);
			new Give_Addon_Activation_Banner( $args );
		}

		return true;
	}
}

endif;

/**
 * The main function responsible for returning the one true Give_Moneris instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $moneris = Give_Moneris(); ?>
 *
 * @since 1.0.0
 *
 * @return Give_Moneris|bool
 */
function Give_Moneris() {
	return Give_Moneris::get_instance();
}

Give_Moneris();
