<?php
/**
 * Catalog hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Catalog;

use \Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Catalog
 */

class Products_Grid {
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
		// Filters Actived
		add_action( 'woocommerce_before_shop_loop', array( $this, 'filters_actived' ), 50 );

		// Add div shop loop
		add_action( 'woocommerce_before_shop_loop', array( $this, 'shop_content_open_wrapper' ), 60 );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'shop_content_close_wrapper' ), 20 );

		if( ! is_customize_preview() ) {
			add_filter( 'loop_shop_columns', array( $this, 'catalog_column' ) );
			add_filter( 'loop_shop_per_page', array( $this, 'shop_per_page' ) );
		}

		// Add button return shop to no product
		remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
		add_action( 'woocommerce_no_products_found', array( $this, 'toolbar_no_products_found' ), 20 );
		add_action( 'woocommerce_no_products_found', array( $this, 'filters_actived' ), 30 );
		add_action( 'woocommerce_no_products_found', array( $this, 'shop_content_no_products_found' ), 40 );
	}

	/**
	 * Filters actived
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function filters_actived() {
		$filter_class = ! isset( $_GET['filter'] ) ? ' hidden' : '';

		echo '<div class="catalog-toolbar__active-filters mb-30'. esc_attr( $filter_class ) .'">';
		echo '<div class="catalog-toolbar__filters-actived d-flex flex-wrap align-items-center gap-10" data-clear-text="'. esc_html__( 'Clear all', 'glozin' ).'"></div>';
		echo '</div>';
	}

	/**
	 * Open Shop Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_content_open_wrapper() {
		echo '<div id="glozin-shop-content" class="glozin-shop-content">';
	}

	/**
	 * Close Shop Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_content_close_wrapper() {
		echo '</div>';
	}

	public function shop_content_no_products_found() {
		$this->shop_content_open_wrapper();
		echo '<ul class="products"><li class="product">';
		if( function_exists( 'wc_no_products_found' ) ) {
			wc_no_products_found();
		}
		echo '</li></ul>';
		$this->shop_content_close_wrapper();
	}

	public function toolbar_no_products_found() {
		echo '<div class="catalog-toolbar">';
		echo '</div>';
	}

	/**
	 * Change catalog column
	 *
	 * @return void
	 */
	public function catalog_column( $column ) {
		$view = \Glozin\WooCommerce\Catalog\View::get_default_view();

		if( ! empty( $view ) && $view == 'list' ) {
			$column = 1;
		}

		return $column;
	}

	public function shop_per_page( $per_page ) {
		$view = \Glozin\WooCommerce\Catalog\View::get_default_view();

		if( ! empty( $view ) && $view == 'list' ) {
			$columns      = get_option( 'woocommerce_catalog_columns', 4 );
			$rows      = get_option( 'woocommerce_catalog_rows', 4 );
			$per_page = $columns * $rows;
		}

		return $per_page;

	}
}