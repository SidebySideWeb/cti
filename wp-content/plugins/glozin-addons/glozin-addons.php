<?php
/**
 * Plugin Name: Glozin Addons
 * Plugin URI: http://uixthemes.com/plugins/glozin-addons.zip
 * Description: Extra elements for Elementor. It was built for Glozin theme.
 * Version: 1.6.0
 * Author: UIXThemes
 * Author URI: http://uixthemes.com
 * License: GPL2+
 * Text Domain: glozin-addons
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! defined( 'GLOZIN_ADDONS_VER' ) ) {
	define( 'GLOZIN_ADDONS_VER', '1.6.0' );
}

if ( ! defined( 'GLOZIN_ADDONS_DIR' ) ) {
	define( 'GLOZIN_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'GLOZIN_ADDONS_URL' ) ) {
	define( 'GLOZIN_ADDONS_URL', plugin_dir_url( __FILE__ ) );
}

require_once GLOZIN_ADDONS_DIR . 'vendors/kirki/kirki.php';

require_once GLOZIN_ADDONS_DIR . 'plugin.php';

\Glozin\Addons::instance();