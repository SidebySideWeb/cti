<?php
/**
 * Plugin Name: SoftOne ↔ WooCommerce Sync
 * Description: Bi-directional integration with SoftOne ERP (products, customers, orders).
 * Version: 0.1.0
 * Author: Side by Side Web
 * Requires PHP: 7.4
 * Tested up to: 8.3
 * Requires at least: 6.0
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'S1WC_VERSION', '0.1.0' );
define( 'S1WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'S1WC_URL',  plugin_dir_url( __FILE__ ) );

// Autoload includes
require_once S1WC_PATH . 'includes/class-softone-logger.php';
require_once S1WC_PATH . 'includes/class-softone-settings.php';
require_once S1WC_PATH . 'includes/class-softone-api.php';
require_once S1WC_PATH . 'includes/class-query-monitor.php';
require_once S1WC_PATH . 'includes/class-product-sync.php';
require_once S1WC_PATH . 'includes/class-customer-sync.php';
require_once S1WC_PATH . 'includes/class-order-sync.php';

/**
 * Activation: schedule crons.
 */
function s1wc_activate() {
	// Schedule using settings helper (adds sensible defaults)
	if ( method_exists( '\S1WC\Settings', 'schedule_crons' ) ) {
		\S1WC\Settings::schedule_crons();
	} else {
		// Fallback defaults if Settings class not available
		if ( ! wp_next_scheduled( 's1wc_sync_products' ) ) {
			wp_schedule_event( time() + 60, 'every_4_hours', 's1wc_sync_products' );
		}
		if ( ! wp_next_scheduled( 's1wc_sync_customers' ) ) {
			wp_schedule_event( time() + 120, 'every_8_hours', 's1wc_sync_customers' );
		}
		if ( ! wp_next_scheduled( 's1wc_sync_orders' ) ) {
			wp_schedule_event( time() + 180, 'every_3_hours', 's1wc_sync_orders' );
		}
	}
}
register_activation_hook( __FILE__, 's1wc_activate' );

/**
 * Deactivation: clear crons.
 */
function s1wc_deactivate() {
	wp_clear_scheduled_hook( 's1wc_sync_products' );
	wp_clear_scheduled_hook( 's1wc_sync_customers' );
	wp_clear_scheduled_hook( 's1wc_sync_orders' );
}
register_deactivation_hook( __FILE__, 's1wc_deactivate' );

/**
 * Init
 */
add_action( 'plugins_loaded', function () {
	// Ensure WooCommerce is active
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>SoftOne ↔ WooCommerce Sync requires WooCommerce to be installed and active.</p></div>';
		});
		return;
	}

	\S1WC\Settings::init();
	\S1WC\Query_Monitor::init();
	\S1WC\Product_Sync::init();
	\S1WC\Customer_Sync::init();
	\S1WC\Order_Sync::init();
});

// Register a Brands taxonomy for products
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

// Manual sync actions (Tools → SoftOne Sync)
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
				$force_full = isset( $_POST['s1wc_force_full'] ) && $_POST['s1wc_force_full'] === '1';
				if ( $type === 'products' ) {
					\S1WC\Product_Sync::sync_products( $force_full );
					echo '<div class="updated notice"><p>Manual product sync finished. Check Logs.</p></div>';
				} elseif ( $type === 'customers' ) {
					\S1WC\Customer_Sync::sync_customers();
					echo '<div class="updated notice"><p>Manual customer sync finished. Check Logs.</p></div>';
				} elseif ( $type === 'orders' ) {
					\S1WC\Order_Sync::sync_orders();
					echo '<div class="updated notice"><p>Manual orders sync finished. Check Logs.</p></div>';
				}
			}
			$last_sync = get_option( 's1wc_last_product_sync_time', 0 );
			$last_sync_str = $last_sync > 0 ? date( 'Y-m-d H:i:s', $last_sync ) : 'Never';
			echo '<p><strong>Last Products Sync:</strong> ' . esc_html( $last_sync_str ) . '</p>';
			echo '<form method="post">';
			wp_nonce_field('s1wc_manual_sync');
			echo '<p>';
			echo '<button name="s1wc_manual" class="button button-primary" value="products">Run Products Sync (Incremental)</button> ';
			echo '<label><input type="checkbox" name="s1wc_force_full" value="1" /> Force full sync (ignore last sync time)</label>';
			echo '</p>';
			echo '<p><button name="s1wc_manual" class="button" value="customers">Run Customers Sync</button></p>';
			echo '<p><button name="s1wc_manual" class="button" value="orders">Run Orders Sync</button></p>';
			echo '</form>';
			echo '</div>';
		}
	);
});

