<?php
/**
 * Hooks of checkout.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of checkout template.
 */
class Checkout {
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

		add_filter('glozin_site_content_container_class', array( $this, 'site_content_container_class' ));

		// Wrap checkout login and coupon notices.
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'before_login_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'login_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'coupon_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'after_login_form' ), 10 );

		add_filter( 'woocommerce_checkout_coupon_message', array( $this, 'coupon_form_name' ), 10);

		add_action( 'woocommerce_checkout_order_review', array( $this, 'information_box' ), 30 );
	}

	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-checkout', apply_filters( 'glozin_get_style_directory_uri', get_template_directory_uri() ) . '/assets/css/woocommerce/checkout' . $debug . '.css', array(), \Glozin\Helper::get_theme_version() );
	}

	public function site_content_container_class( $classes ) {
		if ( ! empty( is_wc_endpoint_url('order-received') ) ) {
			$classes = 'container';
		}

		return $classes;
	}

	/**
	 * Checkout Before login form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function before_login_form() {
		echo '<div class="checkout-form-cols">';
	}

	/**
	 * Checkout After login form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function after_login_form() {
		echo '</div>';
	}

	/**
	 * Checkout login form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function login_form() {
		if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
			return;
		}

		echo '<div class="checkout-login checkout-form-col col-flex gz-col-lg-6 gz-col-md-6 gz-col-12">';
		woocommerce_checkout_login_form();
		echo '</div>';
	}

	/**
	 * Checkout coupon form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function coupon_form() {
		if ( ! wc_coupons_enabled() ) {
			return;
		}

		echo '<div class="checkout-coupon checkout-form-col col-flex gz-col-lg-6 gz-col-md-6 gz-col-12">';
		woocommerce_checkout_coupon_form();
		echo '</div>';
	}

	/**
	 * Checkout coupon form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function coupon_form_name( $html) {
		if ( ! wc_coupons_enabled() ) {
			return;
		}

		return esc_html__( 'Have a coupon?', 'glozin' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Enter your code', 'glozin' ) . '</a>' ;
	}

	public function information_box() {
		if( ! intval( \Glozin\Helper::get_option( 'checkout_information_box' ) ) ) {
			return;
		}

		echo '<div class="gz-woocommerce-information-box gz-checkout-information-box mt-30 pt-27 border-top">';
		echo '<div class="gz-checkout-information-box__content">';
		echo do_shortcode( wp_kses_post( \Glozin\Helper::get_option( 'checkout_information_box_content' ) ) );
		echo '</div>';
		echo '</div>';
	}

}
