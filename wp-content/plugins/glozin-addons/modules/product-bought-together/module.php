<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_Bought_Together;

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
			'Glozin\Addons\Modules\Product_Bought_Together\Frontend'        => GLOZIN_ADDONS_DIR . 'modules/product-bought-together/frontend.php',
			'Glozin\Addons\Modules\Product_Bought_Together\Settings'    	=> GLOZIN_ADDONS_DIR . 'modules/product-bought-together/settings.php',
			'Glozin\Addons\Modules\Product_Bought_Together\Product_Meta'    => GLOZIN_ADDONS_DIR . 'modules/product-bought-together/product-meta.php',
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
			\Glozin\Addons\Modules\Product_Bought_Together\Settings::instance();

			if ( get_option( 'glozin_product_bought_together', 'no' ) === 'yes' ) {
				\Glozin\Addons\Modules\Product_Bought_Together\Product_Meta::instance();
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
		if ( get_option( 'glozin_product_bought_together' ) == 'yes' && ! is_customize_preview() ) {
			\Glozin\Addons\Modules\Product_Bought_Together\Frontend::instance();
		}
	}
	
}
