<?php
/**
 * Register template builder
 */

namespace Glozin\Addons\Elementor\Builder;

class Widgets {

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
	 * Class constructor.
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );

		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );

		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
			add_action( 'init', [ $this, 'register_wc_hooks' ], 5 );
		}
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

		wp_register_style( 'glozin-products-carousel-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/products-carousel'. $debug . '.css', array(), GLOZIN_ADDONS_VER );

		wp_enqueue_style( 'driff-style', get_template_directory_uri() . '/assets/css/plugins/drift-basic.css');
		wp_enqueue_style( 'glozin-widgets-builder-elementor-css',  GLOZIN_ADDONS_URL . 'assets/css/elementor/widgets-builder-elementor'. $debug . '.css', array(), GLOZIN_ADDONS_VER );
	}

	/**
	 * Register styles
	 */
	public function register_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'glozin-products-carousel-widget', GLOZIN_ADDONS_URL . 'assets/js/elementor/products-carousel'. $debug . '.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_register_script( 'glozin-product-elementor-widgets', GLOZIN_ADDONS_URL . 'inc/elementor/builder/assets/js/elementor-widgets.js', ['jquery', 'underscore', 'elementor-frontend', 'regenerator-runtime'], GLOZIN_ADDONS_VER, true );
		wp_enqueue_script( 'driff-js', get_template_directory_uri() . '/assets/js/plugins/drift.min.js', array(), '', true );

		wp_enqueue_script( 'wc-single-product' );
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

		if ( 'Widgets' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/widgets/' . $filename . '.php';
		} elseif ( 'Traits' == $module ) {
			$filename = GLOZIN_ADDONS_DIR . 'inc/elementor/builder/traits/' . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			include( $filename );
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
	 * Init Widgets
	 */
	public function init_widgets() {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Breadcrumb() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Navigation() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Images() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Category() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Rating() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Title() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Short_Description() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_SKU() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Categories() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Available() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Tag() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Badges() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Price() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Countdown() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Add_To_Cart_Form() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Ask_Question() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Share() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Data_Tabs() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Reviews() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Upsells() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Related() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\WC_Notices() );

		if( get_option( 'glozin_people_view_fake' ) == 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\People_View_Fake() );
		}

		if( get_option( 'glozin_variation_compare_toggle' ) == 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Variation_Compare() );
		}

		if( get_option( 'glozin_product_bought_together' ) == 'yes'  ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_FBT() );
		}

		if( get_option( 'glozin_product_variations_listing' ) == 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Product_Variations_Listing() );
		}

		if( get_option( 'glozin_advanced_linked_products' ) == 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Advanced_Linked_Products() );
		}

		if( get_option( 'glozin_recent_sales_count' ) == 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Recent_Sales_Count() );
		}

		if( get_option( 'glozin_linked_variant' ) == 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Linked_Variant() );
		}

		if( get_option( 'glozin_model_sizing' ) === 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Model_Sizing() );
		}

		if( get_option( 'glozin_buy_x_get_y' ) === 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Buy_X_Get_Y() );
		}

		if( get_option( 'glozin_dynamic_pricing_discounts' ) === 'yes' ) {
			$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Dynamic_Pricing_Discounts() );
		}

		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Archive_Products() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Products_Filter() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Products_Filter_Actived() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Archive_Product_View() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Archive_Product_Ordering() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Archive_Product_Total() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Archive_Page_Header() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\Archive_Top_Categories() );

		/* $widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\WC_Cart() );
		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\WC_Cart_Cross_Sell() );

		$widgets_manager->register( new \Glozin\Addons\Elementor\Builder\Widgets\WC_Checkout() ); */
	}

	/**
	 * Add Glozin category
	 */
	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'glozin-addons-product',
			[
				'title' => __( 'Glozin Product', 'glozin-addons' )
			]
		);

		$elements_manager->add_category(
			'glozin-addons-archive-product',
			[
				'title' => __( 'Glozin Product Archive ', 'glozin-addons' )
			]
		);

		$elements_manager->add_category(
			'glozin-addons-cart-page',
			[
				'title' => __( 'Glozin Cart Page', 'glozin-addons' )
			]
		);

		$elements_manager->add_category(
			'glozin-addons-checkout-page',
			[
				'title' => __( 'Glozin Checkout Page', 'glozin-addons' )
			]
		);
	}
}