<?php
/**
 * Glozin init
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
 * Glozin theme init
 */
final class Theme {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance = null;

	/**
	 * Blog manager instance.
	 *
	 * @var $blog_manager
	 */
	public $blog_manager = null;

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
		$this->include_files();
	}

	/**
	 * Function to include files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function include_files() {
		require_once get_template_directory() . '/inc/autoload.php';

		if ( is_admin() ) {
			require_once get_template_directory() . '/inc/libs/tgm-plugin-activation.php';
		}
	}

	/**
	 * Hooks to init
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		// Before init action.
		do_action( 'before_glozin_init' );

		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_content_width' ), 0 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		\Glozin\Admin::instance();
		\Glozin\Maintenance::instance();
		add_action( 'init', array( $this, 'loads' ), 50);

		if( class_exists('WooCommerce')  ) {
			\Glozin\WooCommerce::instance();
		}

		// Init action.
		do_action( 'after_glozin_init' );

	}

	/**
	 * Hooks to loads
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loads() {
		\Glozin\Options::instance();
		\Glozin\Frontend::instance();
		\Glozin\Dynamic_CSS::instance();

		\Glozin\Header\Manager::instance();
		\Glozin\Header\Search::instance();
		\Glozin\Breadcrumb::instance();
		\Glozin\Page_Header::instance();

		\Glozin\Blog::instance();

		\Glozin\Footer::instance();

		\Glozin\Modals::instance();

		\Glozin\Languages\WPML::instance();

		if( class_exists('TRP_Translate_Press') ) {
			\Glozin\Languages\TRP::instance();
		}
	}

	/**
	 * Setup theme
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_theme() {
		// Theme supports
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'customize-selective-refresh-widgets' );

		add_editor_style( 'assets/css/editor-style.css' );

		// Load regular editor styles into the new block-based editor.
		add_theme_support( 'editor-styles' );

		// Load default block styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for responsive embeds.
		add_theme_support( 'responsive-embeds' );

		add_theme_support( 'align-wide' );

		add_theme_support( 'align-full' );


		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary-menu'    => esc_html__( 'Primary Menu', 'glozin' ),
			'secondary-menu'  => esc_html__( 'Secondary Menu', 'glozin' ),
			'category-menu'  => esc_html__( 'Category Menu', 'glozin' ),
			'user_logged'     => esc_html__( 'User Logged Menu', 'glozin' ),
		) );

	}

	/**
	 * Set the $content_width global variable used by WordPress to set image dimennsions.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'glozin_content_width', 1440 );
	}

	/**
	 * Register widget area.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function widgets_init() {
		$sidebars = array(
			'blog-sidebar'            => esc_html__( 'Blog Sidebar', 'glozin' ),
			'catalog-filters-sidebar' => esc_html__( 'Catalog Filters Sidebar', 'glozin' ),
		);

		// Register sidebars
		foreach ( $sidebars as $id => $name ) {
			register_sidebar(
				array(
					'name'          => $name,
					'id'            => $id,
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widget-title">',
					'after_title'   => '</h2>',
				)
			);
		}

	}

	/**
	 * Setup the theme global variable.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function setup_prop( $args = array() ) {
		$default = array(
			'panels'        => array(),
			'modals'        => array(),
			'popovers'      => array(),
			'modals-addons' => array(),
			'first_image_load' => array(),
		);

		if ( isset( $GLOBALS['glozin'] ) ) {
			$default = array_merge( $default, $GLOBALS['glozin'] );
		}

		$GLOBALS['glozin'] = wp_parse_args( $args, $default );
	}

	/**
	 * Get a propery from the global variable.
	 *
	 * @param string $prop Prop to get.
	 * @param string $default Default if the prop does not exist.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_prop( $prop, $default = '' ) {
		self::setup_prop(); // Ensure the global variable is setup.

		return isset( $GLOBALS['glozin'], $GLOBALS['glozin'][ $prop ] ) ? $GLOBALS['glozin'][ $prop ] : $default;
	}

	/**
	 * Sets a property in the global variable.
	 *
	 * @param string $prop Prop to set.
	 * @param string $value Value to set.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function set_prop( $prop, $value = '' ) {
		if ( ! isset( $GLOBALS['glozin'] ) ) {
			self::setup_prop();
		}

		if ( ! isset( $GLOBALS['glozin'][ $prop ] ) ) {
			$GLOBALS['glozin'][ $prop ] = $value;

			return;
		}

		if( array_search( $value,  self::get_prop( $prop ) ) !== false ) {
			return;
		}

		if ( is_array( $GLOBALS['glozin'][ $prop ] ) ) {
			if ( is_array( $value ) ) {
				$GLOBALS['glozin'][ $prop ] = array_merge( $GLOBALS['glozin'][ $prop ], $value );
			} else {
				$GLOBALS['glozin'][ $prop ][] = $value;
				array_unique( $GLOBALS['glozin'][ $prop ] );
			}
		} else {
			$GLOBALS['glozin'][ $prop ] = $value;
		}
	}
}
