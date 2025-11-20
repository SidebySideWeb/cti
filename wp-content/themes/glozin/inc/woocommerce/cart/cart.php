<?php
/**
 * Hooks of cart.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Cart;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of checkout template.
 */
class Cart {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'glozin_wp_script_data', array( $this, 'cart_script_data' ) );
		add_action( 'template_redirect', array( $this, 'add_actions' ), 10 );

		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );
		add_action ('woocommerce_before_cart_totals', array( $this,'order_comments') , 20);
	}

	public function add_actions() {
		add_action( 'woocommerce_cart_is_empty', array( $this, 'cart_empty_content' ), 20 );
		add_filter( 'glozin_get_page_header_elements', array( $this, 'remove_shop_header_cart_empty' ), 10, 1 );
		add_filter( 'woocommerce_return_to_shop_text', array( $this, 'btn_return_to_shop_text' ) );

		// Cross sell product
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		if( intval( \Glozin\Helper::get_option( 'cross_sells_products') ) ) {
			add_action( 'woocommerce_after_cart_table', array( $this, 'cross_sells_display' ), 20 );
		}

		add_filter( 'woocommerce_cross_sells_total', array( $this, 'cross_sells_total' ) );
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sells_columns' ) );

		add_action( 'woocommerce_cart_collaterals', array( $this, 'information_box' ), 20 );
		add_action( 'woocommerce_after_cart_table', array( $this, 'service_highlight' ), 10 );

	}

	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-cart', apply_filters( 'glozin_get_style_directory_uri', get_template_directory_uri() ) . '/assets/css/woocommerce/cart' . $debug . '.css', array(), \Glozin\Helper::get_theme_version() );
	}

	/**
	 * Add cart script data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function cart_script_data( $data ) {
		$data['product_card_hover'] 	= \Glozin\Helper::get_option( 'product_card_hover' );

		if ( intval( \Glozin\Helper::get_option( 'update_cart_page_auto' ) ) ) {
			$data['update_cart_page_auto'] = 1;
		}

		return $data;
	}


	/**
	 * Change total cross cells
	 *
	 * @return void
	 */
	public function cross_sells_total( $total ) {
		$total = \Glozin\Helper::get_option( 'cross_sells_products_numbers' );

		return $total;
	}

	/**
	 * Change columns upsell
	 *
	 * @return void
	 */
	public function cross_sells_columns( $columns ) {
		$columns = \Glozin\Helper::get_option( 'cross_sells_products_columns', [] );
		$columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '2';

		return $columns;
	}

	public function cross_sells_display() {
		ob_start();
		woocommerce_cross_sell_display();
		$content = ob_get_clean();

		if ( empty( $content ) ) {
			$type = \Glozin\Helper::get_option( 'cross_sells_empty_type' );
			$limit = \Glozin\Helper::get_option( 'cross_sells_products_numbers' );
			$cross_sells = \Glozin\WooCommerce\Helper::products_shortcode( $type, $limit );

			$columns = \Glozin\Helper::get_option( 'cross_sells_products_columns', [] );
			$desktop_columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '2';
			$tablet_columns  = isset( $columns['tablet'] ) ? $columns['tablet'] : '2';
			$mobile_columns  = isset( $columns['mobile'] ) ? $columns['mobile'] : '1';

			$args_swiper = array(
				'slidesPerView' => array(
					'desktop' => $desktop_columns,
					'tablet' => $tablet_columns,
					'mobile' => $mobile_columns,
				),
				'spaceBetween' => array(
					'desktop' => 20,
					'tablet' => 20,
					'mobile' => 20,
				),
			);

			echo '<div class="cross-sells">';
			$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may also like&hellip;', 'glozin' ) );
			if ( $heading ) {
				echo '<h2 class="fs-18 mt-0 mb-15">' . esc_html( $heading ) . '</h2>';
			}
			echo '<div class="gz-cross-sells-content glozin-product-carousel glozin-swiper swiper navigation-class-dots navigation-class--tabletdots navigation-class--mobiledots" data-swiper="'. esc_attr( json_encode( $args_swiper ) ) .'" data-desktop="'. esc_attr( $desktop_columns ) .'" data-tablet="'. esc_attr( $tablet_columns ) .'" data-mobile="'. esc_attr( $mobile_columns ) .'">';
			woocommerce_product_loop_start();
			\Glozin\WooCommerce\Helper::products_shortcode_template( $cross_sells, [ 'show_add_to_cart_button' => true ] );
			woocommerce_product_loop_end();
			echo '<div class="swiper-pagination swiper-pagination-bullets--small"></div>';
			echo '</div>';
			echo '</div>';
		} else {
			echo ! empty( $content ) ? $content : '';
		}

	}

	public function remove_shop_header_cart_empty( $elements ) {
		if ( WC()->cart->is_empty() ) {
			return [];
		}

		return $elements;
	}

	public function btn_return_to_shop_text( $text ) {
		return esc_html__( 'Continue Shopping', 'glozin' );
	}

	/**
	 * Add cart empty content
	 *
	 * @return void
	 */
	public function cart_empty_content() {
		echo '<div class="gz-cart-empty-content text-center">';
		echo '<h1 class="mb-20">' . esc_html__( 'Your cart is empty', 'glozin' ) . '</h1>';

		if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
			<p class="return-to-shop">
				<a class="px-30 min-w-200 button gz-button-hover-dark wc-backward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php
						/**
						 * Filter "Return To Shop" text.
						 *
						 * @since 4.6.0
						 * @param string $default_text Default text.
						 */
						echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to shop', 'glozin' ) ) );
					?>
				</a>
			</p>
		<?php endif;

		if ( ! is_user_logged_in() ) :
			echo '<h2>' . esc_html__( 'Have an account?', 'glozin' ) . '</h2>';
			echo '<div><a class="underline" href="'. esc_url( wc_get_page_permalink( 'myaccount' ) ) .'">'. esc_html__( 'Log in', 'glozin' ) .'</a> '. esc_html__( 'to check out faster.', 'glozin' ) .'</div>';
			echo '</div>';
		endif;
	}

	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
		$_product   = $cart_item['data'];
		if( WC()->cart->display_prices_including_tax() ) {
			$_product_regular_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_regular_price() ) ) );
			$_product_sale_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_price() ) ) );
		} else {
			$_product_regular_price = floatval( $_product->get_regular_price() );
			$_product_sale_price = floatval( $_product->get_price() );
		}

		if( $_product_sale_price > 0 && $_product_regular_price > $_product_sale_price ) {
			$subtotal .= '<br/><span class="glozin-price-saved">' . esc_html__( 'Save: ', 'glozin' ) . wc_price( ( $_product_regular_price * $cart_item['quantity'] ) - ( $_product_sale_price * $cart_item['quantity'] ) ) .'</span>';
		}

		return $subtotal;
	}

	public function order_comments() {
		echo '<div class="form-row notes woocommerce-account pb-20 mb-10" id="order_comments_field">';
		echo '<h6 class="fs-16 fw-semibold mt-0 mb-15">' . esc_html__('Add Order Note', 'glozin') . '</h6>';
		echo '<div class="woocommerce-form-row">';
		echo '<label for="order_comments">' . esc_html__('Add Order Note', 'glozin') . '</label>';
		echo '<textarea name="order_comments" class="input-text" id="order_comments" rows="4" cols="5"></textarea>';
		echo '</div>';
		echo '</div>';
	}

	public function information_box() {
		if( ! intval( \Glozin\Helper::get_option( 'cart_information_box' ) ) ) {
			return;
		}

		echo '<div class="gz-cart-information-box px-30 py-30 mt-30 rounded-10 bg-light-grey">';
		echo '<div class="gz-cart-information-box__content">';
		echo do_shortcode( wp_kses_post( \Glozin\Helper::get_option( 'cart_information_box_content' ) ) );
		echo '</div>';
		echo '</div>';
	}

	public function service_highlight() {
		if( ! intval( \Glozin\Helper::get_option( 'cart_service_highlight' ) ) ) {
			return;
		}

		$content = apply_filters( 'glozin_cart_service_highlight_content', (array) \Glozin\Helper::get_option( 'cart_service_highlight_content' ) );

		if( empty( $content ) ) {
			return;
		}

		$args_swiper = array(
			'slidesPerView' => array(
				'desktop' => 3,
				'tablet' => 3,
				'mobile' => 1,
			),
			'spaceBetween' => array(
				'desktop' => 20,
				'tablet' => 20,
				'mobile' => 20,
			),
			'breakpoints' => array(
				'desktop' => 1200,
			),
		);

		echo '<div class="gz-cart-service-highlight pt-60 mt-30">';
		echo '<div class="gz-cart-service-highlight__content glozin-swiper swiper navigation-class-dots navigation-class--tabletdots navigation-class--mobiledots" data-swiper="'. esc_attr( json_encode( $args_swiper ) ) .'" data-desktop="3" data-tablet="3" data-mobile="1">';
		echo '<div class="gz-cart-service-highlight__inner swiper-wrapper">';

		foreach( $content as $item ) {
			echo '<div class="gz-cart-service-highlight__item px-30 py-30 rounded-10 bg-light-grey text-center swiper-slide">';
			echo '<div class="gz-cart-service-highlight__item-icon gz-svg-icon">';
			echo \Glozin\Icon::sanitize_svg( $item['icon'] );
			echo '</div>';
			echo '<div class="gz-cart-service-highlight__item-content">';
			echo '<h6 class="gz-cart-service-highlight__item-title my-5">' . esc_html( $item['title'] ) . '</h6>';
			echo '<div class="gz-cart-service-highlight__item-description">' . wp_kses_post( $item['description'] ) . '</div>';
			echo '</div>';
			echo '</div>';
		}

		echo '</div>';
		echo '<div class="swiper-pagination swiper-pagination-bullets--small"></div>';
		echo '</div>';
		echo '</div>';
	}


}
