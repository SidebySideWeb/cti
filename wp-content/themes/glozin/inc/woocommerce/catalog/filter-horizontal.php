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

class Filter_Horizontal {
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
		add_action( 'woocommerce_before_shop_loop', array( $this, 'product_filters' ), 35 );
	}


	/**
	 * Catalog toolbar horizontal.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_filters() {
		echo '<div class="catalog-filters-horizontal d-flex align-items-center justify-content-end gap-10 flex-1 h-100 position-relative">';
		get_sidebar('catalog-filters-sidebar');
		echo '</div>';
	}

}