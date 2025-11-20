<?php
namespace Glozin\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader.
 *
 */
class Auto_Loader {
	/**
	 * Array of files to be loaded with key as the class name.
	 *
	 * @var array
	 */
	private static $files = [];

	/**
	 * Register files
	 *
	 * @since 1.0.0
	 *
	 * @return void/boolen
	 */
	public static function register( $pathes ) {
		foreach ( $pathes as $namespace => $filename ) {
			self::$files[ $namespace ] = $filename;
		}
		return true;
	}

	/**
	 * Get file path based on the class name.
	 *
	 * @param string $class The class name.
	 * @return string The full file path.
	 */
	public static function get_file_path( $class ) {
		// Qualify namespace.
		$class = ltrim( $class, '\\' );

		if ( strpos( $class, 'Glozin\\Addons\\' ) !== 0 ) {
			return '';
		}

		$path = str_replace(
			[ 'Glozin\Addons\\', '\\', '_' ],
			[ '', ' ', '-' ],
			$class
		);
		$path = strtolower( $path );
		$path = str_replace( ' ', DIRECTORY_SEPARATOR, $path );
		$path = GLOZIN_ADDONS_DIR . $path . '.php';

		return $path;
	}

	/**
	 * Load files
	 *
	 * @since 1.0.0
	 *
	 * @return boolen
	 */
	public static function load( $class ) {
		if ( isset( self::$files[ $class ] ) ) {
			require self::$files[ $class ];

			return true;
		} else {
			$file = self::get_file_path( $class );

			if ( $file && file_exists( $file ) ) {
				require $file;

				return true;
			}
		}

		return false;
	}

}
