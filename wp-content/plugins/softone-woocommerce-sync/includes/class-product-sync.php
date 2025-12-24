<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Product_Sync {

	const META_LAST_SYNC = '_s1wc_last_sync';
	const OPTION_LAST_SYNC_TIME = 's1wc_last_product_sync_time';

	public static function init() {
	}

	public static function sync_products( $force_full_sync = false ) {
		$api = SoftOne_API::instance();
		if ( method_exists( $api, 'force_reauth' ) ) {
			$api->force_reauth();
		}
		$list = Settings::get('browser_items', 'CtiWSItems');

		$last_sync_time = get_option( self::OPTION_LAST_SYNC_TIME, 0 );
		
		$filters = [];
		if ( $force_full_sync ) {
			$filters = [];
			Logger::log( 'Force full sync: fetching all products (no date filter)' );
		} elseif ( $last_sync_time > 0 ) {
			$last_sync_date = date( 'Y-m-d H:i:s', $last_sync_time - 3600 );
			$filters['ITEM.UPDDATE>'] = $last_sync_date;
			Logger::log( sprintf( 'Incremental sync: fetching products updated since %s', $last_sync_date ) );
		} else {
			$filters['ITEM.UPDDATE>'] = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
			Logger::log( 'First sync: fetching products from last 30 days' );
		}

		$start = 0;
		$limit = 1000;
		$total = 0;
		$sync_start_time = time();
		$max_execution_time = ini_get( 'max_execution_time' );
		$max_sync_time = $max_execution_time > 0 ? ( $max_execution_time - 60 ) : 0;

		do {
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
			} else {
				Logger::warning( 'No column metadata from getBrowserInfo, using fallback indices' );
			}

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

				$sku       = trim( (string) ($row[ $i_sku ] ?? '') );
				$name      = trim( (string) ($row[ $i_name ] ?? '') );
				$webname   = trim( (string) ($row[ $i_webname ] ?? '') );
				$upddate_raw = trim( (string) ($row[ $i_upddate ] ?? '') );
				
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

				$sku = $product_data['sku'];
				$existing_id = wc_get_product_id_by_sku( $sku );
				$should_update = true;
				$skip_meta_updates = false;
				if ( $existing_id ) {
					$stored_upddate = get_post_meta( $existing_id, '_updated_at', true );
					$store_ts = false;
					$new_ts = false;
					if ( $stored_upddate ) {
						$store_ts = strtotime( $stored_upddate );
						if ( $store_ts === false ) {
							$store_ts = false;
						}
					}
					if ( $upddate_raw ) {
						$new_ts = strtotime( $upddate_raw );
						if ( $new_ts === false ) {
							$new_ts = false;
						}
					}
					$should_update = true;
					if ( $store_ts !== false && $new_ts !== false ) {
						$should_update = ( $new_ts > $store_ts );
					} elseif ( $stored_upddate !== '' && $upddate_raw !== '' ) {
						$should_update = ( strcmp( (string) $upddate_raw, (string) $stored_upddate ) > 0 );
					}

					if ( ! $should_update ) {
						$skip_meta_updates = true;
					}
				}

				$is_active = mb_strtolower( trim( (string) ($row[ $i_isactive ] ?? '') ) ) === 'ναι';
				$webview   = mb_strtolower( trim( (string) ($row[ $i_webview ] ?? '') ) ) === 'ναι';
				$product_data['status'] = ( $is_active && $webview ) ? 'publish' : 'draft';

				$sku = $product_data['sku'];
				$product_id = wc_get_product_id_by_sku( $sku );
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

				if ( ! empty( $should_update ) ) {
					$product->set_name( $product_data['name'] );
					
					if ( is_numeric( $product_data['regular_price'] ) ) {
						$product->set_regular_price( (string) $product_data['regular_price'] );
					}
					$product->set_status( $product_data['status'] );
				}

				$new_id = $product->save();

				if ( $new_id ) {
					if ( empty( $skip_meta_updates ) ) {
						foreach ( $product_data['meta_data'] as $meta ) {
							if ( isset( $meta['key'] ) ) {
								update_post_meta( $new_id, $meta['key'], $meta['value'] );
							}
						}
					}

					$cat_val = trim( (string) ($row[ $i_category ] ?? '') );
					$group_val = trim( (string) ($row[ $i_group ] ?? '') );
					$mark_val = trim( (string) ($row[ $i_mark ] ?? '') );
					if ( method_exists( $api, 'resolve_reference' ) ) {
						$cat_val = $api->resolve_reference( 'category', $cat_val );
						$group_val = $api->resolve_reference( 'group', $group_val );
						$mark_val = $api->resolve_reference( 'brand', $mark_val );
					}

					$assigned_cat_ids = [];
					$parent_id = 0;
					$child_id = 0;
					$brand_id = 0;
					if ( $cat_val !== '' ) {
						$term = term_exists( $cat_val, 'product_cat' );
						if ( $term ) {
							$parent_id = is_array( $term ) ? $term['term_id'] : (int) $term;
						} else {
							$res = wp_insert_term( $cat_val, 'product_cat' );
							if ( is_wp_error( $res ) ) {
								Logger::warning( 'Failed creating product_cat term', [ 'name' => $cat_val, 'error' => $res->get_error_messages() ] );
								$parent_id = 0;
							} else {
								$parent_id = (int) $res['term_id'];
							}
						}
						if ( ! empty( $parent_id ) ) {
							$assigned_cat_ids[] = $parent_id;
						}
					}

					if ( $group_val !== '' ) {
						$child_term = term_exists( $group_val, 'product_cat' );
						if ( $child_term ) {
							$child_id = is_array( $child_term ) ? $child_term['term_id'] : (int) $child_term;
						} else {
							$args = [];
							if ( ! empty( $parent_id ) ) $args['parent'] = $parent_id;
							$res = wp_insert_term( $group_val, 'product_cat', $args );
							if ( is_wp_error( $res ) ) {
								Logger::warning( 'Failed creating product_cat child term', [ 'name' => $group_val, 'error' => $res->get_error_messages() ] );
								$child_id = 0;
							} else {
								$child_id = (int) $res['term_id'];
							}
						}
						if ( ! empty( $child_id ) ) {
							$assigned_cat_ids[] = $child_id;
						}
					}

					if ( ! empty( $assigned_cat_ids ) ) {
						$existing = wp_get_post_terms( $new_id, 'product_cat', [ 'fields' => 'ids' ] );
						$all = array_unique( array_merge( (array) $existing, $assigned_cat_ids ) );
						wp_set_post_terms( $new_id, $all, 'product_cat' );
					}

					if ( $mark_val !== '' ) {
						$brand_term = term_exists( $mark_val, 'softone_brand' );
						if ( $brand_term ) {
							$brand_id = is_array( $brand_term ) ? $brand_term['term_id'] : (int) $brand_term;
						} else {
							$res = wp_insert_term( $mark_val, 'softone_brand' );
							if ( is_wp_error( $res ) ) {
								Logger::warning( 'Failed creating softone_brand term', [ 'name' => $mark_val, 'error' => $res->get_error_messages() ] );
								$brand_id = 0;
							} else {
								$brand_id = (int) $res['term_id'];
							}
						}
						if ( ! empty( $brand_id ) ) {
							$existing_brands = wp_get_post_terms( $new_id, 'softone_brand', [ 'fields' => 'ids' ] );
							$allb = array_unique( array_merge( (array) $existing_brands, [ $brand_id ] ) );
							wp_set_post_terms( $new_id, $allb, 'softone_brand' );
						}
					}
					if ( isset( $product_data['qty'] ) && $product_data['qty'] !== null ) {
						$qty = (float) $product_data['qty'];
						update_post_meta( $new_id, '_manage_stock', 'yes' );
						update_post_meta( $new_id, '_stock', $qty );
						update_post_meta( $new_id, '_stock_status', $qty > 0 ? 'instock' : 'outofstock' );
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

		update_option( self::OPTION_LAST_SYNC_TIME, $sync_start_time, false );
		
		$duration = time() - $sync_start_time;
		Logger::log( sprintf( 'Products sync completed. Updated/created: %d items in %d seconds', $total, $duration ) );
	}
}
