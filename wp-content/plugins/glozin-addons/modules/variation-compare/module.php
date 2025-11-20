<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Variation_Compare;


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

		add_action('template_redirect', array( $this, 'product_single'));

		add_action('admin_init', array( $this, 'settings'));
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
			'Glozin\Addons\Modules\Variation_Compare\Frontend'      => GLOZIN_ADDONS_DIR . 'modules/variation-compare/frontend.php',
			'Glozin\Addons\Modules\Variation_Compare\Settings'    	=> GLOZIN_ADDONS_DIR . 'modules/variation-compare/settings.php',
			'Glozin\Addons\Modules\Variation_Compare\Product_Options'    	=> GLOZIN_ADDONS_DIR . 'modules/variation-compare/product-options.php',
			'Glozin\Addons\Modules\Variation_Compare\Variation_Select'    => GLOZIN_ADDONS_DIR . 'modules/variation-compare/variation-select.php',
		] );
	}

	/**
	 * Single Product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_single() {
		if ( get_option( 'glozin_variation_compare_toggle', 'yes' ) == 'yes' && is_singular('product') ) {
			\Glozin\Addons\Modules\Variation_Compare\Frontend::instance();
		}
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
			\Glozin\Addons\Modules\Variation_Compare\Settings::instance();
			\Glozin\Addons\Modules\Variation_Compare\Product_Options::instance();
		}
	}

}
