<?php

namespace Glozin\Addons\Modules\Popup;
use Elementor\Core\Files\CSS\Post as Post_CSS;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class FrontEnd {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Post IDs
	 *
	 * @var $post_ids
	 */
	private static $post_ids;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	const POST_TYPE     = 'glozin_popup';

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'glozin_after_site', array($this, 'popup' ) );

		add_action('glozin_after_enqueue_style', array($this, 'popup_inline_style' ) );

	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-popup', GLOZIN_ADDONS_URL . 'modules/popup/assets/frontend' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_enqueue_script( 'glozin-popup', GLOZIN_ADDONS_URL . 'modules/popup/assets/frontend' . $debug . '.js', array( 'jquery' ), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );

	}

	/**
	 * Add the popup HTML to footer
	 *
	 * @since 2.0
	 */
	public function popup() {
		if( ! apply_filters( 'glozin_get_popup', true ) ) {
			return;
		}

		if (is_customize_preview()) {
			return;
		}

		if( is_singular('glozin_popup') ) {
			return;
		}

		if( ! empty($_GET['elementor-preview']) ){
			return;
		}

		if( ! class_exists('\Elementor\Core\Settings\Manager') && ! method_exists('\Elementor\Core\Settings\Manager', 'get_settings_managers') ) {
			return;
		}

		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

		$popup_ids =(array) $this->get_popup_ids();

		if( empty($popup_ids) || empty($popup_ids[0]) ) {
			return;
		}

		foreach( $popup_ids as $popup_id) {
			// Get the settings model for current post
			$page_settings_model = $page_settings_manager->get_model( $popup_id );
			$page_settings = $page_settings_model->get_data( 'settings' );
			$frequency = isset($page_settings['popup_frequency']) ? $page_settings['popup_frequency'] : '1';
			$type = isset($page_settings['popup_display_type']) ? $page_settings['popup_display_type'] : 'popup';

			$popup_cookie = !empty( $_COOKIE['glozin_popup_' . $popup_id] ) ? $_COOKIE['glozin_popup_' . $popup_id] : '';
			if( intval($frequency) > 0 && $popup_cookie ) {
				continue;
			}

			$post_content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($popup_id);
			if( empty($post_content) ) {
				continue;
			}

			$data_options = array();

			$visible =  isset($page_settings['popup_visible']) ? $page_settings['popup_visible'] : 'loaded';

			$seconds =  isset($page_settings['popup_seconds']) ? $page_settings['popup_seconds'] : '5';

			$data_options['post_ID'] = $popup_id;
			$data_options['visiblle'] = $visible;
			$data_options['seconds'] = $seconds;
			$data_options['frequency'] = $frequency;

			$position =  isset($page_settings['popup_position']) ? $page_settings['popup_position'] : 'center';

			$css_classes = 'glozin-popup';
			$css_classes .= ' glozin-popup-' . $popup_id;
			$css_classes .= ' glozin-popup-type--' .  $type;
			$css_classes .= ' glozin-popup-visible--' .  $visible;

			if ( $type == 'slide' ) {
				$css_classes .= ' offscreen-panel';

				$css_classes .= is_rtl() ? ' offscreen-panel--side-left' : ' offscreen-panel--side-right';

				$html = '<div id="glozin_popup_'. $popup_id .'" class="' . esc_attr( $css_classes ) . '" data-options="' . esc_attr(json_encode( $data_options )) . '">';
				$html .= '<div class="glozin-popup__backdrop panel__backdrop"></div>';
				$html .= '<div class="glozin-popup__content panel__container">';
				$html .= '<div class="panel__header">';
				$html .= \Glozin\Addons\Helper::get_svg( 'close', 'ui', 'class=glozin-popup__close panel__button-close position-absolute top-25 end-25 z-1' );
				$html .= '</div>';
				$html .= '<div class="panel__content">'. $post_content .'</div>';
				$html .= '</div>';
				$html .= '</div>';
			} else {
				$css_classes .= ' modal';
				$css_classes .= ' glozin-popup-position--' .  $position;

				$html = '<div id="glozin_popup_'. $popup_id .'" class="' . esc_attr( $css_classes ) . '" data-options="' . esc_attr(json_encode( $data_options )) . '">';
				$html .= '<div class="glozin-popup__backdrop modal__backdrop"></div>';
				$html .= '<div class="glozin-popup__content modal__container">';
				$html .= '<div class="glozin-popup__wrapper modal__wrapper">';
				$html .= \Glozin\Addons\Helper::get_svg( 'close', 'ui', 'class=glozin-popup__close modal__button-close position-absolute top-20 end-20 z-1' );

				$html .= '<div class="modal__content">'. $post_content .'</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}

			echo $html;
		}

	}

	/**
	 * Get product tab ids
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_popup_ids() {
		if( isset( self::$post_ids ) ) {
			return self::$post_ids;
		}
		$current_page = \Glozin\Addons\Helper::get_post_ID();
		$posts = new \WP_Query( array(
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => '5',
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby' 		=> 'menu_order',
			'order' 		=> 'DESC',
			'suppress_filters'       => false,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					array(
						'key' => 'enable_popup',
						'value' => 'yes',
						'compare' => '==',
					),
				),
				array(
					'relation' => 'OR',
					array(
						'key' => 'popup_include_pages',
						'value' => $current_page,
						'compare' => 'LIKE',
					),
					array(
						'key' => 'popup_include_pages',
						'value'   => array('0'),
						'compare' => 'IN',
					),
					array(
						'key' => 'popup_include_pages',
						'compare' => 'NOT EXISTS',
					)
				),
				array(
					'relation' => 'OR',
					array(
						'key' => 'popup_exclude_pages',
						'value' => $current_page,
						'compare' => 'NOT LIKE',
					),
					array(
						'key' => 'popup_exclude_pages',
						'compare' => 'NOT EXISTS',
					)
				),
			)
		) );
		wp_reset_postdata();
		self::$post_ids = $posts->posts;
		return self::$post_ids;
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function popup_inline_style() {
		if( ! apply_filters( 'glozin_get_popup', true ) ) {
			return;
		}

		if( is_singular('glozin_popup') ) {
			return;
		}

		if( ! class_exists('\Elementor\Core\Settings\Manager') && ! method_exists('\Elementor\Core\Settings\Manager', 'get_settings_managers') ) {
			return;
		}

		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

		$popup_ids = (array) $this->get_popup_ids();

		if( empty($popup_ids) || empty($popup_ids[0]) ) {
			return;
		}
		$css_content = '';
		foreach( $popup_ids as $popup_id) {
			// Get the settings model for current post
			$page_settings_model = $page_settings_manager->get_model( $popup_id );

			$page_settings = $page_settings_model->get_data( 'settings' );
			$frequency = isset($page_settings['popup_frequency']) ? $page_settings['popup_frequency'] : '1';

			$popup_cookie = !empty( $_COOKIE['glozin_popup_' . $popup_id] ) ? $_COOKIE['glozin_popup_' . $popup_id] : '';
			if( intval($frequency) > 0 && $popup_cookie ) {
				continue;
			}

			$width =  isset($page_settings['popup_width']) ? $page_settings['popup_width'] : '';
			if( !empty($width ) ) {
				$css_content .= '.glozin-popup-' . $popup_id . ' .glozin-popup__content{max-width:' . $width['size'] . $width['unit'] . ';}' ;
			}

			$background_color =  isset($page_settings['popup_background_color']) ? $page_settings['popup_background_color'] : '';
			if( !empty($background_color ) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__wrapper{background-color:' . $background_color . ';}';
			}

			$border_radius =  isset($page_settings['popup_border_radius']) ? $page_settings['popup_border_radius'] : '';		
			if( !empty($border_radius ) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__wrapper{border-radius:' . $border_radius['top'] . 'px ' . $border_radius['right'] . 'px ' . $border_radius['bottom'] . 'px ' . $border_radius['left'] . 'px;}';
			}

			$close_color =  isset($page_settings['popup_close_color']) ? $page_settings['popup_close_color'] : '';
			$close_color_hover =  isset($page_settings['popup_close_color_hover']) ? $page_settings['popup_close_color_hover'] : '';
			if( !empty($close_color ) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__close{color:' . $close_color . ';}';
			}
			if( !empty($close_color_hover ) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__close:hover{color:' . $close_color_hover . ';}';
			}

			$close_size =  isset($page_settings['popup_close_size']) ? $page_settings['popup_close_size'] : '';
			if( !empty($close_size ) && !empty($close_size['size']) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__close{font-size:' . $close_size['size'] . $close_size['unit'] . ';}';
			}

			$close_position_top =  isset($page_settings['popup_close_position_top']) ? $page_settings['popup_close_position_top'] : '';
			if( !empty($close_position_top ) && !empty($close_position_top['size']) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__close{top:' . $close_position_top['size'] . $close_position_top['unit'] . ';}';
			}

			$close_position_right =  isset($page_settings['popup_close_position_right']) ? $page_settings['popup_close_position_right'] : '';
			if( !empty($close_position_right ) ) {
				$css_content .=  '.glozin-popup-' . $popup_id . ' .glozin-popup__close{right:' . $close_position_right['size'] . $close_position_right['unit'] . ';}';
			}

			$css_file = '';
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( $popup_id );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new \Elementor\Post_CSS_File( $popup_id );
			}

			if( $css_file ) {
				$css_file->enqueue();
			}

		}
		if( ! empty($css_content) ) {
			wp_add_inline_style( 'glozin', $css_content );
		}

	}

}