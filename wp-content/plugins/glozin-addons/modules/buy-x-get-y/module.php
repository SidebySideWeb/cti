<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Buy_X_Get_Y;

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
			'Glozin\Addons\Modules\Buy_X_Get_Y\Settings'   => GLOZIN_ADDONS_DIR . 'modules/buy-x-get-y/settings.php',
			'Glozin\Addons\Modules\Buy_X_Get_Y\Post_Type' => GLOZIN_ADDONS_DIR . 'modules/buy-x-get-y/post-type.php',
			'Glozin\Addons\Modules\Buy_X_Get_Y\Meta_Box'  => GLOZIN_ADDONS_DIR . 'modules/buy-x-get-y/meta-box.php',
			'Glozin\Addons\Modules\Buy_X_Get_Y\Frontend'   => GLOZIN_ADDONS_DIR . 'modules/buy-x-get-y/frontend.php',
		] );
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function actions() {
		if ( is_admin() ) {
			\Glozin\Addons\Modules\Buy_X_Get_Y\Settings::instance();
		}

		if ( get_option( 'glozin_buy_x_get_y' ) == 'yes' ) {
			\Glozin\Addons\Modules\Buy_X_Get_Y\Post_Type::instance();
			\Glozin\Addons\Modules\Buy_X_Get_Y\Frontend::instance();

			if ( is_admin() ) {
				\Glozin\Addons\Modules\Buy_X_Get_Y\Meta_Box::instance();
			}
		}
	}

}
