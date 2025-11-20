<?php

namespace Glozin\Addons\Modules\Variation_Image_By_Attributes;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'variation_image_by_attributes_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'variation_image_by_attributes_settings' ), 20, 2 );
	}

	/**
	 * Variation Image Shop Filter section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function variation_image_by_attributes_section( $sections ) {
		$sections['variation_image_by_attributes'] = esc_html__( 'Variation Image by Attributes', 'glozin-addons' );

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
	public function variation_image_by_attributes_settings( $settings, $section ) {
		if ( 'variation_image_by_attributes' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_variation_image_by_attributes_options',
				'title' => esc_html__( 'Variation Image by Attribute', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_variation_image_by_attributes',
				'title'   => esc_html__( 'Variation Image by Attribute', 'glozin-addons' ),
				'desc_tip'    => esc_html__( 'Change variation image on the Shop page based on selected attribute filter like color.', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_variation_image_by_attributes_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}