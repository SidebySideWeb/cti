<?php
namespace Glozin\Addons\Elementor\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Products_Bundle {
	use \Glozin\Addons\WooCommerce\Products_Base;

	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wc_ajax_glozin_add_product_bundle', [ $this, 'add_product_bundle' ] );
		add_action( 'wc_ajax_glozin_remove_product_bundle', [ $this, 'remove_product_bundle' ] );

		// Add to cart
		add_action( 'wc_ajax_glozin_add_to_cart_bundle', [ $this, 'add_to_cart_bundle' ] );

		// Cart contents
		add_action( 'woocommerce_before_mini_cart_contents', [ $this, 'before_mini_cart_contents' ], 10 );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 9999 );

		// Cart
		add_action( 'glozin_update_cart_item', [ $this, 'cart_item_quantity' ], 10, 2 );
		add_action( 'woocommerce_cart_item_removed', [ $this, 'cart_item_removed' ], 10, 2 );
		add_action( 'woocommerce_cart_item_restored', [ $this, 'cart_item_restored' ], 10, 2 );
		add_filter( 'woocommerce_get_item_data', [ $this, 'get_item_data' ], 10, 2 );

		// Change Quantity in Mini Cart
		add_filter( 'woocommerce_widget_cart_item_quantity', [ $this, 'change_woocommerce_widget_cart_item_quantity' ], 11, 3 );
		add_filter( 'woocommerce_stock_amount_cart_item', [ $this, 'wc_cart_item_quantity' ], 10, 2 );

		// Change Quantity in Cart Page
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'change_woocommerce_cart_item_quantity' ], 10, 3 );
		add_filter( 'woocommerce_update_cart_validation', [ $this, 'wc_update_cart_item_quantity' ], 10, 4 );

		// Empty session
		add_action( 'template_redirect', array( $this, 'empty_session' ) );
	}

	/**
	 * Ajax load add bundle
	 */
	public function add_product_bundle() {
		if ( empty( $_POST['action'] ) || $_POST['action'] !== 'glozin_add_product_bundle' ) {
			return;
		}

		if( empty( $_POST['product_id'] ) ) {
			return;
		}
		

		// Ensure WooCommerce session is initialized
		if ( ! WC()->session ) {
			WC()->initialize_session();
		}

		$product_id = sanitize_text_field($_POST['product_id']);
		$products_bundle = WC()->session->get('glozin_products_bundle', []);
		$products_variable_bundle = WC()->session->get('glozin_products_variable_bundle', []);
		
		$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : null;
		$bundle_min = isset( $_POST['bundle_min'] ) ? $_POST['bundle_min'] : null;
		$bundle_max = isset( $_POST['bundle_max'] ) ? $_POST['bundle_max'] : null;
		$bundle_discount = isset( $_POST['bundle_discount'] ) ? $_POST['bundle_discount'] : null;

		$check_limit = ( empty( $bundle_min ) || intval( $bundle_min ) <= intval( $bundle_max ) ) && count( $products_bundle ) >= intval( $bundle_max ) ? true : false;
		
		
		if( ! in_array( $product_id, $products_bundle ) && ! $check_limit ) {
			$products_bundle[] = $product_id;
			WC()->session->set('glozin_products_bundle', $products_bundle);
			WC()->session->save_data(); // Save immediately
			
			// Update local variable to reflect the change
			$products_bundle = WC()->session->get('glozin_products_bundle', []);
		}
		if( ! empty( $_POST['product_type'] ) && $_POST['product_type'] == 'variable' ) {
			$products_variable_bundle[$product_id] = [
				'variation_id' => sanitize_text_field($product_id)
			];

			foreach ($_POST as $key => $value) {
				if (strpos($key, 'attribute_') === 0) {
					$products_variable_bundle[$product_id]['attributes'][$key] = $value;
				}
			}

			WC()->session->set('glozin_products_variable_bundle', $products_variable_bundle);
			WC()->session->save_data(); // Save immediately
		}

		// Get fresh data from session after all updates
		$products_bundle = WC()->session->get('glozin_products_bundle', []);

		$current_limit = ( empty( $bundle_min ) || intval( $bundle_min ) <= intval( $bundle_max ) ) && count( $products_bundle ) >= intval( $bundle_max ) ? true : false;

		wp_send_json_success( [
			'html' => implode('', self::products_bundle_html($products_bundle, $limit, $bundle_min, $bundle_max, $bundle_discount) ),
			'total_html' => self::products_bundle_total( $products_bundle, $limit, $bundle_min, $bundle_max, $bundle_discount ),
			'limit' => $check_limit,
			'current_limit' => $current_limit
		]);
	}

	/**
	 * Ajax load remove bundle
	 */
	public function remove_product_bundle() {
		if ( empty( $_POST['action'] ) || $_POST['action'] !== 'glozin_remove_product_bundle' ) {
			return;
		}

		$product_id = sanitize_text_field($_POST['product_id']);
		$products_bundle = WC()->session->get('glozin_products_bundle', []);
		$products_variable_bundle = WC()->session->get('glozin_products_variable_bundle', []);

		if( in_array( $product_id, $products_bundle ) ) {
			if( count( $products_bundle ) > 0 ) {
				$products_bundle = array_filter($products_bundle, fn($v) => $v !== $product_id );
				$products_bundle = array_values($products_bundle);
			} else {
				$products_bundle = [];
			}
		}

		if( in_array( $product_id, $products_variable_bundle ) ) {
			if( count( $products_variable_bundle ) > 0 ) {
				$products_variable_bundle = array_filter($products_variable_bundle, fn($v) => $v !== $product_id );
				$products_variable_bundle = array_values($products_variable_bundle);
			} else {
				$products_variable_bundle = [];
			}
		}

		WC()->session->set('glozin_products_bundle', $products_bundle);
		WC()->session->set('glozin_products_variable_bundle', $products_variable_bundle);
		WC()->session->save_data(); // Save immediately

		$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : null;
		$bundle_min = isset( $_POST['bundle_min'] ) ? $_POST['bundle_min'] : null;
		$bundle_max = isset( $_POST['bundle_max'] ) ? $_POST['bundle_max'] : null;
		$bundle_discount = isset( $_POST['bundle_discount'] ) ? $_POST['bundle_discount'] : null;

		wp_send_json_success( [
			'html' => implode('', self::products_bundle_html($products_bundle, $limit,$bundle_min, $bundle_max, $bundle_discount) ),
			'total_html' => self::products_bundle_total( $products_bundle, $limit, $bundle_min, $bundle_max, $bundle_discount ),
			'limit' => count( $products_bundle ) > intval( $bundle_max ) ? true : false,
			'hasproduct' => ! empty( $products_bundle ) ? true : false
		]);
	}

	public function add_to_cart_bundle() {
		if ( empty( $_POST['action'] ) || $_POST['action'] !== 'glozin_add_to_cart_bundle' ) {
			return;
		}

		wc_nocache_headers();

		$products_bundle = WC()->session->get('glozin_products_bundle', []);
		$products_variable_bundle = WC()->session->get('glozin_products_variable_bundle', []);
		$primary_cart_key = null;

		if( empty( $products_bundle ) ) {
			return;
		}

		$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : null;
		$bundle_min = isset( $_POST['bundle_min'] ) ? $_POST['bundle_min'] : null;
		$bundle_max = isset( $_POST['bundle_max'] ) ? $_POST['bundle_max'] : null;
		$bundle_discount = isset( $_POST['bundle_discount'] ) ? $_POST['bundle_discount'] : null;

		$has_discount = ! empty( $products_bundle ) && count( $products_bundle ) >= $bundle_min && ! empty( $bundle_discount ) && intval( $bundle_discount ) > 0 ? true : false;

		$primary_product_id = $products_bundle[0];
		$primary_product = wc_get_product( $products_bundle[0] );
		$primary_variation_id = 0;
		$primary_variations   = [];
		$primary_cart_item_data = [
			'glozin_product_bundle' => [
				'product_ids' => $products_bundle,
				'limit' => $limit,
				'bundle_min' => $bundle_min,
				'bundle_max' => $bundle_max,
				'bundle_discount' => $bundle_discount,
				'products_variable_bundle' => $products_variable_bundle,
			],
		];

		if ( $primary_product->is_type( 'variation' ) ) {
			$primary_variation_id = $products_bundle[0];
			$primary_product_id   = $primary_product->get_parent_id();
			$primary_variations   = $products_variable_bundle[$primary_variation_id]['attributes'];
		}

		$primary_cart_key = WC()->cart->generate_cart_id( $primary_product_id, $primary_variation_id, $primary_variations, $primary_cart_item_data );

		foreach( $products_bundle as $product_id ) {
			$adding_to_cart  = wc_get_product( $product_id );
			$quantity        =  isset( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) && ! empty( $_REQUEST['quantity'][ $product_id ] ) ? wp_unslash( $_REQUEST['quantity'] )[ $product_id ] : 1;

			if ( ! $adding_to_cart ) {
				return;
			}

			$variation_id = 0;
			$variations   = [];
			$cart_item_data = [];

			if( $has_discount ) {
				$cart_item_data['glozin_product_bundle'] = [
						'product_ids'              => $products_bundle,
						'limit'                    => $limit,
						'bundle_min'               => $bundle_min,
						'bundle_max'               => $bundle_max,
						'bundle_discount'          => $bundle_discount,
						'products_variable_bundle' => $products_variable_bundle,
					];
			}

			if ( $adding_to_cart->is_type( 'variation' ) ) {
				$variation_id = $product_id;
				$product_id   = $adding_to_cart->get_parent_id();
				$variations   = $products_variable_bundle[$variation_id]['attributes'];
			}

			if( $has_discount && intval( $primary_product_id ) !== intval( $product_id ) ) {
				$cart_item_data['glozin_product_bundle']['primary_cart_key'] = $primary_cart_key;
			}

			$cart_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );

			if( $has_discount && intval( $primary_product_id ) !== intval( $product_id ) ) {
				WC()->cart->cart_contents[ $primary_cart_key ]['glozin_product_bundle']['child_keys'][] = $cart_key;
			}
		}

		WC()->session->set('glozin_products_bundle', []);
		WC()->session->set('glozin_products_variable_bundle', []);
		WC()->session->save_data(); // Save immediately

		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		wp_send_json_success( [
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => WC()->cart->get_cart_hash(),
			'html' => implode('', self::products_bundle_html( [], $limit, $bundle_min, $bundle_max ) ),
			'total_html' => self::products_bundle_total( [], $limit, $bundle_min, $bundle_max ),
		]);
	}

	public function before_mini_cart_contents() {
		WC()->cart->calculate_totals();
	}

	public function before_calculate_totals( $cart_object ) {
		if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {
			// This is necessary for WC 3.0+
			return;
		}

		$cart_contents = $cart_object->cart_contents;

		foreach ( $cart_contents as $cart_item_key => $cart_item ) {
			if( ! empty( $cart_item['glozin_product_bundle'] ) ) {
				$product_id = $cart_item['variation_id'] > 0 ? $cart_item['variation_id'] : $cart_item['product_id'];
				$product_ids = $cart_item['glozin_product_bundle']['product_ids'];
				$bundle_min = $cart_item['glozin_product_bundle']['bundle_min'];
				$bundle_discount = $cart_item['glozin_product_bundle']['bundle_discount'];

				$has_discount = ! empty( $product_ids ) && count( $product_ids ) >= $bundle_min && ! empty( $bundle_discount ) && intval( $bundle_discount ) > 0 ? true : false;

				if( ! $has_discount ) {
					continue;
				}

				$item_product = wc_get_product( $product_id );

				$cart_item['data']->set_price( self::get_price_discount( $item_product, $bundle_discount ) );
			}
		}
	}

	public function cart_item_quantity( $cart_item_key, $quantity ) {
		$cart_item_length = isset( $_POST['cart_item_length'] ) ? $_POST['cart_item_length'] : 0;
		if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) || ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'] ) ) {
			$parent_key = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) ? $cart_item_key : WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'];
			$keys = WC()->cart->cart_contents[ $parent_key ]['glozin_product_bundle']['child_keys'];

			WC()->cart->set_quantity( $parent_key, $quantity );
			foreach ( $keys as $key ) {
				WC()->cart->set_quantity( $key, $quantity );
			}

			if( intval( $cart_item_length ) == ( count($keys) + 1 ) && $quantity < 1 ) {
				WC()->cart->empty_cart();
			}

			\WC_AJAX::get_refreshed_fragments();
		}
	}

	public function cart_item_removed( $cart_item_key, $cart ) {
		if ( isset( $cart->removed_cart_contents[$cart_item_key]['glozin_product_bundle']['child_keys'] ) || isset( $cart->removed_cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'] ) ) {
			$parent_key = ! empty( $cart->removed_cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'] ) ? $cart->removed_cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'] : $cart_item_key;
			$keys = $cart->removed_cart_contents[$parent_key]['glozin_product_bundle']['child_keys'];

			if( $cart_item_key !== $parent_key ) {
				WC()->cart->remove_cart_item( $parent_key );
			}

			foreach ( $keys as $key ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
	}

	public function cart_item_restored( $cart_item_key, $cart ) {
		if ( ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) || ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'] ) ) {
			$parent_key = ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) ? $cart_item_key : $cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'];
			$keys       = $cart->cart_contents[ $parent_key ]['glozin_product_bundle']['child_keys'];

			if( $parent_key !== $cart_item_key ) {
				WC()->cart->restore_cart_item( $parent_key );
			}

			foreach ( $keys as $key ) {
				WC()->cart->restore_cart_item( $key );
			}
		}
	}

	public function get_item_data( $item_data, $cart_item ) {
		if( ! empty( $cart_item['glozin_product_bundle'] ) ) {
			$item_data[] = array(
				'key'   => 'glozin',
				'value' => esc_html__( 'Bundle and save', 'glozin-addons' ),
			);
		}

		return $item_data;
	}

	/**
	 * Change quantity cart
	 *
	 * @return void
	 */
	public function change_woocommerce_widget_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
		if( ! empty( $cart_item['glozin_product_bundle'] ) && ! empty( $cart_item['glozin_product_bundle']['primary_cart_key'] ) ) {
			$product_quantity = '<span class="glozin-product-quantity__text">' . sprintf( 'Qty: %s', $cart_item['quantity'] ) . '</span>';
		}

		return $product_quantity;
	}

	public function wc_cart_item_quantity( $quantity, $cart_item_key ) {
		if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle'] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) ) {
			$keys = WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'];

			WC()->cart->set_quantity( $cart_item_key, $quantity );
			foreach ( $keys as $key ) {
				WC()->cart->set_quantity( $key, $quantity );
			}
		}

		return $quantity;
	}

	/**
	 * Change quantity in cart page
	 *
	 * @return void
	 */
	public function change_woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		if( ! empty( $cart_item['glozin_product_bundle'] ) && ! empty( $cart_item['glozin_product_bundle']['primary_cart_key'] ) ) {
			$product_quantity = '<span class="glozin-product-quantity__text">' . $cart_item['quantity'] . '</span>';
		}

		return $product_quantity;
	}

	public function wc_update_cart_item_quantity( $changed, $cart_item_key, $values, $quantity ) {
		if( $changed && ( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) || ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'] ) ) ) {
			$parent_key   = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['child_keys'] ) ? $cart_item_key : WC()->cart->cart_contents[ $cart_item_key ]['glozin_product_bundle']['primary_cart_key'];

			if( $parent_key !== $cart_item_key ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Empty Session
	 *
	 * @return void
	 */
	public function empty_session() {
		WC()->session->set('glozin_products_bundle', []);
		WC()->session->set('glozin_products_variable_bundle', []);
		WC()->session->save_data(); // Save immediately
	}

	public function products_bundle_html($products_bundle, $limit, $bundle_min, $bundle_max, $bundle_discount = 0 ) {
		$output = [];

		$has_discount = ! empty( $products_bundle ) && count( $products_bundle ) >= $bundle_min && ! empty( $bundle_discount ) && intval( $bundle_discount ) > 0 ? true : false;

		if( ! empty( $products_bundle ) ) {
			foreach( $products_bundle as $product_id ) {
				$_product = wc_get_product($product_id);
				$output[] = sprintf( 
								'<div class="product-bundle__item d-flex gap-10 mb-15 last-0" data-product_id="%s" data-product_type="%s" data-product_parent="%s">
									<div class="product-bundle__item-thumbnail column-custom">
										<a href="%s" class="gz-ratio woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="%s">
											%s
										</a>
									</div>
									<div class="product-bundle__item-summary column-custom-remaining d-flex flex-column gap-10 justify-content-center">
										<h2 class="woocommerce-loop-product__title my-0 fs-15">
											<a href="%s" class="woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="%s">
												%s
											</a>
										</h2>
										<div class="price">
											%s
										</div>
										<div class="quantity_remove d-flex align-items-center justify-content-between">
											%s
											<div class="product-bundle__item-remove" data-product_id="%s" data-product_type="%s" data-product_parent="%s">%s</div>
										</div>
									</div>
								</div>',
								esc_attr($product_id),
								esc_attr($_product->get_type()),
								$_product->is_type('variation') ? esc_attr($_product->get_parent_id()) : '',
								esc_url( $_product->get_permalink() ),
								esc_attr( $_product->get_title() ),
								$_product->get_image(),
								esc_url( $_product->get_permalink() ),
								esc_attr( $_product->get_title() ),
								$_product->get_title(),
								$has_discount ? wc_format_sale_price( wc_get_price_to_display( $_product ), self::get_price_discount( $_product, $bundle_discount ) ) : $_product->get_price_html(),
								woocommerce_quantity_input( array(
									'input_name'   => "quantity[{$product_id}]",
									'input_value'  => '1',
								), $_product, false ),
								esc_attr($product_id),
								esc_attr($_product->get_type()),
								$_product->is_type('variation') ? esc_attr($_product->get_parent_id()) : '',
								\Glozin\Addons\Helper::inline_svg( 'icon=trash')
							);
			}
		}

		$limit = ! empty( $bundle_max ) && intval( $bundle_max ) > intval( $bundle_min ) ? $bundle_max : $bundle_min;
		if( count($products_bundle) > 0 ) {
			$limit = $limit - count($output);
		}
		
		for($i=0; $i < $limit; $i++) {
			$output[] = sprintf('<div class="product-bundle__loading d-flex gap-10 mb-15 last-0">
						<div class="product-loading__thumbnail bg-bundle column-custom rounded-100"></div>
						<div class="product-loading__summary column-custom-remaining d-flex flex-column gap-10 justify-content-center">
							<div class="product-loading__text bg-bundle h-10 w-100 text-1 rounded-10"></div>
							<div class="product-loading__text bg-bundle h-10 w-100 text-2 rounded-10"></div>
							<div class="product-loading__text bg-bundle h-10 w-100 text-3 rounded-10"></div>
						</div>
					</div>');
		}

		return $output;
	}

	public function products_bundle_total($products_bundle, $limit, $bundle_min, $bundle_max, $bundle_discount = 0 ) {
		$total = 0;
		$has_discount = ! empty( $products_bundle ) && count( $products_bundle ) >= $bundle_min && ! empty( $bundle_discount ) && intval( $bundle_discount ) > 0 ? true : false;

		if( ! empty( $products_bundle ) ) {
			foreach( $products_bundle as $product_id ) {
				$_product = wc_get_product($product_id);
				if( $has_discount ) {
					$total += self::get_price_discount( $_product, $bundle_discount );
				} else {
					$total += wc_get_price_to_display( $_product );
				}
			}
		}

		return wc_price( $total );
	}

	public function get_price_discount( $product, $discount ) {
		return wc_get_price_to_display( $product ) - ( wc_get_price_to_display( $product ) / 100 * (float) $discount );
	}
}