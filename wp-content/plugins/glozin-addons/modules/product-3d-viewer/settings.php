<?php

namespace Glozin\Addons\Modules\Product_3D_Viewer;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'product_3d_viewer_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'product_3d_viewer_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function product_3d_viewer_section( $sections ) {
		$sections['product_3d_viewer'] = esc_html__( 'Product 3D Viewer', 'glozin-addons' );

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
	public function product_3d_viewer_settings( $settings, $section ) {
		if ( 'product_3d_viewer' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_product_3d_viewer_options',
				'title' => esc_html__( 'Product 3D Viewer', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_product_3d_viewer',
				'title'   => esc_html__( 'Product 3D Viewer', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Product 3D Viewer', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_product_3d_viewer_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}