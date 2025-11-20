<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Products_Stock_Progress_Bar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Module {

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
		$this->includes();
		add_action('admin_init', array( $this, 'settings'));
		add_action('template_redirect', array( $this, 'product_single'));
	}

	/**
	 * Includes files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		\Glozin\Addons\Auto_Loader::register( [
			'Glozin\Addons\Modules\Products_Stock_Progress_Bar\Frontend'        => GLOZIN_ADDONS_DIR . 'modules/products-stock-progress-bar/frontend.php',
			'Glozin\Addons\Modules\Products_Stock_Progress_Bar\Settings'    	=> GLOZIN_ADDONS_DIR . 'modules/products-stock-progress-bar/settings.php',
			'Glozin\Addons\Modules\Products_Stock_Progress_Bar\Product_Options' => GLOZIN_ADDONS_DIR . 'modules/products-stock-progress-bar/product-options.php',
		] );
	}


	/**
	 * Settings
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings() {
		if ( is_admin() ) {
			\Glozin\Addons\Modules\Products_Stock_Progress_Bar\Settings::instance();

			if ( get_option( 'glozin_products_stock_progress_bar', 'no' ) === 'yes' ) {
				\Glozin\Addons\Modules\Products_Stock_Progress_Bar\Product_Options::instance();
			}
		}
	}

	/**
	 * Single Product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_single() {
		if ( get_option( 'glozin_products_stock_progress_bar' ) == 'yes' && is_singular('product') && ! is_customize_preview() ) {
			\Glozin\Addons\Modules\Products_Stock_Progress_Bar\Frontend::instance();
		}
	}
	
}
