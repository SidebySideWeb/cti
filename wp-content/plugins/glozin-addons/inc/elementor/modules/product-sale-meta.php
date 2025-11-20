<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

 namespace Glozin\Addons\Elementor\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product Sale Meta Addons Modules
 */
class Product_Sale_Meta {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
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
		add_action('admin_init', array( $this, 'settings'));
	}

	public function settings() {
		// Simple
		add_action( 'save_post_product', array( $this, 'save_product_data' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ) );

		// Variation
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_settings_fields' ), 10, 2 );

		// Update
		add_action( 'woocommerce_recorded_sales', array( $this, 'update_sales' ) );
		add_action( 'woocommerce_scheduled_sales', array( $this, 'schedule_sales' ) );
	}

    /**
	 * Save product data
	 *
	 * @param int $post_id
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		$product = wc_get_product( $post_id );
		if( empty( $product ) ) {
			return;
		}

		if( $product->is_type( 'variable' ) ) {
			$variation_ids = $product->get_children();

			self::update_product_sale_percent_meta( $post_id, $variation_ids );
		} else {
			if( $product->get_sale_price() ) {
				$regular_price = $product->get_regular_price();
				$sales_price   = $product->get_sale_price();

				$percent = round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) );
				update_post_meta( $post_id, 'glozin_product_sale_discount_percent', $percent );
			} else {
				if( metadata_exists( 'post', $post_id, 'glozin_product_sale_discount_percent' ) ) {
					delete_post_meta( $post_id, 'glozin_product_sale_discount_percent' );
				}
			}
		}
	}

	/**
	 * Save Variation
	 *
	 * @return void
	 */
	public function save_variation_settings_fields( $variation_id, $loop ) {
		$variation = new \WC_Product_Variation( $variation_id );

		if( empty( $variation ) ) {
			return;
		}

		$product_id = $variation->get_parent_id();
		$product = wc_get_product( $variation->get_parent_id() );
		$variation_ids = $product->get_children();

		self::update_product_sale_percent_meta( $product_id, $variation_ids );
	}

	/**
	 * Update deal sales count
	 *
	 * @param int $order_id
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_sales( $order_id ) {
		$order_post = get_post( $order_id );

		// Only apply for the main order
		if ( $order_post->post_parent != 0 ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( sizeof( $order->get_items() ) > 0 ) {
			foreach ( $order->get_items() as $item ) {
				if ( $product_id = $item->get_product_id() ) {
					$product = wc_get_product( $product_id );

					if( $product->is_type( 'variation' ) ) {
						$_product = wc_get_product( $product->get_parent_id() );
						$variation_ids = $_product->get_children();

						self::update_product_sale_percent_meta( $product->get_parent_id(), $variation_ids );
					} else if ( $product->is_type( 'variable' ) ) {
						$_product = wc_get_product( $product_id );
						$variation_ids = $_product->get_children();

						self::update_product_sale_percent_meta( $product_id, $variation_ids );
					} else {
						if( $product->get_sale_price() ) {
							$regular_price = $product->get_regular_price();
							$sales_price   = $product->get_sale_price();

							$percent = round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) );
							update_post_meta( $product_id, 'glozin_product_sale_discount_percent', $percent );
						} else {
							if( metadata_exists( 'post', $product_id, 'glozin_product_sale_discount_percent' ) ) {
								delete_post_meta( $product_id, 'glozin_product_sale_discount_percent' );
							}
						}
					}
				}
			}
		}
	}

    /**
	 * Remove deal data when sale is scheduled end
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function schedule_sales() {
		$data_store  = \WC_Data_Store::load( 'product' );
		$product_ids = $data_store->get_ending_sales();

		if ( $product_ids ) {
			foreach ( $product_ids as $product_id ) {
				if ( ! empty( $product = wc_get_product( $product_id ) ) ) {
					if( $product->is_type( 'variation' ) ) {
						$_product = wc_get_product( $product->get_parent_id() );
						$variation_ids = $_product->get_children();

						self::update_product_sale_percent_meta( $product->get_parent_id(), $variation_ids );
					} else if ( $product->is_type( 'variable' ) ) {
						$_product = wc_get_product( $product_id );
						$variation_ids = $_product->get_children();

						self::update_product_sale_percent_meta( $product_id, $variation_ids );
					} else {
						if( metadata_exists( 'post', $product_id, 'glozin_product_sale_discount_percent' ) ) {
							delete_post_meta( $product_id, 'glozin_product_sale_discount_percent' );
						}
					}
				}
			}
		}
	}

	/**
	 * Update product sale percent meta
	 *
	 * @return void
	 */
	public function update_product_sale_percent_meta( $product_id, $variation_ids ) {
		$percent_min = 0;
		$percent_max = 0;
		
		if( metadata_exists( 'post', $product_id, 'glozin_product_sale_discount_percent_min' ) ) {
			delete_post_meta( $product_id, 'glozin_product_sale_discount_percent_min' );
		}

		if( metadata_exists( 'post', $product_id, 'glozin_product_sale_discount_percent_max' ) ) {
			delete_post_meta( $product_id, 'glozin_product_sale_discount_percent_max' );
		}

		foreach ( $variation_ids as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if( $variation->get_sale_price() ) {
				$regular_price = $variation->get_regular_price();
				$sales_price   = $variation->get_sale_price();
	
				$percent = round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) );

				if( ( $percent_min > 0 && $percent < $percent_min ) || ( $percent_min == 0 && $percent > 0 ) ) {
					$percent_min = $percent;
				}

				if( ( $percent_max > 0 && $percent > $percent_max ) || ( $percent_max == 0 && $percent > 0 ) ) {
					$percent_max = $percent;
				}
				
				update_post_meta( $product_id, 'glozin_product_sale_discount_percent_min', $percent_min );
				update_post_meta( $product_id, 'glozin_product_sale_discount_percent_max', $percent_max );
				$check_sale[] = $product_id;
			}
		}
	}
}
