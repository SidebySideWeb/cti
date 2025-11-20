<?php

namespace Glozin\Addons\Modules\Model_Sizing;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'model_sizing_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'model_sizing_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function model_sizing_section( $sections ) {
		$sections['model_sizing'] = esc_html__( "Model's Sizing", 'glozin-addons' );

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
	public function model_sizing_settings( $settings, $section ) {
		if ( 'model_sizing' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_model_sizing_options',
				'title' => esc_html__( "Model's Sizing", 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_model_sizing',
				'title'   => esc_html__( "Model's Sizing", 'glozin-addons' ),
				'desc'    => esc_html__( "Enable Model's Sizing", 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_model_sizing_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}