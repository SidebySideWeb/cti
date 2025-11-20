<?php

namespace Glozin\Addons\Modules\Recent_Sales_Count;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'recent_sales_count_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'recent_sales_count_settings' ), 20, 2 );
		
		add_action( 'admin_init', array( $this, 'clear_recent_sales_count_cache' ) );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function recent_sales_count_section( $sections ) {
		$sections['recent_sales_count'] = esc_html__( 'Recent Sales Count', 'glozin-addons' );

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
	public function recent_sales_count_settings( $settings, $section ) {
		if ( 'recent_sales_count' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_recent_sales_count_options',
				'title' => esc_html__( 'Recent Sales Count', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_recent_sales_count',
				'title'   => esc_html__( 'Recent Sales Count', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable Recent Sales Count', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Random Numbers From', 'glozin-addons' ),
				'id'      => 'glozin_recent_sales_count_random_numbers_from',
				'type'    => 'number',
				'custom_attributes' => array(
					'min'  => 0,
				),
				'default' => '1',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Random Numbers To', 'glozin-addons' ),
				'id'      => 'glozin_recent_sales_count_random_numbers_to',
				'type'    => 'number',
				'custom_attributes' => array(
					'min'  => 1,
				),
				'default' => '100',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Hours', 'glozin-addons' ),
				'id'      => 'glozin_recent_sales_count_hours',
				'type'    => 'number',
				'custom_attributes' => array(
					'min'  => 1,
					'max'  => 50,
				),
				'default' => '7',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Select categories', 'glozin-addons' ),
				'id'      => 'glozin_recent_sales_count_categories',
				'class'   => 'wc-category-search recent-sales-count--condition',
				'type'    => 'multiselect',
				'default' => $this->get_product_selected( 'glozin_recent_sales_count_categories', 'categories', true ),
				'options' => $this->get_product_selected( 'glozin_recent_sales_count_categories', 'categories', false ),
				'custom_attributes' => array(
					'data-action' => 'woocommerce_json_search_categories',
					'data-sortable' => 'true',
					'data-minimum_input_length' => 2,
				),
			);

			$settings[] = array(
				'name'    => esc_html__( 'Select products', 'glozin-addons' ),
				'id'      => 'glozin_recent_sales_count_products',
				'class'   => 'wc-product-search recent-sales-count--condition',
				'type'    => 'multiselect',
				'default' => $this->get_product_selected( 'glozin_recent_sales_count_products', 'product', true ),
				'options' => $this->get_product_selected( 'glozin_recent_sales_count_products', 'product', false ),
				'custom_attributes' => array(
					'data-action' => 'woocommerce_json_search_products_and_variations',
					'data-sortable' => 'true',
					'data-minimum_input_length' => 2,
				),
			);

			$settings[] = array(
				'id'      => 'glozin_recent_sales_count_out_of_stock',
				'title'   => esc_html__( 'Out of stock products', 'glozin-addons' ),
				'desc'    => esc_html__( 'Show out of stock products', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'   => 'glozin_recent_sales_count_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}

	/**
	 * Get product selected function
	 *
	 * @return void
	 */
	public function get_product_selected( $option, $type = 'product', $only = false ) {
		$ids = get_option( $option );
		$json_ids    = array();

		if( empty( $ids ) && ! $ids ) {
			if( $only ) {
				return '';
			} else {
				return [];
			}
		}

		foreach ( (array) $ids as $id ) {
			if( $type == 'product' ) {
				$product = wc_get_product( $id );
				$name = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
			} else {
				$name = get_term_by( 'slug', $id, 'product_cat' )->name;
			}

			if( $only ) {
				$json_ids[] = $id;
			} else {
				$json_ids[ $id ] = $name;
			}
		}

		if( $only ) {
			return implode( ' ', $json_ids );
		} else {
			return $json_ids;
		}
	}

	/**
	 * Clear cart tracking
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear_recent_sales_count_cache() {
		if ( isset( $_REQUEST['section'] ) && 'glozin_recent_sales_count' === $_REQUEST['section'] ) {
			if( isset( $_POST['glozin_recent_sales_count_transient'] ) ) {
				delete_transient( 'recent_sales_count_transient' );
			}
		}
	}
}