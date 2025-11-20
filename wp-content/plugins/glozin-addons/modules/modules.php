<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Modules {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Registered modules.
	 *
	 * Holds the list of all the registered modules.
	 *
	 * @var array
	 */
	private $modules = [];

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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->register( 'mega-menu' );

		$this->includes();
		add_action( 'init', [ $this, 'add_actions' ], 20 );
		\Glozin\Addons\Modules\Products_Filter\Module::instance();

		add_action( 'init', [ $this, 'activate' ] );

	}

	/**
	 * Includes files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		\Glozin\Addons\Auto_Loader::register( [
			'Glozin\Addons\Modules\Settings'             				=> GLOZIN_ADDONS_DIR . 'modules/settings.php',
			'Glozin\Addons\Modules\Base\Variation_Select'             => GLOZIN_ADDONS_DIR . 'modules/base/variation-select.php',
			'Glozin\Addons\Modules\Products_Filter\Module'            => GLOZIN_ADDONS_DIR . 'modules/products-filter/module.php',
			'Glozin\Addons\Modules\Size_Guide\Module'                 => GLOZIN_ADDONS_DIR . 'modules/size-guide/module.php',
			'Glozin\Addons\Modules\Buy_Now\Module'                    => GLOZIN_ADDONS_DIR . 'modules/buy-now/module.php',
			'Glozin\Addons\Modules\Sticky_Add_To_Cart\Module'         => GLOZIN_ADDONS_DIR . 'modules/sticky-add-to-cart/module.php',
			'Glozin\Addons\Modules\Product_Tabs\Module'               => GLOZIN_ADDONS_DIR . 'modules/product-tabs/module.php',
			'Glozin\Addons\Modules\Live_Sales_Notification\Module'    => GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/module.php',
			'Glozin\Addons\Modules\Variation_Images\Module'           => GLOZIN_ADDONS_DIR . 'modules/variation-images/module.php',
			'Glozin\Addons\Modules\Product_Bought_Together\Module'    => GLOZIN_ADDONS_DIR . 'modules/product-bought-together/module.php',
			'Glozin\Addons\Modules\Variation_Compare\Module'          => GLOZIN_ADDONS_DIR . 'modules/variation-compare/module.php',
			'Glozin\Addons\Modules\People_View_Fake\Module'           => GLOZIN_ADDONS_DIR . 'modules/people-view-fake/module.php',
			'Glozin\Addons\Modules\Free_Shipping_Bar\Module'          => GLOZIN_ADDONS_DIR . 'modules/free-shipping-bar/module.php',
			'Glozin\Addons\Modules\Product_Video\Module'              => GLOZIN_ADDONS_DIR . 'modules/product-video/module.php',
			'Glozin\Addons\Modules\Advanced_Linked_Products\Module'   => GLOZIN_ADDONS_DIR . 'modules/advanced-linked-products/module.php',
			'Glozin\Addons\Modules\Product_360_View\Module'           => GLOZIN_ADDONS_DIR . 'modules/product-360-view/module.php',
			'Glozin\Addons\Modules\Advanced_Search\Module'            => GLOZIN_ADDONS_DIR . 'modules/advanced-search/module.php',
			'Glozin\Addons\Modules\Popup\Module'                      => GLOZIN_ADDONS_DIR . 'modules/popup/module.php',
			'Glozin\Addons\Modules\Add_To_Cart_Ajax\Module'           => GLOZIN_ADDONS_DIR . 'modules/add-to-cart-ajax/module.php',
			'Glozin\Addons\Modules\Catalog_Mode\Module'    			  => GLOZIN_ADDONS_DIR . 'modules/catalog-mode/module.php',
			'Glozin\Addons\Modules\Inventory\Module'    			  => GLOZIN_ADDONS_DIR . 'modules/inventory/module.php',
			'Glozin\Addons\Modules\Recent_Sales_Count\Module'         => GLOZIN_ADDONS_DIR . 'modules/recent-sales-count/module.php',
			'Glozin\Addons\Modules\Catalog_Mode\Module'    			  => GLOZIN_ADDONS_DIR . 'modules/catalog-mode/module.php',
			'Glozin\Addons\Modules\Inventory\Module'    			  => GLOZIN_ADDONS_DIR . 'modules/inventory/module.php',
			'Glozin\Addons\Modules\Linked_Variant\Module'    		  => GLOZIN_ADDONS_DIR . 'modules/linked-variant/module.php',
			'Glozin\Addons\Modules\Customer_Reviews\Module'    		  => GLOZIN_ADDONS_DIR . 'modules/customer-reviews/module.php',
			'Glozin\Addons\Modules\Pre_Order\Module'    		      => GLOZIN_ADDONS_DIR . 'modules/pre-order/module.php',
			'Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Module'  => GLOZIN_ADDONS_DIR . 'modules/dynamic-pricing-discounts/module.php',
			'Glozin\Addons\Modules\Variation_Image_By_Attributes\Module'  => GLOZIN_ADDONS_DIR . 'modules/variation-image-by-attributes/module.php',
			'Glozin\Addons\Modules\Buy_X_Get_Y\Module'    		      => GLOZIN_ADDONS_DIR . 'modules/buy-x-get-y/module.php',
		] );
	}


	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_actions() {
		\Glozin\Addons\Modules\Popup\Module::instance();
		\Glozin\Addons\Modules\Advanced_Search\Module::instance();
		\Glozin\Addons\Modules\Inventory\Module::instance();
		\Glozin\Addons\Modules\Catalog_Mode\Module::instance();
		\Glozin\Addons\Modules\Live_Sales_Notification\Module::instance();
		if( class_exists( '\WCBoost\VariationSwatches\Plugin' ) ) {
			\Glozin\Addons\Modules\Multi_Color_Swatches\Module::instance();
		}
		 \Glozin\Addons\Modules\Pre_Order\Module::instance();
		 \Glozin\Addons\Modules\Checkout_Limit\Module::instance();
		\Glozin\Addons\Modules\Product_Video\Module::instance();
		\Glozin\Addons\Modules\Product_3D_Viewer\Module::instance();
		\Glozin\Addons\Modules\Product_360\Module::instance();
		\Glozin\Addons\Modules\Size_Guide\Module::instance();
		\Glozin\Addons\Modules\Variation_Images\Module::instance();
		\Glozin\Addons\Modules\People_View_Fake\Module::instance();
		\Glozin\Addons\Modules\Products_Stock_Progress_Bar\Module::instance();
		\Glozin\Addons\Modules\Linked_Variant\Module::instance();
		\Glozin\Addons\Modules\Add_To_Cart_Ajax\Module::instance();
		\Glozin\Addons\Modules\Buy_Now\Module::instance();
		\Glozin\Addons\Modules\Variation_Compare\Module::instance();
		\Glozin\Addons\Modules\Model_Sizing\Module::instance();
		\Glozin\Addons\Modules\Advanced_Linked_Products\Module::instance();
		\Glozin\Addons\Modules\Free_Shipping_Bar\Module::instance();
		\Glozin\Addons\Modules\Recent_Sales_Count\Module::instance();
		\Glozin\Addons\Modules\Product_Bought_Together\Module::instance();
		\Glozin\Addons\Modules\Product_Tabs\Module::instance();
		\Glozin\Addons\Modules\Customer_Reviews\Module::instance();
		\Glozin\Addons\Modules\Sticky_Add_To_Cart\Module::instance();
		\Glozin\Addons\Modules\Variation_Image_By_Attributes\Module::instance();
		\Glozin\Addons\Modules\Dynamic_Pricing_Discounts\Module::instance();
		// \Glozin\Addons\Modules\Buy_X_Get_Y\Module::instance();

		\Glozin\Addons\Modules\Settings::instance();
	}

	/**
	 * Register a module
	 *
	 * @param string $module_name
	 */
	public function register( $module_name ) {
		if ( ! array_key_exists( $module_name, $this->modules ) ) {
			$this->modules[ $module_name ] = null;
		}
	}

	/**
	 * Deregister a moudle.
	 * Only allow deregistering a module if it is not activated.
	 *
	 * @param string $module_name
	 */
	public function deregister( $module_name ) {
		if ( ! array_key_exists( $module_name, $this->modules ) && empty( $this->modules[ $module_name ] ) ) {
			unset( $this->modules[ $module_name ] );
		}
	}

	/**
	 * Active all registered modules
	 *
	 * @return void
	 */
	public function activate() {
		foreach ( $this->modules as $module_name => $instance ) {
			if ( ! empty( $instance ) ) {
				continue;
			}

			$classname = $this->get_module_classname( $module_name );

			if ( $classname ) {
				$this->modules[ $module_name ] = $classname::instance();
			}
		}

	}

	/**
	 * Get module class name
	 *
	 * @param string $module_name
	 * @return string
	 */
	public function get_module_classname( $module_name ) {
		$class_name = str_replace( '-', ' ', $module_name );
		$class_name = str_replace( ' ', '_', ucwords( $class_name ) );
		$class_name = 'Glozin\\Addons\\Modules\\' . $class_name . '\\Module';

		return $class_name;
	}
}
