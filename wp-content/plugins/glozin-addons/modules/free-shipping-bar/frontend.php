<?php

namespace Glozin\Addons\Modules\Free_Shipping_Bar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Has variation images
	 *
	 * @var $attributes
	 */
	protected static $attributes = null;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
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
		add_action( 'woocommerce_after_mini_cart', array( $this, 'free_shipping_bar_attributes_value' ), 10 );

		$this->add_actions();
	}

	public function add_actions() {
		if ( get_option( 'glozin_free_shipping_bar_cart_page' ) == 'yes' ) {
			add_action('woocommerce_before_cart_totals', array( $this, 'free_shipping_bar' ), 15);
		}
		if ( get_option( 'glozin_free_shipping_bar_checkout_page' ) == 'yes' ) {
			add_action('woocommerce_checkout_before_order_review', array( $this, 'free_shipping_bar' ));
		}
		if ( get_option( 'glozin_free_shipping_bar_mini_cart' ) == 'yes' ) {
			add_action('glozin_before_mini_cart_content', array( $this, 'free_shipping_bar' ), 10);
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-free-shipping-bar', GLOZIN_ADDONS_URL . 'modules/free-shipping-bar/assets/free-shipping-bar' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_enqueue_script('glozin-free-shipping-bar', GLOZIN_ADDONS_URL . 'modules/free-shipping-bar/assets/free-shipping-bar' . $debug . '.js',  array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
	}

	/**
	 * Get shipping amount
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function free_shipping_bar() {
		$attributes = $this->free_shipping_bar_attributes();

		if ( empty($attributes) ) {
			return;
		}
		
		wc_get_template(
			'cart/free-shipping-bar.php',
			$attributes,
			'',
			GLOZIN_ADDONS_DIR . 'modules/free-shipping-bar/templates/'
		);
	}

	/**
	 * Get shipping amount
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_min_amount() {
		$packages =  ! empty( WC()->cart ) ? WC()->cart->get_shipping_packages() : '';
		$min_amount = 0;
		
		if( ! $packages ) {
			return $min_amount;
		}

		$shipping_zone = wc_get_shipping_zone( $packages[0] );
		$shipping_methods = $shipping_zone->get_shipping_methods();

		if ( ! $shipping_methods ) {
			$shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods($packages[0]) : array();
		}

		if( ! $shipping_methods ) {
			return $min_amount;
		}

		foreach ( $shipping_methods as $id => $shipping_method ) {

			if ( ! isset( $shipping_method->enabled ) || 'yes' !== $shipping_method->enabled ) {
				continue;
			}
			
			if ( ! $shipping_method instanceof \WC_Shipping_Free_Shipping ) {
				continue;
			}

			if ( in_array( $shipping_method->requires, array( 'min_amount', 'either', 'both' ) ) ) {
				$min_amount = $shipping_method->min_amount;
			}

			if ( in_array( $shipping_method->requires, array( 'coupon', 'either', 'both' ), true ) ) {
				if ( WC()->cart->applied_coupons ) {
					$min_amount = 'free';
				}
			}

			if( empty( $shipping_method->requires ) ) {
				$min_amount = 0;
			}

		}

		return $min_amount;
	}

	/**
	 * Free shipping bar attributes
	 *
	 * @since 1.0.0
	 *
	 * @return float
	 */
	public function free_shipping_bar_attributes() {
		if( isset( self::$attributes )  ) {
			return self::$attributes;
		}

		if( $this->get_min_amount() == 'free' ) {
			self::$attributes = array(
				'message'      => sprintf(__('Congratulations! You&rsquo;ve got free shipping!', 'glozin-addons')),
				'percent'      => '100%',
				'classes'      => 'gz-is-success'
			);

			return self::$attributes;
		}

		$min_amount = apply_filters( 'glozin_free_shipping_bar_min_amount', (float) $this->get_min_amount() );

		if( $min_amount <=0 ) {
			return self::$attributes;
		}
		
		$coupons = WC()->cart->get_discount_total();
		$min_amount += $coupons;
		$current_total      = WC()->cart->subtotal;
		$amount_more = $min_amount - $current_total ;
		$message = '';
		$classes = '';
		$percent = 0;
		$percent_number = number_format($current_total/$min_amount * 100, 2, '.', '');

		if( $amount_more > 0 ) {
			$message = sprintf(__('Spend %s more to enjoy <strong>Free Shipping!</strong>', 'glozin-addons'), wc_price($amount_more) );
			$percent = $percent_number . '%';
		} else {
			$message = sprintf(__('Congratulations! You&rsquo;ve got free shipping!', 'glozin-addons'));
			$percent = '100%';
		}

		if( $percent_number >= 100 ) {
			$classes .= ' gz-is-success';
		}

		self::$attributes = array(
			'message'      => $message,
			'percent'      => $percent,
			'classes'      => $classes
		);

		return self::$attributes;
	}

	/**
	 * Free shipping bar attributes
	 *
	 * @since 1.0.0
	 *
	 * @return float
	 */
	public function free_shipping_bar_attributes_value() {
		$attributes = $this->free_shipping_bar_attributes();

		if ( empty($attributes) ) {
			return;
		}

		echo '<div id="glozin-free-shipping-bar-attributes" class="screen-reader-text" data-message="'. esc_attr( $attributes['message'] ) .'" data-percent="'. esc_attr( $attributes['percent'] ) .'" data-classes="'. esc_attr( $attributes['classes'] ) .'">'. esc_html__( 'Free Shipping Bar Attributes', 'glozin-addons' ) .'</div>';
	}

}