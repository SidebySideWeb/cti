<?php
/**
 * Breadcrumbs functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Breadcrumbs
 *
 */
class Breadcrumb {
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
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'breadcrumb_args' ) );
	}

	/**
	 * Changes breadcrumb args.
	 *
	 * @param array $args The breadcrumb argurments.
	 *
	 * @return array
	 */
	public function breadcrumb_args( $args ) {
		$args['delimiter']   = '<span class="dot-between d-inline-flex align-items-center"></span>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb site-breadcrumb d-flex flex-wrap align-items-center fs-14">';
		$args['wrap_after']  = '</nav>';

		return $args;
	}

	/**
	 * Display the breadcrumbs
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function breadcrumb( $args = array() ) {
		if ( function_exists( 'yoast_breadcrumb' ) && class_exists( 'WPSEO_Options' ) ) {
			if ( \WPSEO_Options::get( 'breadcrumbs-enable', false ) ) {
				$classes ='site-breadcrumb d-flex flex-wrap align-items-center fs-14';
				if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
					$classes .= ' woocommerce-breadcrumb';
				}
				return yoast_breadcrumb( '<nav class="' . $classes  . '">', '</nav>' );
			}
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			woocommerce_breadcrumb($args);
		} else {
			$this->get_breadcrumb( $args );
		}

	}

	/**
	 * Get breadcrumb HTML
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_breadcrumb( $args = '' ) {
		$args = wp_parse_args(
			$args, array(
				'separator'         => 	'<span class="dot-between d-inline-flex align-items-center"></span>',
				'home_class'        => 'home',
				'before'            => '',
				'before_item'       => '',
				'after_item'        => '',
				'taxonomy'          => 'category',
				'display_last_item' => true,
				'show_on_front'     => true,
				'labels'            => array(
					'home'      => esc_html__( 'Home', 'glozin' ),
					'archive'   => esc_html__( 'Archives', 'glozin' ),
					'blog'      => esc_html__( 'Blog', 'glozin' ),
					'search'    => esc_html__( 'Search results for', 'glozin' ),
					'not_found' => esc_html__( 'Not Found', 'glozin' ),
					'portfolio' => esc_html__( 'Portfolio', 'glozin' ),
					'author'    => esc_html__( 'Author:', 'glozin' ),
					'day'       => esc_html__( 'Daily:', 'glozin' ),
					'month'     => esc_html__( 'Monthly:', 'glozin' ),
					'year'      => esc_html__( 'Yearly:', 'glozin' ),
				),
			)
		);

		$args = apply_filters( 'glozin_breadcrumbs_args', $args );

		if ( is_front_page() && ! $args['show_on_front'] ) {
			return;
		}

		$items = array();

		// HTML template for each item
		$item_tpl = $args['before_item'] . '
			<span itemscope itemtype="http://schema.org/ListItem">
				<a href="%s"><span>%s</span></a>
			</span>
			' . $args['after_item'];

		$item_text_tpl = $args['before_item'] . '
				<span itemscope itemtype="http://schema.org/ListItem">
					<span><span>%s</span></span>
				</span>
			' . $args['after_item'];

		// Home
		if ( ! $args['home_class'] ) {
			$items[] = sprintf( $item_tpl, get_home_url(), $args['labels']['home'] );
		} else {
			$items[] = sprintf(
				'%s<span itemscope itemtype="http://schema.org/ListItem">
					<a class="%s" href="%s"><span>%s </span></a>
				</span>%s',
				$args['before_item'],
				$args['home_class'],
				apply_filters( 'glozin_breadcrumbs_home_url', get_home_url() ),
				$args['labels']['home'],
				$args['after_item']
			);

		}

		// Front page
		if ( is_front_page() ) {
			$items = array();
		} // Blog
		elseif ( is_home() && ! is_front_page() ) {
			$items[] = sprintf(
				$item_text_tpl,
				get_the_title( get_option( 'page_for_posts' ) )
			);
		} // Single
		elseif ( is_single() ) {
			if ( 'post' == get_post_type( get_the_ID() ) && 'page' == get_option( 'show_on_front' ) && ( $blog_page_id = get_option( 'page_for_posts' ) ) ) {
				$items[] = sprintf( $item_tpl, get_page_link( $blog_page_id ), get_the_title( $blog_page_id ) );
			}

			// Terms
			$taxonomy = $args['taxonomy'];
			if ( is_singular( 'product' ) ) {
				$taxonomy = 'product_cat';
			}

			if ( function_exists( 'wc_get_product_terms' ) ) {
				$terms = wc_get_product_terms(
					get_the_ID(), 'product_cat', apply_filters(
						'woocommerce_product_categories_widget_product_terms_args', array(
							'orderby' => 'parent',
						)
					)
				);

				if ( ! empty( $terms ) ) {
					$current_term = '';
					foreach ( $terms as $term ) {
						if ( $term->parent != 0 ) {
							$current_term = $term;
							break;
						}
					}
					$term    = $current_term ? $current_term : $terms[0];
					$terms   = $this->get_term_parents( $term->term_id, $taxonomy );
					$terms[] = $term->term_id;

					foreach ( $terms as $term_id ) {
						$term    = get_term( $term_id, $taxonomy );
						$items[] = sprintf( $item_tpl, get_term_link( $term, $taxonomy ), $term->name );
					}
				}
			}

			$parent_post = apply_filters('glozin_breadcrumbs_get_parent_post', '', $item_tpl);
			if( !empty($parent_post) ) {
				$items[] = $parent_post;
			}

			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, get_the_title() );
			}

		} // Page
		elseif ( is_page() ) {
			if ( ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) ) {
				if ( $page_id = get_option( 'woocommerce_shop_page_id' ) ) {
					$items[] = sprintf( $item_tpl, esc_url( get_permalink( $page_id ) ), get_the_title( $page_id ) );
				}

			} else {
				$pages = $this->get_post_parents( get_queried_object_id() );
				foreach ( $pages as $page ) {
					$items[] = sprintf( $item_tpl, esc_url( get_permalink( $page ) ), get_the_title( $page ) );
				}
			}


			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, get_the_title() );
			}
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, $title );
			}

		} elseif ( is_tax() || is_category() || is_tag() ) {
			$current_term = get_queried_object();
			$terms        = $this->get_term_parents( get_queried_object_id(), $current_term->taxonomy );

			if ( $terms ) {
				foreach ( $terms as $term_id ) {
					$term    = get_term( $term_id, $current_term->taxonomy );
					$items[] = sprintf( $item_tpl, get_term_link( $term, $current_term->taxonomy ), $term->name );
				}
			}

			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, $current_term->name );
			}
		} elseif ( is_post_type_archive( 'portfolio_project' ) ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['portfolio'] );
		}// Dokan Vendor
		elseif ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
			$author    = get_user_by( 'id', get_query_var( 'author' ) );
			$shop_info = get_user_meta( get_query_var( 'author' ), 'dokan_profile_settings', true );
			$shop_name = $author->display_name;
			if ( $shop_info && isset( $shop_info['store_name'] ) && $shop_info['store_name'] ) {
				$shop_name = $shop_info['store_name'];
			}

			$vendor_list = dokan_get_option( 'store_listing', 'dokan_pages' );
			$items[] = sprintf( $item_tpl, get_permalink( $vendor_list ), get_the_title( $vendor_list ) );

			$items[] = sprintf( $item_text_tpl, $shop_name );

		} // Search
		elseif ( is_search() ) {
			$search_item = $args['labels']['search'] . ' &quot;' . get_search_query() . '&quot;';
			$search_item = apply_filters( 'glozin_breadcrumbs_search_item', $search_item );
			$items[] = sprintf( $item_text_tpl, $search_item );
		} // 404
		elseif ( is_404() ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['not_found'] );
		} // Author archive
		elseif ( is_author() ) {
			// Queue the first post, that way we know what author we're dealing with (if that is the case).
			the_post();
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['author'] . ' <span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>'
			);
			rewind_posts();
		} // Day archive
		elseif ( is_day() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html__( '%s %s', 'glozin' ), $args['labels']['day'], get_the_date() )
			);
		} // Month archive
		elseif ( is_month() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html__( '%s %s', 'glozin' ), $args['labels']['month'], get_the_date( 'F Y' ) )
			);
		} // Year archive
		elseif ( is_year() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html__( '%s %s', 'glozin' ), $args['labels']['year'], get_the_date( 'Y' ) )
			);
		} // Archive
		else {
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['archive']
			);

		}
		$items = apply_filters('glozin_get_site_breadcrumb',  $items );
		echo '<nav class="site-breadcrumb d-flex flex-wrap align-items-center fs-14">' . implode( $args['separator'], $items ) . '</nav>';
	}

	/**
	 * Searches for term parents' IDs of hierarchical taxonomies, including current term.
	 * This function is similar to the WordPress function get_category_parents() but handles any type of taxonomy.
	 * Modified from Hybrid Framework
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $term_id The term ID
	 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
	 *
	 * @return array Array of parent terms' IDs.
	 */
	public function get_term_parents( $term_id = '', $taxonomy = 'category' ) {
		// Set up some default arrays.
		$list = array();

		// If no term ID or taxonomy is given, return an empty array.
		if ( empty( $term_id ) || empty( $taxonomy ) ) {
			return $list;
		}

		do {
			$list[] = $term_id;

			// Get next parent term
			$term    = get_term( $term_id, $taxonomy );
			$term_id = $term->parent;
		} while ( $term_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}

	/**
	 * Gets parent posts' IDs of any post type, include current post
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $post_id ID of the post whose parents we want.
	 *
	 * @return array Array of parent posts' IDs.
	 */
	public function get_post_parents( $post_id = '' ) {
		// Set up some default array.
		$list = array();

		// If no post ID is given, return an empty array.
		if ( empty( $post_id ) ) {
			return $list;
		}

		do {
			$list[] = $post_id;

			// Get next parent post
			$post    = get_post( $post_id );
			$post_id = $post->post_parent;
		} while ( $post_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}
}
