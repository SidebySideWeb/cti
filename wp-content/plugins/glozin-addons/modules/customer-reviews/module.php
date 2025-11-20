<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Customer_Reviews;

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
			'Glozin\Addons\Modules\Customer_Reviews\Settings' => GLOZIN_ADDONS_DIR . 'modules/customer-reviews/settings.php',
			'Glozin\Addons\Modules\Customer_Reviews\Meta_Box' => GLOZIN_ADDONS_DIR . 'modules/customer-reviews/meta-box.php',
			'Glozin\Addons\Modules\Customer_Reviews\Frontend' => GLOZIN_ADDONS_DIR . 'modules/customer-reviews/frontend.php',
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
			\Glozin\Addons\Modules\Customer_Reviews\Settings::instance();

			if ( get_option( 'glozin_customer_reviews_upload' ) == 'yes' ) {
				\Glozin\Addons\Modules\Customer_Reviews\Meta_Box::instance();
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
		if ( get_option( 'glozin_customer_reviews_upload' ) == 'yes' ) {
			\Glozin\Addons\Modules\Customer_Reviews\Frontend::instance();
		}
	}
}
