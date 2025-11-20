<?php

namespace Glozin\Addons\Modules\Products_Stock_Progress_Bar;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'products_stock_progress_bar_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'products_stock_progress_bar_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function products_stock_progress_bar_section( $sections ) {
		$sections['products_stock_progress_bar'] = esc_html__( 'Products Stock Progress Bar', 'glozin-addons' );

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
	public function products_stock_progress_bar_settings( $settings, $section ) {
		if ( 'products_stock_progress_bar' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_products_stock_progress_bar_options',
				'title' => esc_html__( 'Products Stock Progress Bar', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_products_stock_progress_bar',
				'title'   => esc_html__( 'Products Stock Progress Bar', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Products Stock Progress Bar', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'   => 'glozin_products_stock_progress_bar_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}