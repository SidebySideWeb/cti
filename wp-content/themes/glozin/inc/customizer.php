<?php
/**
 * Theme customizer
 *
 * @package Glozin
 */

namespace Glozin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customize
 *
 * @var array
 */

class Customizer {
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
	 * Customize settings
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * The class constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	public function __construct( $config = array()) {
		$this->config = apply_filters( 'glozin_customize_config', $config );

		if ( ! class_exists( 'Kirki' ) ) {
			return;
		}

		$this->register();

		add_action( 'customize_preview_init', array( $this, 'enqueue_preview_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_register', array( $this, 'customize_modify' ) );
	}

	/**
	 * Enqueues style and scripts for customizer controls
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_add_inline_style( 'customize-controls', '.customize-control-kirki-radio-image label {margin-inline-end: 5px}.customize-pane-parent > .control-section-kirki-default{min-height: auto}' );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_preview_scripts() {
		wp_add_inline_style( 'wp-admin', '.customize-control-kirki-radio-image label {margin-inline-end: 5px;}.customize-pane-parent > .control-section-kirki-default{min-height: auto}' );
		wp_enqueue_script( 'glozin-customizer-preview', get_template_directory_uri() . '/assets/js/backend/customizer-preview.js', array( 'customize-preview' ), '', true );
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {

		if ( empty( $this->config['theme'] ) ) {
			return;
		}

		$theme = $this->config['theme'];
		// Add the theme configuration.
		\Kirki::add_config( $theme, array(
			'capability'  => 'edit_theme_options',
			'option_type' => 'theme_mod',
		) );

		// Add panels.
		foreach ( $this->config['panels'] as $id => $panel ) {
			\Kirki::add_panel( $id, $panel );
		}

		// Add sections.
		foreach ( $this->config['sections'] as $id => $section ) {
			\Kirki::add_section( $id, $section );
		}

		// Add settings.
		foreach ( $this->config['settings'] as $section => $settings ) {
			foreach ( $settings as $name => $setting ) {
				if ( empty( $setting['section'] ) ) {
					$setting['section'] = $section;
				}

				if ( empty( $setting['settings'] ) ) {
					$setting['settings'] = $name;
				}

				if( $setting['type'] == 'headline' ) {
					if ( class_exists( 'Kirki' ) && class_exists( 'Kirki\Pro\Field\Headline' ) ) {
						new \Kirki\Pro\Field\Headline (
							[
								'settings'    => $name,
								'label'       => $setting['label'],
								'description' => isset($setting['description']) ? $setting['description'] : '',
								'section'     => $section,
								'tooltip'     => isset($setting['tooltip']) ? $setting['tooltip'] : '',
								'tab'	 		=> isset($setting['tab']) ? $setting['tab'] : '',
							]
						);
					}
				} else {
					\Kirki::add_field( $theme, $setting );
				}
			}
		}
	}

	/**
	 * Get config ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_theme() {
		return $this->config['theme'];
	}

	/**
	 * Get customize setting value
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function get_option( $name ) {
		if ( class_exists( 'Kirki\Compatibility\Kirki' ) ) {
			return \Kirki\Compatibility\Kirki::get_option( $this->get_theme(), $name );
		}
		$default = $this->get_option_default( $name );

		return get_theme_mod( $name, $default );
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
	public function get_option_default( $name ) {
		if ( class_exists( 'Kirki' ) && isset( \Kirki::$fields[ $name ] ) && isset( \Kirki::$fields[ $name ]['default'] ) ) {
			$default = \Kirki::$fields[ $name ]['default'];
		} else {
			$settings = array_reduce( $this->config['settings'], 'array_merge', array() );
			$default = isset( $settings[ $name ]['default'] ) ? $settings[ $name ]['default'] : false;
		}

		return $default;
	}

	/**
	 * Move some default sections to `general` panel that registered by theme
	 *
	 * @since 1.0.0
	 *
	 * @param object $wp_customize
	 *
	 * @return void
	 */
	public function customize_modify( $wp_customize ) {
		$wp_customize->get_section( 'title_tagline' )->panel     = 'general';
		$wp_customize->get_section( 'static_front_page' )->panel = 'general';
	}

}