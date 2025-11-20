<?php

namespace Glozin\WooCommerce\Single_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Related {

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
		if( ! intval( \Glozin\Helper::get_option( 'related_products') ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
		// Related options
		add_filter( 'woocommerce_product_related_posts_relate_by_category', array(
			$this,
			'related_products_by_category'
		) );

		add_filter( 'woocommerce_product_related_posts_relate_by_tag', array(
			$this,
			'related_products_by_tag'
		) );

		add_filter( 'woocommerce_output_related_products_args', array(
			$this,
			'get_related_products_args'
		) );

	}

	/**
	 * Related products by category
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function related_products_by_category() {
		$by_categories = !empty(\Glozin\Helper::get_option( 'related_products_by_cats') ) ? true : false;
		return $by_categories;
	}

	/**
	 * Related products by tag
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function related_products_by_tag() {
		$by_tags = !empty(\Glozin\Helper::get_option( 'related_products_by_tags') ) ? true : false;
		return $by_tags;
	}

	/**
	 * Change Related products args
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_related_products_args( $args ) {
		$columns = \Glozin\Helper::get_option( 'related_products_columns', [] );
		$columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';

		$args = array(
			'posts_per_page' => \Glozin\Helper::get_option('related_products_numbers'),
			'columns'        => $columns,
			'orderby'        => 'rand',
		);

		return $args;
	}

}