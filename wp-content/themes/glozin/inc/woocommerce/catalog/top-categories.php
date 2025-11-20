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

class Top_Categories {
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
		add_action( 'glozin_after_site_content_open', array( $this, 'top_categories' ), 10 );
	}


	/**
	 * Show top categories
	 *
	 * @return void
	 */
	public function top_categories() {
		if( is_search() ) {
			return;
		}

		$top_html = '<div class="catalog-top-categories gz-arrows-middle"></div>';

		if ( is_tax( 'product_brand' ) && ! intval( Helper::get_option( 'show_brand_page' ) )) {
			return $top_html;
		}

		$queried        = get_queried_object();
		if( empty( $queried ) ) {
			return $top_html;
		}

		$current_term   = ! empty ( $queried->term_id ) ? $queried->term_id : '';
		$orderby 		= Helper::get_option( 'top_categories_order' );
		$limit			= Helper::get_option( 'top_categories_limit' );
		$ouput          = array();
		$taxonomy       = 'product_cat';

		if( $this->is_shop() ) {
			$args = array(
				'taxonomy' => $taxonomy,
				'parent'   => 0,
			);

		} else {
			$termchildren  = get_term_children( $queried->term_id, $taxonomy );

			$args = array(
				'taxonomy' => $taxonomy,
			);

			if( ! empty( $termchildren ) ) {
				$args['parent'] = $queried->term_id;

				if( count( $termchildren ) == 1 ) {
					$term = get_term_by( 'id', $termchildren[0], $taxonomy );

					if( $term->count == 0 ) {
						$args['parent'] = $queried->parent;
					}
				}

			} else {
				$args['parent'] = $queried->parent;
			}
		}

		if ( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;

			if ( $orderby == 'order' ) {
				$args['menu_order'] = 'asc';
			} else {
				if ( $orderby == 'count' ) {
					$args['order'] = 'desc';
				}
			}
		}

		if( ! empty ( $limit ) && $limit !== '0' ) {
			$args['number'] =  Helper::get_option( 'top_categories_limit' );
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || ! $terms ) {
			return;
		}

		$thumbnail_size = 'medium';
		$title_html_tag = Helper::get_option( 'top_categories_title_html_tag' );
		$image_attributes = array();
		foreach( $terms as $term ) {
			$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$image = '';
			if( ! empty( $thumb_id ) ) {
				$image = wp_get_attachment_image( $thumb_id, $thumbnail_size);
			}

			if( empty( $image ) ) {
				$image = wc_placeholder_img( $thumbnail_size );
			}

			$aria_label = esc_html__( 'Link for', 'glozin' ) . ' ' . $term->name;

			$ouput[] = sprintf(
						'<div class="catalog-top-categories__item overflow-hidden swiper-slide %s">
							<a class="catalog-top-categories__inner gz-arrows-middle__image gz-ratio gz-hover-zoom gz-hover-effect gz-image-rounded overflow-hidden position-relative" href="%s" aria-label="%s">
								%s
							</a>
							<a class="catalog-top-categories__button d-block mt-15" href="%s">
								<%s class="catalog-top-categories__text d-block fs-14 fw-semibold text-dark">%s</%s>
							</a>
						</div>',
						( !empty( $current_term ) && $current_term == $term->term_id ) ? 'active' : '',
						esc_url( get_term_link( $term->term_id ) ),
						esc_attr( $aria_label ),
						$image,
						esc_url( get_term_link( $term->term_id ) ),
						esc_attr( $title_html_tag ),
						esc_html( $term->name ),
						esc_attr( $title_html_tag )
					);
		}

		$columns = Helper::get_option( 'top_categories_columns', [] );
		$desktop_columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '6';
		$tablet_columns  = isset( $columns['tablet'] ) ? $columns['tablet'] : '3';
		$mobile_columns  = isset( $columns['mobile'] ) ? $columns['mobile'] : '2';

		$data_swiper = array(
			'slidesPerView' => array(
				'desktop' => $desktop_columns,
				'tablet' => $tablet_columns,
				'mobile' => $mobile_columns,
			),
			'slidesPerGroup' => array(
				'desktop' => $desktop_columns,
				'tablet' => $tablet_columns,
				'mobile' => $mobile_columns,
			),
			'spaceBetween' => array(
				'desktop' => 30,
				'tablet' => 30,
				'mobile' => 15,
			),
		);

		echo sprintf(
				'<div class="catalog-top-categories position-relative w-100 mb-40 mb-md-60 glozin-swiper swiper glozin-carousel--elementor navigation-class-arrows navigation-class--tabletdots navigation-class--mobiledots gz-arrows-middle" data-swiper="%s" data-desktop="%s" data-tablet="%s" data-mobile="%s">
					<div class="catalog-top-categories__wrapper swiper-wrapper">%s</div>
					<div class="swiper-pagination swiper-pagination-bullets--small"></div>
					%s %s
				</div>',
				esc_attr( json_encode( $data_swiper ) ),
				$desktop_columns,
				$tablet_columns,
				$mobile_columns,
				implode( '', $ouput ),
				\Glozin\Icon::get_svg( 'left-mini', 'ui', 'class=gz-button-light glozin-swiper-button swiper-button swiper-button-prev' ),
				\Glozin\Icon::get_svg( 'right-mini', 'ui', 'class=gz-button-light glozin-swiper-button swiper-button swiper-button-next' )
			);
	}

	/**
	 * Check is shop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function is_shop() {
		if( function_exists('is_product_category') && is_product_category() ) {
			return false;
		} elseif( function_exists('is_shop') && is_shop() ) {
			if ( ! empty( $_GET ) && ( isset($_GET['product_cat']) )) {
				return false;
			}

			return true;
		}

		return true;
	}

}