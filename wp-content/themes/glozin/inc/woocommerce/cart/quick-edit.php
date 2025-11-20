<?php
/**
 * Hooks of Quick Edit.
 *
 * @package Glozin
 */

 namespace Glozin\WooCommerce\Cart;

use \Glozin\Helper;
use Glozin\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Quick Edit template.
 */
class Quick_Edit extends \Glozin\WooCommerce\Single_Product\Product_Base {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'quick_edit_scripts' ), 20 );

		// Quick add modal.
		add_action( 'wc_ajax_product_quick_edit', array( $this, 'quick_edit_content' ) );
		add_action( 'wc_ajax_quick_edit_update_cart_item', array( $this, 'update_cart_item_action' ) );

		add_filter( 'woocommerce_available_variation', array( $this, 'data_product_variations' ), 10, 3 );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @return void
	 */
	public static function quick_edit_scripts() {
		if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}
	}

	/**
	 * Product quick add template.
	 *
	 * @return string
	 */
	public function quick_edit_content() {
		if ( empty( $_POST['cart_item_key'] ) ) {
			wp_send_json_error( esc_html__( 'No product.', 'glozin' ) );
			exit;
		}

		$cart_item_key = sanitize_text_field($_POST['cart_item_key']);
   		$cart = WC()->cart->get_cart();
		if (!isset($cart[$cart_item_key])) {
			wp_send_json_error(esc_html__('Invalid product.', 'glozin'));
			exit;
		}

		$cart_item = $cart[$cart_item_key];
		$product_id = $cart_item['product_id'];
		$post_object = get_post($product_id);

		if (!$post_object || !in_array($post_object->post_type, array('product', 'product_variation'), true)) {
			wp_send_json_error(esc_html__('Invalid product.', 'glozin'));
			exit;
		}

		$GLOBALS['post'] = $post_object;
		wc_setup_product_data($post_object);

		ob_start();
		wc_get_template('content-product-quickedit.php', array(
			'cart_item_key' => $cart_item_key,
			'cart_item'     => $cart_item,
			'product'       => wc_get_product($product_id),
			'variation_id'  => $cart_item['variation_id'],
			'quantity'      => $cart_item['quantity'],
			'available_variations' => wc_get_product($product_id)->get_available_variations(),
			'attributes'           => wc_get_product($product_id)->get_variation_attributes(),
			'selected_attributes'  => $cart_item['variation'],
		));
		wp_reset_postdata();
		wc_setup_product_data($GLOBALS['post']);
		$output = ob_get_clean();

		wp_send_json_success($output);
		exit;
	}

	/**
	 * Update cart item action.
	 *
	 * @return void
	 */
	public static function update_cart_item_action() {
		if( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'quick_edit_update_cart_item' ) {
			return;
		}

		if ( empty( $_REQUEST['cart_item_key'] ) ) {
			return;
		}

		wc_nocache_headers();

		$cart_items = WC()->cart->get_cart();
		$cart_item_key = sanitize_text_field($_POST['cart_item_key']);
		$product_id = absint($_REQUEST['product_id']);
		$adding_to_cart = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		if( ! $adding_to_cart->is_type( 'variable' ) ) {
			return;
		}

		if( empty( $_REQUEST['variation_id'] ) ) {
			return;
		}

		$was_added_to_cart = false;
		$quantity          = absint($_REQUEST['quantity']);
		$variation_id      = absint($_REQUEST['variation_id']);
		$variations        = array();
		$cart_item_data    = isset( $cart_items[$cart_item_key] ) ? $cart_items[$cart_item_key]['data'] : array();
		$item_key          = null;

		foreach ( $_REQUEST as $key => $value ) {
			if ( 'attribute_' !== substr( $key, 0, 10 ) ) {
				continue;
			}

			$variations[ sanitize_title( wp_unslash( $key ) ) ] = wp_unslash( $value );
		}

		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		$item_key = WC()->cart->generate_cart_id($product_id, $variation_id, $variations, $cart_item_data );
		
		if( $item_key == $cart_item_key ) {
			WC()->cart->set_quantity( $item_key, $quantity );
		} else {
			$item_key      = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );
			$cart_contents = WC()->cart->cart_contents;
			$items         = array_keys($cart_contents);
			$index         = array_search($cart_item_key, $items);
			$reordered     = [];

			unset($cart_contents[$cart_item_key], $cart_contents[$item_key]);
			foreach ($items as $i => $key) {
				if( $i == $index ) {
					$reordered[$item_key] = WC()->cart->get_cart_contents()[$item_key];
				} elseif ( isset($cart_contents[$key] ) ) {
					$reordered[$key] = $cart_contents[$key];
				}
			}

			WC()->cart->cart_contents = $reordered;
			WC()->cart->set_session();
   			WC()->cart->calculate_totals();
		}

		if( $passed_validation && ! empty( $item_key ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			$was_added_to_cart = true;
		}

		wc_clear_notices();

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 === wc_notice_count( 'error' ) ) {
			\WC_AJAX::get_refreshed_fragments();
		}

	}

	/**
	 * Data variation
	 *
	 * @return array
	 */
	public function data_product_variations( $data, $product, $variation ) {
		if( is_singular('product') ) {
			return $data;
		}
		
		$availability = $variation->get_availability();

		$data['availability_status'] = $availability['availability'];

		return $data;
	}
}
