<?php

namespace Glozin\Addons\Modules\Popup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Post_Type  {

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

	const POST_TYPE         = 'glozin_popup';
	const TAXONOMY_TAB_TYPE = 'glozin_popup_type';


	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
			// Make sure the post types are loaded for imports
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );

		add_action( 'import_start', array( $this, 'register_post_type' ), 30 );
		$this->register_post_type();

		add_filter( 'single_template', array( $this, 'load_canvas_template' ) );

	}

	/**
	 * Register popup post type
     *
	 * @since 1.0.0
     *
     * @return void
	 */
	public function register_post_type() {
		if(post_type_exists(self::POST_TYPE)) {
			return;
		}

		register_post_type( self::POST_TYPE, array(
			'description'         => esc_html__( 'Theme Popup', 'glozin-addons' ),
			'labels'              => array(
				'name'                  => esc_html__( 'Theme Popup', 'glozin-addons' ),
				'singular_name'         => esc_html__( 'Theme Popup', 'glozin-addons' ),
				'menu_name'             => esc_html__( 'Theme Popup', 'glozin-addons' ),
				'all_items'             => esc_html__( 'Theme Popup', 'glozin-addons' ),
				'add_new'               => esc_html__( 'Add New', 'glozin-addons' ),
				'add_new_item'          => esc_html__( 'Add New Popup', 'glozin-addons' ),
				'edit_item'             => esc_html__( 'Edit Popup', 'glozin-addons' ),
				'new_item'              => esc_html__( 'New Popup', 'glozin-addons' ),
				'view_item'             => esc_html__( 'View Popup', 'glozin-addons' ),
				'search_items'          => esc_html__( 'Search popup', 'glozin-addons' ),
				'not_found'             => esc_html__( 'No popup found', 'glozin-addons' ),
				'not_found_in_trash'    => esc_html__( 'No popup found in Trash', 'glozin-addons' ),
				'filter_items_list'     => esc_html__( 'Filter popups list', 'glozin-addons' ),
				'items_list_navigation' => esc_html__( 'Popup list navigation', 'glozin-addons' ),
				'items_list'            => esc_html__( 'Popup list', 'glozin-addons' ),
			),
			'supports'            => array( 'title', 'editor', 'elementor' ),
			'public'              => true,
			'rewrite'             => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'show_in_menu'        => false,
		) );

	}

	/**
	 * Register the admin menu
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_submenu_page(
			'glozin_dashboard',
			esc_html__( 'Theme Popup', 'glozin-addons' ),
			esc_html__( 'Theme Popup', 'glozin-addons' ),
			'edit_pages',
			'edit.php?post_type=' . self::POST_TYPE . ''
		);

	}

	/**
	 * Load the canvas template
	 *
	 * @since 1.0.0
	 *
	 * @param string $single_template The single template.
	 * @return string The single template.
	 */
	function load_canvas_template( $single_template ) {
		global $post;

		if( 'glozin_popup' == $post->post_type ) {
			add_action( 'elementor/page_templates/canvas/before_content', array( $this, 'add_popup_before_content' ) );
			return ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
			add_action( 'elementor/page_templates/canvas/after_content', array( $this, 'add_popup_after_content' ) );
		}

		return $single_template;
	}

	/**
	 * Add the popup before content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_popup_before_content() {
		echo '<div class="glozin-popup__backdrop"></div>';
		echo '<div class="glozin-popup__wrapper">';
		echo '<span class="glozin-svg-icon glozin-popup__close"><svg aria-hidden="true" role="img" focusable="false" fill="currentColor" width="16" height="16" viewBox="0 0 16 16"><path d="M16 1.4L14.6 0L8 6.6L1.4 0L0 1.4L6.6 8L0 14.6L1.4 16L8 9.4L14.6 16L16 14.6L9.4 8L16 1.4Z" fill="currentColor"></path></svg></span>';
		if(class_exists('\Glozin\Icon') && method_exists('\Glozin\Icon', 'inline_icons')) {
			echo '<div id="svg-defs" class="svg-defs hidden" aria-hidden="true" tabindex="-1">';
			\Glozin\Icon::inline_icons();
			echo '</div>';
		}
	}

	/**
	 * Add the popup after content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_popup_after_content() {
		echo '</div>';
	}
}