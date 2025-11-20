<?php
/**
 * Glozin Addons init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Glozin Addons init
 *
 * @since 1.0.0
 */
class Addons {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_templates' ) );
	}

	/**
	 * Load Templates
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_templates() {
		$this->includes();
		spl_autoload_register( '\Glozin\Addons\Auto_Loader::load' );

		$this->add_actions();

		add_shortcode( 'glozin_year', array( __CLASS__, 'year' ) );
	}

	/**
	 * Includes files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		// Auto Loader
		require_once GLOZIN_ADDONS_DIR . 'autoloader.php';
		\Glozin\Addons\Auto_Loader::register( [
			'Glozin\Addons\Helper'                    => GLOZIN_ADDONS_DIR . 'inc/helper.php',
			'Glozin\Addons\Importer'                  => GLOZIN_ADDONS_DIR . 'inc/backend/importer.php',
			'Glozin\Addons\Page_Header'               => GLOZIN_ADDONS_DIR . 'inc/backend/page-header.php',
			'Glozin\Addons\Single_Post'               => GLOZIN_ADDONS_DIR . 'inc/backend/single-post.php',
			'Glozin\Addons\Theme_Settings'            => GLOZIN_ADDONS_DIR . 'inc/backend/theme-settings.php',
			'Glozin\Addons\Widgets'                   => GLOZIN_ADDONS_DIR . 'inc/widgets/widgets.php',
			'Glozin\Addons\Elementor'                 => GLOZIN_ADDONS_DIR . 'inc/elementor/elementor.php',
			'Glozin\Addons\Modules'                   => GLOZIN_ADDONS_DIR . 'modules/modules.php',
			'Glozin\Addons\WooCommerce\Products_Base' => GLOZIN_ADDONS_DIR . 'inc/woocommerce/products-base.php',
		] );
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function add_actions() {
		// Before init action.
		do_action( 'before_glozin_init' );


		\Glozin\Addons\Theme_Settings::instance();
		if( is_admin() ) {
			\Glozin\Addons\Importer::instance();
			\Glozin\Addons\Page_Header::instance();
			\Glozin\Addons\Single_Post::instance();
		}
		\Glozin\Addons\Widgets::instance();
		if( class_exists('WooCommerce')  ) {
			\Glozin\Addons\Modules::instance();
		}
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			\Glozin\Addons\Elementor::instance();
		}

		// Init action.
		do_action( 'after_glozin_init' );
	}

	/**
	 * Display current year
	 *
	 * @return void
	 */
	public static function year() {
		return date('Y');
	}
}
