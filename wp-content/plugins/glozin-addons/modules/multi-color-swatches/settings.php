<?php

namespace Glozin\Addons\Modules\Multi_Color_Swatches;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Settings  {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'multi_color_swatches_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'multi_color_swatches_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function multi_color_swatches_section( $sections ) {
		$sections['multi_color_swatches'] = esc_html__( 'Dual Color Swatches', 'glozin-addons' );

		return $sections;
	}

	/**
	 * Adds settings to product display settings
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param string $section
	 *
	 * @return array
	 */
	public function multi_color_swatches_settings( $settings, $section ) {
		if ( 'multi_color_swatches' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_multi_color_swatches_options',
				'title' => esc_html__( 'Dual Color Swatches', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_multi_color_swatches',
				'title'   => esc_html__( 'Dual Color Swatches', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Dual Color Swatches', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_multi_color_swatches_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}