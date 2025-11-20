<?php

namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Glozin\Addons\Elementor\Builder\Helper;

/**
 * Main class of plugin for admin
 */
class Navigation_Bar {

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
	 * Navigation id
	 *
	 * @var $navigation_bar_id
	 */
	private static $navigation_bar_id;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'glozin_after_site', array( $this, 'navigation_bar_content_builder' ), 50 );
	}

	public function enqueue_scripts() {
		if( ! apply_filters( 'glozin_get_navigation_bar_builder', true ) ) {
			return;
		}

		$css_file = '';

		if( empty( self::get_navigation_bar_id() ) ) {
			return;
		}

		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( intval( self::get_navigation_bar_id() ) );
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( intval( self::get_navigation_bar_id() ) );
		}

		if( $css_file ) {
			$css_file->enqueue();
		}
	}

    public function navigation_bar_content_builder() {
		if( ! apply_filters( 'glozin_get_navigation_bar_builder', true ) ) {
			return;
		}

		if( empty( self::get_navigation_bar_id() ) ) {
			return;
		}

		echo '<div id="glozin-mobile-navigation-bar" class="glozin-mobile-navigation-bar position-fixed start-0 end-0 bottom-0 bg-light shadow d-none-md">';
		$elementor_instance = \Elementor\Plugin::instance();
		echo $elementor_instance->frontend->get_builder_content_for_display( intval( self::get_navigation_bar_id() ) );
		echo '</div>';
    }

	/**
	 * Get footer id
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_navigation_bar_id() {
		if( isset( self::$navigation_bar_id ) ) {
			return self::$navigation_bar_id;
		}

		$navigation_bar_id = 0;
		if( is_page() || \Glozin\Addons\Helper::is_blog() || \Glozin\Addons\Helper::is_catalog() ) {
			$navigation_bar_id = $this->get_query();
		}

		if( empty( $navigation_bar_id ) ) {
			$navigation_bar_id = $this->get_query( true );
		}

		self::$navigation_bar_id =  $navigation_bar_id;

		return self::$navigation_bar_id;
	}

	public function get_query($get_all = false) {
		$post_id = \Glozin\Addons\Helper::get_post_ID();
		$post_id = empty( $post_id ) ? '-1' : $post_id;
		if( $get_all ) {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => 'page_include',
					'value'   => [0],
					'compare' => 'IN',
				),
				array(
					'key' => 'page_exclude',
					'value' => ',' . $post_id .',',
					'compare' => 'NOT LIKE',
				),
			);

		} else {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => 'page_include',
					'value'   =>  ',' . $post_id .',',
					'compare' => 'LIKE',
				),
				array(
					'key' => 'page_exclude',
					'value' =>  ',' . $post_id .',',
					'compare' => 'NOT LIKE',
				),
			);
		}
		$query = new \WP_Query( array(
			'post_type'        => 'glozin_builder',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'meta_key'         => 'page_include',
			'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'tax_query' => array(
				array(
					'taxonomy' => 'glozin_builder_type',
					'field' => 'slug',
					'operator' => 'AND',
					'terms' => array( 'navigation_bar', 'enable' )
				),
			),
			'meta_query' => $meta_query
		));

		$navigation_bar_id = $query->posts ? $query->posts[0] : 0;
		wp_reset_postdata();
		return $navigation_bar_id;
	}
}