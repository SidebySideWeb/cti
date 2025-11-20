<?php
/**
 * Catalog hooks.
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
 * Class of Catalog
 */

class Manager {
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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action('wp', array( $this, 'add_actions' ));
		\Glozin\WooCommerce\Catalog\Product_Grid_Banner::instance();
	}

	public function add_actions() {
		if ( apply_filters( 'glozin_load_catalog_layout', \Glozin\Helper::is_catalog() ) ) {
			\Glozin\WooCommerce\Catalog\Layout::instance();
			if ( Helper::get_option( 'top_categories' ) ) {
				\Glozin\WooCommerce\Catalog\Top_Categories::instance();
			}

			if ( Helper::get_option( 'catalog_toolbar' ) ) {
				\Glozin\WooCommerce\Catalog\Toolbar::instance();
			}

			if ( Helper::get_option( 'product_filter_type' ) == 'horizontal' ) {
				\Glozin\WooCommerce\Catalog\Filter_Horizontal::instance();
			}
			\Glozin\WooCommerce\Catalog\Products_Grid::instance();
			\Glozin\WooCommerce\Catalog\Pagination::instance();
			\Glozin\WooCommerce\Catalog\Page_Header::instance();
			\Glozin\WooCommerce\Catalog\Sidebar::instance();
		}

		if ( \Glozin\Helper::is_catalog() ) {
			\Glozin\WooCommerce\Catalog\View::instance();
			\Glozin\WooCommerce\Catalog\Products_List::instance();
		}
	}

}