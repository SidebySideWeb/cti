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
class Categories {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;
	private static $products = array();


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
	 * Get selected categories
	 *
	 * @return array
	 */
	public static function get_selected_categories() {
		$categories = get_option( 'glozin_live_sales_notification_category', array() );

		return is_array($categories) ? $categories : array();
	}

	/**
	 * Get product form categories
	 *
	 * @param array $cat_slug
	 * @return void
	 */
	public static function get_product_from_category( $cat_slug ) {
		$products = new \WP_Query( array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'posts_per_page'=> -1,
			'fields'      => 'ids',
			'tax_query'   => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $cat_slug,
				)
			),
		));

		wp_reset_query();

		return $products->posts;
	}

	/**
	 * Get order
	 */
	public static function get_orders() {
		$categories = self::get_selected_categories();
		$products_categories = array();

		foreach( $categories as $category ) {
			$products_list = self::get_product_from_category( $category );
			if( is_array($products_list) ) {
				$products_categories = array_merge( $products_categories, $products_list );
			}
		}

		$products_categories = array_unique($products_categories);
		shuffle($products_categories);
		$products_categories = array_map( 'wc_get_product', $products_categories );

		if( is_array( $products_categories ) && count( $products_categories ) > 0 ) {
			foreach( $products_categories as $product ) {
				if( ! is_object($product) ) {
					continue;
				}

				if( get_option( 'glozin_live_sales_notification_out_of_stock', 'yes' ) == 'no' && ! $product->is_in_stock() ) {
					continue;
				}

				if( count( self::$products ) >= get_option( 'glozin_live_sales_notification_number', 10 ) ) {
					break;
				}

				$order = new Navigation\Orders_Fake();

				self::$products[] = Helper::format_product_obj( $product, $order );
			}

			if( count( self::$products ) < get_option( 'glozin_live_sales_notification_number', 10 ) && count(self::$products) > 0 ) {
				self::get_orders();
			}
		}

		return self::$products;
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