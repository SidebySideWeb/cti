<?php

namespace Glozin\Addons\Modules\Advanced_Search;

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
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'advanced_search_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'advanced_search_settings' ), 20, 2 );
	}

	/**
	 * Free Shipping Bar section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function advanced_search_section( $sections ) {
		$sections['advanced_search'] = esc_html__( 'Advanced Search', 'glozin-addons' );

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
	public function advanced_search_settings( $settings, $section ) {
		if ( 'advanced_search' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_advanced_search_options',
				'title' => esc_html__( 'Advanced Search', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_products_by_sku',
				'title'   => esc_html__( 'Search Products by SKU', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_products_by_title',
				'title'   => esc_html__( 'Search Products by Title', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_products_by_content',
				'title'   => esc_html__( 'Search Products by Content', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'id'    => 'glozin_ajax_search_options',
				'type'  => 'sectionend',
			);

			$settings[] = array(
				'id'    => 'glozin_ajax_search_options',
				'title' => esc_html__( 'Ajax Instant Search', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search',
				'title'   => esc_html__( 'Ajax Instant Search', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_number',
				'title'   => esc_html__( 'Limit', 'glozin-addons' ),
				'type'    => 'number',
				'default' => '10',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_products',
				'title'   => esc_html__( 'Autocomplete', 'glozin-addons' ),
				'desc'    => esc_html__( 'Show Products', 'glozin-addons' ),
				'type'    => 'checkbox',
				'checkboxgroup' => 'start',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_categories',
				'desc'   => esc_html__( 'Show Categories', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => '',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_posts',
				'desc'   => esc_html__( 'Show Posts', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_pages',
				'desc'   => esc_html__( 'Show Pages', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_hidden',
				'type'    => 'hidden',
				'checkboxgroup' => 'end',
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_suggestions',
				'title'   => esc_html__( 'Ajax Search Suggestions', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Suggestions type', 'glozin-addons' ),
				'id'      => 'glozin_ajax_search_suggestions_type',
				'default' => 'best_selling',
				'class'   => 'wc-enhanced-select',
				'type'    => 'multiselect',
				'options' => array(
					'recent'       => esc_html__( 'Recently viewed', 'glozin-addons' ),
					'featured'     => esc_html__( 'Featured', 'glozin-addons' ),
					'best_selling' => esc_html__( 'Best Selling', 'glozin-addons' ),
					'top_rated'    => esc_html__( 'Top Rated', 'glozin-addons' ),
					'sale'         => esc_html__( 'On Sale', 'glozin-addons' ),
				),
			);

			$settings[] = array(
				'id'      => 'glozin_ajax_search_suggestions_number',
				'title'   => esc_html__( 'Suggestions limit', 'glozin-addons' ),
				'type'    => 'number',
				'default' => '5',
			);

			$settings[] = array(
				'id'   => 'glozin_advanced_search_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}