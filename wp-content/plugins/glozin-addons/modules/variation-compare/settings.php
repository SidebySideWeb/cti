<?php

namespace Glozin\Addons\Modules\Variation_Compare;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Settings {

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'variation_compare_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'variation_compare_settings' ), 20, 2 );
	}

	/**
	 * Buy Now section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function variation_compare_section( $sections ) {
		$sections['variation_compare'] = esc_html__( 'Variation Compare', 'glozin-addons' );

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
	public function variation_compare_settings( $settings, $section ) {
		if ( 'variation_compare' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_variation_compare_options',
				'title' => esc_html__( 'Variation Compare', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_variation_compare_toggle',
				'title'   => esc_html__( 'Variation Compare', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Variation Compare', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'      => 'glozin_variation_compare_primary',
				'title'   => esc_html__( 'Primary Attribute', 'glozin-addons' ),
				'type'    => 'select',
				'default' => '',
				'options' => $this->get_product_attributes()
			);

			$settings[] = array(
				'id'   => 'glozin_variation_compare_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}

	/**
	* Get product attributes
	*
	* @return string
	*/
	public function get_product_attributes() {
		$output = array();
		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$attributes_tax = wc_get_attribute_taxonomies();
			if ( $attributes_tax ) {
				$output[''] = esc_html__( 'None', 'glozin-addons' );

				foreach ( $attributes_tax as $attribute ) {
					$output[$attribute->attribute_name] = $attribute->attribute_label;
				}

			}
		}

		return $output;
	}
}