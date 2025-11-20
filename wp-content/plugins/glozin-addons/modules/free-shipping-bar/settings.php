<?php

namespace Glozin\Addons\Modules\Free_Shipping_Bar;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'free_shipping_bar_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'free_shipping_bar_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function free_shipping_bar_section( $sections ) {
		$sections['free_shipping_bar'] = esc_html__( 'Free Shipping Bar', 'glozin-addons' );

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
	public function free_shipping_bar_settings( $settings, $section ) {
		if ( 'free_shipping_bar' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_free_shipping_bar_options',
				'title' => esc_html__( 'Free Shipping Bar', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_free_shipping_bar',
				'title'   => esc_html__( 'Free Shipping Bar', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Free Shipping Bar', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'desc'    => esc_html__( 'Checkout page', 'glozin-addons' ),
				'id'      => 'glozin_free_shipping_bar_checkout_page',
				'default' => 'yes',
				'type'    => 'checkbox',
				'checkboxgroup' => '',
				'checkboxgroup' => 'start',
			);

			$settings[] = array(
				'desc'    => esc_html__( 'Cart page', 'glozin-addons' ),
				'id'      => 'glozin_free_shipping_bar_cart_page',
				'default' => 'yes',
				'type'    => 'checkbox',
				'checkboxgroup' => '',
			);

			$settings[] = array(
				'desc'    => esc_html__( 'Mini cart', 'glozin-addons' ),
				'id'      => 'glozin_free_shipping_bar_mini_cart',
				'default' => 'yes',
				'type'    => 'checkbox',
				'checkboxgroup' => 'end'
			);

			$settings[] = array(
				'id'   => 'glozin_free_shipping_bar_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}

}