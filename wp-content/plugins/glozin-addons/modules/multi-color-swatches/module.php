<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Multi_Color_Swatches;

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
			'Glozin\Addons\Modules\Multi_Color_Swatches\Frontend' => GLOZIN_ADDONS_DIR . 'modules/multi-color-swatches/frontend.php',
			'Glozin\Addons\Modules\Multi_Color_Swatches\Settings' => GLOZIN_ADDONS_DIR . 'modules/multi-color-swatches/settings.php',
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
			\Glozin\Addons\Modules\Multi_Color_Swatches\Settings::instance();

			if ( get_option( 'glozin_multi_color_swatches' ) == 'yes' ) {
				\Glozin\Addons\Modules\Multi_Color_Swatches\Term_Meta::instance();
				\Glozin\Addons\Modules\Multi_Color_Swatches\Product_Options::instance();
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
		if ( get_option( 'glozin_multi_color_swatches' ) == 'yes' ) {
			\Glozin\Addons\Modules\Multi_Color_Swatches\Frontend::instance();
		}
	}
}
