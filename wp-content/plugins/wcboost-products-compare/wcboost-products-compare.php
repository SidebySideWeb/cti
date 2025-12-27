<?php
/**
 * Plugin Name: WCBoost - Products Compare
 * Description: This extension introduces detailed comparison tables that highlight the most significant product details, giving customers the ability to quickly compare products side by side. As you quickly review features, specifications, and more, you can make well-informed decisions.
 * Plugin URI: https://wcboost.com/plugin/woocommerce-products-compare/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Author: WCBoost
 * Version: 1.1.0
 * Author URI: https://wcboost.com/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 * Text Domain: wcboost-products-compare
 * Domain Path: /languages/
 * License: GPLv3 or later
 * Requires PHP: 7.0
 * Requires at least: 4.5
 *
 * @package ProductsCompare
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WCBOOST_PRODUCTS_COMPARE_VERSION', '1.1.0' );
define( 'WCBOOST_PRODUCTS_COMPARE_FILE', __FILE__ );
define( 'WCBOOST_PRODUCTS_COMPARE_FREE', plugin_basename( __FILE__ ) );

require_once plugin_dir_path( __FILE__ ) . 'includes/plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/install.php';

// Declare compatibility with WooCommerce features.
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Load and init plugin's instance
 */
function wcboost_products_compare() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	return \WCBoost\ProductsCompare\Plugin::instance();
}

add_action( 'woocommerce_loaded', 'wcboost_products_compare' );

/**
 * Install plugin on activation
 */
function wcboost_products_compare_activate() {
	// Install the plugin if WooCommerce is installed.
	if ( class_exists( 'WooCommerce' ) ) {
		\WCBoost\ProductsCompare\Install::install();
	}
}

register_activation_hook( __FILE__, 'wcboost_products_compare_activate' );
