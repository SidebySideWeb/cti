<?php
/**
 * Glozin Page Header functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Catalog;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Glozin Page Header
 *
 */
class Page_Header {

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
		// Remove shop loop header
		remove_action('woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10);
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

		add_action('glozin_page_header_description_html', array( $this, 'description' ) );

		add_filter('glozin_page_header_classes', array( $this, 'classes' ));
		add_filter('glozin_get_page_header_elements', array( $this, 'elements' ));

		if ( intval( Helper::get_option( 'taxonomy_description_enable' ) ) ) {
			add_action( 'woocommerce_after_main_content', array( $this, 'taxonomy_description_below_products' ), 5 );
		}
	}

	/**
	 * Description content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function description( $alignment = '', $number_lines = '' ) {
		ob_start();
		if( function_exists('is_shop') && is_shop() ) {
			woocommerce_product_archive_description();
		}

		$description = ob_get_clean();

		if ( is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$description = $term->description;
			}
		}

		if ( empty($description) ) {
			return '';
		}

		$alignment = ! empty($alignment) ? $alignment : 'center';
		$number_lines = ! empty($number_lines) ? $number_lines : \Glozin\Helper::get_option( 'shop_header_number_lines' );

		if( $description ) {
			$option = json_encode([
				'more' => esc_html__('Show More', 'glozin'),
				'less' => esc_html__('Show Less', 'glozin')
			]);

			return sprintf(
				'<div class="page-header__description shop-header__description text-%s" style="--gz-page-header-description-lines: %s">
					<div class="shop-header__description-inner">%s</div>
					<div class="shop-header__more-wrapper hidden">
						<button class="shop-header__more gz-button-subtle mt-20" data-settings="%s">%s</button>
					</div>
				</div>',
				esc_attr($alignment),
				esc_attr($number_lines),
				wpautop(do_shortcode($description)),
				htmlspecialchars($option),
				esc_html__('Show More', 'glozin')
			);
		}
	}

	public static function taxonomy_description_below_products() {
		$alignment = \Glozin\Helper::get_option( 'taxonomy_description_alignment' );
		$number_lines = \Glozin\Helper::get_option( 'taxonomy_description_number_lines' );

		echo '<div class="taxonomy-description-below-products mt-40">';
		echo self::description( $alignment, $number_lines );
		echo '</div>';
	}

	/**
	 * Page Header Classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function classes( $classes ) {
		$classes .= ' page-header--shop';

		return $classes;
	}

	/**
	 * Page Header Elements
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elements( $items ) {
		$items = \Glozin\Helper::get_option('shop_header') ? (array) \Glozin\Helper::get_option( 'shop_header_els' ) : [];

		return apply_filters('glozin_shop_header_elements', $items);
	}
}
