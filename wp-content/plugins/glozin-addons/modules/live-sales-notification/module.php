<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Live_Sales_Notification;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
			'Glozin\Addons\Modules\Live_Sales_Notification\Settings'   => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/settings.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Frontend'   => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/frontend.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Helper'     => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/helper.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Navigation' => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/navigation/navigation.php',
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
			\Glozin\Addons\Modules\Live_Sales_Notification\Settings::instance();
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
		if ( get_option( 'glozin_live_sales_notification' ) == 'yes' && ! is_customize_preview()) {
			\Glozin\Addons\Modules\Live_Sales_Notification\Helper::instance();
			\Glozin\Addons\Modules\Live_Sales_Notification\Frontend::instance();
			\Glozin\Addons\Modules\Live_Sales_Notification\Navigation::instance();
		}
	}

}
