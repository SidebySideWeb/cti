<?php
/**
 * Glozin Addons Helper init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper
 */
class Helper {

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
	 * Get the sharing URL of a social
	 *
	 * @since 1.0.0
	 *
	 * @param string $social
	 *
	 * @return string
	 */
	public static function share_link( $social, $args, $label = true ) {
		$url  = '';
		$text_default = apply_filters( 'glozin_share_link_text', ''  );
		if( empty($args[$social . '_title']) ) {
			$text = ! empty( $text_default ) ? $text_default . ' ' . ucfirst( $social ) : ucfirst( $social );
		} else {
			$text = $args[$social . '_title'];
		}

		$icon = $social;

		switch ( $social ) {
			case 'facebook':
				$url = add_query_arg( array( 'u' => get_permalink() ), 'https://www.facebook.com/sharer.php' );
				break;

			case 'twitter':
				$url = add_query_arg( array( 'url' => get_permalink(), 'text' => get_the_title() ), 'https://twitter.com/intent/tweet' );
				break;

			case 'pinterest';
				$params         = array(
					'description' => get_the_title(),
					'media'       => get_the_post_thumbnail_url( null, 'full' ),
					'url'         => get_permalink(),
				);
				$url            = add_query_arg( $params, 'https://www.pinterest.com/pin/create/button/' );
				break;

			case 'googleplus':
				$url  = add_query_arg( array( 'url' => get_permalink() ), 'https://plus.google.com/share' );
				if( empty($args[$social . '_title']) ) {
					$text = $text_default . ' ' . esc_html__( 'Google+', 'glozin-addons' );
				}
				$icon = 'google';
				break;

			case 'linkedin':
				$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://www.linkedin.com/shareArticle' );
				break;

			case 'tumblr':
				$url = add_query_arg( array( 'url' => get_permalink(), 'name' => get_the_title() ), 'https://www.tumblr.com/share/link' );
				break;

			case 'reddit':
				$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://reddit.com/submit' );
				break;

			case 'stumbleupon':
				$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://www.stumbleupon.com/submit' );
				if( empty($args[$social . '_title']) ) {
					$text = $text_default . ' ' . esc_html__( 'StumbleUpon', 'glozin-addons' );
				}
				break;

			case 'telegram':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://t.me/share/url' );
				break;

			case 'whatsapp':
				$params = array( 'text' => urlencode( get_permalink() ) );

				$url = 'https://wa.me/';

				if ( ! empty( $args['whatsapp_number'] ) ) {
					$url .= urlencode( $args['whatsapp_number'] );
				}

				$url = add_query_arg( $params, $url );
				break;

			case 'pocket':
				$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://getpocket.com/save' );
				if( empty($args[$social . '_title']) ) {
					$text = esc_html__( 'Save On Pocket', 'glozin-addons' );
				}
				break;

			case 'digg':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://digg.com/submit' );
				break;

			case 'vk':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://vk.com/share.php' );
				break;

			case 'email':
				$url  = 'mailto:?subject=' . get_the_title() . '&body=' . __( 'Check out this site:', 'glozin-addons' ) . ' ' . get_permalink();
				if( empty($args[$social . '_title']) ) {
					$text = esc_html__( 'Share Via Email', 'glozin-addons' );
				}
				break;

			case 'instagram':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://www.instagram.com/' );
				break;
		}

		if ( ! $url ) {
			return;
		}

		$icon = ( isset( $args[$icon]['icon'] ) && ! empty( $args[$icon]['icon'] ) ) ? $args[$icon]['icon'] : $icon;
		$class = ( isset( $args[$social]['class'] ) && ! empty( $args[$social]['class'] ) ) ? $args[$social]['class'] : '';
		if( empty ( $args[$social . '_icon_html'] )  ) {
			$icon = self::get_svg($icon, 'social', array( 'class' => $class ) );
		} else {
			$icon = '<span class="glozin-svg-icon glozin-svg-icon--twitter '. $class .'">' . $args[$social . '_icon_html'] . '</span>';
		}

		$repeat_class = ! empty ( $args['repeat_classes'] ) ? $args['repeat_classes'] : '';

		return sprintf(
			'<li class="lh-1"><a href="%s" target="_blank" class="social-share-link gz-socials--%s %s gz-button gz-button-icon gz-button-outline gz-tooltip-inside" data-tooltip="%s">%s %s</a></li>',
			esc_url( $url ),
			esc_attr( $social ),
			esc_attr($repeat_class),
			$text,
			$icon,
			$label ? '<span class="social-share__label">'. $text .'</span>' : ''
		);
	}

