<?php
/**
 * Glozin Blog Header functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Glozin Post
 *
 */
class Page_Header {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

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
		add_filter('glozin_page_header_classes', array( $this, 'classes' ));
		add_filter('glozin_get_page_header_elements', array( $this, 'elements' ));

		add_filter('glozin_page_header_description', array( $this, 'page_header_description' ), 20);
		add_filter('glozin_page_header_description_lines', array( $this, 'description_lines' ));
	}

	/**
	 * Page Header Classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function classes( $classes ) {
		$classes .= ' page-header--blog';

		return $classes;
	}

	/**
	 * Page Header Elements
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elements( $items ) {
		$items = \Glozin\Helper::get_option('blog_header') ? (array) \Glozin\Helper::get_option( 'blog_header_els' ) : [];

		return $items;
	}

	/**
	 * Get description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function page_header_description( $description ) {
		if ( is_category() ) {
			$term = get_queried_object();
			if ( $term ) {
				$description = $term->description;
			}
		}

		return $description;
	}

	/**
	 * Description lines
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function description_lines( $number_lines ) {
		$number_lines = \Glozin\Helper::get_option( 'blog_header_description_lines' );

		return $number_lines;
	}
}
