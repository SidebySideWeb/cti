<?php

namespace Glozin\WooCommerce\Single_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class UpSells {

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
		if ( ! intval( \Glozin\Helper::get_option( 'upsells_products' ) ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		}
		add_filter( 'woocommerce_product_upsells_products_heading', array( $this, 'upsells_title' ) );
		add_filter( 'woocommerce_upsells_total', array( $this, 'upsells_total' ) );
		add_filter( 'woocommerce_upsell_display_args', array( $this, 'get_upsell_args' ) );

	}

	/**
	 * Upsells Product Title
	 *
	 * @return void
	 */
	public function upsells_title() {
		return esc_html__( 'People Also Bought', 'glozin' );
	}

	/**
	 * Change limit upsell
	 *
	 * @return void
	 */
	public function upsells_total( $limit ) {
		$limit = \Glozin\Helper::get_option('upsells_products_numbers');

		return $limit;
	}

	/**
	 * Change Related products args
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_upsell_args( $args ) {
		$columns = \Glozin\Helper::get_option( 'upsells_products_columns', [] );
		$columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';

		$args = array(
			'columns'        => $columns,
		);

		return $args;
	}
}