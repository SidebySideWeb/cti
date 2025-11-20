<?php
/**
 * Integrate with Elementor.
 */

namespace Glozin\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor {
	/**
	 * Instance
	 *
	 * @access private
	 */
	private static $_instance = null;

	/**
	 * Elementor modules
	 *
	 * @var array
	 */
	public $modules = [];

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Glozin_Addons_Elementor An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->setup_hooks();
		$this->_includes();

		\Glozin\Addons\Elementor\Controls\AutoComplete_AjaxLoader::instance();
		\Glozin\Addons\Elementor\Page_Settings\Controls::instance();
		\Glozin\Addons\Elementor\Page_Settings\Frontend::instance();
		\Glozin\Addons\Elementor\Controls\Settings_Layout::instance();
		\Glozin\Addons\Elementor\Builder::instance();
		\Glozin\Addons\Elementor\Library::instance();
		if ( class_exists( 'Woocommerce' ) ) {
			\Glozin\Addons\Elementor\Modules\Shoppable_Images\Module::instance();
			\Glozin\Addons\Elementor\Modules\Product_Sale_Meta::instance();
			\Glozin\Addons\Elementor\AJAX\Products::instance();
			\Glozin\Addons\Elementor\AJAX\Categories::instance();
			\Glozin\Addons\Elementor\AJAX\Shoppable_Images::instance();
			\Glozin\Addons\Elementor\AJAX\Products_Bundle::instance();
		}

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			\Glozin\Addons\Elementor\Modules\Custom_CSS::instance();
		}
	}

	/**
	 * Auto load widgets
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$path = explode( '\\', $class );
		$filename = strtolower( array_pop( $path ) );
		$filename = str_replace( '_', '-', $filename );

		$module = array_pop( $path );

		if ( 'Modules' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/modules/' . $filename . '.php';
		} elseif ( 'Widgets' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/widgets/' . $filename . '.php';
		} elseif ( 'Base' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/base/' . $filename . '.php';
		} elseif ( 'Controls' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/controls/' . $filename . '.php';
		} elseif ( 'Traits' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/widgets/traits/' . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	/**
	 * Includes files which are not widgets
	 */
	private function _includes() {
		$classes = [
			'Glozin\Addons\Elementor\Controls\AjaxLoader'    => GLOZIN_ADDONS_DIR . 'inc/elementor/controls/autocomplete-ajaxloader.php',
			'Glozin\Addons\Elementor\Page_Settings\Controls' => GLOZIN_ADDONS_DIR . 'inc/elementor/page-settings/controls.php',
			'Glozin\Addons\Elementor\Page_Settings\Frontend' => GLOZIN_ADDONS_DIR . 'inc/elementor/page-settings/frontend.php',
			'Glozin\Addons\Elementor\Controls\Settings_Layout' => GLOZIN_ADDONS_DIR . 'inc/elementor/controls/settings_layout.php',
			'Glozin\Addons\Elementor\AJAX\Products' => GLOZIN_ADDONS_DIR . 'inc/elementor/ajax/products.php',
			'Glozin\Addons\Elementor\AJAX\Categories' => GLOZIN_ADDONS_DIR . 'inc/elementor/ajax/categories.php',
			'Glozin\Addons\Elementor\AJAX\Shoppable_Images' => GLOZIN_ADDONS_DIR . 'inc/elementor/ajax/shoppable-images.php',
			'Glozin\Addons\Elementor\AJAX\Products_Bundle' => GLOZIN_ADDONS_DIR . 'inc/elementor/ajax/products-bundle.php',
			'Glozin\Addons\Elementor\Library'  => GLOZIN_ADDONS_DIR . 'inc/elementor/library/library.php',
			'Glozin\Addons\Elementor\Builder'  => GLOZIN_ADDONS_DIR . 'inc/elementor/builder/builder.php',
			'Glozin\Addons\Elementor\Modules\Shoppable_Images\Module'  => GLOZIN_ADDONS_DIR . 'inc/elementor/modules/shoppable-images/module.php',
			'Glozin\Addons\Elementor\Modules\Product_Sale_Meta'  => GLOZIN_ADDONS_DIR . 'inc/elementor/modules/product-sale-meta.php',
		];

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$classes['Glozin\Addons\Elementor\Modules\Custom_CSS'] = GLOZIN_ADDONS_DIR . 'inc/elementor/modules/custom-css.php';
		}

		\Glozin\Addons\Auto_Loader::register( $classes );
	}

	/**
	 * Hooks to init
	 */
	protected function setup_hooks() {
		add_action( 'elementor/init', [ $this, 'init_modules' ] );

		// Widgets
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'register_styles' ] );

		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );

		// Register controls
		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );

		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
			add_action( 'init', [ $this, 'register_wc_hooks' ], 5 );
		}
	}

	/**
	 * Register WC hooks for Elementor editor
	 */
	public function register_wc_hooks() {
		if ( function_exists( 'wc' ) ) {
			wc()->frontend_includes();
		}
	}

	/**
	 * Init modules
	 */
	public function init_modules() {
		$this->modules['section-settings'] = \Glozin\Addons\Elementor\Modules\Section_Settings::instance();
	}


	/**
	 * Register autocomplete control
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_controls( $controls_manager ) {
		$controls_manager->register( new \Glozin\Addons\Elementor\Controls\AutoComplete() );
	}

	/**
	 * Register styles
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_styles() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mapbox', GLOZIN_ADDONS_URL . 'assets/css/mapbox.css', array(), '1.0' );
		wp_register_style( 'mapboxgl', GLOZIN_ADDONS_URL . 'assets/css/mapbox-gl.css', array(), '1.0' );

		wp_register_style( 'magnific',  GLOZIN_ADDONS_URL . 'assets/css/magnific-popup'. $debug . 'css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-slides-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/slides'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-accordion-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/accordion'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-store-locations-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/store-locations'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-countdown-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/countdown'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-brands-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/brands'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-timeline-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/timeline'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-navigation-menu-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/navigation-menu'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-categories-grid-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/categories-grid'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-products-carousel-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/products-carousel'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-banner-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/banner'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-marquee-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/marquee'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-testimonial-carousel-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/testimonial-carousel'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-icon-box-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/icon-box'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-product-tabs-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/product-tabs'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_register_style( 'glozin-lookbook-carousel-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/lookbook-carousel'. $debug . '.css', array(), GLOZIN_ADDONS_VER );

		wp_register_style( 'glozin-elementor-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/elementor'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
	}

	/**
	 * Register styles
	 */
	public function register_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'glozin-image-slide', GLOZIN_ADDONS_URL . 'assets/js/image-slide.js', ['jquery'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-eventmove', GLOZIN_ADDONS_URL . 'assets/js/jquery.event.move.js', ['jquery'], GLOZIN_ADDONS_VER, true );

		wp_register_script( 'mapbox', GLOZIN_ADDONS_URL  . 'assets/js/mapbox.min.js', array(), '1.0', true );
		wp_register_script( 'mapboxgl', GLOZIN_ADDONS_URL  . 'assets/js/mapbox-gl.min.js', array(), '1.0', true );
		wp_register_script( 'mapbox-sdk', GLOZIN_ADDONS_URL  . 'assets/js/mapbox-sdk.min.js', array(), '1.0', true );

		wp_register_script( 'glozin-counter-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/counter'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-contact-form-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/contact-form'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-accordion-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/accordion'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-store-locations-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/store-locations'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-countdown-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/countdown'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-brands-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/brands'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-product-recently-viewed-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/product-recently-viewed'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-products-carousel-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/products-carousel'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-categories-grid-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/categories-grid'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-subscribe-form-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/subscribe-form'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-banner-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/banner'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-marquee-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/marquee'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-shoppable-images-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/shoppable-images'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-product-tabs-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/product-tabs'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-product-grid-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/product-grid'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-shoppable-video-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/shoppable-video'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );

		wp_register_script( 'glozin-elementor-widgets', GLOZIN_ADDONS_URL . 'assets/js/elementor/elementor-widgets'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
	}


	/**
	 * Init Widgets
	 */
	public function init_widgets() {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Heading() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Button() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Counter() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Image_Box() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Contact_Form() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Accordion() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Store_Locations() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Subscribe_Box() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Social_Icons() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Countdown() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Timeline() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Navigation_Menu() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Subscribe_Group() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Slides() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Banner() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Marquee() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Dismiss_Popup_Button() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Code_Discount() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Short_Content() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Icon_Box() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Icon_Box_Carousel() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Promo_Card() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Banner_Carousel() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Tiktok_Video_Carousel() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Posts_Carousel() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Image_Carousel() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Image_Box_Carousel() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Image_Before_After() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Content_Preview_Tabs() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Navigation_Bar_Item() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Glozin_Widget_Image() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Split_Hero_Slider() );

		if ( class_exists( 'Woocommerce' ) ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Brands() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Recently_Viewed() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Recently_Viewed_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Currencies() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Languages() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Categories_Grid() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Categories_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Products_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Banner_Products() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Testimonial_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Testimonial_Carousel_2() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Shoppable_Images() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Shoppable_Images_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_List() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Tabs() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Tabs_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Products_Bundle() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Lookbook_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Sale_Tabs() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Grid() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Showcase() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Shoppable_Video_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Lookbook_Products() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Deals_Carousel() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Highlight_Slider() );
			$widgets_manager->register( new \Glozin\Addons\Elementor\Widgets\Product_Spotlight_Grid() );

		}

	}

	/**
	 * Add Glozin category
	 */
	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'glozin-addons',
			[
				'title' => __( 'Glozin', 'glozin-addons' )
			]
		);

		$elements_manager->add_category(
			'glozin-addons-footer',
			[
				'title' => __( 'Glozin Footer', 'glozin-addons' )
			]
		);

		$elements_manager->add_category(
			'glozin-addons-navigation',
			[
				'title' => __( 'Glozin Navigation', 'glozin-addons' )
			]
		);

		$elements_manager->add_category(
			'glozin-addons-popup',
			[
				'title' => __( 'Glozin Popup', 'glozin-addons' )
			]
		);
	}
}