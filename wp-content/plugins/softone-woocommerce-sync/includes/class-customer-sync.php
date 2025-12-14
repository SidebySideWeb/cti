<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Customer_Sync {

	public static function init() {}

	public static function sync_customers() {
		$api = SoftOne_API::instance();
		$list = Settings::get('browser_customers', 'CtiWSCustomers');

		Logger::log( '=== CUSTOMER SYNC START ===', [] );

		$start = 0;
		$limit = 200;
		$total = 0;

		do {
			$res = $api->get_browser_rows( 'CUSTOMER', $list, '', $limit, $start );

			if ( is_wp_error( $res ) || empty( $res['success'] ) ) {
				Logger::error( 'Customers pull failed', $res );
				break;
			}

			$rows = $res['rows'] ?? [];
			$totalcount = absint( $res['totalcount'] ?? 0 );

			if ( empty( $rows ) ) {
				break;
			}

			// Build column index map from metadata
			$col_index = [];
			$columns_meta = $res['_columns_meta'] ?? [];

			if ( ! empty( $columns_meta ) && is_array( $columns_meta ) ) {
				foreach ( $columns_meta as $idx => $col ) {
					$field_name = strtoupper( trim( (string) ($col['dataIndex'] ?? '') ) );
					if ( $field_name && $field_name !== 'ZOOMINFO' ) {
						$col_index[ $field_name ] = $idx;
					}
				}

				// Adjust for offset if rows have extra leading field
				$rows_sample = $rows[0] ?? null;
				if ( is_array( $rows_sample ) ) {
					$cols_count = count( $columns_meta );
					$row_count = count( $rows_sample );
					if ( $row_count > $cols_count ) {
						$offset = $row_count - $cols_count;
						foreach ( $col_index as $k => $v ) {
							$col_index[ $k ] = $v + $offset;
						}
					}
				}
			}

			// Helper to get column index
			$get_col_index = function( $field_names, $fallback_idx ) use ( $col_index ) {
				$field_names = (array) $field_names;
				foreach ( $field_names as $fname ) {
					$fname_upper = strtoupper( trim( (string) $fname ) );
					if ( isset( $col_index[ $fname_upper ] ) ) {
						return $col_index[ $fname_upper ];
					}
				}
				return $fallback_idx;
			};

			foreach ( $rows as $row ) {
				// Map column indices
				$i_code = $get_col_index( 'CUSTOMER.CODE', 2 );
				$i_name = $get_col_index( 'CUSTOMER.NAME', 3 );
				$i_email = $get_col_index( ['CUSTOMER.EMAIL', 'CUSTOMER.TRDR_CUSBRANCH_EMAIL'], 5 );
				$i_afm = $get_col_index( 'CUSTOMER.AFM', 8 );
				$i_trdr = $get_col_index( 'CUSTOMER.TRDR', 1 );
				$i_trdbranch = $get_col_index( 'CUSTOMER.TRDBRANCH', 7 );
				$i_s1 = $get_col_index( 'CUSTOMER.S1', 2 );
				$i_trdr_cusbranch_code = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_CODE', 4 );
				$i_trdr_cusbranch_name = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_NAME', 6 );
				$i_address = $get_col_index( 'CUSTOMER.ADDRESS', 9 );
				$i_trdr_cusbranch_address = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_ADDRESS', 10 );
				$i_district = $get_col_index( 'CUSTOMER.DISTRICT', 11 );
				$i_trdr_cusbranch_district = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_DISTRICT', 12 );
				$i_city = $get_col_index( 'CUSTOMER.CITY', 13 );
				$i_trdr_cusbranch_city = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_CITY', 14 );
				$i_zip = $get_col_index( 'CUSTOMER.ZIP', 15 );
				$i_trdr_cusbranch_zip = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_ZIP', 16 );
				$i_phone01 = $get_col_index( ['CUSTOMER.PHONE01', 'CUSTOMER.TRDR_CUSBRANCH_PHONE1'], 16 );
				$i_phone02 = $get_col_index( 'CUSTOMER.PHONE02', 18 );
				$i_trdr_cusbranch_email = $get_col_index( 'CUSTOMER.TRDR_CUSBRANCH_EMAIL', 20 );
				$i_upddate = $get_col_index( ['CUSTOMER.UPDDATE', 'UPDDATE', 'CUSTOMER.LASTMODIFIED'], 21 );

				// Extract values
				$code = trim( (string) ($row[ $i_code ] ?? '') );
				$name = trim( (string) ($row[ $i_name ] ?? '') );
				$email = trim( (string) ($row[ $i_email ] ?? '') );
				$afm = trim( (string) ($row[ $i_afm ] ?? '') );
				$trdr = trim( (string) ($row[ $i_trdr ] ?? '') );
				$trdbranch = trim( (string) ($row[ $i_trdbranch ] ?? '') );
				$s1 = trim( (string) ($row[ $i_s1 ] ?? '') );
				$trdr_cusbranch_code = trim( (string) ($row[ $i_trdr_cusbranch_code ] ?? '') );
				$trdr_cusbranch_name = trim( (string) ($row[ $i_trdr_cusbranch_name ] ?? '') );
				$address = trim( (string) ($row[ $i_address ] ?? '') );
				$trdr_cusbranch_address = trim( (string) ($row[ $i_trdr_cusbranch_address ] ?? '') );
				$district = trim( (string) ($row[ $i_district ] ?? '') );
				$trdr_cusbranch_district = trim( (string) ($row[ $i_trdr_cusbranch_district ] ?? '') );
				$city = trim( (string) ($row[ $i_city ] ?? '') );
				$trdr_cusbranch_city = trim( (string) ($row[ $i_trdr_cusbranch_city ] ?? '') );
				$zip = trim( (string) ($row[ $i_zip ] ?? '') );
				$trdr_cusbranch_zip = trim( (string) ($row[ $i_trdr_cusbranch_zip ] ?? '') );
				$phone01 = trim( (string) ($row[ $i_phone01 ] ?? '') );
				$phone02 = trim( (string) ($row[ $i_phone02 ] ?? '') );
				$trdr_cusbranch_email = trim( (string) ($row[ $i_trdr_cusbranch_email ] ?? '') );
				$erp_upddate_raw = trim( (string) ($row[ $i_upddate ] ?? '') );
				$erp_upddate_ts = $erp_upddate_raw ? strtotime( $erp_upddate_raw ) : 0;

				// Generate email if missing
				if ( empty( $email ) ) {
					if ( ! empty( $afm ) ) {
						$email = ! empty( $trdbranch ) ? $afm . '_' . $trdbranch . '@cti.com' : $afm . '@cti.com';
					} else {
						Logger::warning( 'Customer skipped: no email or AFM', [ 'code' => $code, 'name' => $name ] );
						continue;
					}
				}

				// Ensure email uniqueness
				if ( $email ) {
					$orig_email = $email;
					$attempt = 0;
					while ( get_user_by( 'email', $email ) ) {
						$existing = get_user_by( 'email', $email );
						$existing_trdr = $existing ? get_user_meta( $existing->ID, 's1_customer_trdr', true ) : '';
						$existing_trdbranch = $existing ? get_user_meta( $existing->ID, 's1_customer_trdbranch', true ) : '';
						if ( $existing && $existing_trdr === $trdr && $existing_trdbranch === $trdbranch ) {
							break; // Same customer, reuse account
						}
						$attempt++;
						if ( $attempt === 1 && ! empty( $trdbranch ) ) {
							$email = $afm . '_' . $trdbranch . '@cti.com';
							if ( $email === $orig_email ) continue;
						} else {
							$email = preg_replace( '/@.*$/', "_{$attempt}@cti.com", $orig_email );
						}
						if ( $attempt > 10 ) {
							Logger::error( 'Could not generate unique email', ['code' => $code, 'afm' => $afm] );
							break;
						}
					}
				}

				// Generate password
				$password = $afm;
				if ( ! empty( $afm ) && ! empty( $trdr ) ) {
					$existing_user_by_afm = get_users( [
						'meta_key' => 's1_customer_afm',
						'meta_value' => $afm,
						'number' => 1,
						'fields' => 'ID',
					] );
					if ( ! empty( $existing_user_by_afm ) ) {
						$password = $afm . '_' . $trdr;
						if ( ! empty( $trdbranch ) ) {
							$password .= '_' . $trdbranch;
						}
					}
				}
				if ( empty( $password ) ) {
					$password = wp_generate_password( 12, true );
				}

				$username = sanitize_user( $email, true );

				$user = get_user_by( 'email', $email );
				if ( ! $user ) {
					$uid = wc_create_new_customer( $email, $username, $password );
					if ( is_wp_error( $uid ) ) {
						Logger::error( 'Create customer failed', [ 'email' => $email, 'error' => $uid->get_error_messages() ] );
						continue;
					}
					$user = get_user_by( 'id', $uid );
					$total++;
				} else {
					$uid = $user->ID;
				}

				self::sync_user_to_wc_lookup( $uid );

				// Skip update if local record is newer
				$existing_upddate_raw = get_user_meta( $uid, 's1_customer_upddate', true );
				$existing_upddate_ts = $existing_upddate_raw ? strtotime( $existing_upddate_raw ) : 0;
				if ( $erp_upddate_ts && $existing_upddate_ts && $existing_upddate_ts >= $erp_upddate_ts ) {
					continue;
				}

				// Save customer meta
				update_user_meta( $uid, 's1_customer_code', $code );
				if ( ! empty( $afm ) ) update_user_meta( $uid, 's1_customer_afm', $afm );
				if ( ! empty( $trdr ) ) update_user_meta( $uid, 's1_customer_trdr', $trdr );
				if ( ! empty( $trdbranch ) ) update_user_meta( $uid, 's1_customer_trdbranch', $trdbranch );
				if ( ! empty( $s1 ) ) update_user_meta( $uid, 's1_customer_s1', $s1 );
				if ( ! empty( $trdr_cusbranch_code ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_code', $trdr_cusbranch_code );
				if ( ! empty( $trdr_cusbranch_name ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_name', $trdr_cusbranch_name );
				if ( ! empty( $address ) ) update_user_meta( $uid, 's1_customer_address', $address );
				if ( ! empty( $trdr_cusbranch_address ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_address', $trdr_cusbranch_address );
				if ( ! empty( $district ) ) update_user_meta( $uid, 's1_customer_district', $district );
				if ( ! empty( $trdr_cusbranch_district ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_district', $trdr_cusbranch_district );
				if ( ! empty( $city ) ) update_user_meta( $uid, 's1_customer_city', $city );
				if ( ! empty( $trdr_cusbranch_city ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_city', $trdr_cusbranch_city );
				if ( ! empty( $zip ) ) update_user_meta( $uid, 's1_customer_zip', $zip );
				if ( ! empty( $trdr_cusbranch_zip ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_zip', $trdr_cusbranch_zip );
				if ( ! empty( $phone01 ) ) update_user_meta( $uid, 's1_customer_phone01', $phone01 );
				if ( ! empty( $phone02 ) ) update_user_meta( $uid, 's1_customer_phone02', $phone02 );
				if ( ! empty( $trdr_cusbranch_email ) ) update_user_meta( $uid, 's1_customer_trdr_cusbranch_email', $trdr_cusbranch_email );

				// Update WooCommerce billing fields
				$billing_address = ! empty( $trdr_cusbranch_address ) ? $trdr_cusbranch_address : $address;
				$billing_city = ! empty( $trdr_cusbranch_city ) ? $trdr_cusbranch_city : $city;
				$billing_zip = ! empty( $trdr_cusbranch_zip ) ? $trdr_cusbranch_zip : $zip;
				$billing_district = ! empty( $trdr_cusbranch_district ) ? $trdr_cusbranch_district : $district;

				if ( ! empty( $name ) ) {
					update_user_meta( $uid, 'billing_first_name', $name );
					update_user_meta( $uid, 'first_name', $name );
				}
				if ( ! empty( $billing_address ) ) update_user_meta( $uid, 'billing_address_1', $billing_address );
				if ( ! empty( $billing_city ) ) update_user_meta( $uid, 'billing_city', $billing_city );
				if ( ! empty( $billing_zip ) ) update_user_meta( $uid, 'billing_postcode', $billing_zip );
				if ( ! empty( $billing_district ) ) update_user_meta( $uid, 'billing_address_2', $billing_district );
				if ( ! empty( $phone01 ) ) update_user_meta( $uid, 'billing_phone', $phone01 );
				if ( ! empty( $email ) ) update_user_meta( $uid, 'billing_email', $email );

				// Update display name
				if ( ! empty( $name ) && $user ) {
					wp_update_user( [ 'ID' => $uid, 'display_name' => $name ] );
				}
			}

			$start += $limit;
			if ( $start >= $totalcount ) break;
		} while ( ! empty( $rows ) );

		Logger::log( '=== CUSTOMER SYNC END ===', [ 'total_created_or_updated' => $total ] );
	}

	/**
	 * Sync a WordPress user to the WooCommerce customer lookup table
	 * This ensures customers appear in wp_wc_customer_lookup for WooCommerce
	 */
	public static function sync_user_to_wc_lookup( $user_id ) {
		global $wpdb;
		
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			Logger::warning( 'User not found for WC lookup sync', [ 'user_id' => $user_id ] );
			return false;
		}

		// Get customer first and last name if available
		$first_name = get_user_meta( $user_id, 'first_name', true ) ?: '';
		$last_name = get_user_meta( $user_id, 'last_name', true ) ?: '';
		$billing_email = get_user_meta( $user_id, 'billing_email', true ) ?: $user->user_email;
		$billing_phone = get_user_meta( $user_id, 'billing_phone', true ) ?: '';

		// Check if record exists in wp_wc_customer_lookup
		$lookup_table = $wpdb->prefix . 'wc_customer_lookup';
		$existing = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$lookup_table} WHERE user_id = %d",
			$user_id
		) );

		$data = [
			'user_id' => $user_id,
			'username' => $user->user_login,
			'email' => $user->user_email,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'country' => get_user_meta( $user_id, 'billing_country', true ) ?: '',
			'state' => get_user_meta( $user_id, 'billing_state', true ) ?: '',
			'postcode' => get_user_meta( $user_id, 'billing_postcode', true ) ?: '',
			'city' => get_user_meta( $user_id, 'billing_city', true ) ?: '',
			'date_registered' => $user->user_registered,
			'date_modified' => current_time( 'mysql' ),
		];

		if ( $existing ) {
			// Update existing record
			$updated = $wpdb->update(
				$lookup_table,
				$data,
				[ 'user_id' => $user_id ],
				[ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ],
				[ '%d' ]
			);
			Logger::log( 'Updated customer in wp_wc_customer_lookup', [ 'user_id' => $user_id, 'rows_affected' => $updated ] );
		} else {
			// Insert new record
			$inserted = $wpdb->insert(
				$lookup_table,
				$data,
				[ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
			);
			Logger::log( 'Inserted customer to wp_wc_customer_lookup', [ 'user_id' => $user_id, 'inserted' => $inserted ] );
		}

		return true;
	}
}
