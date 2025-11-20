<?php
/**
 * Glozin Autoload init
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
 * Glozin AutoLoad init
 */
class AutoLoad {
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
	public static function init() {
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
		spl_autoload_register( [ $this, 'load' ] );
	}

	/**
	 * Auto load widgets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load( $class ) {
		if ( false === strpos( $class, 'Glozin' ) ) {
			return;
		}

		$relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );
		$relative_class_name = strtolower( $relative_class_name );
		$relative_class_name = str_replace( '_', '-', $relative_class_name );
		$relative_class_name = str_replace( '\\', '/', $relative_class_name );
		$file_name           = $relative_class_name;
		$file_dir            = get_template_directory() . '/inc/';

		$file_name = $file_dir  . $file_name . '.php';

		if ( is_readable( $file_name ) ) {
			include( $file_name );
		}
	}
}

AutoLoad::init();