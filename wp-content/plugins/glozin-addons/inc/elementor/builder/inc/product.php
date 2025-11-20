<?php

namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Glozin\Addons\Elementor\Builder\Helper;

/**
 * Main class of plugin for admin
 */
class Product {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

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
	 * Single Product id
	 *
	 * @var $product_id
	 */
	private static $product_id;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'glozin_builder_classes', array( $this, 'builder_classes' ));
		add_action( 'glozin_before_single_product', array( $this, 'add_post_class' ) );
		add_action( 'glozin_before_woocommerce_product_content', array( $this, 'remove_post_class' ) );

		add_filter( 'template_include', array( $this, 'redirect_template' ), 100 );
		add_filter( 'glozin_is_page_built_with_elementor', '__return_true' );

		// Scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'glozin_woocommerce_product_content', array( $this, 'product_content_builder' ), 5 );

		// Track Product View
		add_action( 'template_redirect', array( $this, 'track_product_view' ) );

		// Disable
		add_filter( 'glozin_ask_question_content', '__return_false' );
		add_filter( 'glozin_delivery_return_content', '__return_false' );
		add_filter( 'glozin_product_share_content', '__return_false' );
	}

	public function add_post_class() {
		add_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	public function remove_post_class() {
		remove_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	public function body_classes( $classes ) {
		$terms = Helper::glozin_get_terms();
		self::$product_id = self::get_product_id();

		if( is_singular( 'glozin_builder' ) && in_array( 'product', $terms ) ) {
			$classes[] = 'glozin-woocommerce-elementor single-product single-product-elementor woocommerce glozin-elementor-id-'.self::$product_id;
		} else {
			$classes[] = 'glozin-woocommerce-elementor single-product-elementor glozin-elementor-id-'.self::$product_id;
		}

		return $classes;
	}

	public function builder_classes($classes) {
		if( ! is_singular( 'glozin_builder' ) ) {
			return $classes;
		}

		global $product;
		if( empty( $product ) ) {
			$product_template_id = self::get_last_product_id();
			$product = wc_get_product( $product_template_id );
		}

		$classes = wc_get_product_class( 'product', $product );
		if ( get_option( 'glozin_buy_now' ) == 'yes' ) {
			$classes[] = 'has-buy-now';
		}

		if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
			$classes[] = 'has-wishlist';
		}

		if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			$classes[] = 'has-compare';
		}

		return $classes;
	}

	/**
	 * Adds classes to products
	 *
	 * @since 1.0
	 *
	 * @param array $classes Post classes.
	 *
	 * @return array
	 */

	 public function product_class( $classes ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		if( ! is_singular('product') ) {
			return $classes;
		}

		if ( get_option( 'glozin_buy_now' ) == 'yes' ) {
			$classes[] = 'has-buy-now';
		}

		global $product;
		if( $product->is_on_backorder() ) {
			$classes[] = 'is-pre-order';
		}

		if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
			$classes[] = 'has-wishlist';
		}

		if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			$classes[] = 'has-compare';
		}

		return $classes;
	}

	public function enqueue_scripts() {
		if( ! apply_filters( 'glozin_get_product_builder', true ) ) {
			return;
		}

		if( ! is_singular('product') ) {
			return;
		}

		$css_file = '';

		self::$product_id = self::get_product_id();

		if( empty( self::$product_id ) ) {
			return;
		}

		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( intval( self::$product_id ) );
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( intval( self::$product_id ) );
		}

		if( $css_file ) {
			$css_file->enqueue();
		}
	}

	public function product_content_builder() {
		if( ! apply_filters( 'glozin_get_product_builder', true ) ) {
			return;
		}

		if( ! is_singular('product') ) {
			return;
		}

		self::$product_id = self::get_product_id();

		if( ! empty( self::$product_id ) ) {
			$css_bool = \Glozin\Addons\Elementor\Builder\Helper::check_elementor_css_print_method();
			$elementor_instance = \Elementor\Plugin::instance();
			echo $elementor_instance->frontend->get_builder_content_for_display( intval( self::$product_id ), $css_bool );
		} else {
			?>
				<div class="glozin-single-product-builder--empty">
					<h4><?php esc_html_e( 'Single Product Builder', 'glozin-addons' ); ?></h4>
					<?php
						printf(
							esc_html__( "It seems like you've turned on the Single Product Builder, but you haven't set up any builders yet. To avoid any issues, please either %s this feature or %s. You can find a step-by-step guide in our documentation.", 'glozin-addons' ),
							sprintf(
								'<a href="%s">%s</a>',
								esc_url( admin_url( 'admin.php?page=theme_builder_settings' ) ),
								esc_html__( 'turn off', 'glozin-addons' )
							),
							sprintf(
								'<a href="%s">%s</a>',
								esc_url( admin_url( 'edit.php?post_type=glozin_builder' ) ),
								esc_html__( 'create a new builder', 'glozin-addons' )
							)
						);
					?>
				</div>
			<?php
		}
    }

	public function redirect_template( $template ){
        $template_part = '';
        $template_id = 0;

		global $product;
        $product = is_object($product) ? $product : wc_get_product( get_the_ID() );

		if ( is_singular( 'product' ) ) {
			self::$product_id = self::get_product_id();
			if ( self::$product_id ) {
				$template_id = self::$product_id;
			}

			$template_part = 'product';

			$template = \Glozin\Addons\Elementor\Builder\Helper::get_redirect_template( $template, $template_part, $template_id );
		}

		return $template;
	}

	/**
	 * Get single product id
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_product_id() {
		self::$product_id = apply_filters( 'glozin_product_builder_page_id', self::$product_id );
		if( isset( self::$product_id ) ) {
			return self::$product_id;
		}

		$product_id = 0;
		if( is_singular( 'product' ) ) {
			$product_id = $this->get_query();

			if( empty( $product_id ) ) {
				$product_id = $this->get_query( true );
			}
		}

		self::$product_id =  $product_id;

		return self::$product_id;
	}

	public function get_query($get_all = false) {
		$post_id = \Glozin\Addons\Helper::get_post_ID();
		if( empty( $post_id ) ) {
			$post_id = '-1';
			$category = '-1';
		}

		if( $get_all ) {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => 'page_include',
					'value'   => [0],
					'compare' => 'IN',
				),
				array(
					'key'     => 'product_cat_include',
					'value'   => [0],
					'compare' => 'IN',
				),
			);

		} else {
			$meta_query = array(
				'relation' => 'OR',
			);
			$categories = get_the_terms( $post_id, 'product_cat' );
			if( !is_wp_error( $categories ) && ! empty($categories) ) {
				foreach( $categories as $category ) {
					$meta_query[] = array(
						'key'     => 'product_cat_include',
						'value'   =>  ',' . $category->slug . ',',
						'compare' => 'LIKE',
					);
				}
			}
			$meta_query[] = array(
				'key'     => 'page_include',
				'value'   =>  ',' . $post_id .',',
				'compare' => 'LIKE',
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
					'terms' => array( 'product', 'enable' )
				),
			),
			'meta_query' => $meta_query
		));

		$product_id = $query->posts ? $query->posts[0] : 0;
		wp_reset_postdata();
		return $product_id;
	}

	/**
	 * Track product views
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function track_product_view() {
		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );
		}

		if ( ! empty( $post->ID ) && ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ), time() + 60 * 60 * 24 * 30 );
	}
}