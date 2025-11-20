<?php

namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Glozin\Addons\Elementor\Builder\Helper;

/**
 * Main class of plugin for admin
 */
class Not_Found_Page {

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
		add_action( 'glozin_woocommerce_product_archive_content', array( $this, 'not_found_content_builder' ), 5 );
        add_filter( 'glozin_get_page_header_elements', '__return_empty_array' );
	}

	public function body_classes( $classes ) {
		self::$page_id = self::get_page_id();
		$classes[] = 'glozin-woocommerce-elementor 404-page-elementor glozin-elementor-id-'.self::$page_id;

		return $classes;
	}

	public function enqueue_scripts() {
		if( ! apply_filters( 'glozin_get_404_page_builder', true ) ) {
			return;
		}

		if( ! is_404() ) {
			return;
		}

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

	public function not_found_content_builder() {
		if( ! apply_filters( 'glozin_get_404_page_builder', true ) ) {
			return;
		}

		if( ! is_404() ) {
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
                <h4><?php esc_html_e( '404 Page Builder', 'glozin-addons' ); ?></h4>
                <?php
                    printf(
                        esc_html__( "It seems like you've turned on the 404 Page Builder, but you haven't set up any builders yet. To avoid any issues, please either %s this feature or %s. You can find a step-by-step guide in our documentation.", 'glozin-addons' ),
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

	public function redirect_template( $template ){
        $template_part = '';
        $template_id = 0;

		if ( is_404() ) {
            self::$page_id = self::get_page_id();
            if ( self::$page_id ) {
                $template_id = self::$page_id;
            }

			$template_part = '404_page';

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
					'terms' => array( '404_page', 'enable' )
				),
			),
		));

		$page_id = $query->posts ? $query->posts[0] : 0;
        self::$page_id =  $page_id;
		wp_reset_postdata();
		return self::$page_id;
	}
}