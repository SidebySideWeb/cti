<?php

namespace Glozin\Addons\Modules\Live_Sales_Notification;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Glozin\Addons\Modules\Live_Sales_Notification\Navigation;

/**
 * Main class of plugin for admin
 */
class Frontend {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wc_ajax_live_sales_notification', array( $this, 'get_orders' ) );

		add_action( 'wp_footer', array( $this, 'live_sales_notification_html' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$exclude_pages = get_option( 'glozin_live_sales_notification_exclude_page', []);
		if( is_page() && $exclude_pages &&  in_array( get_the_ID(), $exclude_pages ) ) {
			return;
		}

		if( is_customize_preview() ) {
			return;
		}

		if( ! empty($_GET['elementor-preview']) ) {
			return;
		}

		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('glozin-live-sales-notification', GLOZIN_ADDONS_URL . 'modules/live-sales-notification/assets/live-sales-notification' . $debug . '.js',  array('jquery'), GLOZIN_ADDONS_VER, array( 'strategy' => 'defer' )	 );
		wp_enqueue_style('glozin-live-sales-notification-style', GLOZIN_ADDONS_URL . 'modules/live-sales-notification/assets/live-sales-notification' . $debug . '.css', array(), GLOZIN_ADDONS_VER );

		$datas = array(
			'numberShow'   => get_option( 'glozin_live_sales_notification_number', 10 ),
			'time_start'   => get_option( 'glozin_live_sales_notification_time_start', 6000 ),
			'time_keep'    => get_option( 'glozin_live_sales_notification_time_keep_opened', 6000 ),
			'time_between' => get_option( 'glozin_live_sales_notification_time_between', 6000 ),
			'ajax_url'	   => class_exists( 'WC_AJAX' ) ? \WC_AJAX::get_endpoint( '%%endpoint%%' ) : ''
		);

		wp_localize_script(
			'glozin-live-sales-notification', 'glozinSBP', $datas
		);
	}

	public function get_orders() {
		wp_send_json_success( self::popups_content() );

		die;
	}

	public function popups_content() {
		$products = array();

		$navigation = get_option( 'glozin_live_sales_notification_navigation' );

		switch( $navigation ) {
			case 'orders':
				$products = Navigation\Orders::get_popups();
				break;

			case 'product-type':
				$products = Navigation\Product_Type::get_popups();
				break;

			case 'selected-products':
				$products = Navigation\Selected_Products::get_popups();
				break;

			case 'selected-categories':
				$products = Navigation\Categories::get_popups();
				break;
		}

		if( empty( $products ) ) {
			return;
		}

		shuffle($products);

		return $products;
	}

	/**
	 * Live sales notification html
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function live_sales_notification_html() {
		echo '<div id="live-sales-notification" class="d-none"></div>';
	}

}