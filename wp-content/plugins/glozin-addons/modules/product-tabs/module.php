<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_Tabs;

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
		$this->actions();
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
			'Glozin\Addons\Modules\Product_Tabs\FrontEnd'        => GLOZIN_ADDONS_DIR . 'modules/product-tabs/frontend.php',
			'Glozin\Addons\Modules\Product_Tabs\Settings'    	=> GLOZIN_ADDONS_DIR . 'modules/product-tabs/settings.php',
			'Glozin\Addons\Modules\Product_Tabs\Product_Meta'    => GLOZIN_ADDONS_DIR . 'modules/product-tabs/product-meta.php',
			'Glozin\Addons\Modules\Product_Tabs\Post_Type'    		=> GLOZIN_ADDONS_DIR . 'modules/product-tabs/post-type.php',
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
		if ( get_option( 'glozin_product_tab' ) == 'yes' && is_singular('product') && ! is_customize_preview() ) {
			\Glozin\Addons\Modules\Product_Tabs\FrontEnd::instance();
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
		if( is_admin() ) {
			\Glozin\Addons\Modules\Product_Tabs\Settings::instance();

			if ( get_option( 'glozin_product_tab' ) == 'yes' ) {
				\Glozin\Addons\Modules\Product_Tabs\Product_Meta::instance();
			}
		}
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function actions() {
		if ( get_option( 'glozin_product_tab' ) == 'yes' ) {
			\Glozin\Addons\Modules\Product_Tabs\Post_Type::instance();
		}
	}

}
