<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Order_Sync {

	public static function init() {
		// Push order async on thankyou (paid) - schedule for immediate execution
		add_action( 'woocommerce_thankyou', [ __CLASS__, 'schedule_order_sync' ], 10, 1 );
		// Hook for async order sync
		add_action( 's1wc_push_order', [ __CLASS__, 'push_order_to_erp' ], 10, 1 );
	}

	/**
	 * Schedule async order sync
	 */
	public static function schedule_order_sync( $order_id ) {
		if ( ! $order_id ) return;
		
		// Schedule for immediate execution (in 1 minute to avoid blocking checkout)
		wp_schedule_single_event( time() + 60, 's1wc_push_order', [ $order_id ] );
		
		Logger::log( "Order {$order_id} scheduled for async ERP sync" );
	}

	/**
	 * Sync orders via cron - retry failed syncs
	 */
	public static function sync_orders() {
		// Find orders that need syncing (completed but not synced to ERP)
		$args = [
			'status' => 'completed',
			'limit' => 50, // Process in batches
			'meta_query' => [
				'relation' => 'OR',
				[
					'key' => '_s1wc_erp_sync_status',
					'compare' => 'NOT EXISTS'
				],
				[
					'key' => '_s1wc_erp_sync_status',
					'value' => 'failed',
					'compare' => '='
				]
			]
		];
		
		$orders = wc_get_orders( $args );
		$synced = 0;
		
		foreach ( $orders as $order ) {
			$result = self::push_order_to_erp( $order->get_id() );
			if ( $result ) {
				$synced++;
			}
		}
		
		Logger::log( "Order sync cron completed: processed " . count($orders) . " orders, synced {$synced}" );
	}

	public static function push_order_to_erp( $order_id ) {
		if ( ! $order_id ) return false;
		$order = wc_get_order( $order_id );
		if ( ! $order ) return false;

		// Check if already synced successfully
		$sync_status = get_post_meta( $order_id, '_s1wc_erp_sync_status', true );
		if ( $sync_status === 'success' ) {
			Logger::log( "Order {$order_id} already synced to ERP successfully" );
			return true;
		}

		$api = SoftOne_API::instance();

		// Map Woo → ERP
		$erp_trdr = get_user_meta( $order->get_user_id(), 's1_customer_code', true );
		if ( empty( $erp_trdr ) ) {
			// fallback: you can implement a lookup by VAT/AFM or email against ERP if needed
			Logger::log( "Order {$order_id}: missing ERP TRDR mapping; skipping." );
			update_post_meta( $order_id, '_s1wc_erp_sync_status', 'failed' );
			update_post_meta( $order_id, '_s1wc_erp_sync_error', 'Missing ERP TRDR mapping' );
			return false;
		}

		// Get TRDBRANCH if available
		$erp_trdbranch = get_user_meta( $order->get_user_id(), 's1_customer_trdbranch', true );

		$payment_map = [
			'cod'        => '12',  // αντικαταβολή
			'irispay'    => '199', // IRIS
			'card'       => '19',  // κάρτα
			'bacs'       => '11',  // έμβασμα
			'cheque'     => '10',  // μετρητοίς (fallback)
		];
		$pay_code = $payment_map[ $order->get_payment_method() ] ?? '10';

		$lines = [];
		$linenum = 1;
		foreach ( $order->get_items() as $item ) {
			/** @var \WC_Order_Item_Product $item */
			$product = $item->get_product();
			if ( ! $product ) continue;
			$sku = $product->get_sku();
			if ( ! $sku ) continue;

			// In many setups MTRL equals ERP Item internal id, but often we post with CODE instead.
			// Here we send CODE in a VARCHAR field (COMMENTS1 in ITELINES not available), so we prefer PRICE + QTY,
			// and the ERP can map by CODE through a custom trigger. For generic approach we assume MTRL is known == SKU numeric.
			$mtrl = is_numeric($sku) ? intval($sku) : 0;

			$lines[] = [
				'LINENUM'  => $linenum++,
				'VAT'      => '24',
				'MTRUNIT4' => '101',
				'MTRL'     => $mtrl, // adapt if you need CODE-based post (custom WS)
				'QTY1'     => (float) $item->get_quantity(),
				'PRICE'    => (float) $order->get_item_total( $item, false, false ),
			];
		}

		$data = [
			'SALDOC' => [[
				'SERIES'    => '600',
				'TRDR'      => (string) $erp_trdr,
				'PAYMENT'   => (string) $pay_code,
				'COMMENTS'  => 'From WooCommerce',
				'CCCFINDOC' => (string) $order_id,
				'COMMENTS1' => 'WEB-' . $order->get_order_number(),
				'VARCHAR02' => $order->get_billing_phone(),
			]],
			'ITELINES' => $lines,
		];

		// Conditionally add TRDBRANCH if available
		if ( ! empty( $erp_trdbranch ) ) {
			$data['SALDOC'][0]['TRDBRANCH'] = (string) $erp_trdbranch;
		}

		$res = $api->set_data( 'SALDOC', $data, '', true );
		if ( is_wp_error( $res ) || empty( $res['success'] ) ) {
			$error_msg = is_wp_error($res) ? implode('; ', $res->get_error_messages()) : (isset($res['error']) ? $res['error'] : 'Unknown error');
			Logger::error( 'ERP order post failed', [ 'order_id' => $order_id, 'error' => $error_msg ] );
			update_post_meta( $order_id, '_s1wc_erp_sync_status', 'failed' );
			update_post_meta( $order_id, '_s1wc_erp_sync_error', $error_msg );
			return false;
		}
		
		// Success
		Logger::log( sprintf( 'Order %d posted to ERP. ERP id: %s', $order_id, $res['id'] ?? '-' ) );
		update_post_meta( $order_id, '_s1wc_erp_sync_status', 'success' );
		update_post_meta( $order_id, '_s1wc_erp_sync_erp_id', $res['id'] ?? '' );
		update_post_meta( $order_id, '_s1wc_erp_sync_time', current_time('mysql') );
		delete_post_meta( $order_id, '_s1wc_erp_sync_error' ); // Clear any previous error
		return true;
	}
}
