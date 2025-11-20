<?php
/**
 * Product Card v2 hooks.
 *
 * @package Glozin
 */

 namespace Glozin\WooCommerce\Product_Card;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card v2
 */
class Product_V2 extends Base {
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
		add_action( 'glozin_product_loop_thumbnail', array( $this, 'product_featured_icons_second_open' ), 35 );
		if(class_exists( '\WCBoost\Wishlist\Frontend' ) && Helper::get_option('product_card_wishlist')) {
			add_action( 'glozin_product_loop_thumbnail', array( \WCBoost\Wishlist\Frontend::instance(), 'loop_add_to_wishlist_button' ), 40 );
		}
		if(class_exists( '\WCBoost\ProductsCompare\Frontend' ) && Helper::get_option('product_card_compare') ) {
			add_action( 'glozin_product_loop_thumbnail', array( \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ), 45 );
		}
		if( Helper::get_option('product_card_quick_view') ) {
			add_action( 'glozin_product_loop_thumbnail', array( \Glozin\WooCommerce\Loop\Quick_View::instance(), 'quick_view_button_icon_light' ), 50 );
		}
		add_action( 'glozin_product_loop_thumbnail', array( $this, 'product_featured_icons_close' ), 55 );

		add_action( 'woocommerce_after_shop_loop_item', array( \Glozin\WooCommerce\Loop\Product_Attribute::instance(), 'loop_primary_attribute' ), 10 );

		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 19 );

		add_filter( 'glozin_product_featured_icons_second_classes', array( $this, 'product_featured_icons_second_classes' ) );
		add_filter( 'glozin_add_to_cart_button_classes', array( $this, 'add_to_cart_button_class' ) );

		add_filter( 'glozin_wishlist_loop_tooltip_position', array( $this, 'tooltip_position') );
		add_filter( 'glozin_compare_loop_tooltip_position', array( $this, 'tooltip_position') );
		add_filter( 'glozin_quickview_tooltip_position', array( $this, 'tooltip_position') );
	}

	public function product_featured_icons_second_classes( $class ) {
		$class = 'd-flex justify-content-end justify-content-xl-center';
		return $class;
	}

	public function add_to_cart_button_class( $class ) {
		$class = 'gz-button-outline gz-button-no-icon w-100 mt-20';
		return $class;
	}

	public function tooltip_position( $position ) {
		return '';
	}
}
