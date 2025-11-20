<?php

namespace Glozin\Addons\Modules\Add_To_Cart_Ajax;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'add_to_cart_ajax_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'add_to_cart_ajax_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_to_cart_ajax_section( $sections ) {
		$sections['add_to_cart_ajax'] = esc_html__( 'Add To Cart Ajax', 'glozin-addons' );

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
	public function add_to_cart_ajax_settings( $settings, $section ) {
		if ( 'add_to_cart_ajax' == $section ) {
			$settings = array();
			$settings[] = array(
				'id'    => 'glozin_add_to_cart_ajax_options',
				'title' => esc_html__( 'Add To Cart Ajax', 'glozin-addons' ),
				'type'  => 'title',
			);
			$settings[] = array(
				'id'      => 'glozin_add_to_cart_ajax_enable',
				'title'   => esc_html__( 'Enable Add To Cart Ajax', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Add To Cart Ajax', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'   => 'glozin_add_to_cart_ajax_options',
				'type' => 'sectionend',
			);

		}

		return $settings;
	}
}