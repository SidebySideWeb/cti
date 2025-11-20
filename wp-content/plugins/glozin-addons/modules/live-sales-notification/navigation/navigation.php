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
 * Addons Navigation
 */
class Navigation {

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
			'Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Orders_Fake'       => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/navigation/orders-fake.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Orders'    	       => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/navigation/orders.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Product_Type' 	   => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/navigation/product-type.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Selected_Products' => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/navigation/selected-products.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Categories'		   => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/navigation/categories.php',
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
		\Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Orders_Fake::instance();
		\Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Orders::instance();
		\Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Product_Type::instance();
		\Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Selected_Products::instance();
		\Glozin\Addons\Modules\Live_Sales_Notification\Navigation\Categories::instance();
	}

}
