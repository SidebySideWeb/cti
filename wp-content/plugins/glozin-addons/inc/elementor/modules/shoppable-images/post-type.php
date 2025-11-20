<?php
/**
 * Glozin Addons Shoppable Images functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Elementor\Modules\Shoppable_Images;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Post_Type {
    /**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

    const POST_TYPE     = 'shoppable_images';
	const OPTION_NAME   = 'shoppable_images';
	const TAXONOMY_TYPE = 'shoppable_images_type';

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

    public function __construct() {
        add_action( 'init', array( $this, 'actions') );
    }

    public function actions() {
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 50 );

		// Make sure the post types are loaded for imports
		add_action( 'import_start', array( $this, 'register_post_type' ) );

		// Register custom post type and custom taxonomy
		$this->register_post_type();

		add_filter( 'single_template', array( $this, 'load_canvas_template' ) );
    }

    /**
	 * Register portfolio post type
	 */
	public function register_post_type() {
		// Template Builder
		$labels = array(
			'name'               => esc_html__( 'Shoppable Images Content', 'glozin-addons' ),
			'singular_name'      => esc_html__( 'Shoppable Image Content', 'glozin-addons' ),
			'menu_name'          => esc_html__( 'Shoppable Image Content', 'glozin-addons' ),
			'name_admin_bar'     => esc_html__( 'Shoppable Image Content', 'glozin-addons' ),
			'add_new'            => esc_html__( 'Add New', 'glozin-addons' ),
			'add_new_item'       => esc_html__( 'Add New Content', 'glozin-addons' ),
			'new_item'           => esc_html__( 'New Content', 'glozin-addons' ),
			'edit_item'          => esc_html__( 'Edit Content', 'glozin-addons' ),
			'view_item'          => esc_html__( 'View Content', 'glozin-addons' ),
			'all_items'          => esc_html__( 'All Content', 'glozin-addons' ),
			'search_items'       => esc_html__( 'Search Content', 'glozin-addons' ),
			'parent_item_colon'  => esc_html__( 'Parent Content:', 'glozin-addons' ),
			'not_found'          => esc_html__( 'No Content found.', 'glozin-addons' ),
			'not_found_in_trash' => esc_html__( 'No Templates found in Trash.', 'glozin-addons' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'rewrite'             => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-editor-kitchensink',
			'supports'            => array( 'title', 'editor', 'elementor' ),
		);

		if ( ! post_type_exists( self::POST_TYPE ) ) {
			register_post_type( self::POST_TYPE, $args );
		}
	}

	public function register_admin_menu() {
		add_submenu_page(
			'glozin_dashboard',
			esc_html__( 'Shoppable Images Content', 'glozin-addons' ),
			esc_html__( 'Shoppable Images Content', 'glozin-addons' ),
			'edit_pages',
			'edit.php?post_type=' . self::POST_TYPE . ''
		);

	}

	function load_canvas_template( $single_template ) {
		global $post;

		if( self::POST_TYPE == $post->post_type ) {
			add_action( 'elementor/page_templates/canvas/before_content', array( $this, 'add_shoppable_images_before_content' ) );
			return ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
			add_action( 'elementor/page_templates/canvas/after_content', array( $this, 'add_shoppable_images_after_content' ) );
		}

		return $single_template;
	}

	public function add_shoppable_images_before_content() {
		echo '<div class="shoppable-images-modal">';
		echo '<div class="modal__backdrop"></div>';
		echo '<div class="modal__container">';
		echo '<div class="modal__wrapper">';
		echo '<span class="modal__button-close position-fixed position-absolute-md z-1 gz-button gz-button-icon"><svg aria-hidden="true" role="img" focusable="false" fill="currentColor" width="16" height="16" viewBox="0 0 16 16"><path d="M16 1.4L14.6 0L8 6.6L1.4 0L0 1.4L6.6 8L0 14.6L1.4 16L8 9.4L14.6 16L16 14.6L9.4 8L16 1.4Z" fill="currentColor"></path></svg></span>';
		if(class_exists('\Glozin\Icon') && method_exists('\Glozin\Icon', 'inline_icons')) {
			echo '<div id="svg-defs" class="svg-defs hidden" aria-hidden="true" tabindex="-1">';
			\Glozin\Icon::inline_icons();
			echo '</div>';
		}
		echo '<div class="modal__content"><div class="modal__shoppable">';
	}

	public function add_shoppable_images_after_content() {
		echo '</div></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}