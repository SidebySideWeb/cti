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

class Pagination {
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
		// Pagination
		add_filter( 'next_posts_link_attributes', array( $this, 'glozin_next_posts_link_attributes' ), 10, 1 );

		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'pagination' ) );

	}


	/**
	 * Next posts link attributes
	 *
	 * @return string $attr
	 */
	public function glozin_next_posts_link_attributes( $attr ) {
		if( Helper::get_option( 'product_catalog_pagination' ) !== 'numeric' ) {
			$attr = 'class="woocommerce-pagination-button gz-button py-17 px-30 mt-30 gz-button-hover-effect min-w-180"';
		}

		return $attr;
	}

	/**
	 * Pagination.
	 */
	public static function pagination() {
		if( ! apply_filters( 'glozin_pagination_elementor', true ) ) {
			return;
		}
		// Display the default pagination for [products] shortcode.
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			woocommerce_pagination();
			return;
		}

		$pagination_type = Helper::get_option( 'product_catalog_pagination' );

		if ( 'numeric' == $pagination_type ) {
			woocommerce_pagination();
		} else {

			$classes = array(
				'woocommerce-pagination',
				'woocommerce-pagination--catalog',
				'next-posts-pagination',
				'woocommerce-pagination--ajax',
				'woocommerce-pagination--' . $pagination_type,
				'text-center'
			);

			echo '<nav class="' . esc_attr( implode( ' ', $classes ) ) . '">';
				self::posts_found();
				if ( get_next_posts_link() ) {
					next_posts_link( '<span>' . esc_html__( 'Load more', 'glozin' ) . '</span>' );
				}
			echo '</nav>';
		}
	}

	/**
	 * Get post found
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function posts_found() {
		global $wp_query;

		Helper::get_posts_found( $wp_query->post_count, wc_get_loop_prop( 'total' ) );
	}

}