<?php

namespace Glozin\Addons\Modules\Live_Sales_Notification\Navigation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Glozin\Addons\Modules\Live_Sales_Notification\Helper,
	Glozin\Addons\Modules\Live_Sales_Notification\Navigation;

/**
 * Main class of plugin for admin
 */
class Product_Type {

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
	 * Get product type
	 */
	public static function product_type() {
		$product_type = array();

		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		switch( get_option( 'glozin_live_sales_notification_product_type' ) ) {
			case 'recent':
				$product_type = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
				break;

			case 'featured':
				$product_type = wc_get_featured_product_ids();
				break;

			case 'best_selling':
				$args = array_merge( array(
					'meta_key' => 'total_sales',
					'order'    => 'DESC',
					'orderby'  => 'meta_value_num',
				), $args );

				$query = new \WP_Query( $args );

				while ( $query->have_posts() ) { $query->the_post();
					$product_type[] = get_the_ID();
				}

				break;

			case 'top_rated':
				$args = array_merge( array(
					'meta_key' => '_wc_average_rating',
					'order'    => 'DESC',
					'orderby'  => 'meta_value_num',
				), $args );

				$query = new \WP_Query( $args );

				while ( $query->have_posts() ) { $query->the_post();
					$product_type[] = get_the_ID();
				}

				break;

			case 'sale':
				$product_type = wc_get_product_ids_on_sale();
				break;
		}

		wp_reset_query();

		if( empty( $product_type ) ) {
			return;
		}

		$product_type = array_map( 'wc_get_product', $product_type );

		return $product_type;
	}

	/**
	 * Get order
	 */
	public static function get_orders() {
		$products = array();

		$product_type = self::product_type();

		if( is_array( $product_type ) && count( $product_type ) > 0 ) {
			shuffle($product_type);

			foreach($product_type as $product) {
				if( ! is_object($product) ) {
					continue;
				}

				if( get_option( 'glozin_live_sales_notification_out_of_stock', 'yes' ) == 'no' && ! $product->is_in_stock() ) {
					continue;
				}

				if( count( $products ) >= get_option( 'glozin_live_sales_notification_number', 10 ) ) {
					break;
				}

				$order = new Navigation\Orders_Fake();

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