// Cron bindings
add_action( 's1wc_sync_products', ['\\S1WC\\Product_Sync', 'sync_products'] );
add_action( 's1wc_sync_customers', ['\\S1WC\\Customer_Sync', 'sync_customers'] );
add_action( 's1wc_sync_orders', ['\\S1WC\\Order_Sync', 'sync_orders'] );
// Async order push
add_action( 's1wc_push_order', ['\\S1WC\\Order_Sync', 'push_order_to_erp'] );

// Show SoftOne customer meta (AFM etc.) on WP user profile / edit screens (read-only)
add_action( 'show_user_profile', function( $user ) {
	if ( ! current_user_can( 'edit_user', $user->ID ) ) return;
	$afm  = get_user_meta( $user->ID, 's1_customer_afm', true );
	$trdr = get_user_meta( $user->ID, 's1_customer_trdr', true );
	$trdbranch = get_user_meta( $user->ID, 's1_customer_trdbranch', true );

	echo '<h2>SoftOne Customer Info</h2>';
	echo '<table class="form-table">';
	echo '<tr><th>AFM</th><td>' . ( $afm ? esc_html( $afm ) : '&mdash;' ) . '<p class="description">Greek VAT/AFM (synced from SoftOne)</p></td></tr>';
	echo '<tr><th>ERP TRDR</th><td>' . ( $trdr ? esc_html( $trdr ) : '&mdash;' ) . '<p class="description">ERP TRDR identifier (read from SoftOne)</p></td></tr>';
	echo '<tr><th>ERP TRDBRANCH</th><td>' . ( $trdbranch ? esc_html( $trdbranch ) : '&mdash;' ) . '<p class="description">ERP TRDBRANCH identifier (read from SoftOne)</p></td></tr>';
	echo '</table>';
} );
add_action( 'edit_user_profile', function( $user ) {
	do_action( 'show_user_profile', $user );
} );

// Add AFM and Synced At columns to Users list
add_filter( 'manage_users_columns', function( $columns ) {
	$columns['s1_afm'] = 'AFM';
	$columns['s1_synced_at'] = 'SoftOne Synced';
	return $columns;
} );

add_action( 'manage_users_custom_column', function( $value, $column_name, $user_id ) {
	if ( $column_name === 's1_afm' ) {
		$afm = get_user_meta( $user_id, 's1_customer_afm', true );
		return $afm ? esc_html( $afm ) : '&mdash;';
	}
	if ( $column_name === 's1_synced_at' ) {
		$syn = get_user_meta( $user_id, 's1_customer_synced_at', true );
		if ( $syn ) {
			// show human readable
			$t = strtotime( $syn ) ?: ( is_numeric( $syn ) ? intval( $syn ) : 0 );
			return $t ? esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $t ) ) : esc_html( $syn );
		}
		return '&mdash;';
	}
	return $value;
}, 10, 3 );

// Add ERP Sync Status column to Orders list
add_filter( 'manage_edit-shop_order_columns', function( $columns ) {
	$columns['s1_erp_sync'] = 'ERP Sync';
	return $columns;
} );

add_action( 'manage_shop_order_posts_custom_column', function( $column, $post_id ) {
	if ( $column === 's1_erp_sync' ) {
		$status = get_post_meta( $post_id, '_s1wc_erp_sync_status', true );
		$erp_id = get_post_meta( $post_id, '_s1wc_erp_sync_erp_id', true );
		$error = get_post_meta( $post_id, '_s1wc_erp_sync_error', true );
		
		if ( $status === 'success' ) {
			echo '<span style="color: green;">✓ Synced</span>';
			if ( $erp_id ) {
				echo '<br><small>ERP ID: ' . esc_html( $erp_id ) . '</small>';
			}
		} elseif ( $status === 'failed' ) {
			echo '<span style="color: red;">✗ Failed</span>';
			if ( $error ) {
				echo '<br><small title="' . esc_attr( $error ) . '">' . esc_html( substr( $error, 0, 20 ) ) . '...</small>';
			}
		} else {
			echo '<span style="color: orange;">⏳ Pending</span>';
		}
	}
}, 10, 2 );

// Add manual sync action to individual orders
add_action( 'woocommerce_order_actions', function( $actions ) {
	$actions['s1wc_sync_to_erp'] = 'Sync to SoftOne ERP';
	return $actions;
} );

add_action( 'woocommerce_order_action_s1wc_sync_to_erp', function( $order ) {
	if ( ! $order ) return;
	
	$result = \S1WC\Order_Sync::push_order_to_erp( $order->get_id() );
	
	if ( $result ) {
		$order->add_order_note( 'Order manually synced to SoftOne ERP' );
	} else {
		$order->add_order_note( 'Failed to sync order to SoftOne ERP - check logs for details' );
	}
} );
