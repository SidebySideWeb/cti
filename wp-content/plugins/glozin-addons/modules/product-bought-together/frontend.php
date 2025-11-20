<?php

namespace Glozin\Addons\Modules\Product_Bought_Together;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Has variation images
	 *
	 * @var $has_variation_images
	 */
	protected static $has_variation_images = null;


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
	 * Main cart key
	 *
	 * @var $cart_key
	 */
	private static $cart_key;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$this->product_bought_together_init();

		// Add to cart
		add_action( 'wp_loaded', array( $this, 'add_to_cart_action' ), 20 );
		add_action( 'template_redirect', [ $this, 'add_to_cart' ] );

		// Add to cart ajax
		add_action( 'wc_ajax_add_to_cart_ajax', array( $this, 'add_to_cart_action' ) );

		// Cart item data
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );
		add_action( 'woocommerce_cart_item_removed', [ $this, 'cart_item_removed' ], 10, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'get_cart_item_from_session' ], 10, 2 );

		// Restore remove
		add_action( 'woocommerce_cart_item_restored', [ $this, 'cart_item_restored' ], 10, 2 );

		// Update quantity
		add_action( 'glozin_update_cart_item', [ $this, 'cart_item_quantity' ], 10, 2 );
		add_filter( 'woocommerce_stock_amount_cart_item', [ $this, 'wc_cart_item_quantity' ], 10, 2 );
		add_filter( 'woocommerce_update_cart_validation', [ $this, 'wc_update_cart_item_quantity' ], 10, 4 );

		// Cart contents
		add_action( 'woocommerce_before_mini_cart_contents', [ $this, 'before_mini_cart_contents' ], 10 );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 9999 );

		// Change mini cart item
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'change_woocommerce_cart_item_quantity' ], 10, 3 );
		add_filter( 'woocommerce_widget_cart_item_quantity', [ $this, 'change_woocommerce_widget_cart_item_quantity' ], 11, 3 );
		add_filter( 'woocommerce_get_item_data', [ $this, 'get_item_data' ], 10, 2 );
	}

		/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_singular( 'product' ) || is_singular('glozin_builder') ) {
			$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style( 'glozin-product-bought-together', GLOZIN_ADDONS_URL . 'modules/product-bought-together/assets/product-bought-together' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
			wp_enqueue_script('glozin-product-bought-together', GLOZIN_ADDONS_URL . 'modules/product-bought-together/assets/product-bought-together' . $debug . '.js',  array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );

			$glozin_data = array(
				'currency_pos'       => get_option( 'woocommerce_currency_pos' ),
				'currency_symbol'    => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'thousand_sep'       => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : '',
				'decimal_sep'        => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '',
				'price_decimals'     => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : '',
				'check_all'          => get_post_meta( get_the_ID(), 'glozin_pbt_checked_all', true ),
				'alert'              => esc_html__( 'Please select a purchasable variation for [name] before adding this product to the cart.', 'glozin-addons' ),
				'add_to_cart_notice' => esc_html__( 'Successfully added to your cart', 'glozin-addons' ),
				'view_cart_text'     => esc_html__( 'View cart', 'glozin-addons' ),
				'view_cart_link'     => esc_url( wc_get_cart_url() ),
			);

			wp_localize_script(
				'glozin-product-bought-together', 'glozinPbt', $glozin_data
			);
		}
	}

	public function product_bought_together_init() {
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'product_bought_together' ), 5 );
		add_action( 'glozin_single_product_fbt_elementor', array( $this, 'product_bought_together' ), 5 );
	}

	/**
	 * Get product bought together
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_bought_together() {
		global $product;
		$product_ids = maybe_unserialize( get_post_meta( $product->get_id(), 'glozin_pbt_product_ids', true ) );
		$product_ids = apply_filters( 'glozin_pbt_product_ids', $product_ids, $product );
		if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
			return;
		}

		if ( $product->is_type( 'grouped' ) || $product->is_type( 'external' ) || $product->get_stock_status() == 'outofstock' ) {
			return;
		}

		$current_product = array( $product->get_id() );
		$product_ids     = apply_filters( 'glozin_product_bought_together_product_ids', array_merge( $current_product, $product_ids ) );

		 wc_get_template(
			'single-product/product-bought-together.php',
			array(
				'product_ids' => $product_ids,
			),
			'',
			GLOZIN_ADDONS_DIR . 'modules/product-bought-together/templates/'
		);
	}

	/**
	 * Add to cart product bought together
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_to_cart_action() {
		if ( empty( $_REQUEST['glozin_pbt_add_to_cart'] ) ) {
			return;
		}

		wc_nocache_headers();

		$product_id = $_REQUEST['glozin_product_id'];

		if ( $product_id == 0 ) {
			$product_ids = explode( ',', $_REQUEST['glozin_pbt_add_to_cart'] );
			$product_id  = $product_ids[0];
		}

		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		$was_added_to_cart = false;
		$quantity          = 1;
		$variation_id      = 0;
		$variations        = array();

		if ( $adding_to_cart->is_type( 'variation' ) ) {
			$variation_id = $product_id;
			$product_id   = $adding_to_cart->get_parent_id();
			$variations   = json_decode( stripslashes( $_REQUEST['glozin_variation_attrs'] ) );
			$variations   = (array) json_decode( $variations->$variation_id );
		}

		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		if ( $passed_validation && false !== self::$cart_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			$was_added_to_cart = true;
		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 === wc_notice_count( 'error' ) ) {
			if( ! empty( $_REQUEST[ 'glozin-pbt-add-to-cart-ajax' ] ) ) {
				self::add_to_cart(self::$cart_key);
				self::get_refreshed_fragments();
			}

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		} else {
			if( ! empty( $_REQUEST[ 'glozin-pbt-add-to-cart-ajax' ] ) ) {
				$notice = WC()->session->get( 'wc_notices', array() )['error'][0]['notice'];
				wc_clear_notices();
				wp_send_json(
					array( 'error' =>  $notice )
				);
			}
		}
	}

	/**
	 * Get a refreshed cart fragment, including the mini cart HTML.
	 */
	public static function get_refreshed_fragments() {
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		$data = array(
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => WC()->cart->get_cart_hash(),
		);
		wc_clear_notices();
		wp_send_json( $data );
	}

	public function add_to_cart( $primary_cart_key ) {
		$cart_item_key = ! empty( self::$cart_key ) ? self::$cart_key : '';
		$cart_item_key = ! empty( $primary_cart_key ) ? $primary_cart_key : $cart_item_key;
		if ( isset( $_REQUEST['glozin_pbt_add_to_cart'] ) || isset( $_REQUEST['data']['glozin_pbt_add_to_cart'] ) ) {
			$ids = '';
			$product_id = isset( $_REQUEST['glozin_current_product_id'] ) ? $_REQUEST['glozin_current_product_id'] : 0;
			if( empty( $product_id ) ) {
				return;
			}

			$variations = ! empty( $_REQUEST['glozin_variation_attrs'] ) ? json_decode( stripslashes( $_REQUEST['glozin_variation_attrs'] ) ) : array();
			if ( isset( $_REQUEST['glozin_pbt_add_to_cart'] ) ) {
				$ids = $_REQUEST['glozin_pbt_add_to_cart'];
				unset( $_REQUEST['glozin_pbt_add_to_cart'] );
			} elseif ( $_REQUEST['data']['glozin_pbt_add_to_cart'] ) {
				$ids = $_REQUEST['data']['glozin_pbt_add_to_cart'];
				unset( $_REQUEST['data']['glozin_pbt_add_to_cart'] );
			}

			$cart_item_data = ! empty( $ids ) ? [ 'glozin_pbt_ids' => $ids ] : array();

			$discount = get_post_meta( $product_id, 'glozin_pbt_discount_all', true );
			$quantity_discount_all = intval( get_post_meta( $product_id, 'glozin_pbt_quantity_discount_all', true ) );
			$has_discount = $discount && $discount > 0 && $quantity_discount_all <= count( explode( ',', $ids ) ) ? true : false;

			if ( $items = self::get_items( $ids, $product_id ) ) {
				if( ! empty( $cart_item_data['glozin_pbt_ids'] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_ids'] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_ids'] = $cart_item_data['glozin_pbt_ids'];
				}

				// add child products
				self::add_to_cart_items( $items, $cart_item_key, $product_id, $variations, $cart_item_data, $has_discount, $discount );
			}
		}
	}

	public function add_to_cart_items( $items, $cart_item_key, $product_id, $variations, $cart_item_data, $has_discount, $discount ) {
		// add child products
		foreach ( $items as $item ) {
			$item_id           = $item['id'];
			$item_qty          = 1;
			$item_product      = wc_get_product( $item_id );
			$item_variation    = [];
			$item_variation_id = 0;

			if( $item_id == 0 ) {
				continue;
			}

			if ( $item_product instanceof \WC_Product_Variation ) {
				$item_variation_id = $item_id;
				$item_id           = $item_product->get_parent_id();
				$item_variation    = (array) json_decode( $variations->$item_variation_id );
			}

			if ( $item_product && $item_product->is_in_stock() && $item_product->is_purchasable() && ( 'trash' !== $item_product->get_status() ) ) {

				if( $item_id == $product_id ) {
					continue;
				}

				if( $has_discount ) {
					$cart_item_data['glozin_pbt_new_price'] = wc_format_decimal( $item_product->get_price() * ( ( 100 - (float) $discount ) / 100 ), wc_get_price_decimals() );
				}

				// add to cart
				$item_key = WC()->cart->add_to_cart( $item_id, $item_qty, $item_variation_id, $item_variation, $cart_item_data );

				if ( $item_key && $has_discount ) {
					WC()->cart->cart_contents[ $item_key ]['glozin_parent_id']       = $product_id;
					WC()->cart->cart_contents[ $item_key ]['glozin_pbt_key']         = $item_key;
					WC()->cart->cart_contents[ $item_key ]['glozin_pbt_parent_key']  = $cart_item_key;
					WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'][] = $item_key;
				}
			}
		}
	}

	public function add_cart_item_data( $cart_item_data, $product_id ) {
		if ( isset( $_REQUEST['glozin_pbt_add_to_cart'] ) || isset( $_REQUEST['data']['glozin_pbt_add_to_cart'] ) ) {
			// make sure that is bought together product
			if ( isset( $_REQUEST['glozin_pbt_add_to_cart'] ) ) {
				$ids = $_REQUEST['glozin_pbt_add_to_cart'];
			} elseif ( isset( $_REQUEST['data']['glozin_pbt_add_to_cart'] ) ) {
				$ids = $_REQUEST['data']['glozin_pbt_add_to_cart'];
			}

			if ( ! empty( $ids ) ) {
				$cart_item_data['glozin_pbt_ids'] = $ids;
			}
		}

		return $cart_item_data;
	}

	public function get_items( $ids, $product_id = 0, $context = 'view' ) {
		$items = array();

		if ( ! empty( $ids ) ) {
			$_items = explode( ',', $ids );

			if ( is_array( $_items ) && count( $_items ) > 0 ) {
				foreach ( $_items as $_item ) {
					$_item_product = wc_get_product( $_item );

					if ( ! $_item_product || ( $_item_product->get_status() === 'trash' ) ) {
						continue;
					}

					if ( ( $context === 'view' ) && ( ! $_item_product->is_purchasable() || ! $_item_product->is_in_stock() ) ) {
						continue;
					}

					$items[] = array(
						'id'    => $_item,
					);
				}
			}
		}

		$items = apply_filters( 'glozin_pbt_get_items', $items, $ids, $product_id, $context );

		if ( $items && is_array( $items ) && count( $items ) > 0 ) {
			return $items;
		}

		return false;
	}

	public function cart_item_removed( $cart_item_key, $cart ) {
		if ( isset( $cart->removed_cart_contents[$cart_item_key]['glozin_pbt_keys'] ) || isset( $cart->removed_cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'] ) ) {
			$parent_key = ! empty( $cart->removed_cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'] ) ? $cart->removed_cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'] : $cart_item_key;
			$keys = $cart->removed_cart_contents[$parent_key]['glozin_pbt_keys'];

			if( $cart_item_key !== $parent_key ) {
				WC()->cart->remove_cart_item( $parent_key );
			}

			foreach ( $keys as $key ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
	}

	public function cart_item_restored( $cart_item_key, $cart ) {
		if ( ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) || ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'] ) ) {
			$parent_key = ! empty( $cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) ? $cart_item_key : $cart->cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'];
			$keys       = $cart->cart_contents[ $parent_key ]['glozin_pbt_keys'];

			if( $parent_key !== $cart_item_key ) {
				WC()->cart->restore_cart_item( $parent_key );
			}

			foreach ( $keys as $key ) {
				WC()->cart->restore_cart_item( $key );
			}
		}
	}

	public function get_cart_item_from_session( $cart_item, $item_session_values ) {
		if ( ! empty( $item_session_values['glozin_pbt_ids'] ) ) {
			$cart_item['glozin_pbt_ids'] = $item_session_values['glozin_pbt_ids'];
		}

		return $cart_item;
	}

	public function cart_item_quantity( $cart_item_key, $quantity ) {
		$cart_item_length = isset( $_POST['cart_item_length'] ) ? $_POST['cart_item_length'] : 0;
		if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_parent_id'] ) || ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) ) {
			if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) ) {
				$parent_key = $cart_item_key;
				$keys = WC()->cart->cart_contents[ $parent_key ]['glozin_pbt_keys'];
			} else {
				$parent_key = WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'];
				$keys = WC()->cart->cart_contents[ $parent_key ]['glozin_pbt_keys'];
			}

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

	public function wc_cart_item_quantity( $quantity, $cart_item_key ) {
		if( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) ) {
			$keys = WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'];

			WC()->cart->set_quantity( $cart_item_key, $quantity );
			foreach ( $keys as $key ) {
				WC()->cart->set_quantity( $key, $quantity );
			}
		}

		return $quantity;
	}

	public function wc_update_cart_item_quantity( $changed, $cart_item_key, $values, $quantity ) {
		if( $changed && ( ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) || ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'] ) ) ) {
			$parent_key   = ! empty( WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_keys'] ) ? $cart_item_key : WC()->cart->cart_contents[ $cart_item_key ]['glozin_pbt_parent_key'];

			if( $parent_key !== $cart_item_key ) {
				return false;
			}
		}

		return true;
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
			if( ! empty( $cart_item['glozin_pbt_ids'] ) ) {
				if ( $cart_item['variation_id'] > 0 ) {
					$item_product = wc_get_product( $cart_item['variation_id'] );
				} else {
					$item_product = wc_get_product( $cart_item['product_id'] );
				}

				$ori_price = $item_product->get_price();

				// has associated products
				$has_associated = false;

				if ( isset( $cart_item['glozin_pbt_keys'] ) ) {
					foreach ( $cart_item['glozin_pbt_keys'] as $key ) {
						if ( isset( $cart_contents[ $key ] ) ) {
							$has_associated = true;
							break;
						}
					}
				}

				// main product
				$discount = get_post_meta( $cart_item['product_id'], 'glozin_pbt_discount_all', true );
				$quantity_discount_all = intval( get_post_meta( $cart_item['product_id'], 'glozin_pbt_quantity_discount_all', true ) );

				if ( $has_associated && $discount && $discount > 0 && $quantity_discount_all <= count( explode( ',', $cart_item['glozin_pbt_ids'] ) ) ) {
					$discount_price = $ori_price * ( 100 - (float) $discount ) / 100;
					$cart_item['data']->set_price( $discount_price );

					// associated products
					if( ! empty( $cart_item['glozin_pbt_keys'] ) ) {
						foreach ( $cart_item['glozin_pbt_keys'] as $key => $glozin_pbt_keys ) {
							if( ! isset( $cart_contents[ $glozin_pbt_keys ] ) ) {
								continue;
							}
							if ( $cart_contents[$glozin_pbt_keys]['variation_id'] > 0 ) {
								$_item_product = wc_get_product( $cart_contents[$glozin_pbt_keys]['variation_id'] );
							} else {
								$_item_product = wc_get_product( $cart_contents[$glozin_pbt_keys]['product_id'] );
							}

							$ori_price_child = $_item_product->get_price();
							$discount_price_child = $ori_price_child * ( 100 - (float) $discount ) / 100;

							$cart_contents[$glozin_pbt_keys]['data']->set_price( $discount_price_child );
						}
					}
				}
			}
		}
	}

	public static function format_price( $price ) {
		// format price to percent or number
		$price = preg_replace( '/[^.%0-9]/', '', $price );

		return apply_filters( 'glozin_pbt_format_price', $price );
	}

	/**
	 * Change quantity in cart page
	 *
	 * @return void
	 */
	public function change_woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		if( ! empty( $cart_item['glozin_pbt_parent_key'] ) ) {
			$product_quantity = '<span class="glozin-product-quantity__text">' . $cart_item['quantity'] . '</span>';
		}

		return $product_quantity;
	}

	/**
	 * Change quantity in mini cart
	 *
	 * @return void
	 */
	public function change_woocommerce_widget_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
		if( ! empty( $cart_item['glozin_pbt_keys'] ) || ! empty( $cart_item['glozin_pbt_parent_key'] ) ) {
			if( empty( $cart_item['glozin_pbt_keys'] ) ) {
				$product_quantity = '<span class="glozin-product-quantity__text">' . sprintf( 'Qty: %s', $cart_item['quantity'] ) . '</span>';
			}
		}

		return $product_quantity;
	}

	public function get_item_data( $item_data, $cart_item ) {
		if( ! empty( $cart_item['glozin_pbt_keys'] ) || ! empty( $cart_item['glozin_pbt_parent_key'] ) ) {
			$item_data[] = array(
				'key'   => 'glozin',
				'value' => esc_html__( 'Bought together and save', 'glozin-addons' ),
			);
		}

		return $item_data;
	}
}