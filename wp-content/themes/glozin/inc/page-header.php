<?php
/**
 * Page_Header functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header initial
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
	 * Page Header items
	 *
	 * @var $items
	 */
	protected static $items = null;


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
		add_action( 'get_the_archive_title', array( $this, 'get_archive_title' ), 30 );
		add_action( 'glozin_after_header', array( $this, 'show_page_header' ), 99 );
	}

	/**
	 * Show page header
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_page_header() {
		if ( is_404() ) {
			return;
		}

		if ( ! $this->get_items() ) {
			return;
		}

		get_template_part( 'template-parts/page-header/page-header' );
	}

	/**
	 * Show page header
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_items() {
		$items = [];

		if ( intval( Helper::get_option( 'page_header' ) ) && is_page() ) {
			$items = Helper::get_option( 'page_header_els' );
		}

		self::$items = $items;

		return apply_filters( 'glozin_get_page_header_elements', self::$items );
	}

	/**
	 * Show classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function classes( $classes ) {
		if ( intval( Helper::get_option( 'page_header' ) ) && is_page() ) {
			$classes .= ' page-header--page';
		}

		if( ! in_array( 'title', self::get_items() ) ) {
			$classes .= ' hide-title';
		}

		echo apply_filters('glozin_page_header_classes', $classes);
	}

	/**
	 * Show title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function title() {
		if( ! in_array( 'title', self::get_items() ) ) {
			return;
		}

		$title = '<h1 class="page-header__title heading-letter-spacing h2 mt-8 mb-0">' . get_the_archive_title() . '</h1>';
		return apply_filters('glozin_page_header_title', $title);
	}

	/**
	 * Show breadcrumb
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function breadcrumb() {
		if( in_array( 'breadcrumb', self::get_items() ) ) {
			\Glozin\Breadcrumb::instance()->breadcrumb();
		}
	}

	/**
	 * Show description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function description() {
		if( in_array( 'description', self::get_items() ) ) {
			$description = apply_filters('glozin_page_header_description_html', '');
			if ( ! empty( $description ) ) {
				return $description;
			}

			$description = get_post_meta( Helper::get_post_ID(), '_page_header_description', true );

			if (is_tax()) {
				$term = get_queried_object();
				if (!$term || empty($term->description)) {
					return '';
				}
			}

			if ( empty( $description ) ) {
				return '';
			}

			$description = apply_filters('glozin_page_header_description', $description);
			$number_lines = \Glozin\Helper::get_option( 'page_header_description_lines' );
			$number_lines = apply_filters('glozin_page_header_description_lines', $number_lines);
			$style = $number_lines ? 'style="--gz-page-header-description-lines: '. esc_attr( $number_lines ) .'"' : '';
			if( $description ) {
				return '<div class="page-header__description" '. $style .'>' . wpautop( $description ) . '</div>';
			}
		}
	}

	/**
	 * Show archive title
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_archive_title( $title ) {
		if ( is_search() ) {
			global $wp_query;
			if( (int) $wp_query->found_posts > 0 ) {
				$title = sprintf(
					esc_html__( '%d Search Results for: "%s"', 'glozin' ),
					(int) $wp_query->found_posts,
					esc_html( get_search_query() )
				);
			} else {
				$title = sprintf( esc_html__( 'Search Results', 'glozin' ) );
			}
		} elseif ( is_404() ) {
			$title = sprintf( esc_html__( 'Page Not Found', 'glozin' ) );
		} elseif ( is_page() ) {
			$title = get_the_title(\Glozin\Helper::get_post_ID());
		} elseif ( is_home() && is_front_page() ) {
			$title = esc_html__( 'The Latest Posts', 'glozin' );
		} elseif ( is_home() && ! is_front_page() ) {
			$title = get_the_title( intval( get_option( 'page_for_posts' ) ) );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$current_term = get_queried_object();
			if ( $current_term && isset( $current_term->term_id ) && ( $current_term->taxonomy == 'product_cat' || $current_term->taxonomy == 'product_brand' ) ) {
				$title = $current_term->name;
			} else {
				$title = get_the_title( intval( get_option( 'woocommerce_shop_page_id' ) ) );
			}
		} elseif ( is_single() ) {
			$title = get_the_title();
		} elseif ( is_tax() || is_category() ) {
			$title = single_term_title( '', false );
		}

		return $title;
	}
}
