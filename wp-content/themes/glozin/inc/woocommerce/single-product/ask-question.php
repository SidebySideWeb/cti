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
class Ask_Question {
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
		add_action( 'glozin_product_extra_link', array( $this, 'ask_question' ), 27 );
	}

	/**
	 * Product Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function ask_question() {
		if( ! apply_filters( 'glozin_ask_question_content', true ) ) {
			return;
		}

		\Glozin\Theme::set_prop( 'modals', 'product-ask-question' );

		echo '<a href="#" class="glozin-extra-link-item glozin-extra-link-item--ask-question d-inline-flex align-items-center gap-10 lh-normal text-base text-hover-color" data-toggle="modal" data-target="product-ask-question-modal">'. Icon::get_svg( 'question' ) . esc_html__( 'Ask a question', 'glozin' ) . '</a>';
	}

	/**
	 * Product Share data
	 */
	public static function ask_question_data() {
		return Helper::get_option( 'product_ask_question_form' );
	}
}
