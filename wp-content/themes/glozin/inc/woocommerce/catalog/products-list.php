<?php
/**
 * Product Card list hooks.
 *
 * @package Glozin
 */

 namespace Glozin\WooCommerce\Catalog;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card list
 */
class Products_List {
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
		add_filter( 'glozin_product_card_loop_style', array( $this, 'loop_start' ), 20 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'add_product_card_list' ) );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'remove_product_card_list' ) );

		add_action( 'glozin_woocommerce_product_archive_before_content', array( $this, 'add_product_card_list' ) );
		add_action( 'glozin_woocommerce_product_archive_after_content', array( $this, 'remove_product_card_list' ) );
	}

	public function loop_start($style){
		global $product;
		$content = $product ? $product->get_short_description() : '';
		if( empty( $content ) ) {
			return $style;
		}
		$line_limit = Helper::get_option( 'product_list_short_description_line_limit' );
		$desktop_line_limit = !empty( $line_limit['desktop'] ) ? $line_limit['desktop'] : 3;
		$tablet_line_limit = !empty( $line_limit['tablet'] ) ? $line_limit['tablet'] : '';
		$mobile_line_limit = !empty( $line_limit['mobile'] ) ? $line_limit['mobile'] : '';

		$style = '';
		if( !empty( $desktop_line_limit ) && $desktop_line_limit !== '3' ) {
			$style .= '--gz-product-short-description: ' . $desktop_line_limit . ';';
		}
		if( !empty( $tablet_line_limit ) && $tablet_line_limit !== '2' ) {
			$style .= '--gz-product-short-description-tablet: ' . $tablet_line_limit . ';';
		}
		if( !empty( $mobile_line_limit ) && $mobile_line_limit !== '3' ) {
			$style .= '--gz-product-short-description-mobile: ' . $mobile_line_limit . ';';
		}
		return $style;
	}

	public function add_product_card_list() {
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'short_description' ), 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_featured_icons_open' ), 20 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_featured_icons_close' ), 90 );
		if( ! Helper::get_option( 'mobile_product_card_atc' )) {		
			add_action( 'woocommerce_after_shop_loop_item', array( \Glozin\WooCommerce\Product_Card\Base::instance(), 'add_to_cart_button_base' ), 30 );
		}
		if(class_exists( '\WCBoost\Wishlist\Frontend' ) && Helper::get_option('product_card_wishlist')) {
			add_action( 'woocommerce_after_shop_loop_item', array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 35 );
		}
		if(class_exists( '\WCBoost\ProductsCompare\Frontend' ) && Helper::get_option('product_card_compare')) {
			add_action( 'woocommerce_after_shop_loop_item', array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 40 );
		}
	}

	public function remove_product_card_list() {
		remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'short_description' ), 10 );
		remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_featured_icons_open' ), 20 );
		remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_featured_icons_close' ), 90 );
		if( ! Helper::get_option( 'mobile_product_card_atc' )) {		
			remove_action( 'woocommerce_after_shop_loop_item', array( \Glozin\WooCommerce\Product_Card\Base::instance(), 'add_to_cart_button_base' ), 30 );
		}
		if(class_exists( '\WCBoost\Wishlist\Frontend' ) && Helper::get_option('product_card_wishlist')) {
			remove_action( 'woocommerce_after_shop_loop_item', array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 35 );
		}
		if(class_exists( '\WCBoost\ProductsCompare\Frontend' ) && Helper::get_option('product_card_compare')) {
			remove_action( 'woocommerce_after_shop_loop_item', array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 40 );
		}
	}

	/**
	 * Product Short Description
	 *
	 * @return  void
	 */
	public function short_description() {
		global $product;
		$content = $product ? $product->get_short_description() : '';
		if( empty( $content ) ) {
			return;
		}

		echo '<div class="short-description d-none">';
			echo wp_kses_post( $content );
		echo '</div>';
	}

	/**
	 * Featured icons open
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_open() {
		echo '<div class="product-list-featured-icons tooltip-top d-none align-items-center gap-10">';
	}

	/**
	 * Featured icons close
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_featured_icons_close() {
		echo '</div>';
	}

	public function wishlist_button_lopp_class( $classes ) {
		$classes = 'gz-button-outline gz-button-hover-dark';
		return $classes;
	}

	public function wishlist_button_lopp_tooltip_position( $tooltip_position ) {
		$tooltip_position = 'top';
		return $tooltip_position;
	}
}
