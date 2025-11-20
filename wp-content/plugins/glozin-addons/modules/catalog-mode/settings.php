<?php

namespace Glozin\Addons\Modules\Catalog_Mode;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'catalog_mode_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'catalog_mode_settings' ), 20, 2 );

	}

	/**
	 * Catalog mode section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function catalog_mode_section( $sections ) {
		$sections['catalog_mode'] = esc_html__( 'Catalog Mode', 'glozin-addons' );

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
	public function catalog_mode_settings( $settings, $section ) {
		if ( 'catalog_mode' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_catalog_mode_options',
				'title' => esc_html__( 'Catalog Mode', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_catalog_mode',
				'title'   => esc_html__( 'Catalog Mode', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Catalog Mode', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			// Price
			$settings[] = array(
				'name'          => esc_html__( 'Price', 'glozin-addons' ),
				'desc'          => esc_html__( 'Hide in the product loop', 'glozin-addons' ),
				'id'            => 'glozin_product_loop_hide_price',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			);

			if ( class_exists( 'WCBoost\Wishlist\Helper' ) ) {
				$settings[] = array(
					'desc'          => esc_html__( 'Hide in the wishlist page', 'glozin-addons' ),
					'id'            => 'glozin_wishlist_hide_price',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				);
			}

			if ( class_exists( 'WCBoost\ProductsCompare\Plugin' ) ) {
				$settings[] = array(
					'desc'          => esc_html__( 'Hide in the compare page', 'glozin-addons' ),
					'id'            => 'glozin_compare_hide_price',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				);
			}

			$settings[] = array(
				'desc'          => esc_html__( 'Hide in the product page', 'glozin-addons' ),
				'id'            => 'glozin_product_hide_price',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			);

			// Add to Cart
			$settings[] = array(
				'name'          => esc_html__( 'Add to Cart', 'glozin-addons' ),
				'desc'          => esc_html__( 'Hide in the product loop', 'glozin-addons' ),
				'id'            => 'glozin_product_loop_hide_atc',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			);

			if ( class_exists( 'WCBoost\Wishlist\Helper' ) ) {
				$settings[] = array(
					'desc'          => esc_html__( 'Hide in the wishlist page', 'glozin-addons' ),
					'id'            => 'glozin_wishlist_hide_atc',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				);
			}

			if ( class_exists( 'WCBoost\ProductsCompare\Plugin' ) ) {
				$settings[] = array(
					'desc'          => esc_html__( 'Hide in the compare page', 'glozin-addons' ),
					'id'            => 'glozin_compare_hide_atc',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				);
			}

			$settings[] = array(
				'desc'          => esc_html__( 'Hide in the product page', 'glozin-addons' ),
				'id'            => 'glozin_product_hide_atc',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			);

			// Page
			$settings[] = array(
				'name'          => esc_html__( 'Page', 'glozin-addons' ),
				'desc'          => esc_html__( 'Hide in the woocommerce cart page', 'glozin-addons' ),
				'id'            => 'glozin_hide_cart_page',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			);

			$settings[] = array(
				'desc'          => esc_html__( 'Hide in the woocommerce checkout page', 'glozin-addons' ),
				'id'            => 'glozin_hide_checkout_page',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			);

			// User
			$settings[] = array(
				'name'    => esc_html__( 'Apply catalog mode to', 'glozin-addons' ),
				'id'      => 'glozin_catalog_mode_user',
				'default' => 'all_user',
				'type'    => 'radio',
				'options' => array(
					'all_user'   => esc_html__( 'All User', 'glozin-addons' ),
					'guest_user' => esc_html__( 'Only guest user', 'glozin-addons' ),
				),
			);

			$settings[] = array(
				'id'   => 'glozin_catalog_mode_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}