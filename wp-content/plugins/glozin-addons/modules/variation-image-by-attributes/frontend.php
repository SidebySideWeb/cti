<?php

namespace Glozin\Addons\Modules\Variation_Image_By_Attributes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Cache for chosen layered nav attributes
	 *
	 * @var array|null
	 */
	private $chosen_attributes_cache = null;

	/**
	 * Cache for normalized filters
	 *
	 * @var array|null
	 */
	private $normalized_filters_cache = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
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
		add_filter('woocommerce_product_get_image_id', array( $this, 'product_get_image_id' ), 20, 2 );

	}

	public function product_get_image_id( $image_id, $product ) {
		if ( ! \Glozin\Addons\Helper::is_catalog() || ! $product->is_type( 'variable' ) ) {
			return $image_id;
		}

		// Get chosen attributes with caching
		$chosen = $this->get_chosen_attributes();
		if ( empty( $chosen ) ) {
			return $image_id;
		}
		
		// Get normalized filters with caching
		$filters = $this->get_normalized_filters( $chosen );
		if ( empty( $filters ) ) {
			return $image_id;
		}
		
		// Check if product has relevant attributes
		$relevant_filters = $this->get_relevant_filters( $product, $filters );
		if ( empty( $relevant_filters ) ) {
			return $image_id;
		}
		
		// Find matching variation with caching
		$matching_variation_id = $this->get_matching_variation( $product, $relevant_filters );
		if ( $matching_variation_id ) {
			$variation_image_id = $this->get_variation_image_id( $matching_variation_id );
			if ( $variation_image_id ) {
				return $variation_image_id;
			}
		}

		// Fallback to the original product image id
		return $image_id;
	}

	/**
	 * Get chosen layered nav attributes with caching
	 */
	private function get_chosen_attributes() {
		if ( $this->chosen_attributes_cache === null ) {
			$this->chosen_attributes_cache = \WC_Query::get_layered_nav_chosen_attributes();
		}
		
		return empty( $this->chosen_attributes_cache ) || ! is_array( $this->chosen_attributes_cache ) 
			? [] 
			: $this->chosen_attributes_cache;
	}

	/**
	 * Get normalized filters with caching
	 */
	private function get_normalized_filters( $chosen ) {
		if ( $this->normalized_filters_cache === null ) {
			$this->normalized_filters_cache = $this->normalize_chosen_filters( $chosen );
		}
		
		return empty( $this->normalized_filters_cache ) ? [] : $this->normalized_filters_cache;
	}

	/**
	 * Get relevant filters for a specific product
	 */
	private function get_relevant_filters( $product, $filters ) {
		$product_attr_tax = array_keys( (array) $product->get_variation_attributes() );
		return array_intersect_key( $filters, array_flip( $product_attr_tax ) );
	}

	/**
	 * Get matching variation with WordPress object cache
	 */
	private function get_matching_variation( $product, $relevant_filters ) {
		$cache_key = $product->get_id() . '_' . md5( serialize( $relevant_filters ) );
		$matching_variation_id = wp_cache_get( $cache_key, 'glozin_variation_images' );
		
		if ( $matching_variation_id === false ) {
			$matching_variation_id = $this->find_matching_variation( $product, $relevant_filters );
			wp_cache_set( $cache_key, $matching_variation_id, 'glozin_variation_images', HOUR_IN_SECONDS );
		}
		
		return $matching_variation_id;
	}

	/**
	 * Get variation image ID
	 */
	private function get_variation_image_id( $variation_id ) {
		$variation = wc_get_product( $variation_id );
		return $variation ? $variation->get_image_id() : null;
	}

	/**
	 * Normalize chosen filters from layered nav attributes
	 */
	private function normalize_chosen_filters( $chosen ) {
		$filters = [];
		foreach ( $chosen as $tax => $data ) {
			if ( ! empty( $data['terms'] ) && is_array( $data['terms'] ) ) {
				$filters[ $tax ] = array_map( 'wc_sanitize_taxonomy_name', array_map( 'sanitize_title', $data['terms'] ) );
			}
		}
		return $filters;
	}

	/**
	 * Find variation that matches all relevant filters
	 */
	private function find_matching_variation( $product, $relevant_filters ) {
		foreach ( $product->get_visible_children() as $child_id ) {
			$variation = wc_get_product( $child_id );
			if ( ! $variation || ! $variation->is_type( 'variation' ) || ! $variation->is_in_stock() ) {
				continue;
			}

			$variation_attrs = $variation->get_attributes();
			$matches_all = true;

			foreach ( $relevant_filters as $tax => $slugs ) {
				if ( empty( $variation_attrs[ $tax ] ) ) {
					$matches_all = false;
					break;
				}
				
				$variation_slug = sanitize_title( $variation_attrs[ $tax ] );
				if ( ! in_array( $variation_slug, $slugs, true ) ) {
					$matches_all = false;
					break;
				}
			}

			if ( $matches_all ) {
				return $child_id;
			}
		}
		
		return null;
	}
}