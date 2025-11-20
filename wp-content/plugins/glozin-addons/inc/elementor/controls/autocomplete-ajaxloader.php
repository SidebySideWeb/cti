<?php

namespace Glozin\Addons\Elementor\Controls;

use OTGS\Installer\Rest\Push;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AutoComplete_AjaxLoader {

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
		// Get Autocomplete
		add_action( 'wp_ajax_glozin_get_autocomplete_suggest', [ $this, 'glozin_get_autocomplete_suggest' ] );
		add_action( 'wp_ajax_glozin_get_autocomplete_render', [ $this, 'glozin_get_autocomplete_render' ] );
	}

	/**
	 * Autocomplete Suggest
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function glozin_get_autocomplete_suggest() {
		$result = [];

		$sources = $_POST && isset( $_POST['source'] ) ? $_POST['source'] : '';
		if ( ! empty( $sources ) ) {
			$sources = explode( ',', $sources );
			foreach( $sources as $source ) {
				$source = trim($source);
				$output = call_user_func( array( $this, 'glozin_autocomplete_' . $source . '_callback' ) );

				if( is_array( $output ) ) {
					$result = array_merge( $output, $result );
				}
			}
		}

		wp_send_json_success( $result );

		exit;
	}

	/**
	 * Product cat callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_cat_callback() {
		return $this->glozin_autocomplete_taxonomy_callback( 'product_cat' );
	}

	/**
	 * Product tag callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_tag_callback() {
		return $this->glozin_autocomplete_taxonomy_callback( 'product_tag' );
	}

	/**
	 * Product brand callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_brand_callback() {
		return $this->glozin_autocomplete_taxonomy_callback( 'product_brand' );
	}

	/**
	 * Product brand callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_author_callback() {
		return $this->glozin_autocomplete_taxonomy_callback( 'product_author' );
	}

	/**
	 * Help Center callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_glozin_help_cat_callback() {
		return $this->glozin_autocomplete_taxonomy_callback( 'glozin_help_cat' );
	}

	/**
	 * Product callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_callback() {
		return $this->glozin_autocomplete_post_type_callback( 'product' );
	}

	/**
	 * Post callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_post_callback() {
		return $this->glozin_autocomplete_post_type_callback( 'post' );
	}

	/**
	 * Page callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_page_callback() {
		return $this->glozin_autocomplete_post_type_callback( 'page' );
	}

	/**
	 * Help Center callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_elementor_library_callback() {
		return $this->glozin_autocomplete_post_type_callback( 'elementor_library' );
	}

	/**
	 * Shoppable images callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_shoppable_images_callback() {
		return $this->glozin_autocomplete_post_type_callback( 'shoppable_images' );
	}
	
	/**
	 * Category callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_category_callback() {
		return $this->glozin_autocomplete_taxonomy_callback( 'category' );
	}

	/**
	 * Attribute callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_attribute_callback() {
		return $this->glozin_autocomplete_attributes_callback();
	}

	/**
	 * Autocomplete Render
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function glozin_get_autocomplete_render() {
		$result = [];

		$sources = $_POST && isset( $_POST['source'] ) ? $_POST['source'] : '';
		if ( ! empty( $sources ) ) {
			$sources = explode( ',', $sources );
			foreach( $sources as $source ) {
				$source = trim($source);
				$output = call_user_func( array( $this, 'glozin_autocomplete_' . $source . '_render' ) );
				if( is_array( $output ) ) {
					$result = array_merge( $output, $result );
				}
			}
		}


		wp_send_json_success( $result );

		die();
	}

	/**
	 * Product cat render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_cat_render() {
		return $this->glozin_autocomplete_taxonomy_render( 'product_cat' );
	}

	/**
	 * Product tag render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_tag_render() {
		return $this->glozin_autocomplete_taxonomy_render( 'product_tag' );
	}

	/**
	 * Product brand render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_brand_render() {
		return $this->glozin_autocomplete_taxonomy_render( 'product_brand' );
	}

	/**
	 * Product brand render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_author_render() {
		return $this->glozin_autocomplete_taxonomy_render( 'product_author' );
	}


	/**
	 * Product render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_product_render() {
		return $this->glozin_autocomplete_post_type_render( 'product' );
	}

	/**
	 * Post render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_post_render() {
		return $this->glozin_autocomplete_post_type_render( 'post' );
	}

	/**
	 * Page render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_page_render() {
		return $this->glozin_autocomplete_post_type_render( 'page' );
	}

	/**
	 * Help Center render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_elementor_library_render() {
		return $this->glozin_autocomplete_post_type_render( 'elementor_library' );
	}

	/**
	 * Shoppable images render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_shoppable_images_render() {
		return $this->glozin_autocomplete_post_type_render( 'shoppable_images' );
	}

	/**
	 * Category render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_category_render() {
		return $this->glozin_autocomplete_taxonomy_render( 'category' );
	}

	/**
	 * Attribute render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_attribute_render() {
		return $this->glozin_autocomplete_attributes_render();
	}

	/**
	 * Taxonomy Autocomplete
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_taxonomy_callback( $taxonomy = 'category' ) {
		$cat_id = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : 0;
		$query  = $_POST && isset( $_POST['term'] ) ? trim( $_POST['term'] ) : '';

		$result = array();

		global $wpdb;

		$post_meta_infos = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.term_id AS id, b.name as name, b.slug AS slug
						FROM {$wpdb->term_taxonomy} AS a
						INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
						WHERE a.taxonomy = %s AND (a.term_id = '%d' OR b.slug LIKE '%%%s%%' OR b.name LIKE '%%%s%%' )", $taxonomy, $cat_id > 0 ? $cat_id : - 1, stripslashes( $query ), stripslashes( $query )
			), ARRAY_A
		);

		// $post_meta_infos = get_terms( array(
		// 		'taxonomy' => $taxonomy,
		// 		'search' => $query
		// 	) );

		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data          = array();
				$data['value'] = $value['slug'];
				$data['label'] = esc_html__( 'Id', 'glozin-addons' ) . ': ' . $value['id'] . ' - ' . esc_html__( 'Name', 'glozin-addons' ) . ': ' . $value['name'];
				$result[]      = $data;
			}
		} else {
			$result[] = array(
				'value' => 'nothing-found',
				'label' => esc_html__( 'Nothing Found', 'glozin-addons' )
			);
		}

		return $result;
	}

	/**
	 * Post type Autocomplete
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_post_type_callback( $post_type = 'product' ) {
		$query  = $_POST && isset( $_POST['term'] ) ? trim( $_POST['term'] ) : '';
		$result = array();

		$args = array(
			'post_type'              => $post_type,
			'posts_per_page'         => - 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'ignore_sticky_posts'    => true,
			's'                      => $query
		);

		$posts = get_posts( $args );

		if ( is_array( $posts ) && ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$data          = array();
				$data['value'] = $post->ID;
				$data['label'] = esc_html__( 'Id', 'glozin-addons' ) . ': ' . $post->ID . ' - ' . esc_html__( 'Title', 'glozin-addons' ) . ': ' . $post->post_title;
				$result[]      = $data;
			}
		} else {
			$result[] = array(
				'value' => 'nothing-found',
				'label' => esc_html__( 'Nothing Found', 'glozin-addons' )
			);
		}

		return $result;
	}

	/**
	 * Attributes Autocomplete
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_attributes_callback() {
		$result = array();

		$attributes = wc_get_attribute_taxonomies();
		
		if ( is_array( $attributes ) && ! empty( $attributes ) ) {
			foreach ($attributes as $attribute) {
				$taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
    			$terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

				foreach ($terms as $term) {
					$data          = array();
					$data['value'] = $term->slug;
					$data['label'] = $term->name;
					$result[]      = $data;
				}
			}
		} else {
			$result[] = array(
				'value' => 'nothing-found',
				'label' => esc_html__( 'Nothing Found', 'glozin-addons' )
			);
		}

		return $result;
	}


	/**
	 * Taxonomy Render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_taxonomy_render( $taxonomy = 'category' ) {
		$query = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : '';

		if ( empty( $query ) ) {
			return false;
		}

		$data   = array();
		$values = explode( ',', $query );

		$terms = get_terms(
			array(
				'taxonomy' => $taxonomy,
				'slug'     => $values,
				'orderby'  => 'slug__in'
			)
		);

		if ( is_wp_error( $terms ) || ! $terms ) {
			return false;
		}

		foreach ( $terms as $term ) {

			$data[] = sprintf(
				'<li class="glozin_autocomplete-label" data-value="%s">
					<span class="glozin_autocomplete-data">%s%s - %s%s</span>
					<a href="#" class="glozin_autocomplete-remove">&times;</a>
				</li>',
				esc_attr( $term->slug ),
				esc_html__( 'Id: ', 'glozin-addons' ),
				esc_html( $term->term_id ),
				esc_html__( 'Name: ', 'glozin-addons' ),
				esc_html( $term->name )
			);
		}

		return $data;
	}

	/**
	 * Post Type Render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_post_type_render( $post_type = 'product' ) {
		$query = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : '';

		if ( empty( $query ) ) {
			return false;
		}

		$values = explode( ',', $query );

		$data = [];

		$args = [
			'post_type'              => $post_type,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'ignore_sticky_posts'    => true,
			'post__in'               => $values,
			'orderby'                => 'post__in'
		];

		$query = new \WP_Query( $args );
		while ( $query->have_posts() ) : $query->the_post();
			$data[] = sprintf(
				'<li class="glozin_autocomplete-label" data-value="%s">
					<span class="glozin_autocomplete-data">%s%s - %s%s</span>
					<a href="#" class="glozin_autocomplete-remove">&times;</a>
				</li>',
				esc_attr( get_the_ID() ),
				esc_html__( 'Id: ', 'glozin-addons' ),
				esc_html( get_the_ID() ),
				esc_html__( 'Title: ', 'glozin-addons' ),
				esc_html( get_the_title() )
			);
		endwhile;
		wp_reset_postdata();

		return $data;
	}

	/**
	 * Attributes Render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function glozin_autocomplete_attributes_render() {
		$query = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : '';

		if ( empty( $query ) ) {
			return false;
		}

		$values = explode( ',', $query );

		$attributes = wc_get_attribute_taxonomies();
		$data   = array();
		foreach ($attributes as $attribute) {
			$taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
    		$terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

			foreach ($terms as $term) {
				if( in_array( $term->name, $values ) || in_array( $term->slug, $values ) ) {
					$data[] = sprintf(
						'<li class="glozin_autocomplete-label" data-value="%s">
							<span class="glozin_autocomplete-data">%s</span>
							<a href="#" class="glozin_autocomplete-remove">&times;</a>
						</li>',
						esc_attr( $term->slug ),
						esc_html( $term->name )
					);
				}
			}
		}

		return $data;
	}
}