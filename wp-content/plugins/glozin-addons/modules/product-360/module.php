<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_360;

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
			'Glozin\Addons\Modules\Product_360\Settings'        => GLOZIN_ADDONS_DIR . 'modules/product-360/settings.php',
			'Glozin\Addons\Modules\Product_360\Frontend'        => GLOZIN_ADDONS_DIR . 'modules/product-360/frontend.php',
			'Glozin\Addons\Modules\Product_360\Product_Options' => GLOZIN_ADDONS_DIR . 'modules/product-360/product-options.php',
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
			\Glozin\Addons\Modules\Product_360\Settings::instance();

			if ( get_option( 'glozin_product_360', 'no' ) === 'yes' ) {
				\Glozin\Addons\Modules\Product_360\Product_Options::instance();
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
		if ( get_option( 'glozin_product_360' ) == 'yes' && is_singular('product') ) {
			\Glozin\Addons\Modules\Product_360\FrontEnd::instance();
		}
	}
}
