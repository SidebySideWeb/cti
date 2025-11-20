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

class Toolbar {
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
		add_action( 'woocommerce_before_shop_loop', array( $this, 'catalog_toolbar' ), 40 );

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		add_action( 'glozin_woocommerce_products_toolbar', array( $this, 'toolbar_left' ), 20 );
		add_action( 'glozin_woocommerce_products_toolbar', array( $this, 'toolbar_center' ), 40 );
		add_action( 'glozin_woocommerce_products_toolbar', array( $this, 'toolbar_right' ), 60 );

		add_filter( 'woocommerce_catalog_orderby', array( $this, 'catalog_orderby' ) );
		add_filter( 'woocommerce_get_catalog_ordering_args', array( $this, 'catalog_ordering_args' ) );
	}

	/**
	 * Catalog toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function catalog_toolbar() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}

        echo '<div class="catalog-toolbar pb-22">';
			/**
			 * Hook: glozin_woocommerce_before_products_toolbar
			 */
			do_action( 'glozin_woocommerce_before_products_toolbar' );

			echo '<div class="catalog-toolbar__toolbar d-flex align-items-center gap-10">';
				/**
				 * Hook: glozin_woocommerce_products_toolbar
				 */
				do_action( 'glozin_woocommerce_products_toolbar' );

			echo '</div>';
			/**
			 * Hook: glozin_woocommerce_after_products_toolbar
			 */
			do_action( 'glozin_woocommerce_after_products_toolbar' );

		echo '</div>';
	}

	/**
	 * Catalog toolbar left item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_left() {
		echo '<div class="catalog-toolbar__left catalog-toolbar__item d-flex align-items-center justify-content-start gap-30 flex-1 h-100">';
			$this->popup_filter_button();
			$this->toolbar_product_total();
		echo '</div>';
	}

	/**
	 * Catalog toolbar center item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_center() {
		if( ! in_array( 'view', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		echo '<div class="catalog-toolbar__center catalog-toolbar__item d-flex align-items-center justify-content-center gap-10 flex-1 h-100">';
		$this->toolbar_view();
		echo '</div>';
	}

	/**
	 * Catalog toolbar right item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_right() {
		if( ! in_array( 'sortby', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		\Glozin\Theme::set_prop( 'popovers', 'mobile-orderby' );

		$orderby_list = (array) $this->orderby_list();
		$default_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : get_option('woocommerce_default_catalog_orderby', 'menu_order');
		$default_orderby_name = isset($orderby_list[$default_orderby]) ? $orderby_list[$default_orderby] : esc_html__('Default Sorting', 'glozin');

		echo '<div class="catalog-toolbar__right catalog-toolbar__item d-flex align-items-center justify-content-end gap-10 flex-1 h-100">';
		echo '<div class="catalog-toolbar__orderby-form position-relative d-none d-block-md">';
		woocommerce_catalog_ordering();
		echo '<div class="catalog-toolbar__orderby-default d-flex align-items-center gap-20 position-relative">';
		echo '<span>'. esc_html__( 'Sort by:', 'glozin' ) .'</span>';
		echo '<span class="catalog-toolbar__orderby-default-name text-dark fw-medium">'. $default_orderby_name .'</span>';
		echo '<span class="gz-collapse-icon fs-10">'. \Glozin\Icon::get_svg( 'arrow-bottom' ) .'</span>';
		echo '</div>';
		echo '<ul class="catalog-toolbar__orderby-list list-unstyled shadow position-absolute top-100 end-0 z-3 bg-light rounded-5 px-25 py-20">';
			foreach ( $orderby_list as $id => $name ) {
				echo '<li><a class="catalog-toolbar__orderby-item text-base py-4 d-block" href="#" data-id="'. esc_attr( $id ) .'">'. esc_html( $name ) .'</a></li>';
			}
		echo '</ul>';
		echo '</div>';
		echo '<button class="gz-button-outline catalog-toolbar__orderby-button d-none-md" data-toggle="popover" data-target="mobile-orderby-popover">' . esc_html__('Sort by:', 'glozin') . \Glozin\Icon::get_svg( 'arrow-bottom' ) .'</button>';
		echo '</div>';
	}

	/**
	 * Toolbar view.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_view() {
		global $wp;
		$columns = ( array ) Helper::get_option( 'catalog_toolbar_views' );
		$output_type = '';
		$current_view = \Glozin\WooCommerce\Catalog\View::get_default_view();
		$default_column = get_option( 'woocommerce_catalog_columns', 4 );

		foreach ($columns as $column) {
			if( $column > 1 ) {
				$default_column_class = '';

				$view = 'grid';
				$icon = 'grid-' . $column;
				$class = 'gz-tooltip-inside grid grid-' . $column;
				$tooltip = $column . ' ' . esc_html__('Columns', 'glozin');

				if( $column == $default_column ) {
					$class .= ' default';
					if( $current_view == $view ) {
						$class .= ' current';
					}
				}

			} else {
				$view = 'list';
				$icon = 'list';
				$class = 'gz-tooltip-inside list';
				$tooltip = esc_html__('List', 'glozin');

				$class .= $current_view == $view ? ' current' : '';
			}
			$aria_label = esc_html__( 'Link for', 'glozin' ) . ' ' . $tooltip;
			$link_url = ['view' => $view];
			if (isset($_GET)) {
				$link_url = wp_parse_args($link_url, $_GET);
			}

			$current_url = add_query_arg($link_url, home_url($wp->request));

			$output_type .= sprintf(
				'<a href="%s" class="gz-shop-view-item %s" data-column="%s" data-tooltip="%s" aria-label="%s">%s</a>',
				esc_url($current_url),
				esc_attr($class),
				esc_attr($column),
				esc_attr($tooltip),
				esc_attr( $aria_label ),
				\Glozin\Icon::get_svg($icon)
			);
		}

		echo sprintf(
			'<div id="glozin-toolbar-view" class="glozin-toolbar-view view-%s d-flex align-items-center gap-15">%s</div>',
			esc_attr($current_view),
			$output_type
		);
	}

	/**
	 * Popup filter button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function popup_filter_button() {
		if ( ! is_active_sidebar( 'catalog-filters-sidebar' ) ) {
			return;
		}

		if ( Helper::get_option( 'product_filter_type' ) == 'no-filter' ) {
			return;
		}

		$classes = Helper::get_option( 'product_filter_type' ) !== 'popup' ? 'd-none-xl' : '';

		echo '<button class="catalog-toolbar__filter-button '. $classes .'" data-toggle="off-canvas" data-target="filter-sidebar-panel">'. \Glozin\Icon::get_svg( 'filter-2' ) . esc_html__('Filter', 'glozin') .'</button>';
	}

	/**
	 * Toolbar product total
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_product_total() {
		if( ! in_array( 'total', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		echo '<div class="catalog-toolbar__result-count d-none d-block-xl">'. esc_html__( 'There are ', 'glozin' ) . wc_get_loop_prop( 'total' ) . esc_html__( ' results in total', 'glozin' ) . '</div>';
	}

	/**
	 * Order by list
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function orderby_list() {
		if( ! in_array( 'sortby', ( array ) Helper::get_option( 'catalog_toolbar_els' ) ) ) {
			return;
		}

		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$orderby = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => __( 'Default sorting', 'glozin' ),
				'popularity' => __( 'Popularity', 'glozin' ),
				'rating'     => __( 'Average rating', 'glozin' ),
				'price'      => __( 'Price, low to high', 'glozin' ),
				'price-desc' => __( 'Price, high to low', 'glozin' ),
				'date'       => __( 'Date, new to old', 'glozin' ),
				'date-asc'   => __( 'Date, old to new', 'glozin' ),
			)
		);

		if ( wc_get_loop_prop( 'is_search' ) ) {
			$orderby = array_merge( array( 'relevance' => __( 'Relevance', 'glozin' ) ), $orderby );

			unset( $orderby['menu_order'] );
		}

		if ( ! $show_default_orderby ) {
			unset( $orderby['menu_order'] );
		}

		if ( ! wc_review_ratings_enabled() ) {
			unset( $orderby['rating'] );
		}

		return $orderby;
	}

	public function catalog_orderby( $orderby ) {
		$orderby['date-asc'] = __( 'Date, old to new', 'glozin' );

		return $orderby;
	}

	public function catalog_ordering_args( $args ) {
		if ( isset( $_GET['orderby'] ) && 'date-asc' === $_GET['orderby'] ) {
			$args['orderby'] = 'date';
			$args['order'] = 'ASC';
		}
		return $args;
	}
}