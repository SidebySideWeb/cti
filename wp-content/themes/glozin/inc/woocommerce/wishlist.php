<?php
/**
 * Hooks of Wishlist.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Wishlist template.
 */
class Wishlist {
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
		add_filter( 'wcboost_wishlist_add_to_wishlist_fragments', array( $this, 'update_wishlist_count' ), 10, 1 );

		// Change the button wishlist
		add_filter('wcboost_wishlist_button_template_args', array( $this, 'wishlist_button_template_args' ), 20, 3 );

		add_filter('wcboost_wishlist_svg_icon', array( $this, 'wishlist_svg_icon' ), 20, 3 );

		add_filter( 'wcboost_wishlist_loop_add_to_wishlist_link', array( $this, 'wishlist_button_product_loop' ), 20, 2 );
		add_filter( 'wcboost_wishlist_single_add_to_wishlist_link', array( $this, 'wishlist_button_single_product' ), 20, 2 );

		// Single Product Wishlist
		if( class_exists('\WCBoost\Wishlist\Frontend') ) {
			add_action( 'woocommerce_after_add_to_cart_button', array( \WCBoost\Wishlist\Frontend::instance(), 'single_add_to_wishlist_button' ), 21 );
		}

		// Wishlist Page
		add_filter( 'wcboost_wishlist_empty_message', array( $this, 'wishlist_empty_message' ), 20, 1 );
		add_filter( 'wcboost_wishlist_return_to_shop_link', array( $this, 'wishlist_return_button' ), 20, 1 );

	}

	/**
	 * Ajaxify update count wishlist
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public function update_wishlist_count($data) {
		$wishlist_counter = intval( \WCBoost\Wishlist\Helper::get_wishlist()->count_items() );
		$wishlist_class = $wishlist_counter == 0 ? ' empty-counter' : '';

		$data['span.header-wishlist__counter'] = '<span class="header-counter header-wishlist__counter' . $wishlist_class . '">'. $wishlist_counter . '</span>';

		return $data;
	}

	/**
	 * Change button args: button title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_template_args( $args, $wishlist, $product ) {
		$args['class'][] = 'product-loop-button';

		return $args;
	}

	/**
	 * Wishlist icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_svg_icon($svg, $icon) {
		if( $icon == 'heart' ) {
			$svg = \Glozin\Icon::inline_svg('icon=icon-wishlist');
		} elseif( $icon == 'heart-filled' ) {
			$svg = \Glozin\Icon::inline_svg('icon=icon-wishlist');
		}

		return $svg;
	}

	/**
	 * Change wishlist button product loop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_product_loop( $html, $args ) {
		$product_title = isset( $args['product_title'] ) ? $args['product_title'] : '';
		if( empty( $product_title ) ) {
			$product = isset($args['product_id']) ? wc_get_product( $args['product_id'] ) : '';
			$product_title = $product ? $product->get_title() : '';
		}

		$label_add = \WCBoost\Wishlist\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'remove' );

		if( get_option( 'wcboost_wishlist_exists_item_button_behaviour', 'view_wishlist' ) == 'view_wishlist' ) {
			$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'view' );
		}

		return sprintf(
			'<a href="%s" class="gz-button-icon gz-button-light gz-tooltip %s" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s" data-tooltip_position="%s">' .
				( ! empty( $args['icon'] ) ? '<span class="wcboost-wishlist-button__icon">' . $args['icon'] . '</span>' : '' ) .
				'<span class="wcboost-wishlist-button__text">%s</span>' .
			'</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-button button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( $product_title ),
			esc_attr( $label_add ),
			esc_attr( $label_added ),
			apply_filters( 'glozin_wishlist_loop_tooltip_position', 'left' ),
			isset( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Add to wishlist', 'glozin' )
		);
	}

	/**
	 * Change wishlist button single product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wishlist_button_single_product( $html, $args ) {
		$label_add = \WCBoost\Wishlist\Helper::get_button_text( 'add' );
		$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'remove' );

		if( get_option( 'wcboost_wishlist_exists_item_button_behaviour', 'view_wishlist' ) == 'view_wishlist' ) {
			$label_added = \WCBoost\Wishlist\Helper::get_button_text( 'view' );
		}

		return sprintf(
			'<a href="%s" class="gz-button-icon gz-button-outline gz-tooltip-inside %s" %s data-product_title="%s" data-tooltip="%s" data-tooltip_added="%s">' .
				( ! empty( $args['icon'] ) ? '<span class="wcboost-wishlist-button__icon">' . $args['icon'] . '</span>' : '' ) .
				'<span class="wcboost-wishlist-button__text">%s</span>' .
			'</a>',
			esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $args['product_id'] ] ) ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-single-button wcboost-wishlist-button button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( isset( $args['product_title'] ) ? $args['product_title'] : wc_get_product( $args['product_id'] )->get_title() ),
			wp_kses_post( $label_add ),
			wp_kses_post( $label_added ),
			isset( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Add to wishlist', 'glozin' )
		);
	}

	/**
	 * Wishlist Page Empty
	 *
	 * @return void
	 */
	public function wishlist_empty_message() {
		return '<p class="mb-0 text-center text-base fw-normal">' . esc_html__( 'No products were added to the shop page.', 'glozin' ) . '</p>';
	}

	/**
	 * Wishlist Page Return Button
	 *
	 * @return void
	 */
	public function wishlist_return_button() {
		return '<a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="button wc-backward glozin-button gz-button mt-20 py-17 px-30 gz-button-hover-effect min-w-200">' . esc_html__( 'Back to Shopping', 'glozin' ) . '</a>';
	}
	
}