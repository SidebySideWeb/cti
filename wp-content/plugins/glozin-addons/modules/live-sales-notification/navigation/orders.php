<?php

namespace Glozin\Addons\Modules\Live_Sales_Notification\Navigation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Glozin\Addons\Modules\Live_Sales_Notification\Helper;

/**
 * Main class of plugin for admin
 */
class Orders {

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
	 * Get order
	 */
	public static function get_orders() {
		$products = array();
		$status = get_option( 'glozin_live_sales_notification_order' , array( 'wc-completed' ) );
		$time = '-' . get_option( 'glozin_live_sales_notification_time' ) . ' ' . get_option( 'glozin_live_sales_notification_time_type' );

		$args = array(
			'status'=> $status,
			'orderby' => 'date',
			'order' => 'DESC',
			'date_created' => '>' . ( strtotime( $time ) ),
		);

		$orders = wc_get_orders( $args );

		if( ! is_array( $orders ) ) {
			return;
		}

		shuffle($orders);

		foreach( $orders as $order ) {
			$order_data     = $order->get_data();
        	$order_id 		= $order_data['id'];
			$order          = wc_get_order( $order_id );

			foreach( $order->get_items() as $item ){
				$product = $item->get_product();

				if( ! is_object( $product) ) {
					continue;
				}

				if( get_option( 'glozin_live_sales_notification_out_of_stock', 'yes' ) == 'no' && ! $product->is_in_stock() ) {
					continue;
				}

				if( count( $products ) >= get_option( 'glozin_live_sales_notification_number', 10 ) ) {
					break;
				}

				$products[] = Helper::format_product_obj( $product, $order );
			}
		}

		return $products;
	}

	public static function get_popups() {
		$popup = array();
		$products = self::get_orders();

		if( ! $products ) {
			return;
		}

		foreach( $products as $product ) {

			if( empty($product) || ! is_array($product) ) {
				continue;
			}

			$popup[] = Helper::get_popup_html( $product );
		}

		return $popup;
	}
}