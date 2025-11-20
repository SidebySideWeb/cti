<?php

/**
 * WooCommerce wcfm functions
 *
 * @package glozin
 */

 namespace Glozin\Vendors;

 if ( ! defined( 'ABSPATH' ) ) {
	 exit; // Exit if accessed directly.
 }


/**
 * Class of Vendor Dokan
 *
 * @version 1.0
 */
class WCFM {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

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
	 * Construction function
	 *
	 * @since  1.0
	 * @return Motta_Vendor
	 */
	public function __construct() {
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 30 );
		add_filter( 'wcfmmp_is_allow_archive_product_sold_by', '__return_false' );

		$this->product_card_layout();

		add_action( 'template_redirect', array( $this, 'product_sold_by_template' ), 20 );
		add_action('glozin_woocommerce_product_quickview_summary', array( $this, 'vendor_name' ), 30 );
		add_action('glozin_woocommerce_product_quickadd_summary_header', array( $this, 'vendor_name' ), 30 );

		// Store list sidebar panel
		add_action( 'glozin_after_close_site_footer', array( $this, 'store_list_button_sidebar' ), 100 );
		add_action( 'glozin_after_close_site_footer', array( $this, 'store_list_sidebar_panel' ), 100 );

		// Store page sidebar panel
		add_action( 'glozin_after_close_site_footer', array( $this, 'store_page_button_sidebar' ), 100 );
		add_action( 'glozin_after_close_site_footer', array( $this, 'store_page_sidebar_panel' ), 100 );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		if ( ( function_exists( 'wcfmmp_is_stores_list_page' ) && wcfmmp_is_stores_list_page() )
			|| ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() )
			&& intval( \Glozin\Helper::get_option( 'vendor_store_style_theme' ) )) {
				$classes[] = 'glozin-store-style-theme';
			}

		if ( function_exists( 'wcfmmp_is_stores_list_page' ) && wcfmmp_is_stores_list_page() ) {
			$classes[] = 'glozin-wcfm-store-list';
		}

		if ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() ) {
			$classes[] = 'glozin-wcfm-store-page';
		}

		return $classes;
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'glozin-wcfm', get_template_directory_uri() . '/assets/css/vendors/wcfm.css', array(), '20241003' );

		wp_enqueue_script( 'glozin-wcfm', get_template_directory_uri() . '/assets/js/vendors/wcfm.js', array(), '20240508', array('strategy' => 'defer') );
	}


	/**
	 * Product Card layout
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_card_layout() {
		if( \Glozin\Helper::get_option( 'product_card_vendor_name' ) == 'none' ) {
			return;
		}

		if ( \Glozin\Helper::get_option( 'product_card_vendor_position' ) == 'after-thumbnail' ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'vendor_name' ), 5 );
		} else {
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'vendor_name' ), 10 );
		}
	}

	/**
	 * Vendor name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function vendor_name() {
		global $product;

		if( ! $product ) {
			return;
        }

		if( ! function_exists('wcfm_get_vendor_id_by_post') ) {
			return;
		}

		$vendor_id = wcfm_get_vendor_id_by_post( $product->get_ID() );

		if ( ! $vendor_id ) {
			return;
		}

		$vendor_type = \Glozin\Helper::get_option( 'product_card_vendor_name' );

		$store_name = function_exists('wcfm_get_vendor_store_name') ? wcfm_get_vendor_store_name( absint($vendor_id) ) : '';

		$store_link = function_exists('wcfmmp_get_store_url') ? wcfmmp_get_store_url($vendor_id) : '#';
		$store_logo = function_exists('wcfm_get_vendor_store_logo_by_vendor') ? wcfm_get_vendor_store_logo_by_vendor( $vendor_id ) : '';
		$store_logo = $store_logo ? sprintf("<img alt='%s' src='%s'/>", esc_attr( $store_name ), $store_logo): '';
		$classes = 'vendor-type-' . $vendor_type;
		$classes .= ' vendor-position-' . \Glozin\Helper::get_option( 'product_card_vendor_position' );
		$classes .= $product->get_attributes() ? ' show-attributes' : '';
		$vendor_avatar = $vendor_type == 'avatar' ? $store_logo : '<span class="vendor-name-text">' . esc_html__( 'By', 'glozin' ) . '</span>';
		?>

		<div class="sold-by-meta <?php echo esc_attr( $classes ) ?>">
			<a href="<?php echo esc_url($store_link); ?>">
				<?php echo ! empty( $vendor_avatar ) ? $vendor_avatar : ''; ?>
				<span class="vendor-name"><?php echo esc_html( $store_name ); ?></span>
			</a>
		</div>

		<?php
	}

	/**
	 * Product sold by template
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_sold_by_template() {
		global $WCFM, $WCFMmp;

		if( ! is_singular('product') ) {
			return;
		}

		$vendor_sold_by_template = !empty($WCFMmp->wcfmmp_vendor) ? $WCFMmp->wcfmmp_vendor->get_vendor_sold_by_template() : '';

		if( $vendor_sold_by_template == 'tab' ) {
			return;
		}
		$vendor_sold_by_position = isset( $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by_position'] ) ? $WCFMmp->wcfmmp_marketplace_options['vendor_sold_by_position'] : 'below_atc';

		if( ! empty( $WCFM->wcfm_enquiry ) ) {
			remove_action( 'woocommerce_single_product_summary',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 15 );
			remove_action( 'woocommerce_single_product_summary',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 25 );
			remove_action( 'woocommerce_single_product_summary',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 35 );
		}

		if( $vendor_sold_by_position == 'bellow_title' ) {
			add_action( 'woocommerce_single_product_summary',	array( $this, 'sold_by_single_product_open' ), 5 );
			if( ! empty($WCFMmp->frontend) ) {
				remove_action( 'woocommerce_single_product_summary',	array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 6);
				add_action( 'woocommerce_single_product_summary',	array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 5);
			}
			if( ! empty( $WCFM->wcfm_enquiry ) ) {
				add_action( 'woocommerce_single_product_summary',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 5 );
			}
			add_action( 'woocommerce_single_product_summary',	array( $this, 'sold_by_single_product_close' ), 5 );
		} elseif( $vendor_sold_by_position == 'bellow_price' ) {
			add_action( 'woocommerce_single_product_summary',	array( $this, 'sold_by_single_product_open' ), 20 );
			if( ! empty($WCFMmp->frontend) ) {
				remove_action( 'woocommerce_single_product_summary',	array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 15);
				add_action( 'woocommerce_single_product_summary',	array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 20);
			}
			if( ! empty( $WCFM->wcfm_enquiry ) ) {
				add_action( 'woocommerce_single_product_summary',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 20 );
			}
			add_action( 'woocommerce_single_product_summary',	array( $this, 'sold_by_single_product_close' ),20 );
		} elseif( $vendor_sold_by_position == 'bellow_sc' ) {
			add_action( 'woocommerce_single_product_summary',	array( $this, 'sold_by_single_product_open' ), 24 );
			if( ! empty( $WCFM->wcfm_enquiry ) ) {
				add_action( 'woocommerce_single_product_summary',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 26 );
			}
			add_action( 'woocommerce_single_product_summary',	array( $this, 'sold_by_single_product_close' ), 26 );
		} else {
			add_action( 'woocommerce_product_meta_start',	array( $this, 'sold_by_single_product_open' ), 49 );
			if( ! empty( $WCFM->wcfm_enquiry ) ) {
				add_action( 'woocommerce_product_meta_start',	array( $WCFM->wcfm_enquiry, 'wcfm_enquiry_button' ), 51 );
			}
			add_action( 'woocommerce_product_meta_start',	array( $this, 'sold_by_single_product_close' ), 51 );
		}

	}

	/**
	 *  Product sold by open
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function sold_by_single_product_open() {
		echo '<div class="glozin-sold-by-template">';
	}

	/**
	 *  Product sold by open
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function sold_by_single_product_close() {
		echo '</div>';
	}

	/**
	 * Button sidebar panel
	 *
	 * @return void
	 */
	public function button_sidebar() {
		?>
			<div class="wcfm-sidebar-panel__button position-fixed d-flex align-items-center justify-content-center" data-toggle="off-canvas" data-target="wcfm-sidebar-panel">
				<?php echo \Glozin\Icon::get_svg( 'sidebar' ); ?>
			</div>
		<?php
	}

	/**
	 * Sidebar panel
	 *
	 * @return void
	 */
	public function sidebar_panel() {
		?>
			<div id="wcfm-sidebar-panel" class="wcfm-sidebar-panel offscreen-panel--side-right">
				<div class="sidebar__backdrop"></div>
				<div class="sidebar__container">
					<div class="sidebar__header">
						<?php echo esc_html__( 'Sidebar', 'glozin' ); ?>
					</div>
					<div class="sidebar__content"></div>
				</div>
			</div>
		<?php
	}

	/**
	 * Store list button sidebar panel
	 *
	 * @return void
	 */
	public function store_list_button_sidebar() {
		if ( ! function_exists( 'wcfmmp_is_stores_list_page' ) ) {
			return;
		}

		if ( ! wcfmmp_is_stores_list_page() ) {
			return;
		}

		$wcfm_marketplace_options = wcfm_get_option('wcfm_marketplace_options', array());
		if ( $wcfm_marketplace_options['store_list_sidebar'] == 'no' ) {
			return;
		}

		$this->button_sidebar();
	}

	/**
	 * Store list sidebar panel
	 *
	 * @return void
	 */
	public function store_list_sidebar_panel() {
		if ( ! function_exists( 'wcfmmp_is_stores_list_page' ) ) {
			return;
		}

		if ( ! wcfmmp_is_stores_list_page() ) {
			return;
		}

		$wcfm_marketplace_options = wcfm_get_option('wcfm_marketplace_options', array());
		if ( $wcfm_marketplace_options['store_list_sidebar'] == 'no' ) {
			return;
		}

		$this->sidebar_panel();
	}

	/**
	 * Store page button sidebar panel
	 *
	 * @return void
	 */
	public function store_page_button_sidebar() {
		if ( ! function_exists( 'wcfm_is_store_page' ) ) {
			return;
		}

		if ( ! wcfm_is_store_page() ) {
			return;
		}

		$wcfm_marketplace_options = wcfm_get_option('wcfm_marketplace_options', array());
		if ( $wcfm_marketplace_options['store_sidebar'] == 'no' ) {
			return;
		}

		$this->button_sidebar();
	}

	/**
	 * Store page sidebar panel
	 *
	 * @return void
	 */
	public function store_page_sidebar_panel() {
		if ( ! function_exists( 'wcfm_is_store_page' ) ) {
			return;
		}

		if ( ! wcfm_is_store_page() ) {
			return;
		}

		$wcfm_marketplace_options = wcfm_get_option('wcfm_marketplace_options', array());
		if ( $wcfm_marketplace_options['store_sidebar'] == 'no' ) {
			return;
		}

		$this->sidebar_panel();
	}
}