<?php
/**
 * Woocommerce Setup functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Helper {

	/**
	 * Get product countdown
	 */
	public static function get_product_countdown( $sale = '', $text = '', $classes = '', $product = null, $expire_date = null ) {
		if( empty( $product ) ) {
			global $product;
		}

		$now         = strtotime( current_time( 'Y-m-d H:i:s' ) );
		$expire_date = $expire_date == null ? self::get_date_on_sale_to( $product ) : $expire_date;
		$expire_date = ! empty( $expire_date ) ? date_i18n( $expire_date ) : '';
		$expire      = ! empty( $expire_date ) ? $expire_date - $now : -1;
		$expire = apply_filters( 'glozin_countdown_product_second', $expire );

		if ( empty( $sale ) ) {
			$sale = array(
				'weeks'   => esc_html__( 'w', 'glozin' ),
				'days'    => esc_html__( 'd', 'glozin' ),
				'hours'   => esc_html__( 'h', 'glozin' ),
				'minutes' => esc_html__( 'm', 'glozin' ),
				'seconds' => esc_html__( 's', 'glozin' ),
			);
		}

		if ( $text ) {
			$text = '<div class="gz-product-countdown__text">'. $text .'</div>';
		}

		$classes = empty( $classes ) ? 'gz-product-countdown__countdown' : $classes;

		if ( empty( $expire ) || $expire < 0 ) {
			return;
		}

		$days = floor($expire / (60 * 60 * 24));
		$hours = str_pad(floor(($expire % (60 * 60 * 24)) / (60 * 60)), 2, '0', STR_PAD_LEFT);
		$minutes = str_pad(floor(($expire % (60 * 60)) / (60)), 2, '0', STR_PAD_LEFT);
		$seconds = str_pad(floor($expire % 60), 2, '0', STR_PAD_LEFT);

		return sprintf( '<div class="gz-product-countdown %s">
							%s
							<div class="glozin-countdown d-flex justify-content-center lh-1" data-expire="%s" data-text="%s">
								<span class="days timer d-flex align-items-end text-inherit">
									<span class="digits fs-inherit fw-inherit text-transform-inherit m-0">%s</span>
									<span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-2">%s</span>
									<span class="divider d-inline fs-inherit fw-normal text-transform-inherit m-0 ps-4 pe-5">:</span>
								</span>
								<span class="hours timer d-flex align-items-end text-inherit">
									<span class="digits fs-inherit fw-inherit text-transform-inherit m-0">%s</span>
									<span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-2">%s</span>
									<span class="divider d-inline fs-inherit fw-normal text-transform-inherit m-0 ps-4 pe-5">:</span>
								</span>
								<span class="minutes timer d-flex align-items-end text-inherit">
									<span class="digits fs-inherit fw-inherit text-transform-inherit m-0">%s</span>
									<span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-2">%s</span>
									<span class="divider d-inline fs-inherit fw-normal text-transform-inherit m-0 ps-4 pe-5">:</span>
								</span>
								<span class="seconds timer d-flex align-items-end text-inherit">
									<span class="digits fs-inherit fw-inherit text-transform-inherit m-0">%s</span>
									<span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-2">%s</span>
								</span>
							</div>
						</div>',
					! empty( $classes ) ? esc_attr( $classes ) : '',
					$text,
					esc_attr( $expire ),
					esc_attr( wp_json_encode( $sale ) ),
					esc_html( $days ),
					$sale['days'],
					esc_html( $hours ),
					$sale['hours'],
					esc_html( $minutes ),
					$sale['minutes'],
					esc_html( $seconds ),
					$sale['seconds']
				);

	}

	public static function get_date_on_sale_to( $product, $args = array() ) {
		$sale_date = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );

		if( ! $product->is_type( 'variable' ) ) {
			return $sale_date;
		}

		$variation_ids = $product->get_visible_children();

		if( empty( $variation_ids ) ) {
			return $sale_date;
		}

		$sale_dates = array();
		foreach( $variation_ids as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( $variation->is_on_sale() ) {
				$date_on_sale_to   = $variation->get_date_on_sale_to();

				if( ! empty($date_on_sale_to) ) {
					$sale_dates[] = $date_on_sale_to;
				}
			}
		}

		if( ! empty( $sale_dates ) ) {
			$sale_date = strtotime( min( $sale_dates ) );
		}

		$sale_date = apply_filters( 'glozin_product_sale_dates_to', $sale_date );

		return $sale_date;
	}

	public static function get_product_taxonomy( $taxonomy = 'product_cat', $product = false ) {
		if( ! $product ) {
			global $product;
		}

		if( empty($taxonomy ) ) {
			return false;
		}

		$terms = wc_get_product_terms(
			$product->get_id(),
			$taxonomy,
			apply_filters(
				'woocommerce_breadcrumb_product_terms_args',
				array(
					'orderby' => 'parent',
					'order'   => 'DESC',
				)
			)
		);
		if( !is_wp_error( $terms ) && !empty($terms) ) {
			return $terms;
		}

		return false;
	}

	public static function lazy_load_image_effect_html() {
		?>
			<div class="gz-lazy-load-image"></div>
		<?php
	}

	/**
	 *  Add to cart button
	 */
	public static function add_to_cart_button($product) {
		$classes = 'gz-add-to-cart-button button gz-button-subtle product-loop-button mt-15';
		$classes .= ' product_type_' . $product->get_type();
		$classes .= $product->is_purchasable() && $product->is_in_stock() ? ' add_to_cart_button' : '';
		$classes .= $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? ' ajax_add_to_cart' : '';

		$data_toggle = '';
		$data_target = '';

		if( $product->get_type() == 'variable' && \Glozin\Helper::get_option( 'product_card_quickadd' ) ) {
			$data_toggle = 'data-toggle="modal"';
			$data_target = 'data-target="quick-view-modal"';
			$classes .= ' glozin-quickview-button';
			\Glozin\Theme::set_prop( 'modals', 'quickview' );
		}

		if( $product->get_stock_status() === 'outofstock'  && class_exists('CWG_Instock_Notifier') ) {
			$data_toggle = 'data-toggle="modal"';
			$data_target = 'data-target="quick-view-modal"';
			$classes .= ' glozin-quickview-button';
			\Glozin\Theme::set_prop( 'modals', 'quickview' );
		}

		$classes = apply_filters( 'glozin_quick_view_button_icon_classes', $classes );

		echo sprintf(
			'<a href="%s" data-quantity="1" class="%s" data-product_id="%s" data-tooltip="%s" aria-label="%s" %s %s rel="nofollow">%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $classes ),
			esc_attr($product->get_id()),
			$product->add_to_cart_text(),
			$product->add_to_cart_text() . esc_attr__( ' for ', 'glozin' ) . $product->get_title(),
			$data_toggle,
			$data_target,
			$product->add_to_cart_text()
		);
	 }

	public static function products_shortcode( $type, $limit = 4 ) {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return;
		}

		$atts = array(
			'per_page'     => intval( $limit ),
			'category'     => '',
			'cat_operator' => 'IN',
		);

		switch ( $type ) {
			case 'sale_products':
			case 'top_rated_products':
				$atts = array_merge( array(
					'orderby' => 'title',
					'order'   => 'ASC',
				), $atts );
				break;

			case 'recent_products':
				$atts = array_merge( array(
					'orderby' => 'date',
					'order'   => 'DESC',
				), $atts );
				break;

			case 'featured_products':
				$atts = array_merge( array(
					'orderby' => 'date',
					'order'   => 'DESC',
				), $atts );
				break;
		}

		$args  = new \WC_Shortcode_Products( $atts, $type );
		$args  = $args->get_query_args();

		if ( function_exists( 'WC' ) && WC()->cart && is_a( WC()->cart, 'WC_Cart' ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$product_id[] = $cart_item['product_id'];
			}

			if ( ! empty( $product_id ) ) {
				$args = array_merge( array(
					'post__not_in' => $product_id,
				), $args );
			}
		}

		$query = new \WP_Query( $args );

		$query_posts = $query->posts;

		if( !count($query_posts) ) {
			return;
		}

		return $query_posts;
	}

	public static function products_shortcode_template( $query_posts, $args = array() ) {
		if ( empty( $query_posts ) ) {
			return;
		}

		if( ! function_exists( 'wc_get_template' ) ) {
			return;
		}

		$original_post = $GLOBALS['post'];

		foreach ( $query_posts as $product ) {
			$_product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

			if( empty( $_product ) || ! is_object( $_product ) ) {
				continue;
			}

			$post_object = get_post( $_product->get_id() );

			setup_postdata( $GLOBALS['post'] = $post_object );

			wc_get_template( 'content-product-list.php', $args );
		}

		$GLOBALS['post'] = $original_post;

		wp_reset_postdata();
	}
}