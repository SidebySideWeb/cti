<?php

namespace Glozin\Addons\Modules\Advanced_Linked_Products;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'advanced_linked_products_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'advanced_linked_products_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function advanced_linked_products_section( $sections ) {
		$sections['advanced_linked_products'] = esc_html__( 'Advanced Linked Products', 'glozin-addons' );

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
	public function advanced_linked_products_settings( $settings, $section ) {
		if ( 'advanced_linked_products' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_advanced_linked_products_options',
				'title' => esc_html__( 'Advanced Linked Products', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_advanced_linked_products',
				'title'   => esc_html__( 'Advanced Linked Products', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Advanced Linked Products', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_advanced_linked_products_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}

}