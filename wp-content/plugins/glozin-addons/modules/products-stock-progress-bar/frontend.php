<?php
/**
 * Single Product hooks.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Products_Stock_Progress_Bar;

use Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Frontend {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'product_simple_stock_progress_bar' ), 10 );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'product_variable_stock_progress_bar' ), 10 );

		// Add data product variations
		add_filter( 'woocommerce_available_variation', array( $this, 'data_product_variations' ), 10, 3 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'products-stock-progress-bar-frontend', GLOZIN_ADDONS_URL . 'modules/products-stock-progress-bar/assets/products-stock-progress-bar' . $debug . '.js', array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
		wp_enqueue_style( 'products-stock-progress-bar', GLOZIN_ADDONS_URL . 'modules/products-stock-progress-bar/assets/products-stock-progress-bar' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
	}

	/**
	 * Product Simple Stock Progress Bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_simple_stock_progress_bar() {
		global $product;

		if( ! apply_filters('glozin_products_stock_progress_bar', true ) ) {
			return;
		}
		
		if( ! $product->is_type( 'simple' ) ) {
			return;
		}

		$total_stock = get_post_meta( get_the_ID(), 'glozin_total_stock', true );
		$current_stock = $product->get_stock_quantity();

		if( $current_stock <= 0 ) {
			return;
		}

		if( $total_stock <= 0 && $total_stock < $current_stock ) {
			return;
		}

		$progress_bar_percentage = ( $current_stock / $total_stock ) * 100;
		echo $this->progress_bar_html( $current_stock, $progress_bar_percentage );
	}

	/**
	 * Product Variable Stock Progress Bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_variable_stock_progress_bar() {
		global $product;

		if( ! apply_filters('glozin_products_stock_progress_bar', true ) ) {
			return;
		}

		if( ! $product->is_type( 'variable' ) ) {
			return;
		}

		$default_variation = $product->get_default_attributes();
		$variations = $product->get_available_variations();
		$has_default_variation = false;
		$variation_id = 0;

		if ( empty( $default_variation ) ) {
			echo '<div class="glozin-stock-progress-bar__no-html"></div>';
			return;
		}

		if ( empty( $variations ) ) {
			return;
		}

		foreach ( $variations as $variation ) {
			if ( count( array_diff($default_variation, $variation['attributes'] ) ) === 0 && count( array_diff( $variation['attributes'], $default_variation ) ) === 0 ) {
				$has_default_variation = true;
				$variation_id  = $variation['variation_id'];
			}
		}

		if( ! $has_default_variation  ) {
			return;
		}

		if( $variation_id === 0 ) {
			return;
		}

		$variation = wc_get_product( $variation_id );
		$current_stock = $variation->get_stock_quantity();
		$total_stock = get_post_meta( $variation_id, 'glozin_variable_total_stock', true );

		if( $current_stock <= 0 ) {
			return;
		}

		if( $total_stock <= 0 && $total_stock < $current_stock ) {
			return;
		}

		$progress_bar_percentage = ( $current_stock / $total_stock ) * 100;
		echo $this->progress_bar_html( $current_stock, $progress_bar_percentage );
	}

	/**
	 * Progress Bar HTML
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function progress_bar_html( $current_stock, $progress_bar_percentage ) {
		return sprintf(
			'<div class="glozin-stock-progress-bar mb-25">
				<div class="glozin-stock-progress-bar-label mb-10 text-dark">
					%s
				</div>
				<div class="glozin-progress-bar position-relative bg-light-grey" style="--progress-bar-percentage: %s%%;">
					<div class="glozin-progress-bar-inner position-absolute top-0 start-0 h-100 bg-primary"></div>
				</div>
			</div>',
			sprintf(
				esc_html__( 'Hurry up! Only %s left in stock', 'glozin-addons' ),
				sprintf(
					'<span class="quantity-stock text-primary fw-medium">%s %s</span>',
					$current_stock,
					$current_stock > 1 ? esc_html__( 'items', 'glozin-addons' ) : esc_html__( 'item', 'glozin-addons' )
				)
			),
			$progress_bar_percentage
		);
	}

	/**
	 * Data variation
	 *
	 * @return array
	 */
	public function data_product_variations( $data, $product, $variation ) {
		$current_stock = $variation->get_stock_quantity();
		$total_stock = get_post_meta( $variation->get_id(), 'glozin_variable_total_stock', true );

		if( $current_stock <= 0 ) {
			return $data;
		}

		if( $total_stock <= 0 && $total_stock < $current_stock ) {
			return $data;
		}

		$progress_bar_percentage = ( $current_stock / $total_stock ) * 100;
		$data['stock_progress_bar_html'] = $this->progress_bar_html( $current_stock, $progress_bar_percentage );

		return $data;
	}
}
