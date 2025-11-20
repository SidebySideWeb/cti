<?php
/**
 * Glozin Addons Pre Order Helper init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Pre_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper
 */
class Helper {
    public static function is_pre_order_active( $product ) {
		$return = false;

		if ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} else {
			$product_id = is_numeric( $product ) && $product > 0 ? $product : false;
		}

		if ( ! $product_id ) {
			return $return;
		}

		if ( 'yes' === get_post_meta( $product_id, '_pre_order_status', true ) && ! wc_get_product( $product_id )->is_type( 'variable' ) ) {
			$return = true;
		}

		return apply_filters( 'glozin_is_pre_order_active', $return, $product );
	}

	/**
	 * Print the pre-order availability date.
	 *
	 * @return string
	 */
	public static function print_availability_html( $product ) {
		if ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} else {
			$product_id = is_numeric( $product ) && $product > 0 ? $product : false;
		}

		$date = get_post_meta( $product_id, '_pre_order_date', true );
		$date = ! empty( $date ) ? wp_date( get_option( 'date_format' ), $date ) : '';

		$date = apply_filters( 'glozin_pre_order_availability_date', $date, $product_id );

		if ( ! empty( $date ) ) {
			return sprintf( 
				'<div class="glozin-pre-order-availability mb-20">%s</div>', 
				sprintf( __( 'Available on: %1$s', 'glozin-addons' ), $date )
			);
		}

		return '';
	}

	/**
	 * Print the pre-order meta.
	 *
	 * @return string
	 */
	public static function print_pre_order_meta( $product ) {
		if ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} else {
			$product_id = is_numeric( $product ) && $product > 0 ? $product : false;
		}

		$date = get_post_meta( $product_id, '_pre_order_date', true );
		$date = ! empty( $date ) ? wp_date( get_option( 'date_format' ), $date ) : '';

		return sprintf(
			'<div class="glozin-pre-order-meta">
				<div>%s</div>
				<div><strong>%s</strong> %s</div>
			</div>',
			esc_html__( 'Pre-order product', 'glozin-addons' ),
			esc_html__( 'Available on:', 'glozin-addons' ),
			$date
		);
	}

	/**
	 * Get the orders by customer.
	 *
	 * @param int $customer_id The customer ID.
	 * @return array
	 */
	public static function get_orders_by_customer( $customer_id ) {
		$statuses          = wc_get_order_statuses();
		$excluded_statuses = apply_filters( 'glozin_get_orders_by_customer_excluded_statuses', array( 'wc-cancelled', 'wc-refunded', 'wc-failed' ) );
		foreach ( $excluded_statuses as $excluded_status ) {
			unset( $statuses[ $excluded_status ] );
		}

		if ( self::is_wc_custom_orders_table_usage_enabled() ) {
			$args = array(
				'status'      => array_keys( $statuses ),
				'customer_id' => $customer_id,
				'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'   => '_glozin_order_has_preorder',
						'value' => 'yes',
					),
				),
				'return'      => 'ids',
				'limit'       => -1,
			);
		} else {
			$args = array(
				'status'             => array_keys( $statuses ),
				'customer_id'        => $customer_id,
				'_glozin_order_has_preorder' => 'yes',
				'return'             => 'ids',
				'limit'              => -1,
			);
		}

		$orders = wc_get_orders( $args );

		return apply_filters( 'glozin_get_orders_by_customer', $orders, $customer_id );
	}

	/**
	 * Check if the WC custom orders table usage is enabled.
	 *
	 * @return bool
	 */
	public static function is_wc_custom_orders_table_usage_enabled(): bool {
		return class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && is_callable( '\Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	}

	/**
	 * Checks if the order contains any Pre-Order product.
	 *
	 * @param mixed $order The order ID or the WC_Order object.
	 *
	 * @return bool
	 */
	public static function is_order_has_pre_order( $order ) {
		$order = wc_get_order( $order );
		if ( ! $order instanceof \WC_Order ) {
			return false;
		}
		return 'yes' === get_post_meta( $order->get_id(), '_glozin_order_has_preorder', true );
	}
}
