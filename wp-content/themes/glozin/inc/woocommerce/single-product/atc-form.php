<?php
/**
 * Woocommerce Setup functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Single_Product;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class ATC_Form {
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
		// Product Add To Cart button
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'open_atc_group_wrapper' ), 11 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'close_atc_group_wrapper' ), 25 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'open_product_featured_buttons' ), 20 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'close_product_featured_buttons' ), 22 );
	}

	/**
	 * Open buy now wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_atc_group_wrapper() {
		echo '<div class="glozin-product-atc-group d-flex flex-wrap align-items-end">';
	}

	/**
	 * Close buy now wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_atc_group_wrapper() {
		echo '</div>';
	}

	/**
	 * Featured button open
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function open_product_featured_buttons() {
		if( class_exists( '\WCBoost\Wishlist\Frontend') || class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			echo '<div class="product-featured-icons product-featured-icons--single-product">';
		}
	}

	/**
	 * Featured button close
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function close_product_featured_buttons() {
		if( class_exists( '\WCBoost\Wishlist\Frontend') || class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			echo '</div>';
		}
	}
}