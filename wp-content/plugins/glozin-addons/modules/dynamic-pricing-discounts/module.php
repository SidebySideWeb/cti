<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Dynamic_Pricing_Discounts;

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
			'Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Post_Type' => GLOZIN_ADDONS_DIR . 'modules/dynamic-pricing-discounts/post-type.php',
			'Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Meta_Box'  => GLOZIN_ADDONS_DIR . 'modules/dynamic-pricing-discounts/meta-box.php',
			'Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Frontend'  => GLOZIN_ADDONS_DIR . 'modules/dynamic-pricing-discounts/frontend.php',
			'Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Settings'  => GLOZIN_ADDONS_DIR . 'modules/dynamic-pricing-discounts/settings.php',
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
			\Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Settings::instance();
		}

		if ( get_option( 'glozin_dynamic_pricing_discounts', 'yes' ) == 'yes' ) {
			\Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Post_Type::instance();
			\Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Frontend::instance();

			if ( is_admin() ) {
				\Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Meta_Box::instance();
			}
		}

	}

}
