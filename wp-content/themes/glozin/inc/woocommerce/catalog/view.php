<?php
/**
 * Catalog view hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Catalog;

use \Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Catalog View
 */

class View {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * @var string catalog view
	 */
	public static $catalog_view;

	protected static $view_cookie_name = 'catalog_view';

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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Get default view
	 *
	 * @return void
	 */
	public static function get_default_view() {
		if( isset( self::$catalog_view ) ) {
			return self::$catalog_view;
		}
		$view = isset( $_GET['view'] ) && ! empty( $_GET['view'] ) ? $_GET['view'] : '';
		if ( ! empty($view) ) {
			self::$catalog_view = $view;
		} else {
			if( isset( $_COOKIE[self::$view_cookie_name] ) ) {
				self::$catalog_view =  $_COOKIE[self::$view_cookie_name];
			} else {
				self::$catalog_view = apply_filters( 'glozin_catalog_default_view', Helper::get_option( 'catalog_toolbar_default_view' ) );
			}
		}

		self::$catalog_view = apply_filters( 'glozin_catalog_view', self::$catalog_view );

		return self::$catalog_view;
	}
}