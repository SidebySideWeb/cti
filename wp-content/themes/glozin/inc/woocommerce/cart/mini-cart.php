<?php
/**
 * Mini Cart hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Cart;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Mini Cart
 */
class Mini_Cart {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Cart item length
	 *
	 * @var $cart_item_length
	 */
	protected static $cart_item_length= null;


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
		add_action( 'wp', array( $this, 'get_cart_item_length' ), 20 );
		// Add html before and after mini cart items
		add_action( 'glozin_before_woocommerce_mini_cart_items', array( $this, 'before_mini_cart_items' ), 20 );
		add_action( 'glozin_after_woocommerce_mini_cart_items', array( $this, 'after_mini_cart_items' ), 50 );

		add_action('glozin_after_woocommerce_mini_cart_items', array( $this, 'mini_cart_recommended_products' ), 30);

		add_action('glozin_after_woocommerce_mini_cart_items', array( $this, 'mini_cart_shipping_calculator' ), 30);

		// Ajax update mini cart.
		add_action( 'wc_ajax_update_cart_item', array( $this, 'update_cart_item' ) );

		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

		add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'button_view_cart'), 10 );
		add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'proceed_to_checkout'), 20 );

		add_action( 'glozin_before_widget_shopping_cart_total', array( $this, 'note_estimate_coupon_mini_cart' ), 20 );
		add_action( 'glozin_after_mini_cart_content', array( $this, 'note_coupon_estimate_popover' ), 99 );

		add_action( 'wc_ajax_glozin_apply_coupon', array( $this, 'ajax_apply_coupon' ) );
		add_action( 'wc_ajax_glozin_remove_coupon', array( $this, 'ajax_remove_coupon' ) );
		add_action( 'wc_ajax_glozin_update_shipping_address', array( $this, 'ajax_update_shipping_address' ) );
		
	}

	public function get_cart_item_length() {
		self::$cart_item_length = WC()->cart && ! WC()->cart->is_empty() ? count( WC()->cart->get_cart() ) : 0;
	}

	/**
	 * Update a cart item.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_cart_item() {
		if ( empty( $_POST['cart_item_key'] ) || ! isset( $_POST['qty'] ) ) {
			wp_send_json_error();
			exit;
		}

		$cart_item_key 		= wc_clean( isset( $_POST['cart_item_key'] ) ? wp_unslash( $_POST['cart_item_key'] ) : '' );
		$cart_item_length 	= isset( $_POST['cart_item_length'] ) ? $_POST['cart_item_length'] : '';
		$qty           		= floatval( $_POST['qty'] );

		check_admin_referer( 'glozin-update-cart-qty--' . $cart_item_key, 'security' );

		do_action( 'glozin_update_cart_item', $cart_item_key, $qty );

		ob_start();
		WC()->cart->set_quantity( $cart_item_key, $qty );

		if ( $cart_item_key && false !== WC()->cart->set_quantity( $cart_item_key, $qty ) ) {
			if ( $cart_item_length == 1 && ! $qty ) {
				WC()->cart->empty_cart();
			}

			\WC_AJAX::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}

	function mini_cart_recommended_products() {
        if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
            return;
        }

        $limit = Helper::get_option( 'mini_cart_products_limit' );
        $type  = Helper::get_option( 'mini_cart_products' );

        if('none' == $type){
            return;
        } elseif('crosssells_products' == $type) {
			$cross_sells = array_filter( array_map( 'wc_get_product', WC()->cart->get_cross_sells() ), 'wc_products_array_filter_visible' );
			$orderby = 'rand';
			$order = 'desc';
			$orderby     = apply_filters( 'woocommerce_cross_sells_orderby', $orderby );
			$order       = apply_filters( 'woocommerce_cross_sells_order', $order );
			$cross_sells = wc_products_array_orderby( $cross_sells, $orderby, $order );
			$limit       = intval( apply_filters( 'woocommerce_cross_sells_total', $limit ) );
			$cross_sells = $limit > 0 ? array_slice( $cross_sells, 0, $limit ) : $cross_sells;
			if( empty( $cross_sells ) ) {
				return;
			}
			$this->products_recommended_content($cross_sells);
		} else {
			$query_posts = \Glozin\WooCommerce\Helper::products_shortcode( $type, $limit );
			$this->products_recommended_content($query_posts);
		}
	}

	/**
    * Get products recommended content
    *
    * @since 1.0.0
    *
    * @param $query_posts
    *
    * @return void
    */
    public function products_recommended_content($query_posts) {
        if ( Helper::get_option( 'mini_cart_products_layout' ) == 'carousel' ) {
			$this->products_recommended_carousel_content($query_posts);
		} else {
			$this->products_recommended_sidebar_content($query_posts);
		}
	}

	public function products_recommended_sidebar_content($query_posts) {
		?>
			<div class="glozin-mini-products-recommended custom-scrollbar">
				<div class="products-recommended-header position-sticky top-0 border-bottom d-flex justify-content-between align-items-center gap-10">
					<h2 class="recommendation-heading heading-letter-spacing my-0 fs-18 fw-semibold"> <?php echo esc_html__( 'You may also like...', 'glozin' ); ?> </h2>
					<?php echo \Glozin\Icon::get_svg( 'close', 'ui', 'class=products-recommended__button-close' ); ?>
				</div>
				<ul class="woocommerce-loop-products products list-unstyled my-0">
					<?php \Glozin\WooCommerce\Helper::products_shortcode_template( $query_posts, [ 'show_add_to_cart_button' => true, 'show_rating' => true ] ); ?>
				</ul>
			</div>
		<?php
	}

	public function products_recommended_carousel_content($query_posts) {
		$args_swiper = array(
			'slidesPerView' => array(
				'desktop' => 1,
				'tablet' => 1,
				'mobile' => 1,
			),
			'spaceBetween' => array(
				'desktop' => 20,
				'tablet' => 20,
				'mobile' => 20,
			),
		);

		?>
			<div class="glozin-mini-products-recommended-carousel py-30 px-30 mt-10 border-top">
			<div class="fs-18 fw-semibold heading-letter-spacing mt-0 mb-15 h4"><?php echo esc_html__( 'You may also like...', 'glozin' ); ?></div>
			<div class="glozin-swiper glozin-product-carousel swiper navigation-class-dots navigation-class--tabletdots navigation-class--mobiledots border rounded-10" data-swiper="<?php echo esc_attr( json_encode( $args_swiper ) ); ?>" data-desktop="1" data-tablet="1" data-mobile="1">
				<?php woocommerce_product_loop_start(); ?>
				<?php \Glozin\WooCommerce\Helper::products_shortcode_template( $query_posts, [ 'show_add_to_cart_button' => true, 'show_rating' => true ] ); ?>
				<?php woocommerce_product_loop_end(); ?>
				<div class="swiper-pagination swiper-pagination-bullets--small"></div>
			</div>
			</div>
		<?php
	}

	public function mini_cart_shipping_calculator() {
		if( ! WC()->cart->needs_shipping() || 'yes' !== get_option( 'woocommerce_enable_shipping_calc' ) || self::$cart_item_length > 0 ) {
			return;
		}

		?>
		<div id="mini-cart-shipping-calculator-items" class="glozin-mini-cart-shipping-calculator hidden">
			<?php woocommerce_shipping_calculator(); ?>
		</div>
		<?php
	}	

	public function before_mini_cart_items() {
		echo '<div class="glozin-mini-cart-items">';
	}

	public function after_mini_cart_items() {
		echo '</div>';
	}

	/**
	 * Output the view cart button.
	 */
	public function button_view_cart() {
		$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
		echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="button gz-button-outline-dark gz-button-hover-effect wc-forward' . esc_attr( $wp_button_class ) . '">' . esc_html__( 'View cart', 'glozin' ) . '</a>';
	}


	/**
	 * Output the proceed to checkout button.
	 */
	public function proceed_to_checkout() {
		$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
		echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="button  gz-button-hover-effect checkout wc-forward' . esc_attr( $wp_button_class ) . '">' . esc_html__( 'Checkout', 'glozin' ) . '</a>';
	}

	public function note_estimate_coupon_mini_cart() {
		?>
			<div class="glozin-note-estimate-coupon d-flex justify-content-center align-items-center bg-light mt-auto border-top border-bottom">
				<div class="glozin-note-estimate-coupon__button glozin-note gz-button gz-button-icon gz-button-light gz-tooltip-inside" data-tooltip="<?php esc_attr_e( 'Add Order Note', 'glozin' ); ?>" data-toggle="popover" data-target="note-popover" data-padding="false">
					<?php echo \Glozin\Icon::get_svg( 'note', 'ui', 'class=icon-fill-none' ); ?>
				</div>
				<?php if( wc_coupons_enabled() ) : ?>
					<div class="glozin-note-estimate-coupon__button glozin-discount gz-button gz-button-icon gz-button-light gz-tooltip-inside text-dark" data-tooltip="<?php esc_attr_e( 'Add Coupon', 'glozin' ); ?>" data-toggle="popover" data-target="discount-popover" data-padding="false">
						<?php echo \Glozin\Icon::get_svg( 'discount', 'ui', 'class=icon-fill-none' ); ?>
					</div>
				<?php endif; ?>
				<?php if ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
					<div class="glozin-note-estimate-coupon__button glozin-estimate gz-button gz-button-icon gz-button-light gz-tooltip-inside" data-tooltip="<?php esc_attr_e( 'Estimate', 'glozin' ); ?>" data-toggle="popover" data-target="estimate-popover" data-padding="false">
						<?php echo \Glozin\Icon::get_svg( 'box', 'ui', 'class=icon-fill-none' ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php
	}

	/**
	 * Output the note, coupon and estimate popover.
	 */
	public function note_coupon_estimate_popover() {
		?>
			<div id="note-popover" class="popover note-popover glozin-note-estimate-coupon__popover" data-padding="false">
				<div class="popover__backdrop"></div>
				<div class="popover__container">
					<div class="popover__content">
						<div class="mb-15 lh-normal d-flex align-items-center gap-10 text-dark fw-semibold">
							<?php echo \Glozin\Icon::get_svg( 'note' ); ?>
							<?php esc_html_e('Add Order Note', 'glozin'); ?>
						</div>
						<div id="order_comments_field" class="woocommerce-form-row">
							<label for="order_comments"><?php esc_html_e( 'How can we help you?', 'glozin' ); ?></label>
							<textarea name="order_comments" class="input-text" id="order_comments" rows="4" cols="5" data-autosave="false"></textarea>
						</div>
						<button class="order-comments-save gz-button gz-button-hover-effect w-100 mb-10" data-popover="close"><?php esc_html_e( 'Save', 'glozin' ); ?></button>
						<button class="gz-button gz-button-outline-dark gz-button-hover-effect w-100" data-popover="close"><?php esc_html_e( 'Close', 'glozin' ); ?></button>
					</div>
				</div>
			</div>

			<?php if( wc_coupons_enabled() ) : ?>
				<div id="discount-popover" class="popover discount-popover glozin-note-estimate-coupon__popover" data-padding="false">
					<div class="popover__backdrop"></div>
					<div class="popover__container">
						<div class="popover__content">
							<div class="fs-30 mb-25 text-center text-dark">
								<?php echo \Glozin\Icon::get_svg( 'discount', 'ui', 'class=icon-fill-none' ); ?>
							</div>
							<div class="woocommerce-notices-wrapper"></div>
							<?php if( ! empty( WC()->cart->get_coupons() ) ) : ?>
								<div class="glozin-mini-cart-coupons mb-15 d-flex flex-column gap-5">
									<?php self::coupon_html(); ?>
								</div>
							<?php endif; ?>
							<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
								<div id="mini_cart_coupon_field" class="woocommerce-form-row mb-10">
									<label for="coupon_code"><?php esc_html_e( 'Coupon code', 'glozin' ); ?></label>
									<input type="text" name="coupon_code" class="input-text w-100" id="coupon_code" value="" />
								</div>
								<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
								<button type="submit" class="button gz-button-hover-effect <?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> w-100 mb-10" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'glozin' ); ?>"><?php esc_html_e( 'Apply coupon', 'glozin' ); ?></button>
								<button class="gz-button gz-button-outline-dark gz-button-hover-effect w-100" data-popover="close"><?php esc_html_e( 'Close', 'glozin' ); ?></button>
							</form>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
				<div id="estimate-popover" class="popover estimate-popover glozin-note-estimate-coupon__popover" data-padding="false">
					<div class="popover__backdrop"></div>
					<div class="popover__container">
						<div class="popover__content">
							<div class="mb-15 lh-normal d-flex align-items-center gap-10 text-dark fw-semibold">
								<?php echo \Glozin\Icon::get_svg( 'box' ); ?>
								<?php esc_html_e('Estimate Shipping', 'glozin'); ?>
							</div>
							<div class="woocommerce-notices-wrapper"></div>
							<div id="mini-cart-shipping-calculator-popover" class="glozin-mini-cart-shipping-calculator">
								<?php woocommerce_shipping_calculator(); ?>
							</div>
							<button class="gz-button gz-button-outline-dark gz-button-hover-effect w-100" data-popover="close"><?php esc_html_e( 'Close', 'glozin' ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php
	}

	/**
	 * Ajax apply coupon.
	 */
	public function ajax_apply_coupon() {
		if( $_POST['action'] !== 'glozin_apply_coupon' ) {
			return;
		}

		if ( ! isset( $_POST['coupon_code'] ) ) {
			return;
		}


		WC()->cart->add_discount( wc_format_coupon_code( $_POST['coupon_code'] ) );

		ob_start();
		self::coupon_html();
		$coupon_html = ob_get_clean();

		wp_send_json( array(
			'coupon_html' => $coupon_html,
			'notices' => wc_print_notices( true )
		) );
	}

	/**
	 * Ajax remove coupon.
	 */
	public function ajax_remove_coupon() {
		if( $_POST['action'] !== 'glozin_remove_coupon' ) {
			return;
		}

		if ( ! isset( $_POST['coupon_code'] ) ) {
			return;
		}

		WC()->cart->remove_coupon( wc_format_coupon_code( $_POST['coupon_code'] ) );

		wp_send_json_success();
	}

	/**
	 * Coupon HTML.
	 */
	public function coupon_html() {
		foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?> d-flex align-items-center justify-content-between text-dark">
				<div class="fw-semibold"><?php wc_cart_totals_coupon_label( $coupon ); ?></div>
				<div><?php wc_cart_totals_coupon_html( $coupon ); ?></div>
			</div>
		<?php endforeach;
	}

	/**
	 * Ajax update shipping address.
	 */
	public function ajax_update_shipping_address() {
		if( $_POST['action'] !== 'glozin_update_shipping_address' ) {
			return;
		}

		$postcode = isset($_POST['calc_shipping_postcode']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_postcode'])) : '';
		$country  = isset($_POST['calc_shipping_country']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_country'])) : '';
		$city     = isset($_POST['calc_shipping_city']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_city'])) : '';
		$state    = isset($_POST['calc_shipping_state']) ? sanitize_text_field(wp_unslash($_POST['calc_shipping_state'])) : '';

		$customer = WC()->customer;
		if ($country) {
			$customer->set_shipping_country($country);
		}
		if ($postcode) {
			$customer->set_shipping_postcode($postcode);
		}
		if ($city) {
			$customer->set_shipping_city($city);
		}
		if ($state && $country) {
			$customer->set_shipping_state($state);
		}

		$customer->save();

		WC()->cart->calculate_shipping();
    	WC()->cart->calculate_totals();

		wp_send_json(array( 'notices' => wc_print_notices( true ) ));
	}
}