<?php
/**
 * Packages manager
 *
 * @deprecated 2.0.0 Use direct class autoloading instead.
 * @package WCBoost\Packages
 */

namespace WCBoost\Packages;

/**
 * Class Manager
 *
 * @deprecated 2.0.0 Use direct class autoloading instead.
 * Example:
 * Instead of: new Manager()->load_package('utilities\singleton-trait');
 * Use: use WCBoost\Packages\Utilities\SingletonTrait;
 */
class Manager {
	/**
	 * Loaded packages
	 *
	 * @var array
	 */
	protected static $packages = [];

	/**
	 * Base path for packages
	 *
	 * @var string
	 */
	protected $base;

	/**
	 * Package name mapping for backward compatibility
	 *
	 * @var array
	 */
	protected $package_map = [
		'utilities\singleton-trait' => ['Utilities\\SingletonTrait'],
		'templates-status' => ['TemplatesStatus\\Status', 'TemplatesStatus\\Notice'],
		'admin-page' => ['AdminPage\\WCBoostAdmin', 'AdminPage\\Section'],
		'subscription-client' => ['SubscriptionClient\\Updater', 'SubscriptionClient\\Activation'],
	];

	/**
	 * Packages manager constructor
	 *
	 * @deprecated 2.0.0 Use direct class autoloading instead.
	 * @param string $dir The base directory path
	 */
	public function __construct( $dir = __DIR__ ) {
		$this->base = untrailingslashit( $dir );
		$this->deprecated_notice();
	}

	/**
	 * Loads and initializes the package
	 *
	 * @deprecated 2.0.0 Use direct class autoloading instead.
	 * @param string $name The package name
	 * @return void
	 */
	public function load_package( $name ) {
		if ( ! isset( $this->package_map[$name] ) ) {
			return;
		}

		// Package already loaded
		if ( array_key_exists( $name, static::$packages ) ) {
			return;
		}

		// Load all classes in the package
		foreach ( $this->package_map[$name] as $class ) {
			$full_class = 'WCBoost\\Packages\\' . $class;
			if ( ! class_exists( $full_class ) ) {
				$this->deprecated_notice();
			}
		}

		static::$packages[$name] = true;
	}

	/**
	 * Get package instance
	 *
	 * @deprecated 2.0.0 Use direct class instantiation instead.
	 * @param string $name
	 * @return mixed|null
	 */
	public function get( $name ) {
		$this->deprecated_notice();
		return static::package( $name );
	}

	/**
	 * Get the package instance
	 *
	 * @deprecated 2.0.0 Use direct class instantiation instead.
	 * @param string $name
	 * @return mixed|null
	 */
	public static function package( $name ) {
		if ( ! isset( static::$packages[$name] ) ) {
			return null;
		}

		// Map old package names to their main class
		$class_map = [
			'templates-status' => 'WCBoost\\Packages\\TemplatesStatus\\Status',
			'admin-page' => 'WCBoost\\Packages\\AdminPage\\WCBoostAdmin',
			'subscription-client' => 'WCBoost\\Packages\\SubscriptionClient\\Activation',
		];

		if ( isset( $class_map[$name] ) && class_exists( $class_map[$name] ) ) {
			return call_user_func( [$class_map[$name], 'instance'] );
		}

		return null;
	}

	/**
	 * Show deprecation notice
	 */
	protected function deprecated_notice() {
		// phpcs:disable
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 );
		$caller = isset( $trace[1]['function'] ) ? $trace[1]['function'] : '';

		trigger_error(
			sprintf(
				'%s is deprecated since version 2.0.0! Use direct class autoloading instead. Called from %s',
				$caller ? "Method '$caller'" : 'The Manager class',
				isset( $trace[1]['file'] ) ? $trace[1]['file'] . ':' . $trace[1]['line'] : 'unknown location'
			),
			E_USER_DEPRECATED
		);
		// phpcs:enable
	}
}
