<?php

/**
 * Theme Options
 *
 * @package Glozin
 */

namespace Glozin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class Options {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * $glozin_customize
	 *
	 * @var $glozin_customize
	 */
	protected static $glozin_customize = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self:: $instance;
	}

	/**
	 * The class constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {
		add_filter('glozin_customize_config', array($this, 'customize_settings'));
		self::$glozin_customize = \Glozin\Customizer::instance();
	}

	/**
	 * This is a short hand function for getting setting value from customizer
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function get_option($name) {
		if ( is_object( self::$glozin_customize ) ) {
			$value = self::$glozin_customize->get_option( $name );
		} elseif (false !== get_theme_mod($name)) {
			$value = get_theme_mod($name);
		} else {
			$value = $this->get_option_default($name);
		}
		return apply_filters('glozin_get_option', $value, $name);
	}

	/**
	 * Get default option values
	 *
	 * @since 1.0.0
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function get_option_default($name) {
		if ( is_object( self::$glozin_customize ) ) {
			return self::$glozin_customize->get_option_default( $name );
		}

		$config   = $this->customize_settings();
		$settings = array_reduce( $config['settings'], 'array_merge', array() );

		if ( ! isset( $settings[ $name ] ) ) {
			return false;
		}

		return isset( $settings[ $name ]['default'] ) ? $settings[ $name ]['default'] : false;
	}

	/**
	 * Options of topbar items
	 *
	 * @return array
	 */
	public static function topbar_items_option() {
		return apply_filters( 'glozin_topbar_items_option', array(
			''     			    => esc_html__( 'Select an Item', 'glozin' ),
			'language' 			=> esc_html__( 'Language', 'glozin' ),
			'currency' 			=> esc_html__( 'Currency', 'glozin' ),
			'slides'        	=> esc_html__( 'Slides', 'glozin' ),
			'menu'        		=> esc_html__( 'Menu', 'glozin' ),
			'custom-html'    	=> esc_html__( 'Custom HTML', 'glozin' ),
		) );
	}

	/**
	 * Options of header items
	 *
	 * @return array
	 */
	public static function header_items_option() {
		return apply_filters( 'glozin_header_items_option', array(
			''     			 => esc_html__( 'Select an Item', 'glozin' ),
			'logo'           => esc_html__( 'Logo', 'glozin' ),
			'primary-menu'   => esc_html__( 'Primary Menu', 'glozin' ),
			'secondary-menu' => esc_html__( 'Secondary Menu', 'glozin' ),
			'search'   		 => esc_html__( 'Search', 'glozin' ),
			'account'   	 => esc_html__( 'Account', 'glozin' ),
			'wishlist'   	 => esc_html__( 'Wishlist', 'glozin' ),
			'compare'   	 => esc_html__( 'Compare', 'glozin' ),
			'cart'   	 	 => esc_html__( 'Cart', 'glozin' ),
			'language'     	 => esc_html__( 'Language', 'glozin' ),
			'currency'     	 => esc_html__( 'Currency', 'glozin' ),
			'custom-html' 	 => esc_html__( 'Custom HTML', 'glozin' ),
		) );
	}
	/**
	 * Options of header items
	 *
	 * @return array
	 */
	public static function header_mobile_items_option() {
		return apply_filters( 'glozin_header_mobile_items_option', array(
			''     			 => esc_html__( 'Select an Item', 'glozin' ),
			'logo'           => esc_html__( 'Logo', 'glozin' ),
			'hamburger'      => esc_html__( 'Hamburger', 'glozin' ),
			'search'         => esc_html__( 'Search', 'glozin' ),
			'cart'           => esc_html__( 'Cart', 'glozin' ),
			'wishlist'       => esc_html__( 'Wishlist', 'glozin' ),
			'compare'        => esc_html__( 'Compare', 'glozin' ),
			'account'        => esc_html__( 'Account', 'glozin' ),
			'custom-html'    => esc_html__( 'Custom HTML', 'glozin' ),
		) );
	}

	/**
	 * Get customize settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function customize_settings() {
		$settings = array(
			'theme' => 'glozin',
		);

		$panels = array(
			'general'    => array(
				'priority' => 10,
				'title'    => esc_html__( 'General', 'glozin' ),
			),
			'styling'    => array(
				'priority' => 15,
				'title'    => esc_html__( 'Styling', 'glozin' ),
			),
			'typography' => array(
				'priority' => 20,
				'title'    => esc_html__( 'Typography', 'glozin' ),
			),
			'header'       => array(
				'priority' => 20,
				'title'    => esc_html__( 'Header', 'glozin' ),
			),
			'page'   => array(
				'title'      => esc_html__('Page', 'glozin'),
				'priority'   => 30,
			),
			'blog'    => array(
				'priority' => 30,
				'title'    => esc_html__( 'Blog', 'glozin' ),
			),
			'mobile' => array(
				'priority'   => 90,
				'title'      => esc_html__('Mobile', 'glozin'),
			),
		);

		$sections = array(
			'maintenance'  => array(
				'title'      => esc_html__('Maintenance', 'glozin'),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
			),
			'color_scheme' => array(
				'title'    => esc_html__('Color Scheme', 'glozin'),
				'panel'    => 'styling',
			),
			'styling_images' => array(
				'title'    => esc_html__('Images', 'glozin'),
				'panel'    => 'styling',
			),
			'styling_buttons' => array(
				'title'    => esc_html__('Buttons', 'glozin'),
				'panel'    => 'styling',
			),
			'styling_form_fields' => array(
				'title'    => esc_html__('Form Fields', 'glozin'),
				'panel'    => 'styling',
			),
			'backtotop' => array(
				'title'    => esc_html__( 'Back To Top', 'glozin' ),
				'panel'    => 'general',
			),
			// Typography
			'typo_font_family'         => array(
				'title'    => esc_html__( 'Font Family', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_main'         => array(
				'title'    => esc_html__( 'Main', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_headings'     => array(
				'title'    => esc_html__( 'Headings', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_header_logo'         => array(
				'title'    => esc_html__( 'Header Logo Text', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_header_menu_primary'       => array(
				'title'    => esc_html__( 'Header Primary Menu', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_page'         => array(
				'title'    => esc_html__( 'Page', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_posts'        => array(
				'title'    => esc_html__( 'Blog', 'glozin' ),
				'panel'    => 'typography',
			),
			'typo_widget'       => array(
				'title'    => esc_html__( 'Widgets', 'glozin' ),
				'panel'    => 'typography',
			),
			// Header
			'header_top'        => array(
				'title'    => esc_html__( 'Topbar', 'glozin' ),
				'panel'    => 'header',
			),
			'header_campaign'   => array(
				'title'    => esc_html__( 'Campaign Bar', 'glozin' ),
				'panel'    => 'header',
			),
			'header_layout'        => array(
				'title'    => esc_html__( 'Header Layout', 'glozin' ),
				'panel'    => 'header',
			),
			'header_main'       => array(
				'title'    => esc_html__( 'Header Main', 'glozin' ),
				'panel'    => 'header',
			),
			'header_bottom'       => array(
				'title'    => esc_html__( 'Header Bottom', 'glozin' ),
				'panel'    => 'header',
			),
			'header_sticky'       => array(
				'title'    => esc_html__( 'Sticky Header', 'glozin' ),
				'panel'    => 'header',
			),
			'header_background'       => array(
				'title'    => esc_html__( 'Header Background', 'glozin' ),
				'panel'    => 'header',
			),
			'header_logo'       => array(
				'title'    => esc_html__( 'Logo', 'glozin' ),
				'panel'    => 'header',
			),
			'header_account'    => array(
				'title'    => esc_html__( 'Account', 'glozin' ),
				'panel'    => 'header',
			),
			'header_wishlist'    => array(
				'title'    => esc_html__( 'Wishlist', 'glozin' ),
				'panel'    => 'header',
			),
			'header_compare'    => array(
				'title'    => esc_html__( 'Compare', 'glozin' ),
				'panel'    => 'header',
			),
			'header_cart'    => array(
				'title'    => esc_html__( 'Cart', 'glozin' ),
				'panel'    => 'header',
			),
			'header_search'    => array(
				'title'    => esc_html__( 'Search', 'glozin' ),
				'panel'    => 'header',
			),
			'header_product_categories'    => array(
				'title'    => esc_html__( 'Product Categories', 'glozin' ),
				'panel'    => 'header',
			),
			'header_custom_html'    => array(
				'title'    => esc_html__( 'Custom HTML', 'glozin' ),
				'panel'    => 'header',
			),
			// Blog
			'post_card'       => array(
				'title'    => esc_html__( 'Post Card Images', 'glozin' ),
				'panel'    => 'blog',
			),
			'blog_header'       => array(
				'title'    => esc_html__( 'Blog Header', 'glozin' ),
				'panel'    => 'blog',
			),
			'blog_page'       => array(
				'title'    => esc_html__( 'Blog Page', 'glozin' ),
				'panel'    => 'blog',
			),
			'blog_single'       => array(
				'title'    => esc_html__( 'Blog Single', 'glozin' ),
				'panel'    => 'blog',
			),
			'share_socials' => array(
				'title'    => esc_html__( 'Share Socials', 'glozin' ),
				'panel'    => 'general',
			),
			// Page
			'page_header'       => array(
				'title'    => esc_html__( 'Page Header', 'glozin' ),
				'panel'    => 'page',
			),
			// Mobile
			'topbar_mobile'        => array(
				'title'    => esc_html__( 'Topbar', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_layout'        => array(
				'title'    => esc_html__( 'Header Layout', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_main'       => array(
				'title'    => esc_html__( 'Header Main', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_bottom'       => array(
				'title'    => esc_html__( 'Header Bottom', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_elements'        => array(
				'title'    => esc_html__( 'Header Elements', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_sticky'       => array(
				'title'    => esc_html__( 'Sticky Header', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_background'       => array(
				'title'    => esc_html__( 'Header Background', 'glozin' ),
				'panel'    => 'mobile',
			),
			'header_mobile_menu'    => array(
				'title'    => esc_html__( 'Header Mobile Menu', 'glozin' ),
				'panel'    => 'mobile',
			),
			'mobile_product_catalog'        => array(
				'title'    => esc_html__( 'Product Catalog', 'glozin' ),
				'panel'    => 'mobile',
			),
			'mobile_product_card'        => array(
				'title'    => esc_html__( 'Product Card', 'glozin' ),
				'panel'    => 'mobile',
			),
			'mobile_single_product'        => array(
				'title'    => esc_html__( 'Single Product', 'glozin' ),
				'panel'    => 'mobile',
			),
		);

		$settings   = array();

		// Maintenance
		$settings['maintenance'] = array(
			'maintenance_enable'             => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable Maintenance Mode', 'glozin'),
				'description' => esc_html__('Put your site into maintenance mode', 'glozin'),
				'default'     => false,
			),
			'maintenance_mode'               => array(
				'type'        => 'radio',
				'label'       => esc_html__('Mode', 'glozin'),
				'description' => esc_html__('Select the correct mode for your site', 'glozin'),
				'tooltip'     => wp_kses_post(sprintf(__('If you are putting your site into maintenance mode for a longer perior of time, you should set this to "Coming Soon". Maintenance will return HTTP 503, Comming Soon will set HTTP to 200. <a href="%s" target="_blank">Learn more</a>', 'glozin'), 'https://yoast.com/http-503-site-maintenance-seo/')),
				'default'     => 'maintenance',
				'choices'     => array(
					'maintenance' => esc_html__('Maintenance', 'glozin'),
					'coming_soon' => esc_html__('Coming Soon', 'glozin'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'maintenance_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'maintenance_page'               => array(
				'type'            => 'dropdown-pages',
				'label'           => esc_html__('Maintenance Page', 'glozin'),
				'default'         => 0,
				'active_callback' => array(
					array(
						'setting'  => 'maintenance_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Color Scheme
		$settings['color_scheme'] = array(
			'primary_color_title'  => array(
				'type'  => 'custom',
				'label' => esc_html__( 'Primary Color', 'glozin' ),
			),
			'primary_color'        => array(
				'type'            => 'color-palette',
				'choices'         => array(
					'colors' => array(
						'#d0473e',
						'#3357d8',
						'#a62658',
						'#0f855b',
						'#0f8482',
						'#197149',
					),
					'style'  => 'round',
				),
				'active_callback' => array(
					array(
						'setting'  => 'primary_color_custom',
						'operator' => '!=',
						'value'    => true,
					),
				),
			),
			'primary_color_custom' => array(
				'type'      => 'checkbox',
				'label'     => esc_html__( 'Pick my favorite color', 'glozin' ),
				'default'   => false,

			),
			'primary_color_custom_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Custom Color', 'glozin' ),
				'default'         => '#d0473e',
				'active_callback' => array(
					array(
						'setting'  => 'primary_color_custom',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'primary_text_color'             => array(
				'type'        => 'select',
				'default'     => false,
				'label'       => esc_html__('Text on Primary Color', 'glozin'),
				'default'         => 'light',
				'choices'         => array(
					'light' 	=> esc_html__( 'Light', 'glozin' ),
					'dark' 	    => esc_html__( 'Dark', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'primary_text_color_custom'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Custom Color', 'glozin' ),
				'default'         => '#fff',
				'active_callback' => array(
					array(
						'setting'  => 'primary_text_color',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'primary_base_color_hr'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'primary_base_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Base Color', 'glozin' ),
				'default'         => '',
			),
			'primary_dark_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Dark Color', 'glozin' ),
				'default'         => '',
			),
			'primary_link_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Link Color', 'glozin' ),
				'default'         => '',
			),
			'primary_link_hover_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Link Hover Color', 'glozin' ),
				'default'         => '',
			),
			'product_card_sale_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Sale Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'transport'       => 'postMessage',
				'js_vars'         => array(
					array(
						'element'  => '.gz-price ins',
						'property' => '--gz-color-price-sale',
					),
					array(
						'element'  => '.price ins',
						'property' => '--gz-color-price-sale',
					),
				),
			),
		);

		$settings['styling_images'] = array(
			'image_rounded_shape'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Round', 'glozin' ),
					'square'  	=> esc_html__( 'Square', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'image_rounded_number'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

		);

		$settings['styling_buttons'] = array(
			'button_rounded_shape'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Circle', 'glozin' ),
					'square'  	=> esc_html__( 'Square', 'glozin' ),
					'round'  	=> esc_html__( 'Round', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'button_rounded_number'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'button_rounded_shape',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'button_eff_hover_bg_disable'       => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Disable Hover Effect', 'glozin' ),
				'default'         => false,
			),
			'button_custom_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'button_solid_dark_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Solid Dark', 'glozin' ),
			),
			'button_solid_dark_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
			),
			'button_solid_dark_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'button_solid_dark_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_solid_dark_eff_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Effect Background Color Hover', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'button_eff_hover_bg_disable',
						'operator' => '==',
						'value'    => false,
					),
				),
			),
			'button_solid_dark_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_custom_hr_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Light
			'button_solid_light_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Solid Light', 'glozin' ),
			),
			'button_solid_light_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
			),
			'button_solid_light_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'button_solid_light_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_solid_light_eff_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Effect Background Color Hover', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'button_eff_hover_bg_disable',
						'operator' => '==',
						'value'    => false,
					),
				),
			),
			'button_solid_light_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_custom_hr_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Outline
			'button_outline_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Outline', 'glozin' ),
			),
			'button_outline_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
			),
			'button_outline_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'button_outline_hover_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_outline_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_outline_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_custom_hr_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Outline
			'button_outline_dark_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Outline Dark', 'glozin' ),
			),
			'button_outline_dark_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
			),
			'button_outline_dark_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'button_outline_dark_hover_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_outline_dark_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_outline_dark_eff_hover_bg_color_select'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Effect Background Color', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 		=> esc_html__( 'Default', 'glozin' ),
					'yes'  	=> esc_html__( 'Yes', 'glozin' ),
					'no'  	=> esc_html__( 'No', 'glozin' ),
				),
			),
			'button_outline_dark_eff_hover_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Effect Background Color Hover', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'button_outline_dark_eff_hover_bg_color_select',
						'operator' => '==',
						'value'    => 'yes',
					),
				),
			),
			'button_outline_dark_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_custom_hr_5'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Underline
			'button_underline_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Underline', 'glozin' ),
			),
			'button_underline_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'button_underline_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'glozin' ),
				'default'         => '',
			),
			'button_custom_hr_6'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			// Button Text
			'button_text_headline' => array(
				'type'            => 'headline',
				'label'           => esc_html__( 'Text', 'glozin' ),
			),
			'button_text_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'button_text_hover_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color Hover', 'glozin' ),
				'default'         => '',
			),
		);

		$settings['styling_form_fields'] = array(
			'form_fields_rounded_shape'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Circle', 'glozin' ),
					'round'  	=> esc_html__( 'Round', 'glozin' ),
					'square'  	=> esc_html__( 'Square', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'form_fields_rounded_number'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'form_fields_rounded_shape',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'form_fields_custom_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'form_fields_bg_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
			),
			'form_fields_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Color', 'glozin' ),
				'default'         => '',
			),
			'form_fields_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
			),
			'form_fields_hover_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color Hover', 'glozin' ),
				'default'         => '',
			),
		);

		// Typography
		// Typography - body.
		$settings['typo_main'] = array(
			'typo_body'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Body', 'glozin' ),
				'description' => esc_html__( 'Customize the body font', 'glozin' ),
				'default'     => array(
					'font-family' => 'Instrument Sans',
					'variant'     => 'regular',
					'font-size'   => '15px',
					'line-height' => '1.6',
					'color'       => '#444',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing'  => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'body',
					),
				),
			),
		);

		$settings['typo_font_family'] = array(
			'typo_font_family'     => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Instrument Sans Font', 'glozin'),
				'description' => esc_html__('Enable this option to load Instrument Sans Font', 'glozin'),
			),
		);


		// Typography - headings.
		$settings['typo_headings'] = array(
			'typo_heading'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading', 'glozin' ),
				'description' => esc_html__( 'Customize the Heading font', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => 'regular',
					'line-height'    => '1.2',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing'  => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
				array(
						'element' => 'h1,h2,h3,h4,h5,h6',
					),
				),
			),
			'typo_heading_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h1'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 1', 'glozin' ),
				'default'     => array(
					'font-size'      => '40px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h1, .h1',
					),
				),
			),
			'typo_heading_hr_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h2'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 2', 'glozin' ),
				'default'     => array(
					'font-size'      => '36px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
				array(
						'element' => 'h2, .h2',
					),
				),
			),
			'typo_heading_hr_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h3'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 3', 'glozin' ),
				'default'     => array(
					'font-size'      => '30px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h3, .h3',
					),
				),
			),
			'typo_heading_hr_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h4'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 4', 'glozin' ),
				'default'     => array(
					'font-size'      => '26px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h4, .h4',
					),
				),
			),
			'typo_heading_hr_5'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h5'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 5', 'glozin' ),
				'default'     => array(
					'font-size'      => '18px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h5, .h5',
					),
				),
			),
			'typo_heading_hr_6'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
			),
			'typo_h6'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 6', 'glozin' ),
				'default'     => array(
					'font-size'      => '16px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h6, .h6',
					),
				),
			),
		);

		// Typography - header primary menu.
		$settings['typo_header_logo'] = array(
			'logo_font'      => array(
				'type'            => 'typography',
				'label'           => esc_html__( 'Logo Font', 'glozin' ),
				'default'         => array(
					'font-family'    => '',
					'variant'		 => '',
					'font-size'      => '',
					'letter-spacing' => '',
					'subsets'        => array( 'latin-ext' ),
					'text-transform' => 'none',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'output'          => array(
					array(
						'element' => '.site-header .header-logo__text',
					),
				),
			),
		);

		// Typography - header primary menu.
		$settings['typo_header_menu_primary'] = array(
			'typo_menu'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Menu', 'glozin' ),
				'description' => esc_html__( 'Customize the menu font', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '15px',
					'line-height' 	 => '1.6667',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.primary-navigation .nav-menu > li > a',
					),
				),
			),
			'typo_submenu'                   => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Sub-Menu', 'glozin' ),
				'description' => esc_html__( 'Customize the sub-menu font', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => 'regular',
					'font-size'      => '15px',
					'line-height' 	 => '1.6667',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.primary-navigation li .menu-item > a, .primary-navigation li .menu-item--widget > a, .primary-navigation .mega-menu ul.mega-menu__column .menu-item--widget-heading a, .primary-navigation li .menu-item > span, .primary-navigation li .menu-item > h6',
					),
				),
			),
		);

		$settings['typo_page'] = array(
			'typo_page_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Title', 'glozin' ),
				'description' => esc_html__( 'Customize the page title font', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '36px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '-1.224px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--page .page-header__title',
					),
				),
			),
		);

		// Typography - posts.
		$settings['typo_posts'] = array(
			'typo_blog_header_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Header Title', 'glozin' ),
				'description' => esc_html__( 'Customize the font of blog header', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '36px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '-1.224px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header--blog .page-header__title',
					),
				),
			),
			'typo_blog_post_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Post Title', 'glozin' ),
				'description' => esc_html__( 'Customize the font of blog post title', 'glozin' ),
				'default'     => array(
					'font-family'    => 'Instrument Sans',
					'variant'        => '600',
					'font-size'      => '20px',
					'line-height'    => '',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
					'letter-spacing' => '-0.68px',
				),
				'choices'   => $this->customizer_fonts_choices(),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.single-post .hentry .entry-header .entry-title',
					),
				),
			),
		);

		$settings['header_top'] = array(
			'topbar'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Topbar', 'glozin' ),
				'description' => esc_html__( 'Display a bar on the top', 'glozin' ),
				'default'     => false,
				'priority' => 5,
			),
			'topbar_fullwidth'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Topbar Full Width', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 5,
			),
			'topbar_custom_hr_1'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 10,
			),
			'topbar_left'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the left side of the topbar', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 15,
			),
			'topbar_right'      => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the right side of the topbar', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 25,
			),
			'topbar_custom_hr_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 30,
			),
			'topbar_slides'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Slides Item', 'glozin' ),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Slide', 'glozin' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'    => 'textarea',
						'label'   => esc_html__( 'Text', 'glozin' ),
						'sanitize_callback' => 'Glozin\Icon::sanitize_svg',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 35,
			),
			'topbar_custom_heading_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Menu Item', 'glozin' ),
				'default'         => '',
				'choices'         => $this->get_menus(),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 40,
			),
			'topbar_custom_html'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Custom HTML', 'glozin' ),
				'description'     => esc_html__( 'Paste your HTML here', 'glozin' ),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 43,
			),
			'topbar_custom_heading_4'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Topbar Background', 'glozin' ) .'</h2>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 45,
			),
			'topbar_background_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Background Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => '--gz-background-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 60,
			),
			'topbar_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => '--gz-text-color',
					),
					array(
						'element'  => '.topbar-slides .swiper .swiper-button-text',
						'property' => '--gz-arrow-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
			'topbar_hover_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Hover Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => '--gz-text-hover-color',
					),
					array(
						'element'  => '.topbar-slides .swiper .swiper-button-text',
						'property' => '--gz-arrow-color-hover',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
			'topbar_border_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Border Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => '--gz-topbar-border-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'topbar_border',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
			'topbar_custom_heading_5'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Topbar Style', 'glozin' ) .'</h2>',
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 70,
			),
			'topbar_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Height', 'glozin'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 42,
					'tablet'  => 42,
					'mobile'  => 42,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 200,
				),
				'output'         => array(
					array(
						'element'  => '.topbar',
						'property' => 'height',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
					array(
						'element'  => '.topbar .topbar-items',
						'property' => 'line-height',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 70,
			),
			'topbar_border'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Border', 'glozin' ),
				'default'     => false,
				'priority' 	=> 75,
				'active_callback' => array(
					array(
						'setting'  => 'topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Header layout settings.
		$settings['header_layout'] = array(
			'header_present' => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Present', 'glozin' ),
				'description' => esc_html__( 'Select a prebuilt header or build your own', 'glozin' ),
				'default'     => 'prebuild',
				'choices'     => array(
					'prebuild' => esc_html__( 'Use pre-build header', 'glozin' ),
					'custom'   => esc_html__( 'Build my own', 'glozin' ),
				),
			),
			'header_version' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Prebuilt Header', 'glozin' ),
				'description'     => esc_html__( 'Select a prebuilt header present', 'glozin' ),
				'default'         => 'v2',
				'choices'         => array(
					'v1'  => esc_html__( 'Header V1', 'glozin' ),
					'v2'  => esc_html__( 'Header V2', 'glozin' ),
					'v3'  => esc_html__( 'Header V3', 'glozin' ),
					'v4'  => esc_html__( 'Header V4', 'glozin' ),
					'v5'  => esc_html__( 'Header V5', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_fullwidth'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Full Width', 'glozin' ),
				'default'     => true,
			),
			'header_element'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_prebuild_currency'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Currency', 'glozin' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_search'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Search', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_account'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Account', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_wishlist'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Wishlist', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_compare'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Compare', 'glozin' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_prebuild_cart'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Cart', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
		);

		// Header main settings.
		$settings['header_main'] = array(
			'header_main_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the left side of header main', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_main_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 10,
			),
			'header_main_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'glozin' ),
				'description'     => esc_html__( 'Control items at the center of header main', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_main_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 15,
			),
			'header_main_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the right of header main', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_main_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 20,
			),
			'header_main_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 25,
			),
			'header_main_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '70',
				'choices'   => array(
					'min' => 50,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop .header-main',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'priority' => 30,
			),
			'header_main_divider'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Divider', 'glozin' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'priority' => 35,
			),
		);

		// Header bottom settings.
		$settings['header_bottom'] = array(
			'header_bottom_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the left side of header bottom', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_bottom_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 10,
			),
			'header_bottom_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'glozin' ),
				'description'     => esc_html__( 'Control items at the center of header bottom', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_bottom_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 15,
			),
			'header_bottom_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the right of header bottom', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_bottom_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Main::instance(), 'render' ),
					),
				),
				'priority' => 20,
			),
			'header_bottom_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 25,
			),
			'header_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 30,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop .header-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'priority' => 30,
			),
			'header_bottom_divider'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Divider', 'glozin' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'priority' => 35,
			),
		);

		// Header sticky settings.
		$settings['header_sticky'] = array(
			'header_sticky'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Sticky Header', 'glozin' ),
				'default'         => false,
			),
			'header_sticky_on'   => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sticky On', 'glozin' ),
				'default'         => 'down',
				'choices'         => array(
					'down' => esc_html__( 'Scroll Down', 'glozin' ),
					'up'   => esc_html__( 'Scroll Up', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'header_sticky_el'   => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sticky Header Section', 'glozin' ),
				'default'         => 'header_main',
				'choices'         => array(
					'header_main'   => esc_html__('Header Main', 'glozin'),
					'header_bottom' => esc_html__('Header Bottom', 'glozin'),
					'both'          => esc_html__('Both', 'glozin'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'header_sticky_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_sticky_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Main Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '85',
				'choices'   => array(
					'min' => 30,
					'max' => 400,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_sticky_el',
						'operator' => '!==',
						'value'    => 'header_bottom',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop.minimized .header-main, .site-header__desktop.headroom--not-top .header-main',
						'property' => 'height',
						'units'    => 'px',
					),
					array(
						'element'  => '.site-header__desktop.minimized .header-sticky + .header-bottom, .site-header__desktop.headroom--not-top .header-sticky + .header-bottom',
						'property' => 'top',
						'units'    => 'px',
					),
				),
			),
			'header_sticky_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Bottom Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '64',
				'choices'   => array(
					'min' => 30,
					'max' => 400,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_sticky_el',
						'operator' => '!==',
						'value'    => 'header_main',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__desktop.minimized .header-bottom, .site-header__desktop.headroom--not-top .header-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
		);

		$settings['header_background'] = array(
			'header_background_heading_1'    => array(
				'type'    => 'custom',
				'default' => '<h2>'. esc_html__( 'Header Main', 'glozin' ) .'</h2>',
			),
			'header_main_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => 'background-color',
					),
				),
			),
			'header_main_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => '--gz-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => 'color',
					),
				),
			),
			'header_main_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => '--gz-header-main-border-color',
					),
				),
			),
			'header_main_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-main',
						'property' => '--gz-header-main-shadow-color',
					),
				),
			),
			'header_background_heading_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Bottom', 'glozin' ) .'</h2>',
			),
			'header_bottom_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => 'background-color',
					),
				),
			),
			'header_bottom_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => '--gz-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => 'color',
					),
				),
			),
			'header_bottom_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => '--gz-header-bottom-border-color',
					),
				),
			),
			'header_bottom_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__desktop .header-bottom',
						'property' => '--gz-header-bottom-shadow-color',
					),
				),
			),
			'header_background_heading_3'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Counter', 'glozin' ) .'</h2>',
			),
			'header_counter_background_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Background Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.header-counter',
						'property' => '--gz-color-primary',
					),
				),
			),
			'header_counter_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.header-counter',
						'property' => '--gz-text-color-on-primary',
					),
				),
			),
		);


		// Campaign bar.
		$settings['header_campaign'] = array(
			'campaign_bar' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Campaign Bar', 'glozin' ),
				'description' => esc_html__( 'Display a bar before the site header.', 'glozin' ),
				'default'     => false,
				'priority' => 0,
			),
			'campaign_bar_type'                 => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Type', 'glozin' ),
				'default'     => 'countdown',
				'choices'     => array(
					'countdown'   => esc_html__('Countdown', 'glozin'),
					'slides' 	=> esc_html__('Slides', 'glozin'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 10,
			),
			'campaign_bar_width' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Width', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '550',
				'choices'   => array(
					'min' => 100,
					'max' => 2000,
				),
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar-type--slides',
						'property' => '--gz-campaign-bar-width',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'slides',
					),
				),
			),
			'campaign_items_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 20,
			),
			'campaign_items'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Campaign Items', 'glozin' ),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Campaign', 'glozin' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'    => 'textarea',
						'label'   => esc_html__( 'Text', 'glozin' ),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'slides',
					),
				),
				'priority' => 25,
			),
			'campaign_image'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Image Before Text', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'countdown',
					),
				),
				'priority' => 30,
			),
			'campaign_text'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Text', 'glozin' ),
				'description'     => esc_html__( 'Paste text of your campaign here', 'glozin' ),
				'output'          => array(
					array(
						'element' => '.campaign-bar',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'countdown',
					),
				),
				'priority' => 35,
			),
			'campaign_date'       => array(
				'type'            => 'date',
				'label'           => esc_html__( 'Date', 'glozin' ),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'campaign_bar_type',
						'operator' => '==',
						'value'    => 'countdown',
					),
				),
				'priority' => 40,
			),
			'campaign_custom_heading'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Campaign Background', 'glozin' ) .'</h2>',
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 55,
			),
			'campaign_background_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Background Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar',
						'property' => '--gz-campaign-background',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 60,
			),
			'campaign_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar',
						'property' => '--gz-campaign-text-color',
					),
					array(
						'element'  => '.campaign-bar-type--slides .swiper .swiper-button-text',
						'property' => '--gz-arrow-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
			'campaign_hover_color' => array(
				'type'    => 'color',
				'label'   => esc_html__( 'Hover Color', 'glozin' ),
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.campaign-bar__close',
						'property' => '--gz-button-color-hover',
					),
					array(
						'element'  => '.campaign-bar-type--slides .swiper .swiper-button-text',
						'property' => '--gz-arrow-color-hover',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 65,
			),
		);

		// Logo.
		$settings['header_logo'] = array(
			'logo_type'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Logo Type', 'glozin' ),
				'default' => 'image',
				'choices' => array(
					'image' => esc_html__( 'Image', 'glozin' ),
					'text'  => esc_html__( 'Text', 'glozin' ),
					'svg'   => esc_html__( 'SVG', 'glozin' ),
				),
			),
			'logo_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Logo Text', 'glozin' ),
				'default'         => 'Glozin',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'logo_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Logo SVG', 'glozin' ),
				'description'     => esc_html__( 'Paste SVG code of your logo here', 'glozin' ),
				'sanitize_callback' => 'Glozin\Icon::sanitize_svg',
				'output'          => array(
					array(
						'element' => '.site-header .header-logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'logo'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'logo_light'           => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo Light', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'logo_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Logo Dimension', 'glozin' ),
				'default'         => array(
					'width'  => 'auto',
					'height' => 'auto',
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '!=',
						'value'    => 'text',
					),
				),
			),
		);

		// Header account.
		$settings['header_account'] = array(
			'header_signin_icon_behaviour' => array(
				'type'            => 'radio',
				'label'           => esc_html__( 'Sign in Icon Behaviour', 'glozin' ),
				'default'         => 'popup',
				'choices'         => array(
					'popup'   => esc_html__( 'Open the account popup', 'glozin' ),
					'page'  => esc_html__( 'Open the account page', 'glozin' ),
				),
			),
			'header_account_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Account Display', 'glozin' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'glozin' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_account_size' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Icon Size', 'glozin' ),
				'default'         => 'medium',
				'choices'         => array(
					'medium'   => esc_html__( 'Medium', 'glozin' ),
					'large'  => esc_html__( 'Large', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header wishlist.
		$settings['header_wishlist'] = array(
			'header_wishlist_size' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Icon Size', 'glozin' ),
				'default'         => 'medium',
				'choices'         => array(
					'medium'   => esc_html__( 'Medium', 'glozin' ),
					'large'  => esc_html__( 'Large', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header wishlist.
		$settings['header_compare'] = array(
			'header_compare_size' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Icon Size', 'glozin' ),
				'default'         => 'medium',
				'choices'         => array(
					'medium'   => esc_html__( 'Medium', 'glozin' ),
					'large'  => esc_html__( 'Large', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header cart.
		$settings['header_cart'] = array(
			'cart_icon_source'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Cart Icon', 'glozin' ),
				'default' => 'icon',
				'choices' => array(
					'icon'  => esc_attr__( 'Built-in Icon', 'glozin' ),
					'svg'   => esc_attr__( 'SVG Code', 'glozin' ),
				),
			),
			'cart_icon'             => array(
				'type'    => 'radio-image',
				'default' => '',
				'choices' => array(
					''   	=> get_template_directory_uri() . '/assets/svg/shopping-bag.svg',
					'shopping-bag-2' 	=> get_template_directory_uri() . '/assets/svg/shopping-bag-2.svg',
					'shopping-cart'  	=> get_template_directory_uri() . '/assets/svg/shopping-cart.svg',
					'shopping-cart-2'  	=> get_template_directory_uri() . '/assets/svg/shopping-cart-2.svg',
					'shopping-cart-3'  	=> get_template_directory_uri() . '/assets/svg/shopping-cart-3.svg',
				),
				'active_callback' => array(
					array(
						'setting'  => 'cart_icon_source',
						'operator' => '==',
						'value'    => 'icon',
					),
				),
			),
			'cart_icon_svg'         => array(
				'type'              => 'textarea',
				'description'       => esc_html__( 'Icon SVG code', 'glozin' ),
				'sanitize_callback' => '\Glozin\Icon::sanitize_svg',
				'active_callback'   => array(
					array(
						'setting'  => 'cart_icon_source',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'cart_icon_svg_size' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Size', 'glozin'),
				'transport' => 'postMessage',
				'default'    => 24,
				'choices'   => array(
					'min' => 0,
					'max' => 50,
				),
				'output'         => array(
					array(
						'element'  => '.header-cart__icon .glozin-svg-icon--custom-cart, ul.products li.product .product-loop-button .glozin-svg-icon.glozin-svg-icon--custom-cart',
						'property' => 'font-size',
						'units'    => 'px',
					),
				),
				'active_callback'   => array(
					array(
						'setting'  => 'cart_icon_source',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'cart_hr_1'          => array(
				'type'    => 'custom',
				'section' => 'header_cart',
				'default' => '<hr>',
			),
			'header_cart_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Display', 'glozin' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'glozin' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_cart_size' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Icon Size', 'glozin' ),
				'default'         => 'medium',
				'choices'         => array(
					'medium'   => esc_html__( 'Medium', 'glozin' ),
					'large'  => esc_html__( 'Large', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mini_cart_products'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Recommended Products', 'glozin' ),
				'description'     => esc_html__( 'Display recommended products on the mini cart', 'glozin' ),
				'default'         => 'recent_products',
				'choices'         => array(
					'none'                  => esc_html__( 'None', 'glozin' ),
					'best_selling_products' => esc_html__( 'Best selling products', 'glozin' ),
					'featured_products'     => esc_html__( 'Featured products', 'glozin' ),
					'recent_products'       => esc_html__( 'Recent products', 'glozin' ),
					'sale_products'         => esc_html__( 'Sale products', 'glozin' ),
					'top_rated_products'    => esc_html__( 'Top rated products', 'glozin' ),
					'crosssells_products'   => esc_html__( 'Cross-sells products', 'glozin' ),

				),
			),
			'mini_cart_products_limit' => array(
				'type'            => 'number',
				'description'     => esc_html__( 'Number of products', 'glozin' ),
				'default'         => 4,
			),
			'mini_cart_products_layout'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Recommended Products Layout', 'glozin' ),
				'default'         => 'sidebar',
				'choices'         => array(
					'sidebar' 	=> esc_html__( 'Sidebar List', 'glozin' ),
					'carousel' 	=> esc_html__( 'Carousel', 'glozin' ),
				),
			),
		);

		// Header search.
		$settings['header_search'] = array(
			'header_search_layout' => array(
				'type'     => 'select',
				'label'    => esc_html__('Layout', 'glozin'),
				'default'  => 'icon',
				'choices'  => array(
					'icon'     => __( 'Icon', 'glozin' ),
					'form'     => __( 'Form', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'priority' => 5
			),
			'header_search_form_width' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Search Field Width', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '',
				'choices'   => array(
					'min' => 0,
					'max' => 1000,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header .header-search__field',
						'property' => 'width',
						'units'    => 'px',
					),
				),
				'active_callback' => function() {
					return ! $this->display_header_search_option();
				},
				'priority' => 5
			),
			'header_search_type' => array(
				'type'     => 'select',
				'label'    => esc_html__('Type', 'glozin'),
				'default'  => 'popup',
				'choices'  => array(
					'popup'       => __( 'Popup', 'glozin' ),
					'sidebar'     => __( 'Sidebar', 'glozin' ),
				),
				'priority' => 5
			),
			'header_search_hr_1'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 10
			),
			'header_search_trending' => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Trending', 'glozin' ),
				'description'     => esc_html__( 'Display a list of links in the search modal', 'glozin' ),
				'default'         => false,
				'priority' => 15
			),
			'header_search_links'       => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Links', 'glozin' ),
				'description'     => esc_html__( 'Add custom links of the trending searches', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Link', 'glozin' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'  => 'text',
						'label' => esc_html__( 'Text', 'glozin' ),
					),
					'url'  => array(
						'type'  => 'text',
						'label' => esc_html__( 'URL', 'glozin' ),
					),
				),
				'priority' => 20
			),
			'header_search_hr_5'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'priority' => 25
			),
			'header_search_products' => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Products', 'glozin' ),
				'description'     => esc_html__( 'Display a products list before searching', 'glozin' ),
				'default'         => false,
				'priority' => 30
			),
			'header_search_products_type' => array(
				'type'     => 'select',
				'label'    => esc_html__('Type', 'glozin'),
				'default'  => 'recent_products',
				'choices'  => array(
					'recent_products'       => __( 'Recent Products', 'glozin' ),
					'featured_products'     => __( 'Featured Products', 'glozin' ),
					'sale_products'         => __( 'Sale Products', 'glozin' ),
					'best_selling_products' => __( 'Best Selling Products', 'glozin' ),
					'top_rated_products'    => __( 'Top Rated Products', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_search_products',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'priority' => 35
			),
			'header_search_product_limit'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Limit', 'glozin' ),
				'default'         => '10',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_products',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'priority' => 40
			),
		);

		// Product Categories
		$settings['header_product_categories'] = array(
			'header_sidebar_categories' => array(
				'type'        	=> 'toggle',
				'default'     	=> '',
				'label'         => esc_html__( 'Sidebar Categories', 'glozin' ),
				'description'   => esc_html__( 'Enable this option to display the category sidebar on desktop screens.', 'glozin' ),
			),
		);

		// Custom HTML
		$settings['header_custom_html'] = array(
			'header_custom_html'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Custom HTML', 'glozin' ),
				'description'     => esc_html__( 'Paste your HTML here', 'glozin' ),
			),
		);

		// Hambuger menu
		$settings['header_mobile_menu'] = array(
			'header_mobile_menu_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Mobile Menu Elements', 'glozin'),
				'default'  => array( 'primary-menu', 'custom-menu' ),
				'choices'  => array(
					'primary-menu' 		=> esc_html__('Primary Menu', 'glozin'),
					'custom-menu' 		=> esc_html__('Custom Menu', 'glozin'),
					'category-menu' 	=> esc_html__('Category Menu', 'glozin'),
					'currency' 			=> esc_html__('Currency', 'glozin'),
					'language' 			=> esc_html__('Language', 'glozin'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'glozin'),
			),
			'header_mobile_menu_primary_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Primary Menu', 'glozin' ),
				'default'         => '',
				'choices'         => $this->get_menus(),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'contains',
						'value'    => 'primary-menu',
					),
				),
			),
			'header_mobile_menu_custom_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Custom Menu', 'glozin' ),
				'default'         => '',
				'choices'         => $this->get_menus(),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_menu_els',
						'operator' => 'contains',
						'value'    => 'custom-menu',
					),
				),
			),
			'header_mobile_menu_category_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_menu_category_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Category Menu', 'glozin' ),
				'default'         => '',
				'choices'         => $this->get_menus(),
			),
			'header_mobile_menu_open_primary_submenus_on_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_menu_open_primary_submenus_on' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Open Submenu Items on', 'glozin' ),
				'default'         => 'all',
				'choices'         => array(
					'all'   => esc_html__( 'Title & Icon click', 'glozin' ),
					'icon'  => esc_html__( 'Icon click', 'glozin' ),
				),
			),
		);

		$settings['post_card'] = array(
			'image_rounded_shape_post_card'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'glozin' ),
					'square'  	=> esc_html__( 'Square', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
			),
			'image_rounded_number_post_card'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_post_card',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

		);

		// Blog Header.
		$settings['blog_header'] = array(
			'blog_header' => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Enable Blog Header', 'glozin'),
				'description' => esc_html__('Enable to show a blog header for the page below the site header', 'glozin'),
			),
			'blog_header_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'glozin'),
				'default'  => array( 'breadcrumb', 'title' ),
				'choices'  => array(
					'breadcrumb'  => esc_html__('BreadCrumb', 'glozin'),
					'title'       => esc_html__('Title', 'glozin'),
					'description' => esc_html__('Description', 'glozin'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'glozin'),
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_description_lines'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Description Number Lines', 'glozin'),
				'default'         => 5,
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
			),
			'blog_header_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/><h3>' . esc_html__('Custom', 'glozin') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_background_image'          => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Background Image', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'blog_header_background_overlay' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Overlay', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .page-header__image::before',
						'property' => 'background-color',
					),
				),
			),
			'blog_header_title_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Title Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .page-header__title',
						'property' => 'color',
					),
				),
			),
			'blog_header_breadcrumb_link_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Link Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .site-breadcrumb',
						'property' => '--gz-site-breadcrumb-link-color',
					),
				),
			),
			'blog_header_breadcrumb_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .site-breadcrumb',
						'property' => '--gz-site-breadcrumb-color',
					),
				),
			),
			'blog_header_description_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Description Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'blog_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--blog .page-header__description',
						'property' => 'color',
					),
				),
			),
			'blog_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'glozin'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 80,
					'tablet'  => 80,
					'mobile'  => 60,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--blog',
						'property' => '--gz-page-header-padding-top',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'blog_header_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'glozin'),
				'transport' => 'postMessage',
				'default'    => [
					'desktop' => 10,
					'tablet'  => 10,
					'mobile'  => 10,
				],
				'responsive' => true,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'output'         => array(
					array(
						'element'  => '.page-header.page-header--blog',
						'property' => '--gz-page-header-padding-bottom',
						'units'    => 'px',
						'media_query' => [
							'desktop' => '@media (min-width: 1200px)',
							'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
							'mobile'  => '@media (max-width: 767px)',
						],
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'blog_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		);

		// Blog.
		$settings['blog_page'] = array(
			'blog_layout'    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Layout', 'glozin' ),
				'default'     => 'list',
				'choices'     => array(
					'grid'          => esc_html__('Grid', 'glozin'),
					'list'          => esc_html__('List', 'glozin'),
				),
			),
			'blog_columns'    => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Grid Columns', 'glozin' ),
				'default'     => '2',
				'choices'     => array(
					'2' => esc_html__('2 Columns', 'glozin'),
					'3' => esc_html__('3 Columns', 'glozin'),
					'4' => esc_html__('4 Columns', 'glozin'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'blog_layout',
						'operator' => '==',
						'value'    => 'grid',
					),
				),
			),
			'blog_sidebar'    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Sidebar', 'glozin' ),
				'default'     => 'sidebar-content',
				'choices'     => array(
					'no-sidebar'      => esc_html__('No Sidebar', 'glozin'),
					'sidebar-content' => esc_html__('Left Sidebar', 'glozin'),
					'content-sidebar' => esc_html__('Right Sidebar', 'glozin'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'blog_columns',
						'operator' => '!==',
						'value'    => '3',
					),
					array(
						'setting'  => 'blog_columns',
						'operator' => '!==',
						'value'    => '4',
					),
				),
			),
			'blog_hr'  => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'blog_pagination' => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Pagination Type', 'glozin' ),
				'default' => 'numeric',
				'choices' => array(
					'numeric'  => esc_attr__( 'Numeric', 'glozin' ),
					'infinite' => esc_attr__( 'Infinite Scroll', 'glozin' ),
					'loadmore' => esc_attr__( 'Load More', 'glozin' ),
				),
			),
			'blog_pagination_ajax_url_change' => array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Change the URL after page loaded', 'glozin' ),
				'default'         => true,
				'active_callback' => array(
					array(
						'setting'  => 'blog_pagination',
						'operator' => '!=',
						'value'    => 'numeric',
					),
				),
			),
		);

		// Blog single.
		$settings['blog_single'] = array(
			'single_post_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Post Header Elements', 'glozin'),
				'default'  => array( 'breadcrumb' ),
				'choices'  => array(
					'breadcrumb'     => esc_html__('BreadCrumb', 'glozin'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'glozin'),
			),
			'single_post_image_rounded_shape_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'post_featured_image'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Featured Image', 'glozin' ),
				'description' => esc_html__( 'Enable featured image.', 'glozin' ),
				'default'     => true,
			),
			'image_rounded_shape_featured_post'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Featured Image Corner Radius', 'glozin' ),
				'default'         => '',
				'choices'         => array(
					'' 			=> esc_html__( 'Default', 'glozin' ),
					'square'  	=> esc_html__( 'Square', 'glozin' ),
					'custom'  	=> esc_html__( 'Custom', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'post_featured_image',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'image_rounded_number_featured_post'       => array(
				'type'            => 'number',
				'label'           => esc_html__( 'Number(px)', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'image_rounded_shape_featured_post',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'post_featured_image',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'single_post_sidebar_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'post_sidebar'                 => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Post Sidebar', 'glozin' ),
				'description' => esc_html__( 'The layout of single posts', 'glozin' ),
				'default'     => 'no-sidebar',
				'choices'     => array(
					'no-sidebar'      => esc_html__('No Sidebar', 'glozin'),
					'content-sidebar' => esc_html__('Right Sidebar', 'glozin'),
					'sidebar-content' => esc_html__('Left Sidebar', 'glozin'),
				),
			),
			'post_sharing'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Post Sharing', 'glozin' ),
				'description' => esc_html__( 'Enable post sharing.', 'glozin' ),
				'default'     => false,
			),
			'post_navigation'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Post Navigation', 'glozin' ),
				'description' => esc_html__( 'Display the next and previous posts', 'glozin' ),
				'default'     => true,
			),
			'posts_related_custom'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'posts_related'   => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Related Posts', 'glozin' ),
				'description' => esc_html__( 'Display related posts', 'glozin' ),
				'default'     => true,
			),
			'posts_related_number'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Posts Numbers', 'glozin'),
				'default'         => 5,
			),
			'posts_related_spacing'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Posts Spacing', 'glozin'),
				'default'         => 30,
			),
		);

		// Back To Top.
		$settings['backtotop'] = array(
			'backtotop'    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Back To Top', 'glozin' ),
				'description' => esc_html__( 'Check this to show back to top.', 'glozin' ),
				'default'     => true,
			),
		);

		// Share socials
		$settings['share_socials'] = array(
			'post_sharing_socials' => array(
				'type'            => 'sortable',
				'description'     => esc_html__( 'Select social media for sharing posts/products', 'glozin' ),
				'default'         => array(
					'twitter',
					'facebook',
					'pinterest',
					'instagram',
				),
				'choices'         => array(
					'facebook'    => esc_html__( 'Facebook', 'glozin' ),
					'twitter'     => esc_html__( 'Twitter', 'glozin' ),
					'googleplus'  => esc_html__( 'Google Plus', 'glozin' ),
					'pinterest'   => esc_html__( 'Pinterest', 'glozin' ),
					'tumblr'      => esc_html__( 'Tumblr', 'glozin' ),
					'reddit'      => esc_html__( 'Reddit', 'glozin' ),
					'linkedin'    => esc_html__( 'Linkedin', 'glozin' ),
					'stumbleupon' => esc_html__( 'StumbleUpon', 'glozin' ),
					'digg'        => esc_html__( 'Digg', 'glozin' ),
					'telegram'    => esc_html__( 'Telegram', 'glozin' ),
					'whatsapp'    => esc_html__( 'WhatsApp', 'glozin' ),
					'vk'          => esc_html__( 'VK', 'glozin' ),
					'email'       => esc_html__( 'Email', 'glozin' ),
					'instagram'   => esc_html__( 'Instagram', 'glozin' ),
				),
			),
			'post_sharing_whatsapp_number' => array(
				'type'        => 'text',
				'description' => esc_html__( 'WhatsApp Phone Number', 'glozin' ),
				'active_callback' => array(
					array(
						'setting'  => 'post_sharing_socials',
						'operator' => 'contains',
						'value'    => 'whatsapp',
					),
				),
			),
		);

		// Page Header.
		$settings['page_header'] = array(
			'page_header' => array(
				'type'        => 'toggle',
				'default'     => true,
				'label'       => esc_html__('Enable Page Header', 'glozin'),
				'description' => esc_html__('Enable to show a page header for the page below the site header', 'glozin'),
			),
			'page_header_hr' => array(
				'type'            => 'custom',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Elements', 'glozin'),
				'default'  => array( 'title' ),
				'choices'  => array(
					'title'      => esc_html__('Title', 'glozin'),
					'breadcrumb' => esc_html__('BreadCrumb', 'glozin'),
					'description' => esc_html__('Description', 'glozin'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'glozin'),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_description_lines'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('Description Number Lines', 'glozin'),
				'default'         => 5,
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
			),
			'page_header_hr_1' => array(
				'type'            => 'custom',
				'default'         => '<hr/><h3>' . esc_html__('Custom', 'glozin') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_background_image'          => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Background Image', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'page_header_background_overlay' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Overlay', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page::before',
						'property' => 'background-color',
					),
				),
			),
			'page_header_title_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Title Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .page-header__title',
						'property' => 'color',
					),
				),
			),
			'page_header_breadcrumb_link_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Link Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .site-breadcrumb',
						'property' => '--gz-site-breadcrumb-link-color',
					),
				),
			),
			'page_header_breadcrumb_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Breadcrumb Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'breadcrumb',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .site-breadcrumb',
						'property' => '--gz-site-breadcrumb-color',
					),
				),
			),
			'page_header_description_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Description Color', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'description',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.page-header.page-header--page .page-header__description',
						'property' => 'color',
					),
				),
			),
			'page_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'glozin'),
				'transport' => 'postMessage',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'default'    => [
                    'desktop' => 80,
                    'tablet'  => 80,
                    'mobile'  => 60,
                ],
				'output'         => array(
                    array(
                        'element'  => '.page-header',
                        'property' => '--gz-page-header-padding-top',
                        'units'    => 'px',
                        'media_query' => [
                            'desktop' => '@media (min-width: 1200px)',
                            'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
                            'mobile'  => '@media (max-width: 767px)',
                        ],
                    ),
                ),
				'responsive' => true,
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'page_header_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'glozin'),
				'transport' => 'postMessage',
				'default'    => [
                    'desktop' => 10,
                    'tablet'  => 10,
                    'mobile'  => 10,
                ],
                'responsive' => true,
                'choices'   => array(
                    'min' => 0,
                    'max' => 500,
                ),
                'output'         => array(
                    array(
                        'element'  => '.page-header',
                        'property' => '--gz-page-header-padding-bottom',
                        'units'    => 'px',
                        'media_query' => [
                            'desktop' => '@media (min-width: 1200px)',
                            'tablet'  => is_customize_preview() ? '@media (min-width: 699px) and (max-width: 1199px)' : '@media (min-width: 768px) and (max-width: 1199px)',
                            'mobile'  => '@media (max-width: 767px)',
                        ],
                    ),
                ),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		$settings['topbar_mobile'] = array(
			'mobile_topbar' => array(
				'type'      => 'toggle',
				'label'     => esc_html__( 'Topbar', 'glozin' ),
				'description' => esc_html__( 'Display topbar on mobile', 'glozin' ),
				'default'   => false,
			),
			'mobile_topbar_breakpoint' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Breakpoint (px)', 'glozin' ),
				'description' => esc_html__( 'Set a breakpoint where the mobile navigation bar displays.', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '1024',
				'choices'   => array(
					'min' => 375,
					'max' => 1199,
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'mobile_topbar_section' => array(
				'type'      => 'select',
				'label'     => esc_html__( 'Topbar Items', 'glozin' ),
				'default'   => 'left',
				'choices' => array(
					'left'   => esc_html__( 'Keep left items', 'glozin' ),
					'right'  => esc_html__( 'Keep right items', 'glozin' ),
					'all'    => esc_html__( 'Keep all items', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
		);

		// Header Mobile
		$settings['header_mobile_layout'] = array(
			'header_mobile_breakpoint' => array(
				'type'      => 'slider',
				'label'       => esc_html__( 'Breakpoint (px)', 'glozin' ),
				'description' => esc_html__( 'Set a breakpoint where the mobile header displays and the desktop header is hidden.', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '1199',
				'choices'   => array(
					'min' => 991,
					'max' => 1199,
				),
			),
			'header_mobile_present_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_present' => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Present', 'glozin' ),
				'description' => esc_html__( 'Select a prebuilt header or build your own', 'glozin' ),
				'default'     => 'prebuild',
				'choices'     => array(
					'prebuild' => esc_html__( 'Use pre-build header', 'glozin' ),
					'custom'   => esc_html__( 'Build my own', 'glozin' ),
				),
			),
			'header_mobile_prebuild_search'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Search', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_mobile_prebuild_account'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Account', 'glozin' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_mobile_prebuild_wishlist'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Wishlist', 'glozin' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_mobile_prebuild_compare'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Compare', 'glozin' ),
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_mobile_prebuild_cart'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Header Cart', 'glozin' ),
				'default'     => true,
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'prebuild',
					),
				),
			),
			'header_mobile_main_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_icon_auto_width'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Auto Icon Width', 'glozin' ),
				'default'     => false,
			),
			'header_mobile_main_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Main Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '64',
				'choices'   => array(
					'min' => 30,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile .header-mobile-main',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
			'header_mobile_bottom_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Bottom Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 30,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile .header-mobile-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Header sticky settings.
		$settings['header_mobile_sticky'] = array(
			'header_mobile_sticky'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Sticky Header', 'glozin' ),
				'default'         => false,
			),
			'header_mobile_sticky_el'   => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Sticky Header Section', 'glozin' ),
				'default'         => 'header_main',
				'choices'         => array(
					'header_main'   => esc_html__('Header Main', 'glozin'),
					'header_bottom' => esc_html__('Header Bottom', 'glozin'),
					'both'          => esc_html__('Both', 'glozin'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_sticky',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'header_mobile_sticky_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'header_mobile_sticky_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Main Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '64',
				'choices'   => array(
					'min' => 30,
					'max' => 200,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_mobile_sticky_el',
						'operator' => '!==',
						'value'    => 'header_bottom',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile.minimized .header-mobile-main, .site-header__mobile.headroom--not-top .header-mobile-main',
						'property' => 'height',
						'units'    => 'px',
					),
					array(
						'element'  => '.site-header__mobile.minimized .header-mobile-sticky + .header-mobile-bottom, .site-header__mobile.headroom--not-top .header-mobile-sticky + .header-mobile-bottom',
						'property' => 'top',
						'units'    => 'px',
					),
				),
			),
			'header_mobile_sticky_bottom_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Bottom Height', 'glozin' ),
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 30,
					'max' => 200,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_sticky',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'header_mobile_sticky_el',
						'operator' => '!==',
						'value'    => 'header_main',
					),
				),
				'js_vars'   => array(
					array(
						'element'  => '.site-header__mobile.minimized .header-mobile-bottom, .site-header__mobile.headroom--not-top .header-mobile-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
		);

		// Header main settings.
		$settings['header_mobile_main'] = array(
			'header_mobile_main_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the left side of header mobile main', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_main_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_main_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'glozin' ),
				'description'     => esc_html__( 'Control items at the center of header mobile main', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_main_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_main_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the right of header mobile main', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_main_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Mobile::instance(), 'render' ),
					),
				),
			),
		);

		// Header bottom settings.
		$settings['header_mobile_bottom'] = array(
			'header_mobile_bottom_left'   => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Left Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the left side of header mobile bottom', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_bottom_left' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_bottom_center' => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Center Items', 'glozin' ),
				'description'     => esc_html__( 'Control items at the center of header mobile bottom', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_bottom_center' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Mobile::instance(), 'render' ),
					),
				),
			),
			'header_mobile_bottom_right'  => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'Right Items', 'glozin' ),
				'description'     => esc_html__( 'Control items on the right of header mobile bottom', 'glozin' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_html__( 'Item', 'glozin' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_mobile_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
				'partial_refresh' => array(
					'header_mobile_bottom_right' => array(
						'selector'        => '#site-header',
						'render_callback' => array( \Glozin\Header\Mobile::instance(), 'render' ),
					),
				),
			),
		);

		$settings['header_mobile_background'] = array(
			'header_mobile_background_heading_1'    => array(
				'type'    => 'custom',
				'default' => '<h2>'. esc_html__( 'Header Main', 'glozin' ) .'</h2>',
			),
			'header_mobile_main_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-main',
						'property' => 'background-color',
					),
				),
			),
			'header_mobile_main_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => '--gz-color-dark',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => '--gz-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => 'color',
					),
				),
			),
			'header_mobile_main_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-main',
						'property' => '--gz-header-mobile-main-border-color',
					),
				),
			),
			'header_mobile_main_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-main',
						'property' => '--gz-header-mobile-main-shadow-color',
					),
				),
			),
			'header_mobile_background_heading_2'    => array(
				'type'    => 'custom',
				'default' => '<hr/><h2>'. esc_html__( 'Header Bottom', 'glozin' ) .'</h2>',
			),
			'header_mobile_bottom_background_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-bottom',
						'property' => 'background-color',
					),
				),
			),
			'header_mobile_bottom_text_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Text Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => '--gz-color-dark',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => '--gz-header-color',
					),
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => 'color',
					),
				),
			),
			'header_mobile_bottom_border_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Border Color', 'glozin' ),
				'default'         => '',
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .header-mobile-bottom',
						'property' => '--gz-header-mobile-bottom-border-color',
					),
				),
			),
			'header_mobile_bottom_shadow_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Box Shadow Color', 'glozin' ),
				'default'         => '',
				'choices'     => [
					'alpha' => true,
				],
				'js_vars'   => array(
					array(
						'element'  => 'body:not(.header-transparent) .site-header__mobile .header-mobile-bottom',
						'property' => '--gz-header-mobile-bottom-shadow-color',
					),
				),
			),
		);

		// Header mobile menu.
		$settings['header_mobile_elements'] = array(
			'mobile_logo_type'      => array(
				'type'    => 'radio',
				'label'   => esc_html__( 'Logo Type', 'glozin' ),
				'default' => 'default',
				'choices' => array(
					'default' => esc_html__( 'Default', 'glozin' ),
					'image' => esc_html__( 'Image', 'glozin' ),
					'text'  => esc_html__( 'Text', 'glozin' ),
					'svg'   => esc_html__( 'SVG', 'glozin' ),
				),
			),
			'mobile_logo_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Logo Text', 'glozin' ),
				'default'         => 'Glozin',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'mobile_logo_svg'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Logo SVG', 'glozin' ),
				'description'     => esc_html__( 'Paste SVG code of your logo here', 'glozin' ),
				'sanitize_callback' => 'Glozin\Icon::sanitize_svg',
				'output'          => array(
					array(
						'element' => '.site-header .header-logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'mobile_logo_image'   => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'mobile_logo_image_light'   => array(
				'type'            => 'image',
				'label'           => esc_html__( 'Logo Light', 'glozin' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'mobile_logo_dimension' => array(
				'type'            => 'dimensions',
				'label'           => esc_html__( 'Logo Dimension', 'glozin' ),
				'default'         => array(
					'width'  => '',
					'height' => '',
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '!=',
						'value'    => 'text',
					),
				),
			),
			'mobile_header_hamburger_menu_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'mobile_header_hamburger_menu_text'      => array(
				'type'            => 'text',
				'label'           => esc_html__( 'Hamburger Menu Text', 'glozin' ),
				'default'         => '',
			),
			'mobile_header_account_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mobile_header_wishlist_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_wishlist_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Wishlist Display', 'glozin' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'glozin' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mobile_header_compare_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_compare_display' => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Compare Display', 'glozin' ),
				'default'         => 'icon',
				'choices'         => array(
					'icon'   => esc_html__( 'Icon Only', 'glozin' ),
					'icon-text'  => esc_html__( 'Icon & Text', 'glozin' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'mobile_header_custom_html_hr'     => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_mobile_custom_html'       => array(
				'type'            => 'textarea',
				'label'           => esc_html__( 'Custom HTML', 'glozin' ),
				'description'     => esc_html__( 'Paste your HTML here', 'glozin' ),
				'active_callback' => array(
					array(
						'setting'  => 'header_mobile_present',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);


		// Mobile Product Catalog
		$settings['mobile_product_catalog'] = array(
			'mobile_product_catalog_heading_1'     => array(
				'type'    => 'custom',
				'default' => '<h2>'. esc_html__( 'Product Grid', 'glozin' ) .'</h2>',
			),
			'mobile_product_columns'     => array(
				'label'   => esc_html__( 'Product Columns', 'glozin' ),
				'type'    => 'select',
				'default' => '2',
				'choices' => array(
					'1' => esc_html__( '1 Column', 'glozin' ),
					'2' => esc_html__( '2 Columns', 'glozin' ),
				),
			),
		);

		// Mobile Product Card
		$settings['mobile_product_card'] = array(
			'mobile_product_card_featured_icons'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Always Show Featured Icons', 'glozin' ),
				'default'         => true,
			),
			'mobile_product_card_atc'        => array(
				'type'            => 'toggle',
				'label'           => esc_html__( 'Show Add To Cart Button', 'glozin' ),
				'default'         => false,
				'active_callback' => array(
					array(
						'setting'  => 'product_card_layout',
						'operator' => '!==',
						'value'    => '2',
					),
				),
			),
			'mobile_product_card_wishlist' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Wishlist button', 'glozin' ),
				'default' => true,
			),
			'mobile_product_card_compare' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Compare button', 'glozin' ),
				'default' => false,
			),
			'mobile_product_card_quick_view' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Quick View button', 'glozin' ),
				'default' => false,
			),
		);

		// Mobile Single Product
		$settings['mobile_single_product'] = array(
			'mobile_single_product_gallery_arrows' => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Show Gallery Arrows', 'glozin' ),
				'default' => false,
				'active_callback' => array(
					array(
						'setting'  => 'product_gallery_layout',
						'operator' => 'in',
						'value'    => array( '', 'bottom-thumbnails', 'hidden-thumbnails' ),
					),
				),
			),
			'mobile_single_product_slides_per_view_auto_hr' => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'mobile_single_product_slides_per_view_auto' => array(
				'type'    => 'multicheck',
				'label'   => esc_html__( 'Slides Per View Auto', 'glozin' ),
				'default' => [],
				'choices' => array(
					'related'         => esc_html__( 'Related', 'glozin' ),
					'upsells'         => esc_html__( 'Upsells', 'glozin' ),
					'recently_viewed' => esc_html__( 'Recently Viewed', 'glozin' ),
				),
			),
		);

		return array(
			'theme'    => 'glozin',
			'panels'   => apply_filters( 'glozin_customize_panels', $panels ),
			'sections' => apply_filters( 'glozin_customize_sections', $sections ),
			'settings' => apply_filters( 'glozin_customize_settings', $settings ),
		);

	}

	/**
	 * Get nav menus
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_menus() {
		if ( ! is_admin() ) {
			return [];
		}

		$menus = wp_get_nav_menus();
		if ( ! $menus ) {
			return [];
		}

		$output = array(
			0 => esc_html__( 'Select Menu', 'glozin' ),
		);
		foreach ( $menus as $menu ) {
			$output[ $menu->slug ] = $menu->name;
		}

		return $output;
	}

	/**
	 * Get the list of fonts for Kirki
	 *
	 * @return array
	 */
	public static function customizer_fonts_choices() {
		if( get_theme_mod('typo_font_family', true) ) {
			$args_fonts = array(
				'families' => array(
					array( 'id' => 'Instrument Sans', 'text' => 'Instrument Sans' ),
				),
				'variants' => array(
					'Instrument Sans' => array( 'regular', '500', '600', '700', '800' ),
				),
			);
		} else {
			$args_fonts = array();
		}

		// Compatible custom fonts plugin
		if( defined( 'BSF_CUSTOM_FONTS_POST_TYPE' ) ) {
			$args                 = array(
				'post_type'      => BSF_CUSTOM_FONTS_POST_TYPE,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => '-1',
			);

			$query = new \WP_Query( $args );
			$bsf_fonts = $query->posts;

			if ( ! empty( $bsf_fonts ) ) {
				foreach ( $bsf_fonts as $key => $post_id ) {
					$bsf_font_data = get_post_meta( $post_id, 'fonts-data', true );
					$variants = [];
					foreach( $bsf_font_data['variations'] as $variations ) {
						$variants[] = $variations['font_weight'] == '400' ? 'regular' : $variations['font_weight'];
					}

					$args_fonts['families'][] = array(
						'id' => $bsf_font_data['font_name'],
						'text' => $bsf_font_data['font_name']
					);

					$args_fonts['variants'][$bsf_font_data['font_name']] = $variants;
				}
			}

			wp_reset_postdata();
		}

		$custom_fonts = apply_filters( 'glozin_custom_fonts_options', $args_fonts );

		$fonts = array(
			'standard' => array( 'serif', 'sans-serif', 'monospace' ),
			'google'   => array(),
		);

		if ( ! empty( $custom_fonts) && ! empty( $custom_fonts['families'] ) ) {
			$fonts['families'] = array(
				'custom' => array(
					'text'     => esc_html__( 'Glozin Custom Fonts', 'glozin' ),
					'children' => $custom_fonts['families'],
				),
			);

			if ( ! empty( $custom_fonts['variants'] ) ) {
				$fonts['variants'] = $custom_fonts['variants'];
			}
		}

		return apply_filters( 'glozin_customize_fonts_choices', array(
			'fonts' => $fonts,
		) );
	}

	/**
	 * Display header search
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_header_search_option() {
		if ( 'custom' == get_theme_mod( 'header_present' ) ) {
			if ( 'icon' == get_theme_mod( 'header_search_layout' ) ) {
				return true;
			}

			return false;
		} 

		return true;
	}
	
}
