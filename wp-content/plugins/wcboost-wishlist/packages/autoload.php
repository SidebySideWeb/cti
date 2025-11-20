<?php
/**
 * Packages Autoloader
 *
 * @version 2.0.0
 * @package WCBoost\Packages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

spl_autoload_register( function ( $class ) {
	$namespace = 'WCBoost\\Packages\\';

	if ( strncmp( $namespace, $class, strlen( $namespace ) ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, strlen( $namespace ) );

	// Do not load the `Manager` class due to it is deprecated,
	// but this class may be loaded from legacy code.
	if ( $relative_class === 'Manager' ) {
		return;
	}

	$relative_path = str_replace( '\\', '/', $relative_class );
	$file          = __DIR__ . '/' . $relative_path . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );
