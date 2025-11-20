<?php

namespace Glozin\Addons\Modules\Checkout_Limit;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'checkout_limit_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'checkout_limit_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function checkout_limit_section( $sections ) {
		$sections['checkout_limit'] = esc_html__( 'Checkout Countdown', 'glozin-addons' );

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
	public function checkout_limit_settings( $settings, $section ) {
		if ( 'checkout_limit' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_checkout_limit_options',
				'title' => esc_html__( 'Checkout Countdown', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_checkout_limit',
				'title'   => esc_html__( 'Checkout Countdown', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Checkout Countdown', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Countdown Time (seconds)', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_time',
				'type'    => 'number',
				'class'   => 'glozin_checkout_limit_time',
				'custom_attributes' => array(
					'min'  => 0,
				),
				'default' => '120',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Action on Countdown Completion', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_action',
				'default' => '',
				'class'   => 'glozin_checkout_limit_action wc-enhanced-select',
				'type'    => 'select',
				'options' => array(
					''           => esc_html__( 'No Action', 'glozin-addons' ),
					'empty_cart' => esc_html__( 'Empty Cart', 'glozin-addons' ),
				),
			);

			$settings[] = array(
				'name'    => esc_html__( 'Wait time before empty cart (seconds)', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_empty_cart_time',
				'type'    => 'number',
				'class'   => 'glozin_checkout_limit_empty_cart_time',
				'custom_attributes' => array(
					'min'  => 3,
					'step' => 1,
				),
				'default' => '3',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Display On', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_display_on',
				'type'    => 'multiselect',
				'class'   => 'wc-enhanced-select glozin_checkout_limit_display_on',
				'options' => array(
					'minicart' => esc_html__( 'Mini Cart', 'glozin-addons' ),
					'cart'     => esc_html__( 'Cart Page', 'glozin-addons' ),
					'checkout' => esc_html__( 'Checkout Page', 'glozin-addons' ),
				),
				'default' => array( 'minicart', 'cart' ),
			);

			$settings[] = array(
				'name'    => esc_html__( 'Mini Cart Countdown Text', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_countdown_text_mini_cart',
				'type'    => 'textarea',
				'class'   => 'glozin_checkout_limit_countdown_text_mini_cart',
				'custom_attributes' => array(
					'rows' => 3,
				),
				'default' => '',
				'placeholder' => esc_html__( 'Products are limited, checkout within {time}', 'glozin-addons' ),
			);

			$settings[] = array(
				'name'    => esc_html__( 'Cart Page Countdown Text', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_countdown_text_cart_page',
				'type'    => 'textarea',
				'class'   => 'glozin_checkout_limit_countdown_text_cart_page',
				'custom_attributes' => array(
					'rows' => 3,
				),
				'default' => '',
				'placeholder' => esc_html__( 'Products are limited, checkout within {time}', 'glozin-addons' ),
			);

			$settings[] = array(
				'name'    => esc_html__( 'Checkout Page Countdown Text', 'glozin-addons' ),
				'id'      => 'glozin_checkout_limit_countdown_text_checkout_page',
				'type'    => 'textarea',
				'class'   => 'glozin_checkout_limit_countdown_text_checkout_page',
				'custom_attributes' => array(
					'rows' => 3,
				),
				'default' => '',
				'placeholder' => esc_html__( 'Products are limited, checkout within {time}', 'glozin-addons' ),
			);

			$settings[] = array(
				'id'   => 'glozin_checkout_limit_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}