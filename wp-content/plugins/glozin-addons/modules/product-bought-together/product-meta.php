<?php

namespace Glozin\Addons\Modules\Product_Bought_Together;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Product_Meta  {

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
		add_action( 'woocommerce_product_options_related', array( $this, 'product_data_panel' ) );

		add_action( 'woocommerce_process_product_meta', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Outputs the size guide panel
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_data_panel() {
		global $post;
		$post_id = $post->ID;
		?>
		<div id="glozin-product-bought-together" class="options_group">
			<p class="form-field">
				<label style="width: auto;"><strong><?php esc_html_e( 'Frequently Bought Together', 'glozin-addons' ); ?></strong></label>
			</p>
			<p class="form-field">
				<label for="product_bought_together"><?php esc_html_e( 'Products', 'glozin-addons' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="product_bought_together" name="glozin_pbt_product_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post_id ); ?>">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post_id, 'glozin_pbt_product_ids', true ) );

					if ( $product_ids && is_array( $product_ids ) ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					}
					?>
				</select> <?php echo wc_help_tip( __( 'This lets you choose which products are part of frequently bought together products.', 'glozin-addons' ) ); // WPCS: XSS ok. ?>
			</p>
			<p class="form-field">
				<label for="product_bought_together_discount_all"><?php esc_html_e( 'Discount (%)', 'glozin-addons' ); ?></label>
				<input id="product_bought_together_discount_all" type="number" name="glozin_pbt_discount_all" value="<?php echo get_post_meta( $post_id, 'glozin_pbt_discount_all', true ); ?>">
			</p>
			<p class="form-field">
				<label for="product_bought_together_checked_all"><?php esc_html_e( 'Checked All', 'glozin-addons' ); ?></label>
				<input type="checkbox" class="checkbox" name="glozin_pbt_checked_all" id="product_bought_together_checked_all" value="yes" <?php echo ! empty( get_post_meta( $post_id, 'glozin_pbt_checked_all', true ) ) ? 'checked="checked"' : ''; ?>>
				<span class="description"><?php esc_html_e( 'Checked all by default.', 'glozin-addons' ); ?></span>
			</p>
			<p class="form-field">
				<label for="product_bought_together_quantity_discount_all"><?php esc_html_e( 'Number of items to get discount', 'glozin-addons' ); ?></label>
				<input id="product_bought_together_quantity_discount_all" type="number" name="glozin_pbt_quantity_discount_all" min="2" value="<?php echo get_post_meta( $post_id, 'glozin_pbt_quantity_discount_all', true ); ?>">
			</p>
		</div>
		<?php
	}


	/**
	 * Save meta box content.
     *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 * @param object $post
     *
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		//If not the flex post.
		if ( 'product' != $post->post_type ) {
			return;
		}

		// Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
		}

		// Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
		}

		if ( isset( $_POST['glozin_pbt_product_ids'] ) ) {
			$woo_data = $_POST['glozin_pbt_product_ids'];
			update_post_meta( $post_id, 'glozin_pbt_product_ids', $woo_data );
		} else {
			update_post_meta( $post_id, 'glozin_pbt_product_ids', 0 );
		}

		if ( isset( $_POST['glozin_pbt_discount_all'] ) ) {
			$woo_data = intval( $_POST['glozin_pbt_discount_all'] );
			update_post_meta( $post_id, 'glozin_pbt_discount_all', $woo_data );
		} else {
			update_post_meta( $post_id, 'glozin_pbt_discount_all', 0 );
		}

		if ( isset( $_POST['glozin_pbt_checked_all'] ) ) {
			$woo_data = $_POST['glozin_pbt_checked_all'];
			update_post_meta( $post_id, 'glozin_pbt_checked_all', $woo_data );
		} else {
			update_post_meta( $post_id, 'glozin_pbt_checked_all', '' );
		}

		if ( isset( $_POST['glozin_pbt_quantity_discount_all'] ) && intval( $_POST['glozin_pbt_quantity_discount_all'] ) !== 0 && intval( $_POST['glozin_pbt_quantity_discount_all'] ) !== 1 ) {
			$woo_data = intval( $_POST['glozin_pbt_quantity_discount_all'] );
			update_post_meta( $post_id, 'glozin_pbt_quantity_discount_all', $woo_data );
		} else {
			update_post_meta( $post_id, 'glozin_pbt_quantity_discount_all', 2 );
		}
	}

}