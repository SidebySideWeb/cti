<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Single_Product;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Product_Layout {
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
		\Glozin\WooCommerce\Single_Product\Product_Base::instance();
		\Glozin\WooCommerce\Single_Product\Related::instance();
		\Glozin\WooCommerce\Single_Product\UpSells::instance();
		\Glozin\WooCommerce\Single_Product\Recently_Viewed::instance();

		if ( intval( Helper::get_option( 'product_ask_question' ) ) && ! empty( Helper::get_option( 'product_ask_question_form' ) ) ) {
			\Glozin\WooCommerce\Single_Product\Ask_Question::instance();
		}

		if ( intval( Helper::get_option( 'product_share' ) ) ) {
			\Glozin\WooCommerce\Single_Product\Share::instance();
		}
	}
}
