<?php

namespace Glozin\Vendors;

/**
 * Vendor Dokan functions and definitions.
 *
 * @package Glozin
 */

class Dokan {
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
     * The constructor
     */
    function __construct() {
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		$this->product_loop_layout();
		add_action( 'glozin_woocommerce_product_quickview_summary', array( $this, 'vendor_name_single' ), 20 );
		add_action( 'glozin_woocommerce_product_quickadd_summary_header', array( $this, 'vendor_name_single' ), 30 );

		// Catalog sidebar panel
		add_action( 'glozin_after_close_site_footer', array( $this, 'button_sidebar' ), 100 );
		add_action( 'glozin_after_close_site_footer', array( $this, 'sidebar_panel' ), 100 );

		add_filter('dokan_register_nonce_check', '__return_false');
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
		// Add class is dokan pro actived
		if ( class_exists( 'Dokan_Pro' ) ) {
			$classes[] = 'glozin-dokan-pro';
		}

		if ( function_exists( 'dokan_is_store_listing' ) && dokan_is_store_listing() ) {
			$classes[] = 'glozin-dokan-store-list-page';
		}

		if ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
			$classes[] = 'glozin-dokan-store-page';
		}

		return $classes;
	}

	/**
     * Enqueue registration scripts
     *
     * @return void
     */
    public function enqueue_scripts() {
		wp_enqueue_style( 'glozin-dokan', get_template_directory_uri() . '/assets/css/vendors/dokan.css', array(), '20241003' );

		wp_enqueue_script( 'glozin-dokan', get_template_directory_uri() . '/assets/js/vendors/dokan.js', array(), '20240508', array('strategy' => 'defer') );

		if(function_exists('dokan') ) {
			dokan()->scripts->load_form_validate_script();
			wp_enqueue_script( 'dokan-vendor-registration' );
		}

    }

	/**
	 * Product Card layout
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_layout() {
		if ( \Glozin\Helper::get_option( 'product_card_vendor_position' ) == 'after-thumbnail' ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'vendor_name_loop' ), 5 );
		} else {
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'vendor_name_loop' ), 10 );
		}
	}

	/**
	 * Vendor name loop.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function vendor_name_loop() {
		$vendor_type = \Glozin\Helper::get_option( 'product_card_vendor_name' );

		if( $vendor_type == 'none' ) {
			return;
		}

		$vendor_position = \Glozin\Helper::get_option( 'product_card_vendor_position');

		$this->vendor_name( $vendor_type, $vendor_position );
	}

	/**
	 * Vendor name loop.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function vendor_name_single() {
		$vendor_type = \Glozin\Helper::get_option( 'single_product_vendor_name' );

		if( $vendor_type == 'none' ) {
			return;
		}

		$this->vendor_name( $vendor_type );
	}

	/**
	 * Vendor name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function vendor_name( $vendor_type, $vendor_position = '' ) {
		global $product;

		if ( ! function_exists( 'dokan_get_store_url' ) ) {
			return;
		}

		global $product;
		$author_id = get_post_field( 'post_author', $product->get_id() );
		$author    = get_user_by( 'id', $author_id );
		if ( empty( $author ) ) {
			return;
		}

		$shop_info = get_user_meta( $author_id, 'dokan_profile_settings', true );
		$shop_name = $author->display_name;
		if ( $shop_info && isset( $shop_info['store_name'] ) && $shop_info['store_name'] ) {
			$shop_name = $shop_info['store_name'];
		}

		$vendor_class = 'vendor-type-' .$vendor_type;
		$vendor_class .= ! empty( $vendor_position ) ? ' vendor-position-' . $vendor_position : '';
		$vendor_avatar = $vendor_type == 'avatar' ? get_avatar( $author_id ) : '<span class="vendor-name-text me-3">' . esc_html__( 'By', 'glozin' ) . '</span>';
		?>
		<div class="sold-by-meta fs-14 fw-medium mt-7 <?php echo esc_attr( $vendor_class ); ?>">
			<a class="d-flex align-items-center" href="<?php echo esc_url( dokan_get_store_url( $author_id ) ); ?>">
				<?php echo ! empty( $vendor_avatar ) ? $vendor_avatar : ''; ?>
				<span class="vendor-name"><?php echo esc_html( $shop_name ); ?></span>
			</a>
		</div>

		<?php
	}

	/**
	 * Button sidebar panel
	 *
	 * @return void
	 */
	public function button_sidebar() {
		if ( ! function_exists( 'dokan_is_store_page' ) ) {
			return;
		}

		if ( ! dokan_is_store_page() ) {
			return;
		}

		if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_appearance', 'off' ) !== 'off' ) {
			return;
		}

		?>
			<div class="dokan-sidebar-panel__button position-fixed d-flex align-items-center justify-content-center" data-toggle="off-canvas" data-target="dokan-sidebar-panel">
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
		if ( ! function_exists( 'dokan_is_store_page' ) ) {
			return;
		}

		if ( ! dokan_is_store_page() ) {
			return;
		}

		if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_appearance', 'off' ) !== 'off' ) {
			return;
		}

		?>
			<div id="dokan-sidebar-panel" class="dokan-sidebar-panel offscreen-panel--side-right">
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
}