<?php

namespace Glozin\Addons\Modules\Advanced_Linked_Products;

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
		<div id="glozin-advanced-linked-products" class="options_group">
			<p class="form-field">
				<label style="width: auto;"><strong><?php esc_html_e( 'Advanced Linked Products', 'glozin-addons' ); ?></strong></label>
			</p>
			<p class="form-field">
				<label for="advanced_linked_products"><?php esc_html_e( 'Products', 'glozin-addons' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="advanced_linked_products" name="glozin_advanced_linked_product_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post_id ); ?>">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post_id, 'glozin_advanced_linked_product_ids', true ) );

					if ( $product_ids && is_array( $product_ids ) ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					}
					?>
				</select> <?php echo wc_help_tip( __( 'This lets you choose which products are part of advanced linked products.', 'glozin-addons' ) ); // WPCS: XSS ok. ?>
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

		if ( isset( $_POST['glozin_advanced_linked_product_ids'] ) ) {
			$woo_data = $_POST['glozin_advanced_linked_product_ids'];
			update_post_meta( $post_id, 'glozin_advanced_linked_product_ids', $woo_data );
		} else {
			update_post_meta( $post_id, 'glozin_advanced_linked_product_ids', 0 );
		}
	}
}