<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Product_3D_Viewer;

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
			'Glozin\Addons\Modules\Product_3D_Viewer\Settings'        => GLOZIN_ADDONS_DIR . 'modules/product-3d-viewer/settings.php',
			'Glozin\Addons\Modules\Product_3D_Viewer\Frontend'        => GLOZIN_ADDONS_DIR . 'modules/product-3d-viewer/frontend.php',
			'Glozin\Addons\Modules\Product_3D_Viewer\Product_Options' => GLOZIN_ADDONS_DIR . 'modules/product-3d-viewer/product-options.php',
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
			\Glozin\Addons\Modules\Product_3D_Viewer\Settings::instance();

			if ( get_option( 'glozin_product_3d_viewer', 'no' ) === 'yes' ) {
				\Glozin\Addons\Modules\Product_3D_Viewer\Product_Options::instance();
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
		if ( get_option( 'glozin_product_3d_viewer' ) == 'yes' && is_singular('product') ) {
			\Glozin\Addons\Modules\Product_3D_Viewer\FrontEnd::instance();
		}
	}
}
