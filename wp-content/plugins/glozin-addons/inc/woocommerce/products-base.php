<?php
namespace Glozin\Addons\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Products_Base {
	/**
	 * Render products loop content for shortcode.
	 *
	 * @param array $settings Shortcode attributes
	 */
	public static function render_products( $settings ) {
		return self::get_products_loop_content( $settings );
	}

	public static function get_product_ids( $settings ) {
		if ( isset( $settings['_id'] ) ) {
			unset( $settings['_id'] );
		}

		if( ! empty( $settings['slides_to_show'] ) ) {
			$settings['columns'] = $settings['slides_to_show'];
		}

		$product_ids = self::products_shortcode( $settings );
		$product_ids = ! empty($product_ids) ? $product_ids['ids'] : 0;

		if( ! $product_ids ) {
			return '';
		}

		return $product_ids;
	}

	/**
	 * Get products loop content for shortcode.
	 *
	 * @param array $settings Shortcode attributes
	 * @return string
	 */
	public static function get_products_loop_content( $settings ) {
		$output = [];

		if ( isset( $settings['_id'] ) ) {
			unset( $settings['_id'] );
		}

		if( ! empty( $settings['slides_to_show'] ) ) {
			$settings['columns'] = $settings['slides_to_show'];
		}

		$product_ids = self::products_shortcode( $settings );
		$product_ids = ! empty($product_ids) ? $product_ids['ids'] : 0;

		if( ! $product_ids ) {
			return '';
		}

		ob_start();

		wc_setup_loop(
			array(
				'columns' => $settings['columns']
			)
		);

		$results = self::get_template_loop( $product_ids );

		$output[] = ob_get_clean();

		if( ! empty( $settings['pagination'] ) ) {
			$pagination_type = isset( $settings['pagination_type'] ) ? esc_attr( $settings['pagination_type'] ) : 'loadmore';
			$output[] = self::get_pagination( $settings, $results, $pagination_type );
		}

		return implode( '', $output );
	}

	/**
	 * Get products loop content for shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Shortcode attributes
	 *
	 * @return array
	 */
	public static function products_shortcode( $settings ) {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return;
		}

		if ( isset( $settings['_id'] ) ) {
			unset( $settings['_id'] );
		}

		$type = $settings['type'];

		if( isset( $settings[ 'type' ] ) && $settings[ 'type' ] == 'featured_products' ) {
			$settings[ 'visibility' ] = 'featured';
		}

		switch ( $type ) {
			case 'recent_products':
				$settings['order']        = 'DESC';
				$settings['orderby']      = 'date';
				$settings['cat_operator'] = 'IN';
				break;

			case 'top_rated_products':
				$settings['orderby']      = 'title';
				$settings['order']        = 'ASC';
				$settings['cat_operator'] = 'IN';
				break;

			case 'percentage_discount':
			case 'day':
			case 'week':
			case 'month':
			case 'deals':
			case 'sale_products':
			case 'best_selling_products':
				$settings['cat_operator'] = 'IN';
				break;

			case 'featured_products':
				$settings['cat_operator'] = 'IN';
				$settings['visibility']   = 'featured';
				break;

			case 'custom_products':
				$settings['orderby']      = empty($settings['orderby']) ? 'post__in' : $settings['orderby'];
				$settings['order']        = 'ASC';
				break;

			case 'product':
				$settings['skus']  = isset( $settings['sku'] ) ? $settings['sku'] : '';
				$settings['ids']   = isset( $settings['id'] ) ? $settings['id'] : '';
				$settings['limit'] = '1';
				break;
		}

		if( ! empty( $settings['slides_to_show'] ) ) {
			$settings['columns'] = $settings['slides_to_show'];
		}

		// Convert category list to string.
		if ( ! empty( $settings['category'] ) && is_array( $settings['category'] ) ) {
			$settings['category'] = implode( ',', $settings['category'] );
		}

		// Convert tag list to string.
		if ( ! empty( $settings['tag'] ) && is_array( $settings['tag'] ) ) {
			$settings['tag'] = implode( ',', $settings['tag'] );
		}

