<?php
/**
 * Glozin Blog Post functions and definitions.
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
class Post {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;


	/**
	 * $fields
	 *
	 * @var $fields
	 */
	protected static $fields = [];

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
	}

	/**
	 * Get entry thumbmail
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function thumbnail( $size = 'large' ) {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$size = apply_filters('glozin_get_post_thumbnail_size', $size);

		$get_image = wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), $size);

		if ( empty( $get_image ) ) {
			return;
		}

		echo sprintf(
			'<a class="post-thumbnail gz-ratio gz-hover-zoom gz-hover-effect overflow-hidden gz-image-rounded" href="%s" aria-hidden="true" tabindex="-1">%s%s</a>',
			esc_url( get_permalink() ),
			$get_image,
			self::get_format_icon()
		);
	}

	/**
	 * Get format
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_format_icon() {
		$icon = '';
		switch ( get_post_format() ) {
			case 'video':
				$icon = \Glozin\Icon::get_svg( 'video', 'ui', array( 'class' => 'post-format-icon icon-video' ) );
				break;

			case 'gallery':
				$icon = \Glozin\Icon::get_svg( 'gallery', 'ui', array( 'class' => 'post-format-icon icon-gallery' ) );
				break;
		}

		return $icon;
	}

	/**
	 * Get post image
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function featured_image() {
		if ( ! has_post_thumbnail() ) {
			return;
		}
		echo '<div class="entry-thumbnail entry-single-thumbnail gz-ratio mb-40">' . get_the_post_thumbnail( get_the_ID(), 'full' ) . '</div>';
	}


	/**
	 * Get entry title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function title($tag = 'h2', $single = false, $class = array() ) {
		if( $single ) {
			$class[] = \Glozin\Blog\Single::sidebar() ? '' : 'text-center';
			the_title( '<'.$tag.' class="entry-title mt-0 mb-15 heading-letter-spacing '.esc_attr(implode(' ', $class)).'">', '</'.$tag.'>' );
		} else {
			the_title( '<'.$tag.' class="entry-title '.esc_attr(implode(' ', $class)).'"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></'.$tag.'>' );
		}
	}


	/**
	 * Get category
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function category( $class = 'gz-blog-badge-light', $single = false ) {
		$categories = get_the_category( get_the_ID() );
		if ( empty( $categories ) ){
			return;
		}

		if( $single ) {
			$class .= \Glozin\Blog\Single::sidebar() ? '' : ' justify-content-center';
		}

		echo '<div class="entry-category d-flex align-items-center '.esc_attr( $class ).' gap-10 mb-15">';
			echo '<a class="d-inline-flex border fs-13 text-dark py-8 px-20 rounded-30 lh-normal" href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . $categories[0]->name . '</a>';
		echo '</div>';
	}


	/**
	 * Meta author
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function author( $dot_between = true ) {
		$byline = sprintf(
		/* translators: %s: post author. */
			esc_html_x( 'Post by %s', 'post author', 'glozin' ),
			'<a class="fw-semibold text-primary ms-5" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
		);

		if( $dot_between ) {
			printf( '<span class="entry-meta__author dot-between d-inline-flex align-items-center">%s</span>', $byline );
		} else {
			printf( '<span class="entry-meta__author d-inline-flex align-items-center">%s</span>', $byline );
		}
	}

	/**
	 * Meta date
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function date( $dot_between = true) {
		if( $dot_between ) {
			printf( '<span class="entry-meta__date dot-between d-inline-flex align-items-center"> <span class="gz-color-dark">%s</span></span>', esc_html( get_the_date() ) );
		} else {
			printf( '<span class="entry-meta__date d-inline-flex align-items-center"> <span class="gz-color-dark">%s</span></span>', esc_html( get_the_date() ) );
		}
	}

	/**
	 * Meta comment
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function comment() {
		echo '<span class="entry-meta__comments">' . get_comments_number() . ' ' . _n('Comment', 'Comments', get_comments_number(), 'glozin') . '</span>';
	}


	/**
	 * Get Excerpt
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function excerpt( $length = 18, $classes = array() ) {
		$classes_entry = ! empty( $classes['entry-excerpt'] ) ? $classes['entry-excerpt'] : array();
		$classes_p = ! empty( $classes['p'] ) ? $classes['p'] : array();
		echo '<div class="entry-excerpt '.esc_attr(implode(' ', $classes_entry)).'">';
			echo \Glozin\Helper::get_content_limit( $length, '', $classes_p );
		echo '</div>';
	}

	/**
	 * Get Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function content() {
		the_content();
	}

	/**
	 * Readmore button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function button() {
		echo sprintf(
			'<div class="entry-read-more"><a class="gz-button gz-button-subtle" href="%s"><span class="glozin-button-text">%s</span> %s</a></div>',
			get_permalink(),
			esc_html__( 'Read More', 'glozin' ),
			\Glozin\Icon::get_svg( 'arrow-top' )
		);
	}

	/**
	 * Meta tag
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function tags() {
		$terms = get_the_terms( get_the_ID(), 'post_tag' );

		if ( empty( $terms ) ) {
			return;
		}

		echo '<div class="entry-tags d-flex align-items-center flex-wrap gap-10">';
			the_tags( '<span class="entry-meta-label text-dark fw-semibold">' . esc_html__( 'Tags:', 'glozin' ) . '</span><ul class="d-flex align-items-center flex-wrap gap-10 my-0 py-0 list-unstyled"><li class="blog-tag">', '</li><li class="blog-tag">', '</li></ul>' );
		echo '</div>';
	}

	/**
	 * Get entry share social
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function share() {
		if( ! \Glozin\Helper::get_option('post_sharing') ) {
			return;
		}
		echo '<div class="entry-meta__share d-flex align-items-center flex-wrap gap-15">';
		echo '<span class="entry-meta-label text-dark fw-semibold">' . esc_html__('Share:', 'glozin') . '</span>';
		echo \Glozin\Helper::share_socials();
		echo '</div>';
	}

	/**
	 * Related post
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_related_terms( $term, $post_id = null ) {
		$post_id     = $post_id ? $post_id : get_the_ID();
		$terms_array = array( 0 );

		$terms = wp_get_post_terms( $post_id, $term );
		foreach ( $terms as $term ) {
			$terms_array[] = $term->term_id;
		}

		return array_map( 'absint', $terms_array );
	}

}
