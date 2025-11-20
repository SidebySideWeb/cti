<?php
/**
 * Glozin helper functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin;

use Glozin\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Glozin Helper initial
 *
 */
class Helper {
	/**
	 * Theme Version
	 *
	 * @var $theme_version
	 */
	protected static $theme_version = null;


	/**
	 * Post ID
	 *
	 * @var $post_id
	 */
	protected static $post_id = null;

	/**
	 * is_build_elementor
	 *
	 * @var $is_build_elementor
	 */
	protected static $is_build_elementor = null;

	/**
	 * Get theme version
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_theme_version() {
		if( isset( self::$theme_version )  ) {
			return self::$theme_version;
		}
		self::$theme_version = wp_get_theme()->get( 'Version' );
		return self::$theme_version;
	}


	/**
	 * Get theme option
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_option( $name ) {
		return \Glozin\Options::instance()->get_option( $name );
	}

	/**
	 * Get theme option default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_option_default( $name ) {
		return \Glozin\Options::instance()->get_option_default( $name );
	}

	/**
	 * Check is blog
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public static function is_blog() {
		if ( ( is_archive() || is_author() || is_category() || is_home() || is_tag() || is_search() ) && 'post' == get_post_type() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check is catalog
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public static function is_catalog() {
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_brand' ) || is_tax( 'product_collection' ) || is_tax( 'product_condition' ) || (function_exists('is_product_taxonomy') && is_product_taxonomy() ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Post ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_post_ID() {
		if( isset( self::$post_id )  ) {
			return self::$post_id;
		}

		if ( self::is_catalog() ) {
			self::$post_id = intval( get_option( 'woocommerce_shop_page_id' ) );
		} elseif ( self::is_blog() ) {
			self::$post_id = intval( get_option( 'page_for_posts' ) );
		} else {
			self::$post_id = get_the_ID();
		}

		return self::$post_id;
	}

	/**
	 * Get font url
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_fonts() {
		if( ! Helper::get_option('typo_font_family') ) {
			return;
		}
		$fonts = array(
			'assets/fonts/InstrumentSans-Regular.woff2',
			'assets/fonts/InstrumentSans-Medium.woff2',
			'assets/fonts/InstrumentSans-SemiBold.woff2',
			'assets/fonts/InstrumentSans-Bold.woff2',
		);
		?>
			<style id="glozin-custom-fonts" type="text/css">
				/* latin-ext */
				@font-face {
					font-family: 'Instrument Sans';
					src: url( '<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/InstrumentSans-Regular.woff2' ); ?>' ) format('woff2');
					font-weight: 400;
      				font-style: normal;
      				font-display: swap;
				}
				/* latin */
				@font-face {
					font-family: 'Instrument Sans';
					src: url( '<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/InstrumentSans-Medium.woff2' ); ?>' ) format('woff2');
					font-weight: 500;
      				font-style: normal;
      				font-display: swap;
				}
				/* latin-ext */
				@font-face {
					font-family: 'Instrument Sans';
					src: url( '<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/InstrumentSans-SemiBold.woff2' ); ?>' ) format('woff2');
					font-weight: 600;
      				font-style: normal;
      				font-display: swap;
				}
				/* latin */
				@font-face {
					font-family: 'Instrument Sans';
					src: url( '<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/InstrumentSans-Bold.woff2' ); ?>' ) format('woff2');
					font-weight: 700;
      				font-style: normal;
      				font-display: swap;
				}

