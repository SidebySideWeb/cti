<?php
/**
 * Elementor Global init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin\Addons\Elementor\Page_Settings;

use \Elementor\Controls_Manager;

/**
 * Integrate with Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Page Settings
	 *
	 * @var $page_settings
	 */
	private static $page_settings;


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

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter('glozin_get_campaign_bar', array( $this, 'get_campaign_bar' ));
		add_filter('glozin_get_topbar', array( $this, 'get_topbar' ));
		add_filter('glozin_get_header_layout', array( $this, 'get_header_layout' ));
		add_filter('body_class', array( $this, 'get_body_classes' ));

		add_filter( 'glozin_header_full_width', array( $this, 'get_container_class' ) );

		add_filter('glozin_get_page_header_elements', array( $this, 'get_page_header_elements' ));

		add_filter('glozin_header_logo_type', array( $this, 'get_header_logo_type' ));
		add_filter('glozin_header_logo', array( $this, 'get_header_logo' ), 20, 2);
		add_filter('glozin_header_logo_light', array( $this, 'get_header_logo_light' ), 20, 2);

	}

	public function get_campaign_bar($value) {
		$page_settings = $this->get_page_settings();
		if( isset($page_settings['hide_campaign_bar_section']) && $page_settings['hide_campaign_bar_section'] == 'yes') {
			return false;
		} 

		return $value;
	}

	public function get_topbar($value) {
		$page_settings = $this->get_page_settings();
		if( isset($page_settings['hide_topbar_section']) && $page_settings['hide_topbar_section'] == 'yes') {
			return false;
		} 

		return $value;
	}

	public function get_header_layout( $header_layout ) {
		$page_settings = $this->get_page_settings();
		if( isset($page_settings['hide_header_section']) && $page_settings['hide_header_section'] == 'yes') {
			$header_layout = '';
		} elseif( isset($page_settings['header_layout']) &&  ! empty($page_settings['header_layout'])) {
			$header_layout = $page_settings['header_layout'];
		}

		return $header_layout;
	}


	public function get_container_class( $header_full_width ) {
		$page_settings = $this->get_page_settings();

		if( isset($page_settings['header_container']) &&  ! empty( $page_settings['header_container'] ) ) {
			$header_container = $page_settings['header_container'];
			if( $header_container == 'fullwidth' ) {
				$header_full_width = true;
			} elseif ( $header_container == 'container' ) {
				$header_full_width = false;
			}
		}

		return $header_full_width;
	}

	public function get_header_logo_type($logo_type) {
		$page_settings = $this->get_page_settings();

		if( isset($page_settings['header_logo_type']) ) {
			$logo_type = $page_settings['header_logo_type'];
		}

		return $logo_type;
	}

	public function get_header_logo($logo, $logo_type) {
		$page_settings = $this->get_page_settings();

		if ( 'text' == $logo_type ) {
			if( isset($page_settings['header_logo_text']) &&  ! empty($page_settings['header_logo_text'])) {
				$logo = $page_settings['header_logo_text'];
			}
		} elseif ( 'svg_upload' == $logo_type ) {
			if( isset($page_settings['header_logo_svg']) &&  ! empty($page_settings['header_logo_svg'])) {
				$logo = \Elementor\Icons_Manager::try_get_icon_html( $page_settings['header_logo_svg'], [ 'aria-hidden' => 'true' ] );
			}

		} elseif ( 'image' == $logo_type ) {
			if( isset($page_settings['header_logo_image']) &&  ! empty($page_settings['header_logo_image'])) {
				$header_logo = $page_settings['header_logo_image'];
				$logo = $header_logo && ! empty( $header_logo['url'] ) ? $header_logo['url'] : $logo;
			}
		}

		return $logo;
	}

	public function get_header_logo_light($logo, $logo_type) {
		$page_settings = $this->get_page_settings();

		if ( 'image' == $logo_type ) {
			if( isset($page_settings['header_logo_image_light']) &&  ! empty($page_settings['header_logo_image_light'])) {
				$header_logo = $page_settings['header_logo_image_light'];
				$logo = $header_logo && ! empty( $header_logo['url'] ) ? $header_logo['url'] : $logo;
			}
		}

		return $logo;
	}

	public function get_body_classes( $classes ) {
		$page_settings = $this->get_page_settings();
		if( isset($page_settings['header_background']) &&  ! empty($page_settings['header_background'])) {
			$classes[] = 'header-transparent';

			if( isset($page_settings['header_text_color']) &&  ! empty($page_settings['header_text_color'])) {
				$classes[] = 'header-transparent-text-' . $page_settings['header_text_color'];
			}
		}

		return $classes;
	}

	public function get_page_header_elements( $items ) {
		$page_settings = $this->get_page_settings();
		if( isset($page_settings['hide_page_header']) &&  $page_settings['hide_page_header'] == 'yes' ) {
			return [];
		}

		if( isset($page_settings['hide_page_header_title']) &&  $page_settings['hide_page_header_title'] == 'yes' ) {
			$key = array_search( 'title', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		if( isset($page_settings['hide_page_header_breadcrumb']) &&  $page_settings['hide_page_header_breadcrumb'] == 'yes' ) {
			$key = array_search( 'breadcrumb', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		if( isset($page_settings['hide_page_header_description']) &&  $page_settings['hide_page_header_description'] == 'yes' ) {
			$key = array_search( 'description', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		return $items;
	}

	public function get_page_settings() {
		if( isset( self::$page_settings )  ) {
			return self::$page_settings;
		}

		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		if( empty( $page_settings_manager ) ) {
			return;
		}
		$page_settings_model = $page_settings_manager->get_model( get_the_ID() );
		self::$page_settings = $page_settings_model->get_data('settings');

		return self::$page_settings;
	}


	
}