	/**
	 * Get Theme SVG.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_svg( $svg_name, $group = 'ui', $attr = array()  ) {
		if ( class_exists( '\Glozin\Icon' ) && method_exists( '\Glozin\Icon', 'get_svg' ) ) {
			return \Glozin\Icon::get_svg( $svg_name, $group, $attr );
		}

		return '';
	}

	/**
	 * Get Theme SVG.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function inline_svg( $args = array()  ) {
		if ( class_exists( '\Glozin\Icon' ) && method_exists( '\Glozin\Icon', 'get_svg' ) ) {
			return \Glozin\Icon::inline_svg( $args );
		}

		return '';
	}

	/**
	 * Get Theme SVG.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function sanitize_svg( $svg ) {
		if ( class_exists( '\Glozin\Icon' ) && method_exists( '\Glozin\Icon', 'sanitize_svg' ) ) {
			return \Glozin\Icon::sanitize_svg( $svg );
		}

		return '';
	}

	/**
	 * Get Theme Options.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_option( $name ) {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'get_option' ) ) {
			return \Glozin\Helper::get_option( $name );
		}

		return '';
	}

	public static function get_prop( $name ) {
		if ( class_exists( '\Glozin\Theme' ) && method_exists( '\Glozin\Theme', 'get_prop' ) ) {
			return \Glozin\Theme::get_prop( $name );
		}

		return '';
	}

	public static function set_prop( $name, $value ) {
		if ( class_exists( '\Glozin\Theme' ) && method_exists( '\Glozin\Theme', 'set_prop' ) ) {
			return \Glozin\Theme::set_prop( $name, $value );
		}
	}

	/**
	 * Get WooCommerce Products Shortcode Template.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function products_list_shortcode_template ( $query_posts, $args = array() ) {
		if ( empty( $query_posts ) ) {
			return;
		}

		if( ! function_exists( 'wc_get_template' ) ) {
			return;
		}

		$original_post = $GLOBALS['post'];

		foreach ( $query_posts as $product ) {
			$_product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

			if( empty( $_product ) || ! is_object( $_product ) ) {
				continue;
			}

			$post_object = get_post( $_product->get_id() );

			setup_postdata( $GLOBALS['post'] = $post_object );

			wc_get_template( 'content-product-list.php', $args );
		}

		$GLOBALS['post'] = $original_post;

		wp_reset_postdata();
	}

	/**
	 * Get terms array for select control
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	public static function get_terms_hierarchy( $taxonomy = 'category', $separator = '-', $hide_empty = true, $child_of = false ) {
		$terms = get_terms( array(
			'taxonomy'   	=> $taxonomy,
			'hide_empty' 	=> $hide_empty,
			'child_of' 		=> $child_of,
			'update_term_meta_cache' => false,
		) );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return array();
		}

		$taxonomy = get_taxonomy( $taxonomy );
		if ( $taxonomy->hierarchical ) {
			$terms = self::sort_terms_hierarchy( $terms );
			$terms = self::flatten_hierarchy_terms( $terms, $separator );
		}

		return $terms;
	}

	/**
	 * Recursively sort an array of taxonomy terms hierarchically.
	 *
	 * @param array $terms
	 * @param integer $parent_id
	 * @return array
	 */
	public static function sort_terms_hierarchy( $terms, $parent_id = 0 ) {
		$hierarchy = array();

		foreach ( $terms as $term ) {
			if ( $term->parent == $parent_id ) {
				$term->children = self::sort_terms_hierarchy( $terms, $term->term_id );
				$hierarchy[] = $term;
			}
		}

		return $hierarchy;
	}

	/**
	 * Flatten hierarchy terms
	 *
	 * @param array $terms
	 * @param integer $depth
	 * @return array
	 */
	public static function flatten_hierarchy_terms( $terms, $separator = '&mdash;', $depth = 0 ) {
		$flatted = array();


		foreach ( $terms as $term ) {
			$children = array();

			if ( ! empty( $term->children ) ) {
				$children = $term->children;
				$term->has_children = true;
				unset( $term->children );
			}

			$term->depth = $depth;
			$term->name = $depth && $separator ? str_repeat( $separator, $depth ) . ' ' . $term->name : $term->name;
			$flatted[] = $term;

			if ( ! empty( $children ) ) {
				$flatted = array_merge( $flatted, self::flatten_hierarchy_terms( $children, $separator, ++$depth ) );
				$depth--;
			}
		}

		return $flatted;
	}

