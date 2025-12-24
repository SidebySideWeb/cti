<?php
/**
 * Plugin Name: SoftOne ↔ WooCommerce Sync
 * Description: Bi-directional integration with SoftOne ERP (products, customers, orders).
 * Version: 0.1.0
 * Author: Side by Side Web
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'S1WC_VERSION', '0.1.0' );
define( 'S1WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'S1WC_URL',  plugin_dir_url( __FILE__ ) );

require_once S1WC_PATH . 'includes/class-softone-logger.php';
require_once S1WC_PATH . 'includes/class-softone-settings.php';
require_once S1WC_PATH . 'includes/class-softone-api.php';
require_once S1WC_PATH . 'includes/class-product-sync.php';
require_once S1WC_PATH . 'includes/class-customer-sync.php';
require_once S1WC_PATH . 'includes/class-order-sync.php';
require_once S1WC_PATH . 'includes/class-order-admin.php';
require_once S1WC_PATH . 'includes/class-customer-admin.php';

function s1wc_activate() {
	if ( method_exists( '\S1WC\Settings', 'schedule_crons' ) ) {
		\S1WC\Settings::schedule_crons();
	} else {
		if ( ! wp_next_scheduled( 's1wc_sync_products' ) ) {
			wp_schedule_event( time() + 120, 'hourly', 's1wc_sync_products' );
		}
		if ( ! wp_next_scheduled( 's1wc_sync_customers' ) ) {
			wp_schedule_event( time() + 180, 'twicedaily', 's1wc_sync_customers' );
		}
	}
}
register_activation_hook( __FILE__, 's1wc_activate' );

function s1wc_deactivate() {
	wp_clear_scheduled_hook( 's1wc_sync_products' );
	wp_clear_scheduled_hook( 's1wc_sync_customers' );
}
register_deactivation_hook( __FILE__, 's1wc_deactivate' );

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>SoftOne ↔ WooCommerce Sync requires WooCommerce to be installed and active.</p></div>';
		});
		return;
	}

	\S1WC\Settings::init();
	\S1WC\Product_Sync::init();
	\S1WC\Customer_Sync::init();
	\S1WC\Order_Sync::init();
	\S1WC\Order_Admin::init();
	\S1WC\Customer_Admin::init();
});

add_action( 'init', function() {
	$labels = [
		'name' => 'Brands',
		'singular_name' => 'Brand',
		'search_items' => 'Search Brands',
		'all_items' => 'All Brands',
		'edit_item' => 'Edit Brand',
		'update_item' => 'Update Brand',
		'add_new_item' => 'Add New Brand',
		'new_item_name' => 'New Brand Name',
		'menu_name' => 'Brands',
	];
	$args = [
		'labels' => $labels,
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => false,
		'show_tagcloud' => false,
	];
	register_taxonomy( 'softone_brand', [ 'product' ], $args );
} );

add_action( 'admin_menu', function () {
	add_submenu_page(
		'woocommerce',
		'SoftOne Sync Tools',
		'SoftOne Sync',
		'manage_woocommerce',
		's1wc-tools',
		function () {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}
			echo '<div class="wrap"><h1>SoftOne Sync Tools</h1>';
			if ( isset($_POST['s1wc_manual']) && check_admin_referer('s1wc_manual_sync') ) {
				$type = sanitize_text_field($_POST['s1wc_manual']);
				if ( $type === 'products' ) {
					\S1WC\Product_Sync::sync_products();
					echo '<div class="updated notice"><p>Manual product sync finished. Check Logs.</p></div>';
				} elseif ( $type === 'customers' ) {
					\S1WC\Customer_Sync::sync_customers();
					echo '<div class="updated notice"><p>Manual customer sync finished. Check Logs.</p></div>';
				}
			}
			echo '<form method="post">';
			wp_nonce_field('s1wc_manual_sync');
			echo '<p><button name="s1wc_manual" class="button button-primary" value="products">Run Products Sync</button></p>';
			echo '<p><button name="s1wc_manual" class="button" value="customers">Run Customers Sync</button></p>';
			echo '</form>';
			echo '</div>';
		}
	);
});

add_action( 's1wc_sync_products', ['\\S1WC\\Product_Sync', 'sync_products'] );
add_action( 's1wc_sync_customers', ['\\S1WC\\Customer_Sync', 'sync_customers'] );

add_action( 'wp_ajax_s1wc_manual_sync_single_order', function() {
	check_ajax_referer( 's1wc_manual_sync_single_order', 'nonce' );
	
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( 'Insufficient permissions' );
	}
	
	$order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;
	if ( ! $order_id ) {
		wp_send_json_error( 'Invalid order ID' );
	}
	
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		wp_send_json_error( 'Order not found' );
	}
	
	\S1WC\Order_Sync::push_order_to_erp( $order_id );
	
	$status = get_post_meta( $order_id, '_s1wc_erp_sync_status', true );
	if ( $status === 'success' ) {
		wp_send_json_success( 'Order synced successfully' );
	} else {
		$error = get_post_meta( $order_id, '_s1wc_erp_sync_error', true );
		wp_send_json_error( $error ?: 'Sync failed' );
	}
} );
