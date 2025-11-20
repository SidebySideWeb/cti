<?php
/**
 * Blog functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Header;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Manager {
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
		add_action( 'template_redirect', array( $this, 'template_hooks' ) );
	}

	/**
	 * Add template hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function template_hooks() {
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		if( apply_filters( 'glozin_get_campaign_bar', \Glozin\Helper::get_option( 'campaign_bar' ) ) ) {
			add_action( 'glozin_before_header', array( $this, 'campaign_bar' ) );
		}

		if( apply_filters( 'glozin_get_topbar', \Glozin\Helper::get_option( 'topbar' ) ) ) {
			add_action( 'glozin_before_header', array( $this, 'topbar' ) );
		}

		add_action( 'glozin_header', array( $this, 'header' ) );

		add_filter( 'glozin_header_container_classes', array( $this, 'header_container_class' ) );
		add_filter( 'glozin_topbar_container_classes', array( $this, 'topbar_container_class' ) );

		add_filter( 'nav_menu_link_attributes', array( $this, 'menu_links' ), 20, 4 );

		if( apply_filters( 'glozin_get_header_sidebar_categories', \Glozin\Helper::get_option( 'header_sidebar_categories' ) ) ) {
			add_action( 'glozin_after_header', array( $this, 'sidebar_categories' ) );
		}
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		if( apply_filters( 'glozin_get_header_sidebar_categories', \Glozin\Helper::get_option( 'header_sidebar_categories' ) ) && has_nav_menu( 'category-menu' ) ) {
			$classes[] = 'glozin-header-sidebar-categories-enable';
		}

		return $classes;
	}

	/**
	 * Displays header content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function header() {
		$header  = \Glozin\Header\Main::instance();
		if( ! empty($header->get_layout()) ) {
			$classes = 'header-' .  $header->get_layout();

			if( \Glozin\Helper::get_option( 'header_sticky' ) ) {
				$classes .= ' glozin-header-sticky';

				if( \Glozin\Helper::get_option( 'header_sticky_el' ) === 'both' ) {
					$classes .= ' header-sticky--both';
				}
			}

			if ( $header->get_layout() == 'custom' ) {
				if ( intval( \Glozin\Helper::get_option( 'header_main_divider' ) ) ) {
					$classes .= ' glozin-header-main-divider';
				}

				if ( intval( \Glozin\Helper::get_option( 'header_bottom_divider' ) ) ) {
					$classes .= ' glozin-header-bottom-divider';
				}
			}

			$classes = apply_filters( 'glozin_header_section_classes', $classes );

			echo '<div class="site-header__desktop site-header__section ' . esc_attr( $classes ) . '">';
			$header->render();
			echo '</div>';
		}

		$header_mobile 	= \Glozin\Header\Mobile::instance();
		if( ! empty($header_mobile->get_layout()) ) {
			if ( $header->get_layout() == 'v4' ) {
				$classes = 'header-v4';
			} else {
				$classes = 'header-' .  $header_mobile->get_layout();
			}

			if( \Glozin\Helper::get_option( 'header_mobile_sticky' ) ) {
				$classes .= ' glozin-header-mobile-sticky';

				if( \Glozin\Helper::get_option( 'header_mobile_sticky_el' ) === 'both' ) {
					$classes .= ' header-sticky--both';
				}
			}

			$classes = apply_filters( 'glozin_header_section_classes', $classes );

			echo '<div class="site-header__mobile site-header__section ' . esc_attr( $classes ) . '">';
			$header_mobile->render();
			echo '</div>';
		}
	}

	/**
	 * Header class container in header version
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function header_container_class( $classes ) {
		$header_full_width = intval(\Glozin\Helper::get_option( 'header_fullwidth' ));
		$header_full_width = apply_filters( 'glozin_header_full_width', $header_full_width);
		if ( $header_full_width ) {
			$classes = 'container-fluid';
		}

		return $classes;
	}

	/**
	 * Header class container in topbar version
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function topbar_container_class( $classes ) {
		$topbar_full_width = intval(\Glozin\Helper::get_option( 'topbar_fullwidth' ));
		$topbar_full_width = apply_filters( 'glozin_topbar_full_width', $topbar_full_width);
		if ( $topbar_full_width ) {
			$classes = 'container-fluid';
		}

		$classes .= ' d-flex align-items-center justify-content-between';

		return $classes;
	}

	/**
	 * Display header campaign bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function campaign_bar() {
		$args =  array( 'type' => \Glozin\Helper::get_option( 'campaign_bar_type' ) );

		if( $args['type'] == 'countdown' && empty(\Glozin\Header\Campaign_Bar::get_countdown_time()) ) {
			return;
		 }

		get_template_part( 'template-parts/header/campaign-bar', '', $args );
	}

	/**
	 * Display header top bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function topbar() {
		$items = array(
			'left_items' => (array) \Glozin\Helper::get_option( 'topbar_left' ),
			'right_items' => (array) \Glozin\Helper::get_option( 'topbar_right' )
		);

		get_template_part( 'template-parts/header/topbar', '', $items );
	}

	/**
	 * Add arrow menu item
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function menu_links( $atts, $item, $args, $depth ) {
		if ( empty( $args->theme_location ) ) {
			return $atts;
		}

		if ( $item->title ) {
			$atts['data-title'] = $item->title ? $item->title : '';
		}

		return $atts;
	}

	/**
	 * Display header sidebar categories
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function sidebar_categories() {
		get_template_part( 'template-parts/header/sidebar-categories' );
	}

}