				<?php self::get_stable_google_fonts_url(); ?>
			</style>
		<?php
		foreach ( $fonts as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>',
				esc_url( get_template_directory_uri() . '/' . $font )
			);
		}
	}

	/**
	 * Content limit
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_content_limit( $num_words, $content = '', $class = array() ) {
		$content = empty( $content ) ? get_the_excerpt() : $content;

		// Strip tags and shortcodes so the content truncation count is done correctly
		$content = strip_tags(
			strip_shortcodes( $content ), apply_filters(
				'glozin_content_limit_allowed_tags', '<script>,<style>'
			)
		);

		// Remove inline styles / scripts
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char
		$content = wp_trim_words( $content, $num_words );

		return sprintf( '<p class="%s">%s</p>', esc_attr(implode(' ', $class)), $content );
	}

	/**
	 * Check is built with elementor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function is_built_with_elementor() {
		if( isset( self::$is_build_elementor )  ) {
			return self::$is_build_elementor;
		}

		if( ! class_exists('\Elementor\Plugin') ) {
			self::$is_build_elementor = false;
			return self::$is_build_elementor;
		}

		$document = \Elementor\Plugin::$instance->documents->get( self::get_post_ID() );
		if ( ( is_page() && $document && $document->is_built_with_elementor() ) || apply_filters( 'glozin_is_page_built_with_elementor', false ) ) {
			self::$is_build_elementor = true;
		}

		return self::$is_build_elementor;
	}

	/**
	 * Get an array of posts.
	 *
	 * @static
	 * @access public
	 *
	 * @param array $args Define arguments for the get_posts function.
	 *
	 * @return array
	 */
	public static function customizer_get_posts( $args ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( is_string( $args ) ) {
			$args = add_query_arg(
				array(
					'suppress_filters' => false,
				)
			);
		} elseif ( is_array( $args ) && ! isset( $args['suppress_filters'] ) ) {
			$args['suppress_filters'] = false;
		}

		$args['posts_per_page'] = - 1;

		$posts = get_posts( $args );

		// Properly format the array.
		$items    = array();
		$source = isset($args['source']) ? $args['source'] : '';
		if( $args['post_type'] == 'glozin_builder' && $source == 'page') {
			$items[0] = esc_html__( 'Default Footer Global', 'glozin' );
			$items['page'] = esc_html__( 'Default Footer Page', 'glozin' );
		} else {
			$items[0] = esc_html__( 'Select an item', 'glozin' );
		}
		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}
		wp_reset_postdata();

		return $items;

	}

	/**
	 * Button Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function share_socials() {
		if ( ! class_exists( '\Glozin\Addons\Helper' ) && ! method_exists( '\Glozin\Addons\Helper','share_link' )) {
			return;
		}

		$args = array();
		$socials = (array) Helper::get_option( 'post_sharing_socials' );
		if ( ( ! empty( $socials ) ) ) {
			$output = array();

			foreach ( $socials as $social => $value ) {
				if( $value == 'whatsapp' ) {
					$args['whatsapp_number'] = Helper::get_option( 'post_sharing_whatsapp_number' );
				}

				if( $value == 'facebook' ) {
					$args[$value]['icon'] = 'facebook-f';
				}

				$output[] = \Glozin\Addons\Helper::share_link( $value, $args, false );
			}
			return sprintf( '<ul class="post__socials-share d-flex align-items-center flex-wrap gap-10 my-0 py-0 list-unstyled">%s</ul>', implode( '', $output )	);
		};
	}

	/**
	 * Get counter wishlist
	 *
	 * @since 1.0.0
	 *
	 * @param string $account
	 *
	 * @return string
	 */
	public static function wishlist_counter() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		if ( ! class_exists( 'WCBoost\Wishlist\Helper' ) ) {
			return;
		}

		$wishlist = \WCBoost\Wishlist\Helper::get_wishlist();

		$wishlist_counter = intval( $wishlist->count_items() );

		return sprintf('<span class="header-counter header-wishlist__counter">%s</span>', $wishlist_counter);
	}

	/**
	 * Compatible custom fonts plugin
	 *
	 * @return void
	 */
	public static function get_stable_google_fonts_url() {
		if( defined( 'BSF_CUSTOM_FONTS_POST_TYPE' ) && class_exists( 'BCF_Google_Fonts_Compatibility' ) ) {
			$bcf = \BCF_Google_Fonts_Compatibility::get_instance();
			$bcf_folder = $bcf->get_fonts_folder();
			$bcf_folder = explode( '/', $bcf_folder );
			$bcf_folder = end( $bcf_folder );
			$args                 = array(
				'post_type'      => BSF_CUSTOM_FONTS_POST_TYPE,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => '-1',
			);

			$query = new \WP_Query( $args );
			$bsf_fonts = $query->posts;

			if ( ! empty( $bsf_fonts ) ) {
				foreach ( $bsf_fonts as $key => $post_id ) {
					$bsf_font_data = get_post_meta( $post_id, 'fonts-data', true );
					$bsf_font_type = get_post_meta( $post_id, 'font-type', true );
					if( $bsf_font_type == 'google' ) {
						foreach( $bsf_font_data['variations'] as $variation ) {
							$font_files = $bcf->get_fonts_file_url( $bsf_font_data['font_name'], $variation['font_weight'], $variation['font_style'], $variation['id'] );

							foreach( $font_files as $font_file ) {
								if( ! empty( $font_file ) ) {
									$_file = explode( '/', $font_file );
									$_file = end($_file);
									$_file_url = content_url() . '/'. $bcf_folder .'/' . $bsf_font_data['font_name'] . '/' . $_file;

									$type = explode( '.', $_file );
									$type = end( $type );

									$font_weight = filter_var( $variation['font_weight'], FILTER_SANITIZE_NUMBER_INT );

									if( $bcf->get_remote_url_contents( $_file_url ) ) {
										printf( "@font-face {
												font-family: '%s';
												font-style: %s;
												font-weight: %s;
												font-display: swap;
												src: url( '%s' ) format('%s');
											}",
											$bsf_font_data['font_name'],
											$variation['font_style'],
											! empty( $font_weight ) ? $font_weight : 400,
											esc_url( $_file_url ),
											! empty( $type ) ? $type : 'woff2'
										);
									}
								}
							}
						}
					}
				}
			}

			wp_reset_postdata();
		}
	}

	public static function get_cart_icons($original_size = null) {
		if (is_null($original_size)) {
			$original_size = true;
		}

		if ( \Glozin\Helper::get_option( 'cart_icon_source' ) == 'icon' ) {
			$cart_icon = !empty(\Glozin\Helper::get_option( 'cart_icon' )) ? 'shopping-cart' : 'shopping-bag';
			if( $cart_icon == 'shopping-cart' ) {
				$icon_class = \Glozin\Helper::get_option( 'cart_icon' );
				$html = \Glozin\Icon::inline_svg( ['icon' => $cart_icon, 'width' => 24, 'height' => 24, 'class' => 'has-vertical-align ' . $icon_class] );
			} else {
				$html = \Glozin\Icon::inline_svg( ['icon' => $cart_icon, 'width' => 15, 'height' => 16, 'class' => 'has-vertical-align', 'original_size' => $original_size] );
			}
		} else {
			if ( ! empty( \Glozin\Helper::get_option( 'cart_icon_svg' ) ) ) {
				$html = '<span class="glozin-svg-icon glozin-svg-icon--custom-cart">' . \Glozin\Icon::sanitize_svg( \Glozin\Helper::get_option( 'cart_icon_svg' ) ) . '</span>';
			} else {
				$html = \Glozin\Icon::inline_svg( [ 'icon' => 'shopping-bag', 'class' => 'has-vertical-align' ] );
			}
		}

		return $html;
	}

	/**
	 * Get swiper navigation
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_swiper_navigation( $class = [ 'swiper-button' ], $icon_prev = 'icon-back', $icon_next = 'icon-next' ) {
		echo \Glozin\Icon::inline_svg( [ 'icon' => $icon_prev, 'class' => 'swiper-button-prev ' . implode( ' ', $class ) ] );
		echo \Glozin\Icon::inline_svg( [ 'icon' => $icon_next, 'class' => 'swiper-button-next ' . implode( ' ', $class ) ] );
	}

	/**
	 * Get swiper pagination
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_swiper_pagination( $class = [] ) {
		echo '<div class="swiper-pagination ' . implode( ' ', $class ) . '"></div>';
	}

	/**
	 * Get posts found
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_posts_found( $post_count, $found_posts ) {
		echo sprintf( '<div class="gz-posts-found gz-progress text-center">
								<div class="gz-posts-found__inner gz-progress__inner">
									%s
									<span class="current-post"> %s </span>
									%s
									<span class="found-post"> %s </span>
									%s
									<span class="count-bar gz-progress__count-bar"></span>
								</div>
							</div>',
					esc_html__( "You've viewed", 'glozin' ),
					$post_count,
					esc_html__( 'of', 'glozin' ),
					$found_posts,
					esc_html__( 'result', 'glozin' )
		);
	}
}
