<?php
/**
 * Shortcodes
 *
 * @version 1.0.0
 *
 * @package WCBoost\Wishlist
 */

namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Class \WCBoost\Wishlist\Shortcodes
 */
class Shortcodes {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		add_shortcode( 'wcboost_wishlist', [ __CLASS__, 'wishlist' ] );
		add_shortcode( 'wcboost_wishlist_button', [ __CLASS__, 'button' ] );
	}

	/**
	 * Wishlist shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function wishlist( $atts ) {
		$atts = shortcode_atts(
			[
				'token' => get_query_var( 'wishlist_token' ),
			],
			$atts,
			'wcboost_wishlist'
		);

		$wishlist = Helper::get_wishlist( $atts['token'] );
		$template = Templates::get_wishlist_template( $wishlist );
		$args     = Templates::get_wishlist_template_args( $wishlist );
		$html     = Templates::get_template_html( $template, $args );

		return '<div class="woocommerce wocommerce-wishlist wcboost-wishlist">' . $html . '</div>';
	}

	/**
	 * Add to wishlist button shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function button( $atts ) {
		$atts = shortcode_atts(
			[
				'product_id' => '',
				'quantity'   => 1,
				'wishlist'   => '',
				'class'      => '',
			],
			$atts,
			'wcboost_wishlist_button'
		);

		$product_id = $atts['product_id'] ? $atts['product_id'] : ( ! empty( $GLOBALS['product'] ) ? $GLOBALS['product']->get_id() : 0 );

		if ( ! $product_id ) {
			return '';
		}

		/**
		 * Product object.
		 *
		 * @var \WC_Product|\WC_Product_Variable $_product
		 */
		$_product = wc_get_product( $product_id );

		if ( ! $_product ) {
			return '';
		}

		$wishlist = Helper::get_wishlist( $atts['wishlist'] );
		$item     = new Wishlist_Item( $_product );

		if ( $wishlist->has_item( $item ) && 'hide' == get_option( 'wcboost_wishlist_exists_item_button_behaviour' ) ) {
			return '';
		}

		$args = Templates::get_button_template_args( $wishlist, $item );

		$args['quantity'] = $atts['quantity'];

		if ( ! empty( $atts['class'] ) ) {
			$args['class'] .= ' ' . $atts['class'];
		}

		$html = Templates::get_template_html( 'loop/add-to-wishlist.php', $args );

		return apply_filters( 'wcboost_wishlist_shortcode_button_html', $html, $wishlist, $item, $atts );
	}

	/**
	 * Get the wishlist template.
	 *
	 * @deprecated 1.2.2
	 *
	 * @param \WCBoost\Wishlist\Wishlist $wishlist Wishlist object.
	 * @return string
	 */
	public static function get_wishlist_template( $wishlist ) {
		_deprecated_function( __FUNCTION__, '1.2.2', 'WCBoost\Wishlist\Templates::get_wishlist_template()' );

		return Templates::get_wishlist_template( $wishlist );
	}
}