		// Convert attributes string to array.
		if ( ! empty( $settings['attributes'] ) && is_string( $settings['attributes'] ) ) {
			$settings['attributes'] = explode( ',', $settings['attributes'] );
		}

		if ( empty( $settings['orderby'] ) ) {
			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : 'DESC';

			if ( in_array( $orderby, array( 'menu_order', 'price' ) ) ) {
				$order = 'ASC';
			}

			$settings['orderby'] = strtolower( $orderby );
			$settings['order'] = strtoupper( $order );
		}

		$shortcode  = new \WC_Shortcode_Products( $settings, $type );
		$query_args = $shortcode->get_query_args();

		$product_type = in_array( $type, array( 'day', 'week', 'month', 'deals', 'percentage_discount' ) ) ? 'sale_products' : $type;

		if(strpos($product_type, 'products') === false){
			$product_type .= '_products';
		}

		if ( $product_type !='custom_products' && method_exists( static::class, "set_{$product_type}_query_args" ) ) {
			self::{"set_{$product_type}_query_args"}( $query_args );
		}

		if ( isset( $settings['product_brands'] ) && ! empty( $settings['product_brands'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_brand',
				'terms'    => explode( ',', $settings['product_brands'] ),
				'field'    => 'slug',
				'operator' => 'IN',
			);
		}

		if ( isset( $settings['attributes'] ) && ! empty( $settings['attributes'] ) && is_array( $settings['attributes'] ) ) {
			$attribute_terms = array_map( 'sanitize_title', $settings['attributes'] );
			$attribute_taxonomies = wc_get_attribute_taxonomies();

			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $tax ) {
					$taxonomy = wc_attribute_taxonomy_name( $tax->attribute_name );

					$terms = get_terms( array(
						'taxonomy' => $taxonomy,
						'slug'     => $attribute_terms,
						'fields'   => 'slugs',
						'hide_empty' => false,
					) );

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						$query_args['tax_query'][] = array(
							'taxonomy' => $taxonomy,
							'field'    => 'slug',
							'terms'    => $terms,
							'operator' => 'IN',
						);
					}
				}
			}
		}

		if( isset( $settings['exclude_grouped'] ) && $settings['exclude_grouped'] ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array('grouped'),
				'operator' => 'NOT IN',
			);
		}

		if( isset( $settings['exclude_external'] ) && $settings['exclude_external'] ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'external' ),
				'operator' => 'NOT IN',
			);
		}

		if( isset( $settings['hide_product_outofstock'] ) && $settings['hide_product_outofstock'] == 'yes' ) {
			$query_args['meta_query'] = apply_filters(
				'glozin_product_outofstock_meta_query', array_merge(
					WC()->query->get_meta_query(), array(
						array(
							'key'       => '_stock_status',
							'value'     => 'outofstock',
							'compare'   => 'NOT IN'
						)
					)
				)
			);
		}

		if (in_array($type, ['day', 'week', 'month'])) {
			$now = strtotime( current_time( 'Y-m-d H:i:s' ) );
			switch ($type) {
				case 'day':
					$expire_date = strtotime( '00:00 next day', $now );
					break;
				case 'week':
					$expire_date = strtotime( '00:00 next monday', $now );
					break;
				case 'month':
					$expire_date = strtotime( 'last day of this month 23:59:59', $now );
					break;
			}

			$meta_query = array_merge(
				WC()->query->get_meta_query(),
				[
					[
						'key'     => '_sale_price_dates_to',
						'value'   => 0,
						'compare' => '>',
					],
					[
						'key'     => '_sale_price_dates_to',
						'value'   => $expire_date,
						'compare' => '<=',
					],
				]
			);

			$query_args['meta_query'] = apply_filters('glozin_product_deals_meta_query', $meta_query);
		}

		if ( $type == 'sale_products' ) {
			if ( !empty( $settings['sale_date_usage'] ) && in_array( $settings['sale_date_usage'], array( 'filter_products', 'both' ) ) ) {
				$date = ! empty( $settings['sale_date_to'] ) ? $settings['sale_date_to'] : '';
				if ( ! empty( $date ) ) {
					$meta_query = array_merge(
						WC()->query->get_meta_query(),
						[
							[
								'key'     => '_sale_price_dates_to',
								'value'   => 0,
								'compare' => '>',
							],
							[
								'key'     => '_sale_price_dates_to',
								'value'   => strtotime($date),
								'compare' => '<=',
							],
						]
					);

					$query_args['meta_query'] = apply_filters('glozin_product_on_sale_meta_query', $meta_query);
				}
			}
		}

		if ( $type == 'percentage_discount' && ( ! empty( $settings['percent_from'] ) || ! empty( $settings['percent_to'] ) ) && ( $settings['percent_from'] > 0 || $settings['percent_to'] > 0 ) ) {
			$query_args['meta_query'] = apply_filters(
				'glozin_product_sale_discount_percent_meta_query', array_merge(
					WC()->query->get_meta_query(), array(
						'relation' => 'OR',
						array(
							'key'     => 'glozin_product_sale_discount_percent',
							'value'   => array( intval( $settings['percent_from'] ), intval( $settings['percent_to'] ) ),
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC',
						),
						array(
							'key'     => 'glozin_product_sale_discount_percent_min',
							'value'   => array( intval( $settings['percent_from'] ), intval( $settings['percent_to'] ) ),
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC',
						),
						array(
							'key'     => 'glozin_product_sale_discount_percent_max',
							'value'   => array( intval( $settings['percent_from'] ), intval( $settings['percent_to'] ) ),
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC',
						),
					)
				)
			);
		}

		if ( isset( $settings['page'] ) ) {
			$query_args['paged'] = isset( $settings['page'] ) ? absint( $settings['page'] ) : 1;
		}

		if ( isset( $settings['post__not_in'] ) && ! empty($settings['post__not_in']) ) {
			$query_args['post__not_in'] = array($settings['post__not_in']);
		}

		if( isset( $settings['product_outofstock_last'] ) && $settings['product_outofstock_last'] == 'yes' ) {
			add_filter('posts_clauses', array(\Glozin\Addons\Modules\Inventory\Frontend::instance(), 'order_by_stock_clauses'), 2000);
		}

		$results = self::get_query_results( $query_args, $type );

		if( isset( $settings['product_outofstock_last'] ) && $settings['product_outofstock_last'] == 'yes' ) {
			remove_filter('posts_clauses', array(\Glozin\Addons\Modules\Inventory\Frontend::instance(), 'order_by_stock_clauses'), 2000);
		}
		return $results;
	}

	/**
	 * Pagination product elementor
	 *
	 * @param array $settings Shortcode attributes
	 */
	public static function get_pagination( $settings, $results, $type = 'loadmore' ) {
		$settings['paginate'] = true;

		if( empty( $results ) ) {
			$results = self::products_shortcode( $settings );
		}

		if ( $results['current_page'] > $results['total_pages'] || $results['total_pages'] == '1'  ) {
			return;
		}

		if( $type == 'numeric' ) {
			$args = array(
				'total'   => $results['total_pages'],
				'current' => $results['current_page'],
				'base'    => esc_url_raw( add_query_arg( 'product-page', '%#%', false ) ),
				'format'  => '?product-page=%#%',
			);

			if ( ! wc_get_loop_prop( 'is_shortcode' ) ) {
				$args['format'] = '';
				$args['base']   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
			}

			ob_start();
			wc_get_template( 'loop/pagination.php', $args );
			$output = ob_get_clean();
			return $output;
		}

		$classes = array(
			'woocommerce-pagination',
			'woocommerce-pagination--catalog',
			'next-posts-pagination',
			'woocommerce-pagination--ajax',
			'woocommerce-pagination--' . esc_attr( $type ),
			'text-center'
		);

		$button_class = ! empty( $settings['button_style'] ) ? 'gz-button-'  . $settings['button_style'] : '';
		$button_class .= in_array( $settings['button_style'], ['', 'light', 'outline-dark' , 'outline'] ) ? ' px-30 px-md-48' : '';
		$button_class .= in_array( $settings['button_style'], ['', 'light', 'outline-dark'] ) ? ' gz-button-hover-effect' : '';

		$output = sprintf( '<a href="#" class="woocommerce-pagination-button ajax-load-products glozin-button gz-button fw-semibold %s" data-page="%s">
				<span>%s</span>
			</a>',
			esc_attr( $button_class ),
			esc_attr( $results['current_page'] + 1 ),
			! empty( $settings['pagination_text'] ) ? esc_html( $settings['pagination_text'] ) : esc_html__( 'Load more', 'glozin-addons' ),
			);

		return '<nav class="'. esc_attr( implode( ' ', $classes ) ) . '">' . $output . '</nav>';
	}

	/**
	 * Run the query and return an array of data, including queried ids.
	 *
	 * @since 1.0.0
	 *
	 * @return array with the following props; ids
	 */
	public static function get_query_results( $query_args, $type ) {
		$transient_name       = self::get_transient_name( $query_args, $type );
		$transient_version    = \WC_Cache_Helper::get_transient_version( 'product_query' );
		$transient_value      = get_transient( $transient_name );

		if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
			$results = $transient_value['value'];
		} else {
			if( in_array( $type, array( 'day', 'week', 'month' ) ) || $type == 'deals' ) {
				$product_variables = self::get_product_variable_ids( $query_args, $type );

				if( ! empty ( $product_variables ) ) {
					foreach( $product_variables as $key => $value ) {
						$key = array_search( $value, $query_args['post__in'] );
						if( $key ) {
							unset($query_args['post__in'][$key]);
						}
					}
				}
			}

			$query = new \WP_Query( $query_args );

			$paginated = ! $query->get( 'no_found_rows' );

			$results = array(
				'ids'          => wp_parse_id_list( $query->posts ),
				'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
				'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
				'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
			);

			wp_reset_postdata();
		}

		// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
		WC()->query->remove_ordering_args();

		return $results;
	}

	/**
	 * Generate and return the transient name for this shortcode based on the query args.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_transient_name( $query_args, $type ) {
		$transient_name = 'glozin_product_loop_' . md5( wp_json_encode( $query_args ) . $type );

		if ( 'rand' === $query_args['orderby'] ) {
			// When using rand, we'll cache a number of random queries and pull those to avoid querying rand on each page load.
			$rand_index     = wp_rand( 0, max( 1, absint( apply_filters( 'woocommerce_product_query_max_rand_cache_count', 5 ) ) ) );
			$transient_name .= $rand_index;
		}

		return $transient_name;
	}

	/**
	 * Run the query and return an array of data, including queried ids.
	 *
	 * @since 1.0.0
	 *
	 * @return array with the following props; ids
	 */
	public static function get_product_variable_ids( $query_args, $type ) {
		$product_variables = [];
		$variable = false;

		if( in_array( $type, array( 'day', 'week', 'month' ) ) || $type == 'deals' ) {
			$query = new \WP_Query( $query_args );
			$ids = wp_parse_id_list( $query->posts );

			if( ! empty( $ids ) ) {
				foreach( $ids as $key => $id ) {
					$_product = wc_get_product( $id );
					if( $_product->is_type( 'variable') ) {
						$variations = $_product->get_available_variations();
						$variations_id = wp_list_pluck( $variations, 'variation_id' );

						foreach( $variations_id as $variation ) {
							$deal_quantity = get_post_meta( $variation, '_deal_quantity', true );
							if( $deal_quantity > 0 ) {
								$variable = true;
								break;
							}
						}

						if( ! $variable ) {
							$product_variables[] = $id;
						}
					}
				}
			}
		}

		wp_reset_query();

		return $product_variables;
	}

	/**
	 * Loop over products
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	public static function get_template_loop( $products_ids, $template = 'product' ) {
		if( empty( $products_ids ) ) {
			return;
		}
		update_meta_cache( 'post', $products_ids );
		update_object_term_cache( $products_ids, 'product' );

		$original_post = $GLOBALS['post'];

		woocommerce_product_loop_start();

		foreach ( $products_ids as $product_id ) {
			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', $template );
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		woocommerce_product_loop_end();

		wp_reset_postdata();
		wc_reset_loop();
	}

	/**
	 * Set ids query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	public static function set_recent_products_query_args( &$query_args ) {
		$query_args['order']   = 'DESC';
		$query_args['orderby'] = 'date';
	}

	/**
	 * Set ids query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	public static function set_custom_products_query_args( &$query_args ) {
		//$query_args['orderby'] = 'post__in';
	}

	/**
	 * Set sale products query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	public static function set_sale_products_query_args( &$query_args ) {
		$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
	}

	/**
	 * Set best selling products query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	public static function set_best_selling_products_query_args( &$query_args ) {
		$query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$query_args['order']    = 'DESC';
		$query_args['orderby']  = 'meta_value_num';
	}

	/**
	 * Set top rated products query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	public static function set_top_rated_products_query_args( &$query_args ) {
		$query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$query_args['order']    = 'DESC';
		$query_args['orderby']  = 'meta_value_num';
	}

	/**
	 * Set visibility as featured.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	public static function set_featured_products_query_args( &$query_args ) {
		$query_args['tax_query'] = array_merge( $query_args['tax_query'], WC()->query->get_tax_query() ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'featured',
			'field'            => 'name',
			'operator'         => 'IN',
			'include_children' => false,
		);
	}

	/**
	 * Get all available orderby options.
	 *
	 * @return array
	 */
	protected function get_options_product_orderby() {
		return [
			''           => __( 'Default', 'glozin-addons' ),
			'menu_order' => __( 'Menu Order', 'glozin-addons' ),
			'date'       => __( 'Date', 'glozin-addons' ),
			'id'         => __( 'Product ID', 'glozin-addons' ),
			'title'      => __( 'Product Title', 'glozin-addons' ),
			'rand'       => __( 'Random', 'glozin-addons' ),
			'price'      => __( 'Price', 'glozin-addons' ),
			'popularity' => __( 'Popularity (Sales)', 'glozin-addons' ),
			'rating'     => __( 'Rating', 'glozin-addons' ),
		];
	}

	/**
	 * Get all supported product type options.
	 *
	 * @return array
	 */
	protected function get_options_product_type() {
		return [
			'recent_products'       => __( 'Recent Products', 'glozin-addons' ),
			'featured_products'     => __( 'Featured Products', 'glozin-addons' ),
			'sale_products'         => __( 'Sale Products', 'glozin-addons' ),
			'best_selling_products' => __( 'Best Selling Products', 'glozin-addons' ),
			'top_rated_products'    => __( 'Top Rated Products', 'glozin-addons' ),
			'custom_products'    => __( 'Custom Products', 'glozin-addons' ),
		];
	}

	/**
	 * Get deal progress
	 *
	 * @return void
	 */
	public static function deal_progress() {
		global $product;

		if( $product->is_type( 'simple' ) ) {
			$limit = get_post_meta( $product->get_id(), '_deal_quantity', true );
			$sold  = intval( get_post_meta( $product->get_id(), '_deal_sales_counts', true ) );
		}

		if( $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();
			$variations_id = wp_list_pluck( $variations, 'variation_id' );

			$args = array();
			foreach( $variations_id as $variation ) {
				$limit = get_post_meta( $variation, '_deal_quantity', true );
				$sold = get_post_meta( $variation, '_deal_sales_counts', true );

				if( $limit > 0 ) {
					$args[$variation] = $sold;
				}
			}

			if( ! empty( $args ) ) {
				$limit = get_post_meta( array_search( max($args), $args ), '_deal_quantity', true );
				$sold = max($args);
			}
		}

		if( empty( $limit ) ) {
			return;
		}
		?>

		<div class="deal-sold">
			<div class="deal-progress">
				<div class="progress-bar">
					<div class="progress-value" style="width: <?php echo $sold / $limit * 100 ?>%"></div>
				</div>
				<div class="deal-content">
					<div class="deal-text">
						<?php esc_html_e( 'Sold:', 'glozin-addons' ) ?>
						<span class="amount"><span class="sold"><?php echo $sold ?></span></span>
					</div>
					<div class="deal-available">
						<?php esc_html_e( 'Available:', 'glozin-addons' ) ?>
						<span class="amount"><span class="sold"><?php echo $limit - $sold ?></span></span>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Get an array of attributes and terms selected with the layered nav widget.
	 *
	 * @return array
	 */
	public static function get_layered_nav_chosen_attributes() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$chosen_attributes = array();

		if ( ! empty( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( 0 === strpos( $key, 'filter_' ) ) {
					$attribute    = wc_sanitize_taxonomy_name( str_replace( 'filter_', '', $key ) );
					$taxonomy     = wc_attribute_taxonomy_name( $attribute );
					$filter_terms = ! empty( $value ) ? explode( ',', wc_clean( wp_unslash( $value ) ) ) : array();

					if ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! wc_attribute_taxonomy_id_by_name( $attribute ) ) {
						continue;
					}

					$query_type                                    = ! empty( $_GET[ 'query_type_' . $attribute ] ) && in_array( $_GET[ 'query_type_' . $attribute ], array( 'and', 'or' ), true ) ? wc_clean( wp_unslash( $_GET[ 'query_type_' . $attribute ] ) ) : '';
					$chosen_attributes[ $taxonomy ]['terms'] = array_map( 'sanitize_title', $filter_terms ); // Ensures correct encoding.
					$chosen_attributes[ $taxonomy ]['query_type'] = $query_type ? $query_type : apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
				}
			}
		}

		return $chosen_attributes;
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
	}

	public function get_recently_viewed_products( $settings ) {
		$products_ids = self::get_product_recently_viewed_ids();

		$limit = ! empty( $settings['limit'] ) ? $settings['limit'] : 5;
		$columns = ! empty( $settings['columns'] ) ? $settings['columns'] : 4;
		$page = ! empty( $settings['page'] ) ? $settings['page'] : 1;

		$paged = ! empty( $page ) ? $page : 1;
		$offset = ($paged - 1) * $limit;
		$paged_products_ids = array_slice($products_ids, $offset, $limit);
		$total_pages = ceil( count($products_ids) / $limit );

		if ( empty( $paged_products_ids ) ) {
			?>
				<div class="no-products">
					<p><?php echo esc_html__( 'No products in recent viewing history.', 'glozin-addons' ) ?></p>
				</div>

			<?php
		} else {
			update_meta_cache( 'post', $paged_products_ids );
			update_object_term_cache( $paged_products_ids, 'product' );

			$original_post = $GLOBALS['post'];

			wc_setup_loop(
				array(
					'columns' => $columns
				)
			);

			woocommerce_product_loop_start();

			$index = 1;

			foreach ( $paged_products_ids as $product_id ) {
				if ( $index > intval( $limit ) ) {
					break;
				}

				$product_id = apply_filters('wpml_object_id', $product_id, 'product', true);
				if (empty($product_id)) {
					continue;
				}

				$index ++;

				$product = get_post( $product_id );
				if ( empty( $product ) ) {
					continue;
				}

				$GLOBALS['post'] = $product; // WPCS: override ok.
				setup_postdata( $GLOBALS['post'] );
				wc_get_template_part( 'content', 'product' );
			}

			$GLOBALS['post'] = $original_post; // WPCS: override ok.

			woocommerce_product_loop_end();

			wp_reset_postdata();
			wc_reset_loop();

			if( ! empty( $settings['pagination'] ) && $settings['pagination'] == 'yes' && $total_pages > 1 && $paged < $total_pages ) {
				echo '<nav class="woocommerce-pagination w-100">';
					echo sprintf( '<a href="#" class="woocommerce-pagination-button gz-button gz-button-outline-dark py-17 px-30 px-md-46" data-page="%s">%s</a>',
						esc_attr( $paged + 1 ),
						esc_html__( 'Load more', 'glozin-addons' )
					);

				echo '</nav>';
			}
		}
	}

	/**
	 * Get recently viewed ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_product_recently_viewed_ids() {
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();

		return array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
	}
}