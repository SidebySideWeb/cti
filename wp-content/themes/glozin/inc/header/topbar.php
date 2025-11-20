<?php
/**
 * Posts functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Header;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts initial
 *
 */
class Topbar {
	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Custom template tags of header
	 *
	 * @package Glozin
	 *
	 * @since 1.0.0
	 *
	 * @param $items
	 */
	public static function items( $items ) {
		if ( empty( $items ) ) {
			return;
		}

		$args = [];

		foreach ( $items as $item ) {
			$item['item'] = $item['item'] ? $item['item'] : key( \Glozin\Options::topbar_items_option() );

			switch ( $item['item'] ) {
				case 'language':
					get_template_part( 'template-parts/header/language' );
					break;

				case 'currency':
					get_template_part( 'template-parts/header/currency' );
					break;

				case 'slides':
					$args = array(
						'slidesPerView' => array(
							'desktop' => 1,
							'tablet' => 1,
							'mobile' => 1,
						),
						'spaceBetween' => array(
							'desktop' => 15,
							'tablet' => 15,
							'mobile' => 15,
						),
						'loop' => true,
						'autoplay' => true,
						'speed' => 400,
					);

					get_template_part( 'template-parts/header/slides', '', $args );
					break;

				case 'menu':
					if ( ! empty( \Glozin\Helper::get_option( 'topbar_menu' ) ) ) {
						wp_nav_menu( array(
							'theme_location' 	=> '__no_such_location',
							'menu'           	=> Helper::get_option('topbar_menu'),
							'container'      	=> 'nav',
							'container_id'   	=> 'topbar-menu',
							'container_class'   => 'topbar-navigation topbar-menu',
							'menu_class'     	=> 'nav-menu menu',
							'depth'          	=> 1,
						) );
					}
					break;

				case 'custom-html':
					$topbar_custom_html = \Glozin\Helper::get_option('topbar_custom_html');
					if( empty( $topbar_custom_html ) ) {
						break;
					}
					echo '<div class="topbar-custom-html">';
					echo do_shortcode( wp_kses_post( $topbar_custom_html ) );
					echo '</div>';
					break;

				default:
					do_action( 'glozin_header_topbar_item', $item['item'] );
					break;
			}
		}
	}

	/**
	 * Display slides item.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items
	 */
	public static function slides() {
		$slides = (array) \Glozin\Helper::get_option( 'topbar_slides' );

		$slides = apply_filters('glozin_topbar_slides', $slides);

		if( empty( $slides ) ) {
			return;
		}

		foreach ( $slides as $item ) {
			echo '<div class="topbar-slides__item swiper-slide">' . $item['text'] . '</div>';
		}
	}
}
