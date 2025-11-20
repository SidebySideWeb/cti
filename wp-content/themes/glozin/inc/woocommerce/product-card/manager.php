<?php
/**
 * Product Card hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Product_Card;

use Glozin\Helper, Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Card
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
		\Glozin\WooCommerce\Product_Card\Base::instance();
		$this->render_product_card();
	}

	public function render_product_card() {
		switch ( \Glozin\WooCommerce\Product_Card\Base::get_layout() ) {
			case '1':
				\Glozin\WooCommerce\Product_Card\Product_V1::instance();
				break;
			case '2':
				\Glozin\WooCommerce\Product_Card\Product_V2::instance();
				break;
		}
	}

}