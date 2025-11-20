<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Sticky_Add_To_Cart;


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
			'Glozin\Addons\Modules\Sticky_Add_To_Cart\Frontend'      => GLOZIN_ADDONS_DIR . 'modules/sticky-add-to-cart/frontend.php',
			'Glozin\Addons\Modules\Sticky_Add_To_Cart\Settings'    	=> GLOZIN_ADDONS_DIR . 'modules/sticky-add-to-cart/settings.php',
			'Glozin\Addons\Modules\Sticky_Add_To_Cart\Variation_Select'    => GLOZIN_ADDONS_DIR . 'modules/sticky-add-to-cart/variation-select.php',
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
		if ( get_option( 'glozin_sticky_add_to_cart_toggle', 'yes' ) == 'yes' && is_singular('product') && ! is_customize_preview() ) {
			\Glozin\Addons\Modules\Sticky_Add_To_Cart\Frontend::instance();
		}
	}


	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings() {
		if ( is_admin() ) {
			\Glozin\Addons\Modules\Sticky_Add_To_Cart\Settings::instance();
		}
	}

}
