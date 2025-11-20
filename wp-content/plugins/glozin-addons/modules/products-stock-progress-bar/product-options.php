<?php

namespace Glozin\Addons\Modules\Products_Stock_Progress_Bar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Settings
 */
class Product_Options {
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
		// Product Simple
		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'product_data_panel' ), 80 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ) );

		// Product Variable
		add_action( 'woocommerce_variation_options_inventory', array( $this, 'variation_data_panel' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_settings_fields' ), 10, 2 );
	}

	/**
	 * Product Data Panel
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_data_panel() {
		?>
		<div id="glozin-product-stock-progress-bar" class="options_group">
			<div class="stock_fields show_if_simple show_if_variable">
				<?php woocommerce_wp_text_input(
						array(
							'id'                => 'glozin_total_stock',
							'value'             => wc_stock_amount( get_post_meta( get_the_ID(), 'glozin_total_stock', true ) ),
							'label'             => __( 'Initial number in stock', 'glozin-addons' ),
							'desc_tip'          => true,
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
							),
							'data_type'         => 'stock',
						)
					); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Product Data
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}
		
		$total_stock = isset( $_POST['glozin_total_stock'] ) ? wc_stock_amount( $_POST['glozin_total_stock'] ) : 0;
		update_post_meta( $post_id, 'glozin_total_stock', $total_stock );
	}

	/**
	 * Variation Data Panel
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function variation_data_panel( $loop, $variation_data, $variation ) {
		?>
		<div id="glozin-product-stock-progress-bar" class="options_group">
			<?php woocommerce_wp_text_input(
						array(
							'id'                => "glozin_variable_total_stock{$loop}",
							'name'              => "glozin_variable_total_stock[{$loop}]",
							'value'             => wc_stock_amount( get_post_meta( $variation->ID, 'glozin_variable_total_stock', true ) ),
							'label'             => __( 'Initial number in stock', 'glozin-addons' ),
							'desc_tip'          => true,
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
							),
							'data_type'         => 'stock',
						)
					); ?>
		</div>
		<?php
	}

	/**
	 * Save Variation Settings Fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_variation_settings_fields( $variation_id, $loop ) {
		$total_stock = isset( $_POST['glozin_variable_total_stock'][ $loop ] ) ? wc_stock_amount( $_POST['glozin_variable_total_stock'][ $loop ] ) : 0;
		update_post_meta( $variation_id, 'glozin_variable_total_stock', wc_clean( $total_stock ) );
	}
}