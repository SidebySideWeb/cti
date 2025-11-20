<?php
/**
 * Style functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Style initial
 *
 * @since 1.0.0
 */
class Dynamic_CSS {
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
		add_action( 'glozin_after_enqueue_style', array( $this, 'add_static_css' ) );
	}

	/**
	 * Get get style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function add_static_css() {
		$parse_css = $this->primary_color_static_css();
		$parse_css .= $this->typography_css();
		$parse_css .= $this->header_static_css();
		$parse_css .= $this->header_mobile_static_css();
		$parse_css .= $this->campaign_bar_static_css();
		$parse_css .= $this->topbar_static_css();
		$parse_css .= $this->page_header_static_css();
		$parse_css .= $this->blog_header_static_css();
		$parse_css .= $this->images_static_css();
		$parse_css .= $this->post_thumbnail_static_css();
		$parse_css .= $this->buttons_static_css();
		$parse_css .= $this->form_fields_static_css();

		wp_add_inline_style( 'glozin', apply_filters( 'glozin_inline_style', $parse_css ) );
	}

	/**
	 * Get Color Scheme style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function primary_color_static_css() {
		$static_css = '';

		if( intval( Helper::get_option( 'primary_color_custom' ) && ! empty( Helper::get_option('primary_color_custom_color') ) ) ) {
			$static_css = '--gz-color-primary:' . Helper::get_option('primary_color_custom_color') . '; --gz-link-color-hover:' . Helper::get_option('primary_color_custom_color') . ';';
		} else {
			if ( ! empty( Helper::get_option( 'primary_color' ) ) ) {
				$static_css = '--gz-color-primary:' . Helper::get_option('primary_color') . '; --gz-link-color-hover:' . Helper::get_option('primary_color') . ';';
			}
		}

		$primary_text_color = Helper::get_option('primary_text_color');
		if( $primary_text_color !== 'light' ) {
			$custom_color = $primary_text_color == 'custom' ? Helper::get_option('primary_text_color_custom') : '#000';
			$static_css .= '--gz-text-color-on-primary:' . $custom_color . ';';
		}

		if( $base_color = \Glozin\Helper::get_option('primary_base_color') ) {
			$static_css .= '--gz-color-base:' . $base_color . ';';
		}

		if( $dark_color = \Glozin\Helper::get_option('primary_dark_color') ) {
			$static_css .= '--gz-color-dark:' . $dark_color . ';';
		}

		if( $link_color = \Glozin\Helper::get_option('primary_link_color') ) {
			$static_css .= '--gz-link-color:' . $link_color . ';';
		}
		if( $link_hover_color = \Glozin\Helper::get_option('primary_link_hover_color') ) {
			$static_css .= '--gz-link-color-hover:' . $link_hover_color . ';';
		}

		if( $color = Helper::get_option('product_card_sale_color')) {
			$static_css .= '.gz-price ins, .price ins {--gz-color-price-sale: ' . $color . ';}';
		}

		return 'body {'. $static_css .'}';
	}

	/**
	 * Get typography CSS base on settings
	 */
	protected function typography_css() {
		$settings = array(
			'typo_body'                  	=> 'body, .block-editor .editor-styles-wrapper',
			'typo_heading'                  => 'heading',
			'typo_h1'                    	=> 'h1,.h1',
			'typo_h2'                    	=> 'h2,.h2',
			'typo_h3'                    	=> 'h3,.h3',
			'typo_h4'                    	=> 'h4,.h4',
			'typo_h5'                    	=> 'h5,.h5',
			'typo_h6'                    	=> 'h6,.h6',
			'typo_menu'                  	=> '.primary-navigation .nav-menu > li > a',
			'typo_submenu'               	=> '.primary-navigation li .menu-item > a, .primary-navigation li .menu-item--widget > a, .primary-navigation .mega-menu ul.mega-menu__column .menu-item--widget-heading a, .primary-navigation li .menu-item > span, .primary-navigation li .menu-item > h6',
			'typo_secondary_menu'        	=> '.secondary-navigation .nav-menu > li > a',
			'typo_sub_secondary_menu'    	=> '.secondary-navigation li .menu-item > a, .secondary-navigation li .menu-item--widget > a, .secondary-navigation .mega-menu ul.mega-menu__column .menu-item--widget-heading a, .secondary-navigation li .menu-item > span, .secondary-navigation li .menu-item > h6',
			'typo_category_menu_title'   	=> '.header-category__name',
			'typo_category_menu'       	 	=> '.header-category__menu > ul > li > a',
			'typo_sub_category_menu'     	=> '.header-category__menu ul ul li > *',
			'typo_page_title'     		 	=> '.page-header--page .page-header__title',
			'typo_blog_header_title'     	=> '.page-header--blog .page-header__title',
			'typo_blog_post_title'     		=> '.single-post .hentry .entry-header .entry-title',
			'typo_catalog_page_title'     	=> '.page-header--shop .page-header__title',
			'typo_catalog_page_description' => '.page-header--shop .page-header__description',
			'typo_catalog_product_title' 	=> 'ul.products li.product h2.woocommerce-loop-product__title a',
			'typo_product_title'     		=> '.single-product div.product .product-gallery-summary h1.product_title',
		);

		return $this->get_typography_css( $settings );
	}

	/**
	 * Get typography CSS base on settings
	 */
	protected function get_typography_css( $settings, $print_default = false ) {
		if ( empty( $settings ) ) {
			return '';
		}

		$css        = '';
		$properties = array(
			'font-family'    => 'font-family',
			'font-size'      => 'font-size',
			'variant'        => 'font-weight',
			'line-height'    => 'line-height',
			'letter-spacing' => 'letter-spacing',
			'color'          => 'color',
			'text-transform' => 'text-transform',
			'text-align'     => 'text-align',
			'font-weight'    => 'font-weight',
			'font-style'     => 'font-style',
		);

		foreach ( $settings as $setting => $selector ) {
			if ( ! is_string( $setting ) ) {
				continue;
			}

			$selector   = is_array( $selector ) ? implode( ',', $selector ): $selector;
			$typography = Helper::get_option( $setting );
			$default    = (array) Helper::get_option_default( $setting );
			$style      = '';

			// Correct the default values. Copy from Kirki_Field_Typography::sanitize
			if ( isset( $default['variant'] ) ) {
				if ( ! isset( $default['font-weight'] ) ) {
					$default['font-weight'] = filter_var( $default['variant'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
					$default['font-weight'] = ( 'regular' === $default['variant'] || 'italic' === $default['variant'] ) ? '400' : absint( $default['font-weight'] );
				}

				// Get font-style from variant.
				if ( ! isset( $default['font-style'] ) ) {
					$default['font-style'] = ( false === strpos( $default['variant'], 'italic' ) ) ? 'normal' : 'italic';
				}
			}

			if ( isset( $typography['variant'] ) && ( ! empty( $typography['font-weight'] ) || ! empty( $typography['font-style'] ) ) ) {
				unset( $typography['variant'] );
			}


			foreach ( $properties as $key => $property ) {
				if ( ! isset( $default[ $key ] ) ) {
					continue;
				}

				if ( isset( $typography[ $key ] ) && ! empty( $typography[ $key ] ) ) {
					if ( ! $print_default && strtoupper( $default[ $key ] ) == strtoupper( $typography[ $key ] ) ) {
						continue;
					}
					if( $selector == 'heading' ) {
						$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
						$property = 'font-family' == $property ? '--gz-heading-font' : $property;
						$property = 'font-weight' == $property ? '--gz-heading-font-weight' : $property;
						$property = 'line-height' == $property ? '--gz-heading-line-height' : $property;
						$property = 'color' == $property ? '--gz-heading-color' : $property;
						$property = 'text-transform' == $property ? '--gz-heading-text-transform' : $property;
						$style .= $property . ': ' . $value . ';';
					} else {
						$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
						$value = 'variant' == $key ? str_replace( 'regular', '400', $value ) : $value;

						if ( $value ) {
							$style .= $property . ': ' . $value . ';';
						}
					}
				}

			}
			$selector = 'heading' == $selector ? 'body' : $selector;
			if ( ! empty( $style ) ) {
				$css .= $selector . '{' . $style . '}';
			}
		}

		return $css;
	}

	/**
	 * Header static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function header_static_css() {
		$static_css = '';

		// Logo dimension.
		$logo_dimension = (array) \Glozin\Helper::get_option( 'logo_dimension' );
		$logo_dimension = apply_filters('glozin_header_logo_dimension', $logo_dimension);
		$logo_width = ! empty($logo_dimension['width']) ? $logo_dimension['width'] : '';
		$logo_height = ! empty($logo_dimension['height']) ? $logo_dimension['height'] : '';

		$unit_width = $logo_width != 'auto' ? 'px;' : ';';
		$unit_height = $logo_height != 'auto' ? 'px;' : ';';

		$width = ! empty($logo_width) ? 'width: ' . $logo_width . $unit_width : '';
		$height = ! empty($logo_height) ? 'height: ' . $logo_height . $unit_height : '';
		if ( $width || $height ) {
			$static_css .= '.header-logo > a img, .header-logo > a svg {' . $width . $height . '}';
		}

		// Header Main height
		$header_main_height = Helper::get_option( 'header_main_height' );
		if ( $header_main_height != 70 ) {
			$static_css .= '.site-header__desktop .header-main { height: ' . $header_main_height . 'px }';
		}

		// Header Bottom height
		$header_bottom_height = Helper::get_option( 'header_bottom_height' );
		if ( $header_bottom_height != 60 ) {
			$static_css .= '.site-header__desktop .header-bottom { height: ' . $header_bottom_height . 'px }';
		}

		// Header Sticky height
		$height_sticky = Helper::get_option( 'header_sticky_height' );
		if ( $height_sticky && $height_sticky != 85 ) {
			$static_css .= '.site-header__desktop.minimized .header-main, .site-header__desktop.headroom--not-top .header-main { height: ' . $height_sticky . 'px; }';

			if( Helper::get_option( 'header_sticky_el' ) == 'both' ) {
				$static_css .= '.site-header__desktop.minimized .header-sticky + .header-bottom, .site-header__desktop.headroom--not-top .header-sticky + .header-bottom { top: ' . $height_sticky . 'px; }';
				$static_css .= '.admin-bar .site-header__desktop.minimized .header-sticky + .header-bottom, .admin-bar .site-header__desktop.headroom--not-top .header-sticky + .header-bottom { top: ' . ( $height_sticky + 32 ) . 'px; }';
			}
		}

		$height_sticky_bottom = Helper::get_option( 'header_sticky_bottom_height' );
		if ( $height_sticky_bottom && $height_sticky_bottom != 64 ) {
			$static_css .= '.site-header__desktop.minimized .header-bottom { height: ' . $height_sticky_bottom . 'px; }';
		}

		// Header Background
		if( $main_background_color = \Glozin\Helper::get_option('header_main_background_color') ) {
			$static_css .= '.site-header__desktop .header-main { --gz-header-main-bg-color: ' . $main_background_color . '; --gz-header-sticky-bg-color: ' . $main_background_color . '; }';
		}
		if( $main_color = \Glozin\Helper::get_option('header_main_text_color') ) {
			$static_css .= '.site-header__desktop .header-main { --gz-header-color: ' . $main_color . '; --gz-header-sticky-color: ' . $main_color . '; color: ' . $main_color . '; }';
		}
		if( $main_border_color = \Glozin\Helper::get_option('header_main_border_color') ) {
			$static_css .= '.site-header__desktop .header-main { --gz-header-main-border-color: ' . $main_border_color . '; }';
		}
		if( $main_shadow_color = \Glozin\Helper::get_option('header_main_shadow_color') ) {
			$static_css .= '.site-header__desktop .header-main { --gz-header-main-shadow-color: ' . $main_shadow_color . '; }';
		}

		if( $bottom_background_color = \Glozin\Helper::get_option('header_bottom_background_color') ) {
			$static_css .= '.site-header__desktop .header-bottom { --gz-header-bottom-bg-color: ' . $bottom_background_color . '; --gz-header-sticky-bg-color: ' . $bottom_background_color . '; }';
		}
		if( $bottom_color = \Glozin\Helper::get_option('header_bottom_text_color') ) {
			$static_css .= '.site-header__desktop .header-bottom { --gz-header-color: ' . $bottom_color . '; --gz-header-sticky-color: ' . $bottom_color . '; color: ' . $bottom_color . '; }';
		}
		if( $bottom_border_color = \Glozin\Helper::get_option('header_bottom_border_color') ) {
			$static_css .= '.site-header__desktop .header-bottom { --gz-header-bottom-border-color: ' . $bottom_border_color . '; }';
		}
		if( $bottom_shadow_color = \Glozin\Helper::get_option('header_bottom_shadow_color') ) {
			$static_css .= '.site-header__desktop .header-bottom { --gz-header-bottom-shadow-color: ' . $bottom_shadow_color . '; }';
		}

		// Header Counter Background
		if( $header_counter_background_color = \Glozin\Helper::get_option('header_counter_background_color') ) {
			$static_css .= '.header-counter { --gz-color-primary: ' . $header_counter_background_color . '; }';
		}

		if( $header_counter_color = \Glozin\Helper::get_option('header_counter_color') ) {
			$static_css .= '.header-counter { --gz-text-color-on-primary: ' . $header_counter_color . '; }';
		}


		if ( \Glozin\Helper::get_option('header_present') == 'custom' ) {
			if( $header_search_field = \Glozin\Helper::get_option('header_search_form_width') ) {
				$static_css .= '.site-header .header-search__field { width: ' . $header_search_field . 'px; }';
			}
		}

		return $static_css;
	}

	/**
	 * Header mobile static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function header_mobile_static_css() {
		$static_css = '';

		$header_breakpoint = \Glozin\Helper::get_option( 'header_mobile_breakpoint' );
		$header_breakpoint = ! empty( $header_breakpoint ) ? $header_breakpoint : '1199';

		if ( intval( $header_breakpoint ) ) {
			$static_css .= '@media (max-width: '. $header_breakpoint .'px) { .site-header__mobile { display: block; } }';
			$static_css .= '@media (max-width: '. $header_breakpoint .'px) { .site-header__desktop { display: none; } }';
		}

		// Header height.
		$height_main = \Glozin\Helper::get_option( 'header_mobile_main_height' );
		if ( $height_main && $height_main != 64 ) {
			$static_css .= '.site-header__mobile .header-mobile-main { height: ' . $height_main . 'px; }';
		}

		// Header bottom
		$height_bottom = \Glozin\Helper::get_option( 'header_mobile_bottom_height' );
		if ( $height_bottom && $height_bottom != 60 ) {
			$static_css .= '.site-header__mobile .header-mobile-bottom { height: ' . $height_bottom . 'px; }';
		}

		// Header sticky
		$height_sticky = Helper::get_option( 'header_mobile_sticky_height' );
		if ( $height_sticky && $height_sticky != 64 ) {
			$static_css .= '.site-header__mobile.minimized .header-mobile-main, .site-header__mobile.headroom--not-top .header-mobile-main { height: ' . $height_sticky . 'px; }';
			$static_css .= '.site-header__mobile.minimized .header-mobile-sticky + .header-mobile-bottom, .site-header__mobile.headroom--not-top .header-mobile-sticky + .header-mobile-bottom { top: ' . $height_sticky . 'px; }';
		}

		$height_sticky_bottom = Helper::get_option( 'header_mobile_sticky_bottom_height' );
		if ( $height_sticky_bottom && $height_sticky_bottom != 60 ) {
			$static_css .= '.site-header__mobile.minimized .header-mobile-bottom, .site-header__mobile.headroom--not-top .header-mobile-bottom { height: ' . $height_sticky_bottom . 'px; }';
		}

		// Mobile Logo dimension.
		$logo_dimension = (array) \Glozin\Helper::get_option( 'mobile_logo_dimension' );
		$logo_dimension = apply_filters('glozin_header_mobile_logo_dimension', $logo_dimension);
		$logo_width = ! empty($logo_dimension['width']) ? $logo_dimension['width'] : '';
		$logo_height = ! empty($logo_dimension['height']) ? $logo_dimension['height'] : '';
		$unit_width = $logo_width != 'auto' ? 'px;' : ';';
		$unit_height = $logo_height != 'auto' ? 'px;' : ';';
		$width = ! empty($logo_width) ? 'width: ' . $logo_width . $unit_width : '';
		$height = ! empty($logo_height) ? 'height: ' . $logo_height . $unit_height : '';
		if ( $width || $height ) {
			$static_css .= '.site-header__mobile .header-logo > a img,.site-header__mobile .header-logo > a svg {' . $width . $height . '}';
		}

		// Header Background
		if( $main_background_color = \Glozin\Helper::get_option('header_mobile_main_background_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-main { --gz-header-main-bg-color: ' . $main_background_color . '; --gz-header-sticky-bg-color: ' . $main_background_color . '; }';
		}
		if( $main_color = \Glozin\Helper::get_option('header_mobile_main_text_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-main { --gz-header-color: ' . $main_color . '; --gz-header-sticky-color: ' . $main_color . '; color: ' . $main_color . '; }';
		}
		if( $main_border_color = \Glozin\Helper::get_option('header_mobile_main_border_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-main { --gz-header-mobile-main-border-color: ' . $main_border_color . '; }';
		}
		if( $main_shadow_color = \Glozin\Helper::get_option('header_mobile_main_shadow_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-main { --gz-header-mobile-main-shadow-color: ' . $main_shadow_color . '; }';
		}

		if( $bottom_background_color = \Glozin\Helper::get_option('header_mobile_bottom_background_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-bottom { --gz-header-bottom-bg-color: ' . $bottom_background_color . '; --gz-header-sticky-bg-color: ' . $bottom_background_color . '; }';
		}
		if( $bottom_color = \Glozin\Helper::get_option('header_mobile_bottom_text_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-bottom { --gz-header-color: ' . $bottom_color . '; --gz-header-sticky-color: ' . $bottom_color . '; color: ' . $bottom_color . '; }';
		}
		if( $bottom_border_color = \Glozin\Helper::get_option('header_mobile_bottom_border_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-bottom { --gz-header-mobile-bottom-border-color: ' . $bottom_border_color . '; }';
		}
		if( $bottom_shadow_color = \Glozin\Helper::get_option('header_mobile_bottom_shadow_color') ) {
			$static_css .= '.site-header__mobile .header-mobile-bottom { --gz-header-mobile-bottom-shadow-color: ' . $bottom_shadow_color . '; }';
		}

		return $static_css;
	}

	/**
	 * Topbar static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function campaign_bar_static_css() {
		$static_css = '';

		$campaign_bar_width = Helper::get_option( 'campaign_bar_width' );
		if ( $campaign_bar_width != 550 ) {
			$static_css .= '.campaign-bar-type--slides { --gz-campaign-bar-width: ' . $campaign_bar_width . 'px }';
		}

		if( $background_color = \Glozin\Helper::get_option('campaign_background_color') ) {
			$static_css .= '.campaign-bar { --gz-campaign-background: ' . $background_color . '; }';
		}

		if( $color = \Glozin\Helper::get_option('campaign_color') ) {
			$static_css .= '.campaign-bar { --gz-campaign-text-color: ' . $color . '; }';
			$static_css .= '.campaign-bar-type--slides .swiper .swiper-button-text { --gz-arrow-color: ' . $color . '; }';
		}

		if( $color = \Glozin\Helper::get_option('campaign_hover_color') ) {
			$static_css .= '.campaign-bar-type--slides .swiper .swiper-button-text { --gz-arrow-color-hover: ' . $color . '; }';
		}

		return $static_css;
	}

	/**
	 * Topbar static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function topbar_static_css() {
		$static_css = '';

		if( $background_color = \Glozin\Helper::get_option('topbar_background_color') ) {
			$static_css .= '.topbar { --gz-background-color: ' . $background_color . '; }';
		}

		if( $color = \Glozin\Helper::get_option('topbar_color') ) {
			$static_css .= '.topbar { --gz-text-color: ' . $color . ';}';
			$static_css .= '.topbar-slides .swiper .swiper-button-text { --gz-arrow-color: ' . $color . '; }';
		}

		if( $color = \Glozin\Helper::get_option('topbar_hover_color') ) {
			$static_css .= '.topbar { --gz-text-hover-color: ' . $color . ';}';
			$static_css .= '.topbar-slides .swiper .swiper-button-text { --gz-arrow-color-hover: ' . $color . '; }';
		}

		if ( intval( \Glozin\Helper::get_option('topbar_border') ) ) {
			$static_css .= '.topbar { --gz-topbar-border-width: 1px;}';
			if( $color = \Glozin\Helper::get_option('topbar_border_color') ) {
				$static_css .= '.topbar { --gz-topbar-border-color: ' . $color . ';}';
			}
		}

		$mobile_topbar_breakpoint = \Glozin\Helper::get_option( 'mobile_topbar_breakpoint' );
		$mobile_topbar_breakpoint = ! empty( $mobile_topbar_breakpoint ) ? $mobile_topbar_breakpoint : 1024;

		if ( intval( $mobile_topbar_breakpoint ) ) {
			$static_css .= '@media (max-width: '. ( $mobile_topbar_breakpoint ) .'px) {
				.topbar:not(.topbar-mobile) {
					display: none;
				}
				.topbar-mobile .topbar-items {
					flex: 0 1 auto;
				}
				.topbar-mobile--keep-left .topbar-right-items {
					display: none;
				}
				.topbar-mobile--keep-left .topbar-container {
					justify-content: center;
				}
				.topbar-mobile--keep-right .topbar-left-items {
					display: none;
				}
				.topbar-mobile--keep-right .topbar-container {
					justify-content: center;
				}
				.topbar-mobile--keep-all .topbar-container {
					overflow: hidden;
					overflow-x: auto;
				}
				.topbar-mobile--keep-left .topbar-slides,
				.topbar-mobile--keep-right .topbar-slides {
					max-width: 100vw;
					text-align: center;
				}
			}';

			$static_css .= '@media (min-width: '. ( $mobile_topbar_breakpoint + 1 ) .'px) and (max-width: 1300px) {
				.topbar-items { flex: none; }
			}';
		}

		return $static_css;
	}

	/**
	 * Blog Header static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function page_header_static_css() {
		$static_css = '';

		if( Helper::get_option('page_header') && is_page() ) {
			if( $bg_image = Helper::get_option( 'page_header_background_image' ) ) {
				$static_css .= '.page-header {background-image: url(' . esc_url( $bg_image ) . ');}';
			}

			if( $bg_overlay = Helper::get_option( 'page_header_background_overlay' ) ) {
				$static_css .= '.page-header {--gz-page-header-background-overlay: ' . $bg_overlay . ';}';
			}

			if ( ( $color = Helper::get_option( 'page_header_title_color' ) ) && $color != '' ) {
				$static_css .= '.page-header .page-header__title {color: ' . $color . ';}';
			}

			if ( ( $color = Helper::get_option( 'page_header_breadcrumb_link_color' ) ) && $color != '' ) {
				$static_css .= '.page-header .site-breadcrumb {--gz-site-breadcrumb-link-color: ' . $color . ';}';
			}

			if ( ( $color = Helper::get_option( 'page_header_breadcrumb_color' ) ) && $color != '' ) {
				$static_css .= '.page-header .site-breadcrumb {--gz-site-breadcrumb-color: ' . $color . ';}';
			}

			if ( ( $color = Helper::get_option( 'page_header_description_color' ) ) && $color != '' ) {
				$static_css .= '.page-header .page-header__description {color: ' . $color . ';}';
			}

		}

		return $static_css;
	}


	/**
	 * Blog Header static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function blog_header_static_css() {
		$static_css = '';

		if( Helper::get_option('blog_header') && ( Helper::is_blog() || (is_search() && 'product' != get_query_var('post_type') ) ) ) {
			if( $bg_image = Helper::get_option( 'blog_header_background_image' ) ) {
				$static_css .= '.page-header.page-header--blog {background-image: url(' . esc_url( $bg_image ) . ');}';
			}

			if( $bg_overlay = Helper::get_option( 'blog_header_background_overlay' ) ) {
				$static_css .= '.page-header.page-header--blog {--gz-page-header-background-overlay: ' . $bg_overlay . ';}';
			}

			if ( ( $color = Helper::get_option( 'blog_header_title_color' ) ) && $color != '' ) {
				$static_css .= '.page-header.page-header--blog .page-header__title {color: ' . $color . ';}';
			}

			if ( ( $color = Helper::get_option( 'blog_header_breadcrumb_link_color' ) ) && $color != '' ) {
				$static_css .= '.page-header.page-header--blog .site-breadcrumb {--gz-site-breadcrumb-link-color: ' . $color . ';}';
			}

			if ( ( $color = Helper::get_option( 'blog_header_breadcrumb_color' ) ) && $color != '' ) {
				$static_css .= '.page-header.page-header--blog .site-breadcrumb {--gz-site-breadcrumb-color: ' . $color . ';}';
			}

			if ( ( $color = Helper::get_option( 'blog_header_description_color' ) ) && $color != '' ) {
				$static_css .= '.page-header.page-header--blog .page-header__description {color: ' . $color . ';}';
			}
		}

		return $static_css;
	}

	/**
	 * Images static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function images_static_css() {
		$static_css = '';

		switch( Helper::get_option('image_rounded_shape') ) {
			case 'square':
				$static_css .= '--gz-image-rounded: 0;';
				break;
			case 'custom':
				if( $number = Helper::get_option('image_rounded_number')) {
					$static_css .= '--gz-image-rounded:' . $number . 'px;';
				}
				break;
		}

		$static_css = $static_css ? ':root{' . $static_css . '}' : '';

		return $static_css;
	}

	public function post_thumbnail_static_css() {
		$static_css = '';

		switch( Helper::get_option('image_rounded_shape_post_card') ) {
			case 'square':
				$static_css .= '--gz-image-rounded: 0;';
				break;
			case 'custom':
				if( $number = Helper::get_option('image_rounded_number_post_card')) {
					$static_css .= '--gz-image-rounded:' . $number . 'px;';
				}
				break;
		}

		$static_css = $static_css ? '.post-thumbnail{' . $static_css . '}' : '';

		return $static_css;
	}

	/**
	 * Form Fields static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function form_fields_static_css() {
		$static_css = '';

		if( Helper::get_option('form_fields_rounded_shape') == 'round' ) {
			$static_css .= '--gz-input-rounded: 5px;';
		} else if( Helper::get_option('form_fields_rounded_shape') == 'square' ) {
			$static_css .= '--gz-input-rounded: 0;';
		} else if( Helper::get_option('form_fields_rounded_shape') == 'custom' ) {
			if( $number = Helper::get_option('form_fields_rounded_number')) {
				$static_css .= '--gz-input-rounded:' . $number . 'px;';
			}
		}

		if( $bg_color = Helper::get_option('form_fields_bg_color')) {
			$static_css .= '--gz-input-bg-color:' . $bg_color . ';';
		}

		if( $color = Helper::get_option('form_fields_color')) {
			$static_css .= '--gz-input-color:' . $color . ';';
		}

		if( $border_color = Helper::get_option('form_fields_border_color')) {
			$static_css .= '--gz-input-border-color:' . $border_color . ';';
		}

		if( $border_hover_color = Helper::get_option('form_fields_hover_border_color')) {
			$static_css .= '--gz-input-border-color-hover:' . $border_hover_color . ';';
		}

		$static_css = $static_css ? ':root{' . $static_css . '}' : '';

		return $static_css;
	}

	/**
	 * Buttons static css
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function buttons_static_css() {
		$static_css = '';

		if( Helper::get_option('button_rounded_shape') == 'round' ) {
			$static_css .= '--gz-button-rounded: 5px;';
		} else if( Helper::get_option('button_rounded_shape') == 'square' ) {
			$static_css .= '--gz-button-rounded: 0;';
		} else if( Helper::get_option('button_rounded_shape') == 'custom' ) {
			if( $number = Helper::get_option('button_rounded_number')) {
				$static_css .= '--gz-button-rounded:' . $number . 'px;';
			}
		}

		// Solid dark
		$args = array (
			'color' => Helper::get_option('button_solid_dark_color'),
			'color_hover' => Helper::get_option('button_solid_dark_hover_color'),
			'bg_color' => Helper::get_option('button_solid_dark_bg_color'),
			'bg_color_hover' => Helper::get_option('button_solid_dark_hover_bg_color'),
			'eff_bg_color_hover' => Helper::get_option('button_solid_dark_eff_hover_bg_color')
		);
		$static_css .= $this->get_button_color($args);

		$static_css = $static_css ? ':root {' . $static_css . '}' : '';

		// Solid Light
		$args = array (
			'color' => Helper::get_option('button_solid_light_color'),
			'color_hover' => Helper::get_option('button_solid_light_hover_color'),
			'bg_color' => Helper::get_option('button_solid_light_bg_color'),
			'bg_color_hover' => Helper::get_option('button_solid_light_hover_bg_color'),
			'eff_bg_color_hover' => Helper::get_option('button_solid_light_eff_hover_bg_color')
		);
		$color_css = $this->get_button_color( $args );

		$static_css .= $color_css ? '.gz-button-light{' . $color_css . '}' : '';


		// Outline
		$args = array (
			'color' => Helper::get_option('button_outline_color'),
			'color_hover' => Helper::get_option('button_outline_hover_color'),
			'border_color' => Helper::get_option('button_outline_border_color'),
			'bg_color_hover' => Helper::get_option('button_outline_hover_bg_color'),
			'border_color_hover' => Helper::get_option('button_outline_hover_border_color')
		);
		$color_css = $this->get_button_color( $args );

		$static_css .= $color_css ? '.gz-button-outline{' . $color_css . '}' : '';
		$static_css .= $color_css ? '.single-product div.product .product-featured-icons--mobile .gz-button-outline.gz-button-icon{' . $color_css . '}' : '';
		$static_css .= $color_css ? '.single-product div.product form.cart .product-featured-icons .gz-button-outline.gz-button-icon{' . $color_css . '}' : '';

		// Outline Dark
		$args = array (
			'color' => Helper::get_option('button_outline_dark_color'),
			'color_hover' => Helper::get_option('button_outline_dark_hover_color'),
			'border_color' => Helper::get_option('button_outline_dark_border_color'),
			'bg_color_hover' => Helper::get_option('button_outline_dark_hover_bg_color'),
			'border_color_hover' => Helper::get_option('button_outline_dark_hover_border_color'),
			'eff_bg_color_hover' => Helper::get_option('button_outline_dark_eff_hover_bg_color')
		);
		$color_css = $this->get_button_color( $args );

		$static_css .= $color_css ? '.gz-button-outline-dark{' . $color_css . '}' : '';

		if( Helper::get_option('button_outline_dark_eff_hover_bg_color_select') == 'no' ) {
			$static_css .= '.gz-button-outline-dark.gz-button-hover-effect:not(.loading)::before, .gz-button-outline-dark.gz-button-hover-effect:not(.loading)::after {display: none;}';
		}
		// Underline
		$args = array (
			'color' => Helper::get_option('button_underline_color'),
			'color_hover' => Helper::get_option('button_underline_hover_color'),
		);
		$color_css = $this->get_button_color( $args );

		$static_css .= $color_css ? '.gz-button-subtle{' . $color_css . '}' : '';

		// Text
		$args = array (
			'color' => Helper::get_option('button_text_color'),
			'color_hover' => Helper::get_option('button_text_hover_color'),
		);
		$color_css = $this->get_button_color( $args );

		$static_css .= $color_css ? '.gz-button-text{' . $color_css . '}' : '';

		return $static_css;
	}

	protected function get_button_color($args) {
		$static_css = '';
		$static_css .= ! empty($args['color']) ? '--gz-button-color:' . $args['color'] . ';' : '';
		$static_css .= ! empty($args['color_hover']) ? '--gz-button-color-hover:' . $args['color_hover'] . ';' : '';
		$static_css .= ! empty($args['bg_color']) ? '--gz-button-bg-color:' . $args['bg_color'] . ';' : '';
		$static_css .= ! empty($args['bg_color_hover']) ? '--gz-button-bg-color-hover:' . $args['bg_color_hover'] . ';' : '';
		$static_css .= ! empty($args['eff_bg_color_hover']) ? '--gz-button-eff-bg-color-hover:' . $args['eff_bg_color_hover'] . ';' : '';
		$static_css .= ! empty($args['border_color']) ? '--gz-button-border-color:' . $args['border_color'] . ';' : '';
		$static_css .= ! empty($args['border_color_hover']) ? '--gz-button-border-color-hover:' . $args['border_color_hover'] . ';' : '';

		return $static_css;
	}
}
