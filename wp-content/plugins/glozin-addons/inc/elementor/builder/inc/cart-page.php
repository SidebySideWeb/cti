<?php

namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Glozin\Addons\Elementor\Builder\Helper;

/**
 * Main class of plugin for admin
 */
class Cart_Page {

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
	 * Page id
	 *
	 * @var $page_id
	 */
	private static $page_id;

	/**
	 * Page Empty id
	 *
	 * @var $page_empty_id
	 */
	private static $page_empty_id;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'template_include', array( $this, 'redirect_template' ), 100 );
		add_filter( 'glozin_is_page_built_with_elementor', '__return_true' );

		// Scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'glozin_woocommerce_product_archive_content', array( $this, 'cart_content_builder' ), 5 );
        add_filter( 'glozin_get_page_header_elements', '__return_empty_array' );

		// Change row actions in admin
		add_filter( 'display_post_states', [ $this, 'glozin_post_state' ], 11, 2 );
		add_filter( 'get_edit_post_link', [ $this, 'glozin_get_edit_post_link' ], 11, 3 );
		add_filter( 'page_row_actions', array( $this, 'glozin_page_row_actions' ), 11, 2 );
	}

	public function body_classes( $classes ) {
		$terms = Helper::glozin_get_terms();
		self::$page_id = self::get_page_id();
		if( is_singular( 'glozin_builder' ) && ( in_array( 'cart_page', $terms ) ) ) {
			$classes[] = 'glozin-woocommerce-elementor cart-page-elementor woocommerce woocommerce-cart woocommerce-page glozin-elementor-id-'.self::$page_id;
		} else {
			$classes[] = 'glozin-woocommerce-elementor cart-page-elementor glozin-elementor-id-'.self::$page_id;
		}

		return $classes;
	}

	public function enqueue_scripts() {
		if( ! apply_filters( 'glozin_get_cart_page_builder', true ) ) {
			return;
		}

		if( ! is_cart() ) {
			return;
		}

		wp_enqueue_script( 'wc-cart' );

		$css_file = '';

		self::$page_id = self::get_page_id();

		if( empty( self::$page_id ) ) {
			return;
		}

		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( intval( self::$page_id ) );
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( intval( self::$page_id ) );
		}

		if( $css_file ) {
			$css_file->enqueue();
		}
	}

	public function cart_content_builder() {
		if( ! apply_filters( 'glozin_get_cart_page_builder', true ) ) {
			return;
		}

		if( ! is_cart() ) {
			return;
		}

		self::$page_id = self::get_page_id();
            
		if( ! empty( self::$page_id ) ) {
			$css_bool = \Glozin\Addons\Elementor\Builder\Helper::check_elementor_css_print_method();
			$elementor_instance = \Elementor\Plugin::instance();
			echo $elementor_instance->frontend->get_builder_content_for_display( intval( self::$page_id ), $css_bool );
		} else {
			$this->get_content_empty_html();
		}
    }

    public function get_content_empty_html() {
        ?>
            <div class="glozin-single-product-builder--empty">
                <h4><?php esc_html_e( 'Cart Page Builder', 'glozin-addons' ); ?></h4>
                <?php
                    printf(
                        esc_html__( "It seems like you've turned on the Cart Page Builder, but you haven't set up any builders yet. To avoid any issues, please either %s this feature or %s. You can find a step-by-step guide in our documentation.", 'glozin-addons' ),
                        sprintf(
                            '<a href="%s">%s</a>',
                            esc_url( admin_url( 'themes.php?page=theme_settings' ) ),
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

	public function redirect_template( $template ){
        $template_part = '';
        $template_id = 0;

		if ( is_cart() ) {
			self::$page_id = self::get_page_id();
			if ( self::$page_id ) {
				$template_id = self::$page_id;
			}

			$template_part = 'cart_page';

			$template = \Glozin\Addons\Elementor\Builder\Helper::get_redirect_template( $template, $template_part, $template_id );
		}

		return $template;
	}

	/**
	 * Get page id
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_page_id() {
		if( isset( self::$page_id ) ) {
			return self::$page_id;
		}

		$page_id = 0;
		$query = new \WP_Query( array(
			'post_type'        => 'glozin_builder',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'tax_query' => array(
				array(
					'taxonomy' => 'glozin_builder_type',
					'field' => 'slug',
					'operator' => 'AND',
					'terms' => array( 'cart_page', 'enable' )
				),
			),
		));

		$page_id = $query->posts ? $query->posts[0] : 0;
        self::$page_id =  $page_id;
		wp_reset_postdata();
		return self::$page_id;
	}

	public function glozin_post_state( $post_states, $post ) {
		if( ! is_admin() ) {
			return $post_states;
		}

		if( ! get_option( 'glozin_cart_page_builder_enable') ) {
			return $post_states;
		}

		if( ! function_exists( 'wc_get_page_id') ) {
			return $post_states;
		}

		if ( get_the_ID() == wc_get_page_id( 'cart' ) ) {
			if( isset( $post_states['elementor'] ) ) {
				unset($post_states['elementor']);
			}

			$post_states['glozin_builder'] = esc_html__( 'Glozin Cart Builder', 'glozin-addons' );
		}

		return $post_states;
	}

	public function glozin_get_edit_post_link( $link, $post_id, $context ) {
		if( ! get_option( 'glozin_cart_page_builder_enable') ) {
			return $link;
		}

		if( ! function_exists( 'wc_get_page_id') ) {
			return $link;
		}

		if ( $post_id == wc_get_page_id( 'cart' ) ) {
			self::$page_id = self::get_page_id();
			if ( ! self::$page_id ) {
				return $link;
			}

            $link = esc_url( admin_url( 'post.php?post=' . self::$page_id . '&action=edit' ) );
        }

        return $link;
	}

	public function glozin_page_row_actions( $actions, $post ) {
		if( ! is_admin() ) {
			return $actions;
		}

		if( ! get_option( 'glozin_cart_page_builder_enable') ) {
			return $actions;
		}

		if( ! function_exists( 'wc_get_page_id') ) {
			return $actions;
		}

		if( get_the_ID() == wc_get_page_id( 'cart' ) ) {
			self::$page_id = self::get_page_id();
			if ( ! self::$page_id ) {
				return $actions;
			}

			$url = esc_url( admin_url( 'post.php?post=' . self::$page_id ) );

			$actions['edit'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				$url . '&action=edit',
				__( 'Edit', 'glozin-addons' )
			);

			if( isset( $actions['edit_with_elementor'] ) ) {
				$actions['edit_with_elementor'] = sprintf(
					'<a href="%1$s">%2$s</a>',
					$url . '&action=elementor',
					__( 'Edit with Elementor', 'glozin-addons' )
				);
			}
		}

		return $actions;
	}
}