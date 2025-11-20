<?php
/**
 * Woocommerce Setup functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class General {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );
		add_filter( 'glozin_wp_script_data', array( $this, 'script_data' ), 10, 3 );

		// Update counter via ajax.
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_link_fragment' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_subtotal_fragment' ) );

		// Change star rating HTML.
		add_filter( 'woocommerce_product_get_rating_html', array( $this, 'product_get_rating_html' ), 10, 3 );
		add_filter( 'woocommerce_get_star_rating_html', array( $this, 'star_rating_html' ), 10, 3 );

		// Change position widget Product Grid
		add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'change_position_product_grid' ), 10, 3 );

		// Clear Cache
		add_action( 'woocommerce_scheduled_sales', array( $this, 'glozin_woocommerce_clear_cache_daily' ) );
		add_action( 'customize_save_after', array( $this, 'glozin_woocommerce_clear_cache_daily' ) );
		add_action( 'save_post', array( $this, 'glozin_woocommerce_clear_cache' ) );
		add_action( 'wp_trash_post', array( $this, 'glozin_woocommerce_clear_cache' ) );
		add_action( 'before_delete_post', array( $this, 'glozin_woocommerce_clear_cache' ) );

		// Change pagination
		add_filter( 'woocommerce_pagination_args', array( $this, 'pagination_args' ) );

		// Change the quantity format of the cart widget.
		add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'cart_item_quantity'	), 10, 3 );
		add_action('woocommerce_before_quantity_input_field', array($this, 'quantity_icon_decrease'));
		add_action('woocommerce_after_quantity_input_field', array($this, 'quantity_icon_increase'));

		// Review
		add_filter( 'woocommerce_reviews_title', array( $this, 'reviews_title' ) );

		add_action( 'woocommerce_before_cart_totals', array( $this, 'before_cart_totals' ), 10 );
		add_action( 'woocommerce_after_cart_totals', array( $this, 'after_cart_totals' ), 10 );

		add_filter( 'woocommerce_cart_subtotal', array( $this, 'cart_subtotal' ), 10, 3);

		add_filter( 'woocommerce_cart_item_name', array( $this, 'review_product_name_html' ), 10, 3);
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'review_cart_item_quantity_html' ), 10, 3);

		add_filter('woocommerce_order_item_name', array( $this, 'order_item_name' ), 20, 3);

		if( intval( Helper::get_option( 'recently_viewed_products_ajax' ) ) ) {
			add_action( 'wc_ajax_glozin_get_recently_viewed', array( $this, 'do_ajax_products_content' ) );
		}

		// Change availability text in single product
		add_filter( 'woocommerce_get_availability_text', array( $this, 'get_product_availability_text' ), 20, 2 );

		// Add to cart grouped actions
		add_action( 'template_redirect', array( $this, 'add_to_cart_grouped_actions' ) );

		// Change step price filter widget
		add_filter( 'woocommerce_price_filter_widget_step', array( $this, 'price_filter_widget_step' ) );

		// Allow HTML in taxonomy desc
		if ( intval( Helper::get_option( 'taxonomy_description_html' ) )) {
			$rm_filter = 'remove_filter';
			$rm_filter( 'pre_term_description', 'wp_filter_kses' );
			$rm_filter( 'term_description', 'wp_kses_data' );
		}
	}

	/**
	 * Add class to the body tag.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes CSS classes applied to the body tag.
	 *
	 * @return array $classes
	 */
	public function body_class( $classes ) {
		$cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );
		if ('uncropped' == $cropping) {
			$classes[] = 'product-image-uncropped';
		}

		return $classes;
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( class_exists( 'CWG_Instock_Notifier' ) ) {
			wp_enqueue_style( 'glozin-back-in-stock-notifier', get_template_directory_uri() . '/assets/css/woocommerce/plugins/back-in-stock-notifier' . $debug . '.css', array(),  \Glozin\Helper::get_theme_version() );
		}

		if ( class_exists( 'YITH_Request_Quote' ) ) {
			wp_enqueue_style( 'glozin-request-a-quote', get_template_directory_uri() . '/assets/css/woocommerce/plugins/request-a-quote' . $debug . '.css', array(),  \Glozin\Helper::get_theme_version() );
		}

		wp_enqueue_style( 'glozin-woocommerce-style', apply_filters( 'glozin_get_style_directory_uri', get_template_directory_uri() ) . '/assets/css/woocommerce/woocommerce' . $debug . '.css', array(),  \Glozin\Helper::get_theme_version() );

		$parse_css = apply_filters( 'glozin_wc_inline_style', false );
		if( $parse_css ) {
			wp_add_inline_style( 'glozin-woocommerce-style', $parse_css );
		}

		if ( apply_filters( 'glozin_load_catalog_layout', \Glozin\Helper::is_catalog() ) ) {
			wp_enqueue_script( 'glozin-product-catalog', get_template_directory_uri() . '/assets/js/woocommerce/product-catalog' . $debug . '.js',
				array(
					'glozin',
				),
				\Glozin\Helper::get_theme_version(),
				array('strategy' => 'defer')
			);

		}

		wp_enqueue_script( 'wc-cart-fragments' );
	}

	/**
	 * Script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function script_data( $data ) {
		$data['currency_pos']    = get_option( 'woocommerce_currency_pos' );
		$data['currency_symbol'] = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';
		$data['thousand_sep']    = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : '';
		$data['decimal_sep']     = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '';
		$data['price_decimals']  = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : '';

		return $data;
	}

	/**
	 * Ensure cart contents update when products are added to the cart via AJAX.
     *
	 * @since 1.0.0
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 *
	 * @return array Fragments to refresh via AJAX.
	 */
	public function cart_link_fragment( $fragments ) {
		$hidden = WC()->cart->is_empty() ? 'empty-counter' : '';

		$fragments['span.header-cart__counter'] = '<span class="header-counter header-cart__counter '. esc_attr( $hidden ) .'">' . intval( WC()->cart->get_cart_contents_count() ) . '</span>';

		return $fragments;
	}

	/**
	 * Ensure cart contents update when products are added to the cart via AJAX.
     *
	 * @since 1.0.0
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 *
	 * @return array Fragments to refresh via AJAX.
	 */
	public function cart_subtotal_fragment( $fragments ) {
		$fragments['span#header-cart-subtotal'] = '<span id="header-cart-subtotal" class="total-price fs-14 fw-semibold">' . WC()->cart->get_cart_subtotal() . '</span>';

		return $fragments;
	}

	/**
	 * Star rating HTML.
     *
	 * @since 1.0.0
	 *
	 * @param string $html Star rating HTML.
	 * @param int $rating Rating value.
	 * @param int $count Rated count.
	 *
	 * @return string
	 */
	public function product_get_rating_html( $html, $rating, $count ) {
		if ( $rating == 0 ) {
			$html = sprintf( '<div class="star-rating" role="img">
									<span class="max-rating rating-stars">
										%s
										%s
										%s
										%s
										%s
									</span>
								</div>',
							\Glozin\Icon::inline_svg( 'icon=star' ),
							\Glozin\Icon::inline_svg( 'icon=star' ),
							\Glozin\Icon::inline_svg( 'icon=star' ),
							\Glozin\Icon::inline_svg( 'icon=star' ),
							\Glozin\Icon::inline_svg( 'icon=star' ),
					);
		}

		return $html;
	}

	/**
	 * Star rating HTML.
     *
	 * @since 1.0.0
	 *
	 * @param string $html Star rating HTML.
	 * @param int $rating Rating value.
	 * @param int $count Rated count.
	 *
	 * @return string
	 */
	public function star_rating_html( $html, $rating, $count ) {
		$html = '<span class="max-rating rating-stars">'
		        . \Glozin\Icon::inline_svg( 'icon=star' )
		        . \Glozin\Icon::inline_svg( 'icon=star' )
		        . \Glozin\Icon::inline_svg( 'icon=star' )
		        . \Glozin\Icon::inline_svg( 'icon=star' )
		        . \Glozin\Icon::inline_svg( 'icon=star' )
		        . '</span>';
		$html .= '<span class="user-rating rating-stars" style="--gz-rating-width:' . ( ( $rating / 5 ) * 100 ) . '%">'
				. \Glozin\Icon::inline_svg( 'icon=star' )
				. \Glozin\Icon::inline_svg( 'icon=star' )
				. \Glozin\Icon::inline_svg( 'icon=star' )
				. \Glozin\Icon::inline_svg( 'icon=star' )
				. \Glozin\Icon::inline_svg( 'icon=star' )
		         . '</span>';

		$html .= '<span class="screen-reader-text">';

		if ( 0 < $count ) {
			/* translators: 1: rating 2: rating count */
			$html .= sprintf( _n( 'Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'glozin' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>', '<span class="rating">' . esc_html( $count ) . '</span>' );
		} else {
			/* translators: %s: rating */
			$html .= sprintf( esc_html__( 'Rated %s out of 5', 'glozin' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>' );
		}

		$html .= '</span>';

		return $html;
	}

	/**
	 * Filters the HTML for products in the grid.
	 *
	 * @param string $html Product grid item HTML.
	 * @param object $data Product data passed to the template.
	 * @param \WC_Product $product Product object.
	 * @return string Updated product grid item HTML.
	 *
	 * @since 2.2.0
	 */
	public function change_position_product_grid( $html, $data, $product ) {
		$html = "<li class=\"wc-block-grid__product\">
					<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
						{$data->badge}
						{$data->image}
					</a>
					<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-title-group\">
						{$data->title}
					</a>
					{$data->price}
					{$data->rating}
					{$data->button}
				</li>";

		return $html;
	}

	/**
	 * Get IDs of the products that are set as new ones.
	 *
	 * @return array
	 */
	public static function glozin_woocommerce_get_new_product_ids() {
		// Load from cache.
		$product_ids = get_transient( 'glozin_woocommerce_products_new' );

		// Valid cache found.
		if ( false !== $product_ids ) {
			return $product_ids;
		}

		$product_ids = array();

		// Get products which are set as new.
		$meta_query   = WC()->query->get_meta_query();
		$meta_query[] = array(
			'key'   => '_is_new',
			'value' => 'yes',
		);
		$new_products = new \WP_Query( array(
			'posts_per_page'   => -1,
			'post_type'        => 'product',
			'fields'           => 'ids',
			'suppress_filters' => true,
			'meta_query'       => $meta_query,
		) );

		if ( $new_products->have_posts() ) {
			$product_ids = array_merge( $product_ids, $new_products->posts );
		}

		// Get products after selected days.
		if ( Helper::get_option( 'badges_new' ) ) {
			$newness = intval( Helper::get_option( 'badges_newness' ) );

			if ( $newness > 0 ) {
				$new_products = new \WP_Query( array(
					'posts_per_page'   => -1,
					'post_type'        => 'product',
					'fields'           => 'ids',
					'suppress_filters' => true,
					'date_query'       => array(
						'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) ),
					),
				) );

				if ( $new_products->have_posts() ) {
					$product_ids = array_merge( $product_ids, $new_products->posts );
				}
			}
		}

		set_transient( 'glozin_woocommerce_products_new', $product_ids, DAY_IN_SECONDS );

		return $product_ids;
	}

	/**
	 * Clear new product ids cache with the sale schedule which is run daily.
	 */
	public static function glozin_woocommerce_clear_cache_daily() {
		delete_transient( 'glozin_woocommerce_products_new' );
	}

	/**
	 * Clear new product ids cache when update/trash/delete products.
	 *
	 * @param int $post_id
	 */
	public static function glozin_woocommerce_clear_cache( $post_id ) {
		if ( 'product' != get_post_type( $post_id ) ) {
			return;
		}

		delete_transient( 'glozin_woocommerce_products_new' );
	}

	/**
	 * WooCommerce pagination arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The pagination args.
	 *
	 * @return array
	 */
	public function pagination_args( $args ) {
		$args['prev_text'] = \Glozin\Icon::inline_svg( array( 'icon' => 'icon-back' ) ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'glozin' ) . '</span>';
		$args['next_text'] = \Glozin\Icon::inline_svg( array( 'icon' => 'icon-next' ) ) . '<span class="screen-reader-text">' . esc_html__( 'Next', 'glozin' ) . '</span>';

		return $args;
	}

	/**
	 * Change the quantity HTML of widget cart.
     *
	 * @since 1.0.0
	 *
	 * @param string $product_quantity
	 * @param array $cart_item
	 * @param string $cart_item_key
	 *
	 * @return string
	 */
	public function cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product->is_sold_individually() ) {
			$min_quantity = 1;
			$max_quantity = 1;
		} else {
			$min_quantity = 0;
			$max_quantity = $_product->get_max_purchase_quantity();
		}

		$quantity = woocommerce_quantity_input( array(
			'input_name'   => "cart[{$cart_item_key}][qty]",
			'input_value'  => $cart_item['quantity'],
			'max_value'    => $max_quantity,
			'min_value'    => $min_quantity,
			'product_name' => $_product->get_name(),
			'step'		   => apply_filters( 'glozin_woocommerce_quantity_input_step', 1, $_product, $cart_item, $cart_item_key ),
		), $_product, false );


		return $quantity;
	}

	/**
	 * Quantity Decrease Icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function quantity_icon_decrease() {
		echo \Glozin\Icon::get_svg( 'minus', 'ui', array( 'class' => 'glozin-qty-button decrease' ) );
	}

		/**
	 * Quantity Increase Icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function quantity_icon_increase() {
		echo \Glozin\Icon::get_svg( 'plus', 'ui', array( 'class' => 'glozin-qty-button increase' ) );
	}

	/**
	 * Reviews title
	 *
	 * @return array
	 */
	public function reviews_title() {
		return esc_html__( 'Customer Reviews', 'glozin' );
	}

	/**
	 * Open before cart total tag
	 *
	 * @return void
	 */
	public function before_cart_totals( $columns ) {
		echo '<div class="cart_totals_summary">';
	}

	/**
	 * Close before cart total tag
	 *
	 * @return void
	 */
	public function after_cart_totals( $columns ) {
		echo '</div>';
	}

	public function cart_subtotal( $cart_subtotal, $compound, $cart ) {
		$items               = $cart->get_cart();
		$total_regular_price = 0;
		$total_discount      = 0;

		foreach ( $items as $item ) {
			$product       = $item['data'];
			$quantity      = $item['quantity'];
			$regular_price = wc_format_decimal( $product->get_regular_price(), wc_get_price_decimals());
			$sale_price    = wc_format_decimal( $product->get_sale_price(), wc_get_price_decimals() );

			if ( $sale_price && $regular_price > $sale_price ) {
				$total_regular_price += $regular_price * $quantity;
				$total_discount += ( $regular_price - $sale_price ) * $quantity;
			} else {
				$total_regular_price += $regular_price * $quantity;
			}
		}

		if ( WC()->cart->display_prices_including_tax() && $product ) {
			$total_regular_price = wc_get_price_including_tax( $product, array( 'price' => $total_regular_price ) );
			$total_sale_price = floatval( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );

			if( $total_sale_price > 0 && floatval( $total_regular_price ) > $total_sale_price ) {
				$cart_subtotal = '<span class="price">' . $cart_subtotal . $this->get_price_saved_html( $total_regular_price, $total_sale_price ) . '</span>';
			}
		} else {
			if( floatval(WC()->cart->get_subtotal()) > 0 && floatval( $total_regular_price ) > floatval(WC()->cart->get_subtotal()) ) {
				$cart_subtotal = '<span class="price">' . $cart_subtotal . $this->get_price_saved_html( $total_regular_price, WC()->cart->get_subtotal() ) . '</span>';
			}
		}

		return $cart_subtotal;
	}

	public function get_price_saved_html( $regular_price, $sale_price ) {
		$output = ' <span class="glozin-price-saved">' . esc_html__( 'Save: ', 'glozin' ) . wc_price( $regular_price - $sale_price ) .'</span>';
        return $output;
	}

	/**
	 * Review product name html
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function review_product_name_html( $name, $cart_item, $cart_item_key ) {
		if ( function_exists('is_checkout') && is_checkout() ) {
			if( WC()->cart->display_prices_including_tax() ) {
				$_product_regular_price = floatval( wc_get_price_including_tax( $cart_item['data'], array( 'price' => $cart_item['data']->get_regular_price() ) ) );
				$_product_sale_price = floatval( wc_get_price_including_tax( $cart_item['data'], array( 'price' => $cart_item['data']->get_price() ) ) );
			} else {
				$_product_regular_price = floatval( $cart_item['data']->get_regular_price() );
				$_product_sale_price = floatval( $cart_item['data']->get_price() );
			}

			if( $_product_sale_price > 0 && $_product_regular_price > $_product_sale_price ) {
				$product_price = wc_format_sale_price( $_product_regular_price, $_product_sale_price );
			} else {
				$product_price = WC()->cart->get_product_price( $cart_item['data'] );
			}

			return sprintf( '<span class="checkout-review-product-image">
							%s
							<strong class="product-quantity">%s</strong>
							</span>
							<span class="checkout-review-product-name">%s</span>
							<span class="checkout-review-product-price price">%s</span>',
					$cart_item['data']->get_image('woocommerce_gallery_thumbnail'),
					$cart_item['quantity'],
					$name,
					$product_price
		    	);
		}

		return $name;
	}

	/**
	 * Review product quantity html
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function review_cart_item_quantity_html( $quantity, $cart_item, $cart_item_key ) {
		if ( function_exists('is_checkout') && is_checkout() ) {
			return '';
		}

		return $quantity;
	}

	/**
	 * Get product content AJAX
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function do_ajax_products_content() {
		ob_start();
			\Glozin\WooCommerce\Single_Product\Recently_Viewed::get_recently_viewed_products();
		$output = ob_get_clean();

		wp_send_json_success( $output );
		die();
	}

	public function order_item_name( $name, $item, $is_visible ) {
		if( ! function_exists('is_order_received_page') || ! is_order_received_page()) {
			return $name;
		}

		$product = $item->get_product();
		$product_permalink = $is_visible ? $product->get_permalink( $item ) : '';

		$name = $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name();
		$name = '<span class="product-title">' . $name . '</span>';
		$image = $product->get_image('woocommerce_gallery_thumbnail');
		$image = $image ? '<span class="product-thumbnail">' . $image . '</span>' : '';

		$name = $image . $name;

		return $name;
	}

	/**
	 * Get Stock Availability Text
     *
	 * @since 1.0.0
     *
	 * @param string $availability.
	 * @param object $product.
	 *
	 * @return string
	 */
	public function get_product_availability_text( $availability, $product ) {
		if ( ! $product->managing_stock() && $product->get_stock_status() == 'instock' ) {
			$availability = esc_html__( 'In stock', 'glozin' );
		}

		return $availability;
	}

	/**
	 * Add to cart grouped actions
	 *
	 * @return void
	 */
	public function add_to_cart_grouped_actions() {
		if( empty( $_REQUEST['add-to-cart'] ) ) {
			return;
		}

		$product_id = absint( $_REQUEST['add-to-cart'] );
		$product    = wc_get_product( $product_id );

		if( ! $product || 'grouped' !== $product->get_type() ) {
			return;
		}

		if( empty( $_REQUEST['attributes'] ) || empty( $_REQUEST['variation_ids'] ) ) {
			return;
		}

		$count = 0;
		foreach( $_REQUEST['variation_ids'] as $variation_id ) {
			$_product        = wc_get_product( $variation_id );
			$_parent_product = $_product->get_parent_id();
			$quantity        = $_REQUEST['quantity'][ $variation_id ];
			$variation       = (array) json_decode( stripslashes( $_REQUEST['attributes'][ $variation_id ] ) );

			if( $quantity == 0 || ! $_product->is_in_stock() ) {
				continue;
			}

			if( false == WC()->cart->add_to_cart( $_parent_product, $quantity, $variation_id, $variation ) ) {
				continue;
			} else {
				$notice = WC()->session->get( 'wc_notices', array() );

				if( ! empty( $notice['error'] ) ) {
					$notice['error'][$count] = array();
					WC()->session->set( 'wc_notices', $notice );
				}
				wc_add_to_cart_message( array( $_parent_product => $quantity ), true );
			}

			$count++;
		}
	}


	/**
	 * Change step price filter widget step
     *
	 * @since 1.0.0
     *
	 * @return string
	 */
	public function price_filter_widget_step( $step ) {
		$step = '2';

		return $step;
	}
}