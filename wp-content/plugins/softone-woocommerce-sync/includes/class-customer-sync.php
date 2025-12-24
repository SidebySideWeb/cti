<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Customer_Sync {

	const OPTION_LAST_SYNC_TIME = 's1wc_last_customer_sync_time';

	public static function init() {}

	public static function sync_customers( $force_full_sync = false ) {
		$api = SoftOne_API::instance();
		$list = Settings::get('browser_customers', 'CtiWSCustomers');

		$last_sync_time = get_option( self::OPTION_LAST_SYNC_TIME, 0 );
		
		$filters = [];
		if ( $force_full_sync ) {
			$filters = [];
			Logger::log( 'Force full sync: fetching all customers (no date filter)' );
		} elseif ( $last_sync_time > 0 ) {
			$last_sync_date = date( 'Y-m-d H:i:s', $last_sync_time - 3600 );
			$filters['CUSTOMER.UPDDATE>'] = $last_sync_date;
			Logger::log( sprintf( 'Incremental sync: fetching customers updated since %s', $last_sync_date ) );
		} else {
			$filters['CUSTOMER.UPDDATE>'] = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
			Logger::log( 'First sync: fetching customers from last 30 days' );
		}

		$start = 0;
		$limit = 200;
		$total = 0;
		$sync_start_time = time();

		do {
			$res = $api->get_browser_rows( 'CUSTOMER', $list, $filters, $limit, $start );
			if ( is_wp_error( $res ) || empty( $res['success'] ) ) {
				Logger::error( 'Customers pull failed', is_wp_error($res) ? $res->get_error_messages() : $res );
				break;
			}
			$rows = $res['rows'] ?? [];
			$totalcount = absint( $res['totalcount'] ?? 0 );

			$col_index = [];
			$columns_meta = $res['_columns_meta'] ?? [];
			if ( ! empty( $columns_meta ) && is_array( $columns_meta ) ) {
				foreach ( $columns_meta as $idx => $col ) {
					$field_name = strtoupper( trim( (string) ($col['dataIndex'] ?? '') ) );
					if ( $field_name ) {
						$col_index[ $field_name ] = $idx;
					}
				}
			}
			
			$col_map = [];
			if ( empty( $col_index ) && ! empty( $res['cols'] ) && is_array( $res['cols'] ) ) {
				foreach ( $res['cols'] as $i => $col ) {
					$norm = strtoupper( trim( (string) $col ) );
					$col_map[ $norm ] = $i;
				}
			}

			$get_index = function( $candidates, $fallback ) use ( $col_index, $col_map ) {
				foreach ( (array) $candidates as $c ) {
					$c_norm = strtoupper( trim( (string) $c ) );
					if ( ! empty( $col_index ) && isset( $col_index[ $c_norm ] ) ) {
						return $col_index[ $c_norm ];
					}
					if ( ! empty( $col_map ) && isset( $col_map[ $c_norm ] ) ) {
						return $col_map[ $c_norm ];
					}
				}
				return $fallback;
			};

			foreach ( $rows as $row ) {
				$i_code  = $get_index( ['CUSTOMER.CODE','CODE'], 2 );
				$i_name  = $get_index( ['CUSTOMER.NAME','NAME'], 3 );
				$i_email = $get_index( ['EMAIL','CUSTOMER.EMAIL'], 5 );
				$i_trdr  = $get_index( ['CUSTOMER.TRDR','TRDR'], 1 );
				$i_trdbranch = $get_index( ['CUSTOMER.TRDBRANCH','TRDBRANCH'], 4 );

				$code = (string) ($row[ $i_code ] ?? '');
				$email = (string) ($row[ $i_email ] ?? '');
				$name = (string) ($row[ $i_name ] ?? '');
				$trdr = (string) ($row[ $i_trdr ] ?? '');
				$trdbranch = (string) ($row[ $i_trdbranch ] ?? '');

				if ( empty( $email ) ) continue;

				$user = get_user_by( 'email', $email );
				if ( ! $user ) {
					$pass = wp_generate_password( 12, true );
					$uid = wc_create_new_customer( $email, sanitize_user( $email ), $pass );
					if ( is_wp_error( $uid ) ) {
						Logger::error( 'Create customer failed', $uid->get_error_messages() );
						continue;
					}
					update_user_meta( $uid, 's1_customer_code', $code );
					if ( ! empty( $trdr ) ) {
						update_user_meta( $uid, 's1_customer_trdr', $trdr );
					}
					if ( ! empty( $trdbranch ) ) {
						update_user_meta( $uid, 's1_customer_trdbranch', $trdbranch );
					}
					$total++;
				} else {
					update_user_meta( $user->ID, 's1_customer_code', $code );
					if ( ! empty( $trdr ) ) {
						update_user_meta( $user->ID, 's1_customer_trdr', $trdr );
					}
					if ( ! empty( $trdbranch ) ) {
						update_user_meta( $user->ID, 's1_customer_trdbranch', $trdbranch );
					}
				}
			}

			$start += $limit;
			if ( $start >= $totalcount ) break;
		} while ( ! empty( $rows ) );

		update_option( self::OPTION_LAST_SYNC_TIME, $sync_start_time, false );
		
		$duration = time() - $sync_start_time;
		Logger::log( sprintf( 'Customers sync completed. Updated/created: %d customers in %d seconds', $total, $duration ) );
	}
}
