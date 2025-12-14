<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Product_Sync {

	const META_LAST_SYNC = '_s1wc_last_sync';
	const OPTION_LAST_SYNC_TIME = 's1wc_last_product_sync_time';

	public static function init() {
		// nothing extra for now; bind to cron in main plugin
	}

	public static function sync_products( $force_full_sync = false ) {
		$api = SoftOne_API::instance();
		// Ensure fresh clientID/reqID for this sync run
		if ( method_exists( $api, 'force_reauth' ) ) {
			$api->force_reauth();
		}
		$list = Settings::get('browser_items', 'CtiWSItems');

		// Get last sync timestamp
		$last_sync_time = get_option( self::OPTION_LAST_SYNC_TIME, 0 );
		
		// Build filters to only get items updated since last sync
		$filters = [];
		if ( $force_full_sync ) {
			// Force full sync: no date filter
			$filters = [];
			Logger::log( 'Force full sync: fetching all products (no date filter)' );
		} elseif ( $last_sync_time > 0 ) {
			// Format timestamp for SoftOne API (typically expects Y-m-d H:i:s format)
			// Subtract 1 hour buffer to account for any timezone differences or clock skew
			$last_sync_date = date( 'Y-m-d H:i:s', $last_sync_time - 3600 );
			$filters['ITEM.UPDDATE>'] = $last_sync_date;
			Logger::log( sprintf( 'Incremental sync: fetching products updated since %s', $last_sync_date ) );
		} else {
			// First sync: get items from last 30 days to avoid syncing everything
			$filters['ITEM.UPDDATE>'] = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
			Logger::log( 'First sync: fetching products from last 30 days' );
		}

		// Pull chunked
		$start = 0;
		$limit = 1000;
		$total = 0;
		$sync_start_time = time();
		$max_execution_time = ini_get( 'max_execution_time' );
		$max_sync_time = $max_execution_time > 0 ? ( $max_execution_time - 60 ) : 0; // Leave 60 seconds buffer

		// Performance optimization: Cache for lookups
		$product_id_cache = []; // Cache SKU => product_id lookups
		$term_cache = []; // Cache term name => term_id lookups
		$meta_cache = []; // Cache product_id => _updated_at meta

		do {
			// Check if we're running out of time
			if ( $max_sync_time > 0 ) {
				$elapsed = time() - $sync_start_time;
				if ( $elapsed > $max_sync_time ) {
					Logger::warning( sprintf( 'Sync stopped early due to execution time limit. Processed %d items in %d seconds.', $total, $elapsed ) );
					break;
				}
			}
			
			$res = $api->get_browser_rows( 'ITEM', $list, $filters, $limit, $start );
			if ( is_wp_error( $res ) ) {
				$error_code = $res->get_error_code();
				Logger::error( 'Products pull failed', [ 'error' => $res->get_error_message(), 'code' => $error_code ] );
				
				// If timeout error, stop the sync to avoid further timeouts
				if ( strpos( $error_code, 'timeout' ) !== false || strpos( $error_code, 'timeout_risk' ) !== false ) {
					Logger::error( 'Stopping sync due to timeout error. Please check SoftOne API connectivity.' );
					break;
				}
				break;
			}
			
			if ( empty( $res['success'] ) ) {
				Logger::error( 'Products pull failed - API returned unsuccessful response', $res );
				break;
			}
			$rows = $res['rows'] ?? [];
			$totalcount = absint( $res['totalcount'] ?? 0 );

			// Build column index map from getBrowserInfo metadata
			$col_index = [];
			$columns_meta = $res['_columns_meta'] ?? [];
			if ( ! empty( $columns_meta ) && is_array( $columns_meta ) ) {
				foreach ( $columns_meta as $idx => $col ) {
					$field_name = strtoupper( trim( (string) ($col['dataIndex'] ?? '') ) );
					if ( $field_name ) {
						$col_index[ $field_name ] = $idx;
					}
				}
				Logger::log( 'Column mapping loaded', ['count' => count( $col_index ), 'columns' => array_keys( $col_index )] );
				// Detect if rows contain leading ZOOMINFO or extra columns which cause an offset
				$rows_sample = $rows[0] ?? null;
				if ( is_array( $rows_sample ) ) {
					$cols_count = count( $columns_meta );
					$row_count = count( $rows_sample );
					if ( $row_count > $cols_count ) {
						$offset = $row_count - $cols_count;
						foreach ( $col_index as $k => $v ) {
							$col_index[ $k ] = $v + $offset;
						}
						Logger::log( 'Adjusted product column mapping offset', ['offset' => $offset] );
					}
				}
			} else {
				Logger::warning( 'No column metadata from getBrowserInfo, using fallback indices' );
			}

			// Helper to get column index by field name(s), with fallback
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
				// Map columns using metadata; fallback to hardcoded indices if metadata missing
				$i_sku        = $get_col_index( 'ITEM.CODE', 2 );
				$i_name       = $get_col_index( 'ITEM.NAME', 5 );
				$i_webname    = $get_col_index( 'ITEM.WEBNAME', 6 );
				$i_unit       = $get_col_index( 'ITEM.MTRUNIT1', 7 );
				$i_vat        = $get_col_index( 'ITEM.VAT', 8 );
				$i_pricew     = $get_col_index( 'ITEM.PRICEW', 9 );
				$i_pricer     = $get_col_index( 'ITEM.PRICER', 10 );
				$i_category   = $get_col_index( 'ITEM.MTRCATEGORY', 11 );
				$i_group      = $get_col_index( 'ITEM.MTRGROUP', 12 );
				$i_mark       = $get_col_index( 'ITEM.MTRMARK', 13 );
				$i_isactive   = $get_col_index( 'ITEM.ISACTIVE', 14 );
				$i_webview    = $get_col_index( 'ITEM.WEBVIEW', 15 );
				$i_insdate    = $get_col_index( 'ITEM.INSDATE', 16 );
				$i_upddate    = $get_col_index( 'ITEM.UPDDATE', 17 );
				$i_barcode    = $get_col_index( 'ITEM.CODE1', 3 );
				$i_mtrl       = $get_col_index( 'ITEM.MTRL', 1 );
				$i_qty        = $get_col_index( ['ITEM.MTRL_ITEMTRDATA_QTY1','QTY1','ITEM.QTY1'], 18 );

				// Extract and sanitize fields
				$sku       = trim( (string) ($row[ $i_sku ] ?? '') );
				$name      = trim( (string) ($row[ $i_name ] ?? '') );
				$webname   = trim( (string) ($row[ $i_webname ] ?? '') );
				$upddate_raw = trim( (string) ($row[ $i_upddate ] ?? '') );
				
				// Use ITEM.WEBNAME as primary product name, fallback to ITEM.NAME if empty
				$display_name = ! empty( $webname ) ? $webname : $name;

				$product_data = [
					'sku'           => $sku,
					'name'          => $display_name,
					'regular_price' => isset($row[ $i_pricer ]) ? (float) $row[ $i_pricer ] : 0.0,
					'status'        => 'draft',
					'qty'           => isset($row[ $i_qty ]) ? (float) $row[ $i_qty ] : null,
					'meta_data'     => [
						['key' => '_softone_mtrl', 'value' => $row[ $i_mtrl ] ?? ''],
						['key' => '_barcode', 'value' => $row[ $i_barcode ] ?? ''],
						['key' => '_unit', 'value' => $row[ $i_unit ] ?? ''],
						['key' => '_vat', 'value' => $row[ $i_vat ] ?? ''],
						['key' => '_wholesale_price', 'value' => isset($row[ $i_pricew ]) ? (float) $row[ $i_pricew ] : 0.0],
						['key' => '_category', 'value' => $row[ $i_category ] ?? ''],
						['key' => '_group', 'value' => $row[ $i_group ] ?? ''],
						['key' => '_mark', 'value' => $row[ $i_mark ] ?? ''],
						['key' => '_created_at', 'value' => $row[ $i_insdate ] ?? ''],
						['key' => '_updated_at', 'value' => $upddate_raw ?? ''],
					],
				];

				if ( empty( $product_data['sku'] ) ) {
					continue; 
				}

				// If product exists, check UPDDATE to avoid unnecessary updates
				$sku = $product_data['sku'];
				
				// Cache product ID lookup to avoid duplicate queries
				if ( ! isset( $product_id_cache[ $sku ] ) ) {
					$product_id_cache[ $sku ] = wc_get_product_id_by_sku( $sku );
				}
				$existing_id = $product_id_cache[ $sku ];
				
				$should_update = true;
				$skip_meta_updates = false;
				if ( $existing_id ) {
					// Cache meta lookups
					if ( ! isset( $meta_cache[ $existing_id ] ) ) {
						$meta_cache[ $existing_id ] = get_post_meta( $existing_id, '_updated_at', true );
					}
					$stored_upddate = $meta_cache[ $existing_id ];
					// Compare timestamps when possible, fallback to string compare
					// PHP 8.x: strtotime() returns false on failure, so check explicitly
					$store_ts = false;
					$new_ts = false;
					if ( $stored_upddate ) {
						$store_ts = strtotime( $stored_upddate );
						if ( $store_ts === false ) {
							$store_ts = false; // Explicitly set to false on failure
						}
					}
					if ( $upddate_raw ) {
						$new_ts = strtotime( $upddate_raw );
						if ( $new_ts === false ) {
							$new_ts = false; // Explicitly set to false on failure
						}
					}
					$should_update = true;
					if ( $store_ts !== false && $new_ts !== false ) {
						$should_update = ( $new_ts > $store_ts );
					} elseif ( $stored_upddate !== '' && $upddate_raw !== '' ) {
						$should_update = ( strcmp( (string) $upddate_raw, (string) $stored_upddate ) > 0 );
					}

					if ( ! $should_update ) {
						// No full field update required; still update stock/taxonomy below
						$skip_meta_updates = true;
					}
				}

				// Convert Greek "Ναι" to boolean for ISACTIVE and WEBVIEW
				$is_active = mb_strtolower( trim( (string) ($row[ $i_isactive ] ?? '') ) ) === 'ναι';
				$webview   = mb_strtolower( trim( (string) ($row[ $i_webview ] ?? '') ) ) === 'ναι';
				// Publish when ITEM.ISACTIVE == 'ναι' (regardless of WEBVIEW)
				$product_data['status'] = $is_active ? 'publish' : 'draft';

				// Use cached product ID instead of querying again
				$product_id = $existing_id;
				if ( ! $product_id ) {
					$product = new \WC_Product_Simple();
					$product->set_sku( $sku );
				} else {
					$product = wc_get_product( $product_id );
					if ( ! $product ) {
						$product = new \WC_Product_Simple();
						$product->set_sku( $sku );
					}
				}

				// Update basic fields only when ERP indicates a newer update
				if ( ! empty( $should_update ) ) {
					// Use webname if available, otherwise fallback to name
					$product->set_name( $product_data['name'] );
					
					if ( is_numeric( $product_data['regular_price'] ) ) {
						$product->set_regular_price( (string) $product_data['regular_price'] );
					}
					$product->set_status( $product_data['status'] );
				}

				// Save
				$new_id = $product->save();

				// Save meta fields
				if ( $new_id ) {
					// Save meta fields (unless we skipped full meta updates due to UPDDATE)
					// Batch meta updates for better performance
					if ( empty( $skip_meta_updates ) ) {
						$meta_to_update = [];
						foreach ( $product_data['meta_data'] as $meta ) {
							if ( isset( $meta['key'] ) ) {
								$meta_to_update[ $meta['key'] ] = $meta['value'];
							}
						}
						// Use update_post_meta with batching (WordPress handles this internally)
						foreach ( $meta_to_update as $key => $value ) {
							update_post_meta( $new_id, $key, $value );
						}
					}

					// Map SoftOne categories/groups/brands to WP terms
					$cat_val = trim( (string) ($row[ $i_category ] ?? '') );
					$group_val = trim( (string) ($row[ $i_group ] ?? '') );
					$mark_val = trim( (string) ($row[ $i_mark ] ?? '') );
					// Resolve numeric ERP ids to human names when possible
					// Note: resolve_reference() will skip API calls during admin page loads automatically
					if ( method_exists( $api, 'resolve_reference' ) ) {
						$cat_val = $api->resolve_reference( 'category', $cat_val );
						$group_val = $api->resolve_reference( 'group', $group_val );
						$mark_val = $api->resolve_reference( 'brand', $mark_val );
					}

					$assigned_cat_ids = [];
					$parent_id = 0;
					$child_id = 0;
					$brand_id = 0;
					
					// Ensure parent category term (MTRCATEGORY) - lookup/create by name (case-insensitive)
					if ( $cat_val !== '' ) {
						$cache_key = 'product_cat_' . md5( strtolower( $cat_val ) );
						if ( ! isset( $term_cache[ $cache_key ] ) ) {
							$term_obj = get_term_by( 'name', $cat_val, 'product_cat' );
							if ( ! $term_obj ) {
								// try by slug
								$slug = sanitize_title( $cat_val );
								$term_obj = get_term_by( 'slug', $slug, 'product_cat' );
							}
							if ( $term_obj && ! is_wp_error( $term_obj ) ) {
								$term_cache[ $cache_key ] = (int) $term_obj->term_id;
							} else {
								$res = wp_insert_term( $cat_val, 'product_cat', [ 'slug' => sanitize_title( $cat_val ) ] );
								if ( is_wp_error( $res ) ) {
									Logger::warning( 'Failed creating product_cat term', [ 'name' => $cat_val, 'error' => $res->get_error_messages() ] );
									$term_cache[ $cache_key ] = 0;
								} else {
									$term_cache[ $cache_key ] = (int) $res['term_id'];
								}
							}
						}
						$parent_id = $term_cache[ $cache_key ];
						if ( ! empty( $parent_id ) ) {
							$assigned_cat_ids[] = $parent_id;
						}
					}

					// Ensure child category term (MTRGROUP) with parent if available - with caching
					// Ensure child category term (MTRGROUP) with parent if available - lookup/create by name
					if ( $group_val !== '' ) {
						$cache_key = 'product_cat_' . md5( strtolower( $group_val . '|' . $parent_id ) );
						if ( ! isset( $term_cache[ $cache_key ] ) ) {
							$term_obj = get_term_by( 'name', $group_val, 'product_cat' );
							if ( ! $term_obj ) {
								$slug = sanitize_title( $group_val );
								$term_obj = get_term_by( 'slug', $slug, 'product_cat' );
							}
							if ( $term_obj && ! is_wp_error( $term_obj ) ) {
								$term_cache[ $cache_key ] = (int) $term_obj->term_id;
							} else {
								$args = [ 'slug' => sanitize_title( $group_val ) ];
								if ( ! empty( $parent_id ) ) $args['parent'] = $parent_id;
								$res = wp_insert_term( $group_val, 'product_cat', $args );
								if ( is_wp_error( $res ) ) {
									Logger::warning( 'Failed creating product_cat child term', [ 'name' => $group_val, 'error' => $res->get_error_messages() ] );
									$term_cache[ $cache_key ] = 0;
								} else {
									$term_cache[ $cache_key ] = (int) $res['term_id'];
								}
							}
						}
						$child_id = $term_cache[ $cache_key ];
						if ( ! empty( $child_id ) ) {
							$assigned_cat_ids[] = $child_id;
						}
					}

					// Assign categories (merge with existing) - only update if changed
					if ( ! empty( $assigned_cat_ids ) ) {
						$existing = wp_get_post_terms( $new_id, 'product_cat', [ 'fields' => 'ids' ] );
						$all = array_unique( array_merge( (array) $existing, $assigned_cat_ids ) );
						// Only update if categories actually changed to avoid unnecessary queries
						$existing_sorted = array_values( array_unique( (array) $existing ) );
						sort( $existing_sorted );
						$all_sorted = array_values( $all );
						sort( $all_sorted );
						if ( $existing_sorted !== $all_sorted ) {
							Logger::log( 'Assigning product categories', [ 'product_id' => $new_id, 'assign' => $all ] );
							wp_set_post_terms( $new_id, $all, 'product_cat' );
						}
					} else {
						// assigned_cat_ids empty but category value existed earlier — try fallback
						if ( ! empty( $cat_val ) ) {
							$fallback_name = 'Category ' . preg_replace('/[^A-Za-z0-9_\- ]/', '', (string) $cat_val );
							$cache_key_fb = 'product_cat_' . $fallback_name;
							if ( ! isset( $term_cache[ $cache_key_fb ] ) ) {
								$res_fb = wp_insert_term( $fallback_name, 'product_cat' );
								if ( is_wp_error( $res_fb ) ) {
									Logger::warning( 'Fallback creating product_cat term failed', [ 'name' => $fallback_name, 'error' => $res_fb->get_error_messages() ] );
									$term_cache[ $cache_key_fb ] = 0;
								} else {
									$term_cache[ $cache_key_fb ] = (int) $res_fb['term_id'];
								}
							}
							if ( ! empty( $term_cache[ $cache_key_fb ] ) ) {
								Logger::log( 'Assigning fallback category to product', [ 'product_id' => $new_id, 'term_id' => $term_cache[ $cache_key_fb ] ] );
								wp_set_post_terms( $new_id, [ $term_cache[ $cache_key_fb ] ], 'product_cat' );
							}
						}
					}

					// Ensure and assign brand taxonomy term (softone_brand) - with caching
					if ( $mark_val !== '' ) {
						$cache_key = 'softone_brand_' . md5( strtolower( $mark_val ) );
						if ( ! isset( $term_cache[ $cache_key ] ) ) {
							$term_obj = get_term_by( 'name', $mark_val, 'softone_brand' );
							if ( ! $term_obj ) {
								$slug = sanitize_title( $mark_val );
								$term_obj = get_term_by( 'slug', $slug, 'softone_brand' );
							}
							if ( $term_obj && ! is_wp_error( $term_obj ) ) {
								$term_cache[ $cache_key ] = (int) $term_obj->term_id;
							} else {
								$res = wp_insert_term( $mark_val, 'softone_brand', [ 'slug' => sanitize_title( $mark_val ) ] );
								if ( is_wp_error( $res ) ) {
									Logger::warning( 'Failed creating softone_brand term', [ 'name' => $mark_val, 'error' => $res->get_error_messages() ] );
									$term_cache[ $cache_key ] = 0;
								} else {
									$term_cache[ $cache_key ] = (int) $res['term_id'];
								}
							}
						}
						$brand_id = $term_cache[ $cache_key ];
						if ( ! empty( $brand_id ) ) {
							$existing_brands = wp_get_post_terms( $new_id, 'softone_brand', [ 'fields' => 'ids' ] );
							$allb = array_unique( array_merge( (array) $existing_brands, [ $brand_id ] ) );
							// Only update if brands actually changed
							if ( ! in_array( $brand_id, (array) $existing_brands, true ) ) {
								Logger::log( 'Assigning product brand', [ 'product_id' => $new_id, 'brand_id' => $brand_id ] );
								wp_set_post_terms( $new_id, $allb, 'softone_brand' );
							}
						}
					}
					// Update stock quantity if available - batch meta updates
					if ( isset( $product_data['qty'] ) && $product_data['qty'] !== null ) {
						$qty = (float) $product_data['qty'];
						// Batch stock-related meta updates
						update_post_meta( $new_id, '_manage_stock', 'yes' );
						update_post_meta( $new_id, '_stock', $qty );
						update_post_meta( $new_id, '_stock_status', $qty > 0 ? 'instock' : 'outofstock' );
						// Use WooCommerce function which is optimized
						if ( function_exists( 'wc_update_product_stock' ) ) {
							wc_update_product_stock( $new_id, $qty, 'set' );
						}
					}
				}

				$total++;
			}

			$start += $limit;
			if ( $start >= $totalcount ) break;
		} while ( ! empty( $rows ) );

		// Update last sync timestamp after successful sync
		update_option( self::OPTION_LAST_SYNC_TIME, $sync_start_time, false );
		
		$duration = time() - $sync_start_time;
		$avg_time = $total > 0 ? ( $duration / $total ) : 0;
		
		// Performance summary
		$perf_summary = sprintf( 
			'Products sync completed: %d items in %d seconds (avg: %.2fs/item). Cache hits: %d product IDs, %d terms',
			$total,
			$duration,
			$avg_time,
			count( $product_id_cache ),
			count( $term_cache )
		);
		Logger::log( $perf_summary );
		
		// Log if sync was slow
		if ( $duration > 300 ) { // More than 5 minutes
			Logger::warning( sprintf( 'Sync took %d seconds - consider optimizing or reducing batch size', $duration ) );
		}
	}
}
