<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Order_Sync {

	public static function init() {
		add_action( 'woocommerce_thankyou', [ __CLASS__, 'push_order_to_erp' ], 10, 1 );
	}

	public static function push_order_to_erp( $order_id ) {
		if ( ! $order_id ) return;
		$order = wc_get_order( $order_id );
		if ( ! $order ) return;

		$api = SoftOne_API::instance();

		$erp_trdr = get_user_meta( $order->get_user_id(), 's1_customer_trdr', true );
		if ( empty( $erp_trdr ) ) {
			$erp_trdr = get_user_meta( $order->get_user_id(), 's1_customer_code', true );
		}
		
		if ( empty( $erp_trdr ) ) {
			Logger::log( "Order {$order_id}: missing ERP TRDR mapping; skipping." );
			return;
		}
		
		$erp_trdbranch = get_user_meta( $order->get_user_id(), 's1_customer_trdbranch', true );
		
		if ( strpos( $erp_trdr, '.' ) !== false ) {
			$parts = explode( '.', $erp_trdr );
			$erp_trdr = $parts[0];
			if ( empty( $erp_trdbranch ) && isset( $parts[1] ) ) {
				$erp_trdbranch = $parts[1];
			}
		}
		
		$erp_trdr = (string) intval( $erp_trdr );

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
				$product = $item->get_product();
				if ( ! $product ) continue;
				$sku = $product->get_sku();
				if ( ! $sku ) continue;

				$product_id = $product->get_id();
				$mtrl = get_post_meta( $product_id, '_softone_mtrl', true );
				
				if ( empty( $mtrl ) && is_numeric( $sku ) ) {
					$mtrl = intval( $sku );
				}
				
				if ( empty( $mtrl ) ) {
					Logger::warning( "Order {$order_id}: Product SKU {$sku} (ID: {$product_id}) has no MTRL; skipping line item." );
					continue;
				}
				
				$mtrl = (int) $mtrl;

			$lines[] = [
				'LINENUM'  => $linenum++,
				'VAT'      => '24',
				'MTRUNIT4' => '101',
				'MTRL'     => $mtrl,
				'QTY1'     => (float) $item->get_quantity(),
				'PRICE'    => (float) $order->get_item_total( $item, false, false ),
			];
		}

		$saldoc_header = [
			'SERIES'    => '600',
			'TRDR'      => $erp_trdr,
			'PAYMENT'   => (string) $pay_code,
			'COMMENTS'  => 'From WooCommerce',
			'CCCFINDOC' => (string) $order_id,
			'COMMENTS1' => 'WEB-' . $order->get_order_number(),
		];
		
		if ( ! empty( $erp_trdbranch ) ) {
			$saldoc_header['TRDBRANCH'] = (string) intval( $erp_trdbranch );
		}
		
		if ( $order->get_billing_phone() ) {
			$saldoc_header['VARCHAR02'] = $order->get_billing_phone();
		}

		$data = [
			'SALDOC' => [ $saldoc_header ],
			'ITELINES' => $lines,
		];

		$res = $api->set_data( 'SALDOC', $data, '', true );
		if ( is_wp_error( $res ) || empty( $res['success'] ) ) {
			if ( is_wp_error( $res ) ) {
				$error_msg = $res->get_error_message();
				$error_code = $res->get_error_code();
				$error_data = $res->get_error_data();
			} else {
				$error_msg = $res['message'] ?? $res['error'] ?? 'Unknown error';
				$error_code = $res['code'] ?? 'unknown';
				$error_data = $res;
			}
			
			Logger::error( "Order {$order_id} ERP post failed", [
				'error_message' => $error_msg,
				'error_code' => $error_code,
				'error_data' => $error_data,
				'order_id' => $order_id,
				'order_number' => $order->get_order_number(),
			] );
			
			// Store failure status
			update_post_meta( $order_id, '_s1wc_erp_sync_status', 'failed' );
			update_post_meta( $order_id, '_s1wc_erp_sync_error', $error_msg );
			update_post_meta( $order_id, '_s1wc_erp_sync_error_code', $error_code );
			update_post_meta( $order_id, '_s1wc_erp_sync_time', time() );
			return;
		}
		
		$erp_id = $res['id'] ?? '';
		Logger::log( sprintf( 'Order %d posted to ERP. ERP id: %s', $order_id, $erp_id ) );
		
		update_post_meta( $order_id, '_s1wc_erp_sync_status', 'success' );
		update_post_meta( $order_id, '_s1wc_erp_sync_erp_id', $erp_id );
		update_post_meta( $order_id, '_s1wc_erp_sync_time', time() );
		delete_post_meta( $order_id, '_s1wc_erp_sync_error' );
		delete_post_meta( $order_id, '_s1wc_erp_sync_error_code' );
	}

	public static function sync_orders( $force_full_sync = false ) {
		$args = [
			'limit' => -1,
			'status' => [ 'wc-processing', 'wc-completed', 'wc-on-hold' ],
			'orderby' => 'date',
			'order' => 'DESC',
		];

		if ( ! $force_full_sync ) {
			$args['date_created'] = date( 'Y-m-d', strtotime( '-30 days' ) ) . '...' . date( 'Y-m-d' );
		}

		$orders = wc_get_orders( $args );
		$total = 0;
		$synced = 0;
		$failed = 0;
		$sync_start_time = time();

		foreach ( $orders as $order ) {
			$order_id = $order->get_id();
			
			if ( ! $force_full_sync ) {
				$synced_meta = get_post_meta( $order_id, '_s1wc_erp_sync_status', true );
				if ( $synced_meta === 'success' ) {
					continue;
				}
			}

			$erp_trdr = get_user_meta( $order->get_user_id(), 's1_customer_trdr', true );
			if ( empty( $erp_trdr ) ) {
				$erp_trdr = get_user_meta( $order->get_user_id(), 's1_customer_code', true );
			}
			
			if ( empty( $erp_trdr ) ) {
				Logger::log( "Order {$order_id}: missing ERP TRDR mapping; skipping." );
				update_post_meta( $order_id, '_s1wc_erp_sync_status', 'skipped' );
				update_post_meta( $order_id, '_s1wc_erp_sync_error', 'Missing ERP customer code' );
				$failed++;
				continue;
			}
			
			$erp_trdbranch = get_user_meta( $order->get_user_id(), 's1_customer_trdbranch', true );
			
			if ( strpos( $erp_trdr, '.' ) !== false ) {
				$parts = explode( '.', $erp_trdr );
				$erp_trdr = $parts[0];
				if ( empty( $erp_trdbranch ) && isset( $parts[1] ) ) {
					$erp_trdbranch = $parts[1];
				}
			}
			
			$erp_trdr = (string) intval( $erp_trdr );

			$api = SoftOne_API::instance();

			$payment_map = [
				'cod'        => '12',
				'irispay'    => '199',
				'card'       => '19',
				'bacs'       => '11',
				'cheque'     => '10',
			];
			$pay_code = $payment_map[ $order->get_payment_method() ] ?? '10';

			$lines = [];
			$linenum = 1;
			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( ! $product ) continue;
				$sku = $product->get_sku();
				if ( ! $sku ) continue;

				$product_id = $product->get_id();
				$mtrl = get_post_meta( $product_id, '_softone_mtrl', true );
				
				if ( empty( $mtrl ) && is_numeric( $sku ) ) {
					$mtrl = intval( $sku );
				}
				
				if ( empty( $mtrl ) ) {
					Logger::warning( "Order {$order_id}: Product SKU {$sku} (ID: {$product_id}) has no MTRL; skipping line item." );
					continue;
				}
				
				$mtrl = (int) $mtrl;

				$lines[] = [
					'LINENUM'  => $linenum++,
					'VAT'      => '24',
					'MTRUNIT4' => '101',
					'MTRL'     => $mtrl,
					'QTY1'     => (float) $item->get_quantity(),
					'PRICE'    => (float) $order->get_item_total( $item, false, false ),
				];
			}

			if ( empty( $lines ) ) {
				Logger::log( "Order {$order_id}: no line items; skipping." );
				update_post_meta( $order_id, '_s1wc_erp_sync_status', 'skipped' );
				update_post_meta( $order_id, '_s1wc_erp_sync_error', 'No line items' );
				$failed++;
				continue;
			}

			$saldoc_header = [
				'SERIES'    => '600',
				'TRDR'      => $erp_trdr,
				'PAYMENT'   => (string) $pay_code,
				'COMMENTS'  => 'From WooCommerce',
				'CCCFINDOC' => (string) $order_id,
				'COMMENTS1' => 'WEB-' . $order->get_order_number(),
			];
			
			if ( ! empty( $erp_trdbranch ) ) {
				$saldoc_header['TRDBRANCH'] = (string) intval( $erp_trdbranch );
			}
			
			if ( $order->get_billing_phone() ) {
				$saldoc_header['VARCHAR02'] = $order->get_billing_phone();
			}

			$data = [
				'SALDOC' => [ $saldoc_header ],
				'ITELINES' => $lines,
			];

			$res = $api->set_data( 'SALDOC', $data, '', true );
			if ( is_wp_error( $res ) || empty( $res['success'] ) ) {
				if ( is_wp_error( $res ) ) {
					$error_msg = $res->get_error_message();
					$error_code = $res->get_error_code();
					$error_data = $res->get_error_data();
				} else {
					$error_msg = $res['message'] ?? $res['error'] ?? 'Unknown error';
					$error_code = $res['code'] ?? 'unknown';
					$error_data = $res;
				}
				
				Logger::error( "Order {$order_id} sync failed", [
					'error_message' => $error_msg,
					'error_code' => $error_code,
					'error_data' => $error_data,
					'order_id' => $order_id,
					'order_number' => $order->get_order_number(),
					'erp_trdr' => $erp_trdr,
					'payment_method' => $order->get_payment_method(),
					'line_items_count' => count( $lines ),
				] );
				
				update_post_meta( $order_id, '_s1wc_erp_sync_status', 'failed' );
				update_post_meta( $order_id, '_s1wc_erp_sync_error', $error_msg );
				update_post_meta( $order_id, '_s1wc_erp_sync_error_code', $error_code );
				update_post_meta( $order_id, '_s1wc_erp_sync_time', time() );
				$failed++;
			} else {
				Logger::log( sprintf( 'Order %d posted to ERP. ERP id: %s', $order_id, $res['id'] ?? '-' ) );
				update_post_meta( $order_id, '_s1wc_erp_sync_status', 'success' );
				update_post_meta( $order_id, '_s1wc_erp_sync_erp_id', $res['id'] ?? '' );
				update_post_meta( $order_id, '_s1wc_erp_sync_time', time() );
				delete_post_meta( $order_id, '_s1wc_erp_sync_error' );
				$synced++;
			}
			$total++;
		}

		$duration = time() - $sync_start_time;
		Logger::log( sprintf( 'Orders sync completed. Processed: %d, Synced: %d, Failed: %d in %d seconds', $total, $synced, $failed, $duration ) );
	}
}
