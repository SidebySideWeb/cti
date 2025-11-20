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

class Layout {
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
		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_filter( 'glozin_wp_script_data', array( $this, 'script_data' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		add_action( 'woocommerce_before_main_content', array( $this, 'output_content_wrapper' ), 10 );

		// Site content container
		add_filter( 'glozin_site_content_container_class', array( $this, 'site_content_container_class' ), 10, 1 );

		// Sidebar
		add_filter( 'glozin_site_layout', array( $this, 'layout' ), 55 );
		add_filter( 'glozin_get_sidebar', array( $this, 'sidebar' ), 10 );

		add_filter( 'glozin_primary_sidebar_classes', array( $this, 'sidebar_classes' ), 10 );

	}

	/**
	 * Add 'woocommerce-active' class to the body tag.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes CSS classes applied to the body tag.
	 *
	 * @return array $classes modified to include 'woocommerce-active' class.
	 */
	public function body_class( $classes ) {
		$classes[] = 'glozin-catalog-page';

		return $classes;
	}

	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-catalog', apply_filters( 'glozin_get_style_directory_uri', get_template_directory_uri() ) . '/assets/css/woocommerce/catalog' . $debug . '.css', array(), \Glozin\Helper::get_theme_version() );
	}

	/**
	 * Script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function script_data( $data ) {
		$data['shop_nav_ajax_url_change']    = \Glozin\Helper::get_option( 'product_catalog_pagination_ajax_url_change' );

		return $data;
	}

	/**
	 * Site content container class
	 *
	 * @return string $classes
	 */
	public function site_content_container_class( $classes ) {
		$classes .= ' site-content-container container-xxl d-flex-xl flex-wrap justify-content-between column-gap-60';

		return $classes;
	}


	/**
	 * Output content wrapper
	 *
	 * @return void
	 */
	public function output_content_wrapper() {
		echo '<div id="primary" class="content-area position-sticky-lg top-30"><main id="main" class="site-main" role="main">';
	}

	/**
	 * Layout
	 *
	 * @return string
	 */
	public function layout( $layout ) {
		if( ! is_active_sidebar( 'catalog-filters-sidebar' ) ){
			return;
		}

		$layout = Helper::get_option( 'product_filter_type' );

		return $layout;
	}

	/**
	 * Get Sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function sidebar() {
		if ( ! is_active_sidebar( 'catalog-filters-sidebar' ) ) {
			return false;
		}

		if ( Helper::get_option( 'product_filter_type' ) == 'no-filter' ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function sidebar_classes( $classes ) {
		if ( ! is_active_sidebar( 'catalog-filters-sidebar' ) ) {
			return $classes;
		}

		if ( Helper::get_option( 'product_filter_type' ) == 'no-filter' ) {
			return $classes;
		}

		if ( Helper::get_option( 'product_filter_type' ) == 'popup' ) {
			$classes .= ' offscreen-panel';
		}

		$classes .= ' offscreen-panel--side-right';

		return $classes;
	}


}