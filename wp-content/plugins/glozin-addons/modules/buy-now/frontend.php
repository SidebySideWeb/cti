<?php

namespace Glozin\Addons\Modules\Buy_Now;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


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
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'buy_now_button' ), 80 );
		add_action( 'woocommerce_before_single_product', array( $this, 'add_post_class' ) );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'remove_post_class' ) );

		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'buy_now_redirect' ), 99 );

		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_before') );
	}

	public function scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'glozin-buy-now-script', GLOZIN_ADDONS_URL . 'modules/buy-now/assets/buy-now' . $debug . '.js', array( 'jquery' ), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
	}

	public function add_to_cart_before() {
		if ( ! isset( $_REQUEST['glozin_buy_now'] ) || $_REQUEST['glozin_buy_now'] == false ) {
			return true;
		}

		if( get_option('glozin_buy_now_reset_cart') == 'yes' ) {
			global $woocommerce;
			$woocommerce->cart->empty_cart();
		}

		// Do nothing with the data and return
		return true;
	}

	/**
	 * Display buy now button
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	function buy_now_button() {
		if( ! apply_filters( 'glozin_buy_now_button', true ) ) {
			return;
		}

		global $product;
		if ( $product->get_type() == 'external' ) {
			return;
		}

		$classes = $product->is_in_stock() ? '' : 'disabled';

		echo '<button type="submit" class="gz-buy-now-button gz-button '. esc_attr( $classes ) .'">';
		echo '<span class="glozin-button-text">' . esc_html__( 'Buy it now', 'glozin-addons' ) . '</span>';
		echo '</button>';
	}

	public function add_post_class() {
		add_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	public function remove_post_class() {
		remove_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	/**
	 * Adds classes to products
	 *
	 * @since 1.0
	 *
	 * @param array $classes Post classes.
	 *
	 * @return array
	 */

	public function product_class( $classes ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		if( ! is_singular('product') ) {
			return $classes;
		}

		$classes[] = 'has-buy-now';

		return $classes;
	}

	/**
	 * Buy now redirect
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	function buy_now_redirect( $url ) {
		if ( ! isset( $_REQUEST['glozin_buy_now'] ) || $_REQUEST['glozin_buy_now'] == false ) {
			return $url;
		}

		if ( empty( $_REQUEST['quantity'] ) ) {
			return $url;
		}

		if ( is_array( $_REQUEST['quantity'] ) ) {
			$quantity_set = false;
			foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;
			}

			if ( ! $quantity_set ) {
				return $url;
			}
		}

		$redirect = get_option( 'glozin_buy_now_redirect_location' );
		if ( empty( $redirect ) ) {
			return wc_get_checkout_url();
		} else {
			return get_permalink( $redirect );
		}
	}
}