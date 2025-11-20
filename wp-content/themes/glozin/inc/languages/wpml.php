<?php
/**
 * WPML compatibility functions
 *
 * @package Glozin
 */

namespace Glozin\Languages;

use \Glozin\Helper;

class WPML {
	const CAMPAIGNS_DOMAIN = 'Campaign Bar';
	const CAMPAIGN_PREFIX = 'campaign_';
	const TOPBAR_SLIDES_DOMAIN = 'Topbar Slides';
	const TOPBAR_SLIDES_PREFIX = 'topbar_slide_';
	const SEARCH_LINKS_DOMAIN = 'Search Links';
	const SEARCH_LINK_PREFIX = 'search_link_';
	const PRODUCT_SHIPPING_PROMOTIONS_DOMAIN = 'Product Shipping Promotions';
	const PRODUCT_SHIPPING_PROMOTIONS_PREFIX = 'product_shipping_promotions_';
	const PRODUCT_HIGHLIGHTS_DOMAIN = 'Product Highlights';
	const PRODUCT_HIGHLIGHTS_PREFIX = 'product_highlight_';
	const CART_SERVICE_HIGHLIGHT_DOMAIN = 'Cart Service Highlight';
	const CART_SERVICE_HIGHLIGHT_PREFIX = 'cart_service_highlight_';

	/**
	 * The single instance of the class
	 *
	 * @var WPML
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return WPML
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_save_after', array( $this, 'register_strings' ) );

		add_filter( 'glozin_campaign_item_args', array( $this, 'translate_campaign_item_args' ), 10, 2 );
		add_filter( 'glozin_topbar_slides', array( $this, 'translate_topbar_slides' ) );
		add_filter( 'glozin_search_trending', array( $this, 'translate_search_quicklinks' ) );
		add_filter( 'glozin_product_shipping_promotions_list', array( $this, 'translate_product_shipping_promotions_list' ) );

	}

	/**
	 * Register special theme strings for translation
	 *
	 * @return void
	 */
	public function register_strings() {
		$this->register_campaign_strings();
		$this->register_topbar_slides_strings();
	}

	/**
	 * Register campaign strings for translation
	 */
	public function register_campaign_strings() {
		$campaigns = array_filter( (array) Helper::get_option( 'campaign_items' ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		foreach ( $campaigns as $id => $campaign ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_text', $campaign['text'] );
		}
	}

	/**
	 * Register header search links for translation
	 */
	public function register_search_link_strings() {
		$links = ( array ) Helper::get_option( 'header_search_links' );

		if ( empty( $links ) ) {
			return;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_text', $link['text'] );
			do_action( 'wpml_register_single_string', self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_url', $link['url'] );
		}
	}

	/**
	 * Register header search links for translation
	 */
	public function register_topbar_slides_strings() {
		$links = ( array ) Helper::get_option( 'topbar_slides' );

		if ( empty( $links ) ) {
			return;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::TOPBAR_SLIDES_DOMAIN, self::TOPBAR_SLIDES_PREFIX . $count . '_text', $link['text'] );
		}
	}

	/**
	 * Apply the WPML translation for campaign items
	 *
	 * @param array $args
	 * @param int $id
	 *
	 * @return array
	 */
	public function translate_campaign_item_args( $args, $id ) {
		$count = $id + 1;

		$args['text']   = apply_filters( 'wpml_translate_single_string', $args['text'], self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_text' );

		return $args;
	}

	/**
	 * Apply the WPML translation for search quick links
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function translate_topbar_slides( $slides ) {
		if ( empty( $slides ) ) {
			return $slides;
		}

		foreach ( $slides as $id => $slide ) {
			$count = $id + 1;

			$slides[ $id ]['text'] = apply_filters( 'wpml_translate_single_string', $slide['text'], self::TOPBAR_SLIDES_DOMAIN, self::TOPBAR_SLIDES_PREFIX . $count . '_text' );
		}

		return $slides;
	}

	/**
	 * Apply the WPML translation for search quick links
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function translate_search_quicklinks( $links ) {
		if ( empty( $links ) ) {
			return $links;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			$links[ $id ]['text'] = apply_filters( 'wpml_translate_single_string', $link['text'], self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_text' );
			$links[ $id ]['url']  = apply_filters( 'wpml_translate_single_string', $link['url'], self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_url' );
		}

		return $links;
	}

	/**	
	 * Apply the WPML translation for product shipping promotions list
	 *
	 * @param array $lists
	 *
	 * @return array
	 */
	public function translate_product_shipping_promotions_list( $lists ) {
		if ( empty( $lists ) ) {
			return $lists;
		}

		foreach ( $lists as $id => $list ) {
			$count = $id + 1;	

			$lists[ $id ]['description'] = apply_filters( 'wpml_translate_single_string', $list['description'], self::PRODUCT_SHIPPING_PROMOTIONS_DOMAIN, self::PRODUCT_SHIPPING_PROMOTIONS_PREFIX . $count . '_description' );
		}

		return $lists;
	}

	/**
	 * Apply the WPML translation for product highlights list
	 *
	 * @param array $lists
	 *
	 * @return array
	 */
	public function translate_product_highlights_list( $lists ) {
		if ( empty( $lists ) ) {
			return $lists;
		}
		
		foreach ( $lists as $id => $list ) {
			$count = $id + 1;

			$lists[ $id ]['text'] = apply_filters( 'wpml_translate_single_string', $list['text'], self::PRODUCT_HIGHLIGHTS_DOMAIN, self::PRODUCT_HIGHLIGHTS_PREFIX . $count . '_text' );
		}

		return $lists;
	}

	/**
	 * Apply the WPML translation for cart service highlight content
	 *
	 * @param array $content
	 *
	 * @return array
	 */
	public function translate_cart_service_highlight_content( $content ) {
		if ( empty( $content ) ) {
			return $content;
		}

		foreach ( $content as $id => $item ) {
			$count = $id + 1;

			$content[ $id ]['title'] = apply_filters( 'wpml_translate_single_string', $item['title'], self::CART_SERVICE_HIGHLIGHT_DOMAIN, self::CART_SERVICE_HIGHLIGHT_PREFIX . $count . '_title' );
			$content[ $id ]['description'] = apply_filters( 'wpml_translate_single_string', $item['description'], self::CART_SERVICE_HIGHLIGHT_DOMAIN, self::CART_SERVICE_HIGHLIGHT_PREFIX . $count . '_description' );
		}

		return $content;
	}
}
