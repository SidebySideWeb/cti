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

class Product_Grid_Banner {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	protected $banner_image = null;

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
		if ( Helper::get_option( 'product_grid_banner' ) ) {
			add_filter( 'woocommerce_shop_loop', array( $this, 'product_grid_banner' ), 15 );
		}

		add_action('pre_get_posts', array( $this, 'shop_query_for_banner' ));
	}

	/**
	 * Add banner to product loop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_grid_banner() {
		global $woocommerce_loop, $wp_query;

		if( ! is_shop() && ! is_product_category() ) {
			return;
		}

		$current_page = max(1, get_query_var('paged'));

		if ( $current_page !== 1 ) {
			return;
		}

		$position = intval($this->get_product_grid_banner_position());
		$current_position = $woocommerce_loop['loop'] + 1;

		if ($current_position != $position) {
			return;
		}

		$image = $this->get_product_grid_banner_image();

		if ( empty( $image ) ) {
			return;
		}

		$link = $this->get_product_grid_banner_link();

		$aria_label = esc_html__( 'Link for', 'glozin' ) . ' ' . esc_html__( 'Product Grid Banner', 'glozin' );
		echo '<li class="product gz-product-grid-banner">';
		echo '<a class="gz-ratio--product-image gz-lazy-load" href="'. esc_url( $link ) .'" aria-label="'. esc_attr( $aria_label ) .'">';
			echo '<img width="100%" height="100%" src="'. esc_url( $image ) .'" alt="'. esc_attr__( 'Product Grid Banner', 'glozin' ) .'">';
			echo '<div class="gz-lazy-load-image"><span class="gz-lazy-load-image__loader"></span></div>';
		echo '</a>';
		echo '</li>';
	}

	/**
	 * Shop query for banner
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_query_for_banner( $query ) {
		if ((is_shop() || is_product_category()) && $query->is_main_query() && $this->get_product_grid_banner_image()) {
			$per_page = $query->get('posts_per_page');
			$paged = max(1, get_query_var('paged'));
			if ($paged == 1) {
				$query->set('posts_per_page', max(1, $per_page - 1));
			} else {
				$query->set('posts_per_page', $per_page);
				$query->set('offset', ($paged - 1) * $per_page - 1);
			}
		}
	}

	/**
	 * Get product grid banner image
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_grid_banner_image() {
		if ( $this->banner_image ) {
			return $this->banner_image;
		}

		$this->banner_image = Helper::get_option('product_grid_banner_image');

		if (function_exists('is_shop') && is_shop()) {
			return $this->banner_image;
		}

		if (!function_exists('is_product_category') || !is_product_category()) {
			return $this->banner_image;
		}

		$banner_fallback = Helper::get_option('category_product_grid_banner_fallback');
		$term_banner_id = get_term_meta(get_queried_object()->term_id, 'gz_cat_product_banner_id', true);

		if (empty($term_banner_id)) {
			return $banner_fallback === 'shop' ? $this->banner_image : false;
		}

		$term_banner = wp_get_attachment_image_src($term_banner_id, 'full');
		if ($term_banner) {
			$this->banner_image = $term_banner[0];
		}

		return $this->banner_image;
	}

	/**
	 * Get product grid banner link
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */	
	public function get_product_grid_banner_link() {
		if (function_exists('is_product_category') && is_product_category()) {
			$link = get_term_meta(get_queried_object()->term_id, 'gz_cat_product_banner_link', true);
			if ($link) {
				return $link;
			}
		}

		return Helper::get_option( 'product_grid_banner_link' );
	}

	/**
	 * Get product grid banner position
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */	
	public function get_product_grid_banner_position() {
		if (function_exists('is_product_category') && is_product_category()) {
			$position = get_term_meta(get_queried_object()->term_id, 'gz_cat_product_banner_position', true);	
			if ($position) {
				return $position;
			}
		}

		return Helper::get_option( 'product_grid_banner_position' );
	}
}