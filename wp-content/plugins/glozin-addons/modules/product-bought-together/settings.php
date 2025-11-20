<?php

namespace Glozin\Addons\Modules\Product_Bought_Together;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'product_bought_together_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'product_bought_together_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function product_bought_together_section( $sections ) {
		$sections['product_bought_together'] = esc_html__( 'Product Bought Together', 'glozin-addons' );

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
	public function product_bought_together_settings( $settings, $section ) {
		if ( 'product_bought_together' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_product_bought_together_options',
				'title' => esc_html__( 'Product Bought Together', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_product_bought_together',
				'title'   => esc_html__( 'Product Bought Together', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Product Bought Together', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_product_bought_together_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}

}