	/**
	 * Functions that used to get coutndown texts
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_countdown_shorten_texts() {
		return apply_filters( 'glozin_get_countdown_texts', array(
			'weeks'    	=> esc_html__( 'Weeks', 'glozin-addons' ),
			'week'    	=> esc_html__( 'Week', 'glozin-addons' ),
			'days'    	=> esc_html__( 'Days', 'glozin-addons' ),
			'day'    	=> esc_html__( 'Day', 'glozin-addons' ),
			'hours'   	=> esc_html__( 'Hours', 'glozin-addons' ),
			'hour'   	=> esc_html__( 'Hour', 'glozin-addons' ),
			'minutes' 	=> esc_html__( 'Mins', 'glozin-addons' ),
			'minute' 	=> esc_html__( 'Min', 'glozin-addons' ),
			'seconds' 	=> esc_html__( 'Secs', 'glozin-addons' ),
			'second' 	=> esc_html__( 'Sec', 'glozin-addons' )
		) );
	}

	/**
	 * Check is product deals
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_product_deal( $product ) {
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

		// It must be a sale product first
		if ( ! $product->is_on_sale() ) {
			return false;
		}

		// Only support product type "simple" and "external"
		if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'external' ) ) {
			return false;
		}

		$deal_quantity = get_post_meta( $product->get_id(), '_deal_quantity', true );

		if ( $deal_quantity > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Get is post ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_post_ID() {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'get_post_ID' ) ) {
			return \Glozin\Helper::get_post_ID();
		}

		return '';
	}

		/**
	 * Get is catalog
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function is_catalog() {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'is_catalog' ) ) {
			return \Glozin\Helper::is_catalog();
		}

		return '';
	}

		/**
	 * Get is blog
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function is_blog() {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'is_blog' ) ) {
			return \Glozin\Helper::is_blog();
		}

		return '';
	}


	/**
	 * Get nav menus
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_navigation_bar_get_menus() {
		if ( ! is_admin() ) {
			return [];
		}

		$menus = wp_get_nav_menus();
		if ( ! $menus ) {
			return [];
		}

		$output = array(
			0 => esc_html__( 'Select Menu', 'glozin-addons' ),
		);
		foreach ( $menus as $menu ) {
			$output[ $menu->slug ] = $menu->name;
		}

		return $output;
	}

	public static function get_responsive_image_elementor( $settings, $ratio = false, $size = 'full' ) {
		$output = [];
		$image = [  'image' => '', 'image_size' => $size ];

		if( ! empty( $settings['image'] ) && ! empty( $settings['image']['id'] ) ) {
			$first_image_load = self::get_prop( 'first_image_load' );
			$first_image_load_mobile = false;
			$image['image'] = $settings['image'];
			$class = '';
			if( ! empty( $settings['image_mobile'] ) && ! empty( $settings['image_mobile']['id'] ) ) {
				$class = 'hidden-mobile';

				if ( wp_is_mobile() && empty( $first_image_load ) ) {
					$first_image_load = 'loaded';
				}
			}

			if( ! empty( $settings['image_tablet'] ) && ! empty( $settings['image_tablet']['id'] ) ) {
				$class = 'hidden-tablet hidden-mobile';
			}

			if( $ratio ) {
				$class .= ' gz-ratio';
			}
			$image_attributes = array();
			if( empty( $first_image_load ) ) {
				$image_attributes['fetchpriority'] = 'high';
				$image_attributes['loading'] = 'eager';
			}
			$output[] = sprintf( '<div class="gz-responsive-image w-100 gz-responsive-image__desktop %s">%s</div>',
									esc_attr( $class ),
									wp_get_attachment_image( $image['image']['id'], $size, false, $image_attributes )
								);
		}

		if( ! empty( $settings['image_tablet'] ) && ! empty( $settings['image_tablet']['id'] ) ) {
			$image['image'] = $settings['image_tablet'];

			$output[] = sprintf( '<span class="gz-responsive-image gz-ratio gz-responsive-image__tablet hidden-desktop %s %s">%s</span>',
									! empty( $settings['image_mobile'] ) && ! empty( $settings['image_mobile']['id'] ) ? 'hidden-mobile' : '',
									$ratio ? 'gz-ratio' : '',
									wp_get_attachment_image( $image['image']['id'], $size )
								);
		}

		if( ! empty( $settings['image_mobile'] ) && ! empty( $settings['image_mobile']['id'] ) ) {
			$image['image'] = $settings['image_mobile'];

			if ( ! wp_is_mobile() && empty( $first_image_load_mobile ) ) {
				$first_image_load_mobile = 'loaded';
			}

			$image_attributes = array();
			if( ! empty( $first_image_load_mobile ) ) {
				$image_attributes['loading'] = 'lazy';
			} else {
				$image_attributes['fetchpriority'] = 'high';
				$image_attributes['loading'] = 'eager';
			}
			$output[] = sprintf( '<span class="gz-responsive-image gz-responsive-image__mobile hidden-desktop hidden-tablet %s">%s</span>',
									$ratio ? 'gz-ratio' : '',
									wp_get_attachment_image( $image['image']['id'], $size, false, $image_attributes )
								);
		}

		self::set_prop( 'first_image_load', 'loaded' );


		return implode( '', $output );
	}

	public static function get_cart_icons() {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'get_cart_icons' ) ) {
			return \Glozin\Helper::get_cart_icons();
		}

		return '';
	}

	public static function get_content_limit( $num_words, $content = '', $class = array() ) {
		if ( class_exists( '\Glozin\Helper' ) && method_exists( '\Glozin\Helper', 'get_content_limit' ) ) {
			return \Glozin\Helper::get_content_limit( $num_words, $content, $class );
		}

		return '';
	}
}
