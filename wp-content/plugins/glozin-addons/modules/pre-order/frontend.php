<?php

namespace Glozin\Addons\Modules\Pre_Order;

use Glozin\Addons\Modules\Pre_Order\Helper;

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Change Label
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'pre_order_label' ), 20, 2 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'pre_order_label' ), 10, 2 );

		// Add label in stock
		add_filter( 'woocommerce_get_availability_text', [ $this, 'pre_order_label' ], 30, 2 );

		// Add label in cart page and mini cart
		add_filter( 'woocommerce_cart_item_name', [ $this, 'change_woocommerce_cart_item_name' ], 5, 3 );

		// Add label in variable product
		add_filter( 'woocommerce_available_variation', array( $this, 'available_variation_data' ), 10, 3 );

		// Show pre-order info on single product page
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'print_pre_order_info_on_single_product_page' ), 5 );

		// Edit price
		add_filter( 'woocommerce_product_get_price', array( $this, 'edit_price' ), 10, 2 );
		add_filter( 'woocommerce_product_get_regular_price', array( $this, 'edit_regular_price' ), 10, 2 );
		add_filter( 'woocommerce_product_is_on_sale', array( $this, 'product_is_on_sale' ), 10, 2 );

		// Edit price for variable product
		add_filter( 'woocommerce_product_variation_get_price', array( $this, 'edit_price' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'edit_regular_price' ), 10, 2 );
		add_filter( 'woocommerce_get_price_html', array( $this, 'edit_variable_product_price_html' ), 10, 2 );

		// Change out of stock to pre-order
		add_filter( 'woocommerce_product_get_stock_status', array( $this, 'change_out_of_stock_to_instock' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_get_stock_status', array( $this, 'change_out_of_stock_to_instock' ), 10, 2 );

		// Add label in order item meta
		add_action( 'woocommerce_new_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );
		add_filter( 'woocommerce_display_item_meta', array( $this, 'display_item_meta' ), 10, 3 );

		// Add MyAccount Page
		add_action( 'woocommerce_my_account_my_orders_column_order-status', array( $this, 'add_pre_order_button_on_orders_page' ) );

		$this->custom_add_preorders_endpoint();
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
		add_action( 'woocommerce_account_pre-orders_endpoint', array( $this, 'endpoint_content' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_singular( 'product' ) || is_singular( 'glozin_builder' ) || is_account_page() ) {
			$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'glozin-pre-order', GLOZIN_ADDONS_URL . 'modules/pre-order/assets/pre-order' . $debug . '.js', array( 'jquery' ), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
			wp_enqueue_style( 'glozin-pre-order', GLOZIN_ADDONS_URL . 'modules/pre-order/assets/pre-order' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
		}
	}

	/**
	 * Pre order label
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pre_order_label( $text, $product ) {
		if ( Helper::is_pre_order_active( $product ) && ! $product->is_type( 'variable' ) ) {
			$text = esc_html__( 'Pre-Order', 'glozin-addons' );
		}
		return $text;
	}

	/**
	 * Change quantity in mini cart
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function change_woocommerce_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
		if ( Helper::is_pre_order_active( $cart_item['data'] ) ) {
			$product_name = '<div class="mb-5"><span class="gz-pre-order-label pre-order woocommerce-badge badge-small">' . apply_filters( 'glozin_pre_order_cart_label', esc_html__( 'Pre-Order', 'glozin-addons' ), $cart_item['data']->get_id(), $cart_item ) . '</span></div>' .$product_name; 
		}

		return $product_name;
	}

	/**
	 * Add the pre-order data that will be used for replacing the Add to cart text in the pre-order variations.
	 *
	 * @param array                $array            The variable product data array.
	 * @param WC_Product_Variable  $variable_product The WC_Product_Variable object.
	 * @param WC_Product_Variation $variation        The WC_Product_Variation object.
	 *
	 * @return array
	 */
	public function available_variation_data( $array, $variable_product, $variation ) {
		if ( Helper::is_pre_order_active( $variation ) ) {
			$label = apply_filters( 'glozin_pre_order_variation_default_label', esc_html__( 'Pre-Order', 'glozin-addons' ), $variation->get_id() );

			$array['is_pre_order']    = 'yes';
			$array['pre_order_label'] = apply_filters( 'glozin_variation_pre_order_label', $label, $variation->get_id() );
			$array['pre_order_html']  = apply_filters( 'glozin_variation_pre_order_html', Helper::print_availability_html( $variation->get_id() ), $variation->get_id() );
		}

		return $array;
	}
	
	public function print_pre_order_info_on_single_product_page() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $product;

		if ( Helper::is_pre_order_active( $product ) ) {
			echo apply_filters( 'glozin_pre_order_info_on_single_product_page', Helper::print_availability_html( $product ), $product );
		}
	}

	/**
	 * Edit the product price for the pre-order.
	 *
	 */
	public function edit_price( $price, $product ) {
		if ( apply_filters( 'glozin_pre_order_return_original_price', false, $product ) ) {
			return $price;
		}

		if ( ! Helper::is_pre_order_active( $product ) ) {
			return $price;
		}

		$_price = get_post_meta( $product->get_id(), '_pre_order_price_discount', true );
		$price  = ! empty( $_price ) ? $_price : $price;

		return $price;
	}
	
	/**
	 * Edit the product regular price for the pre-order.
	 *
	 */
	public function edit_regular_price( $price, $product ) {
		if ( apply_filters( 'glozin_pre_order_return_original_price', false, $product ) ) {
			return $price;
		}

		if ( ! Helper::is_pre_order_active( $product ) ) {
			return $price;
		}

		if( empty( get_post_meta( $product->get_id(), '_pre_order_price_discount', true ) ) ) {
			return $price;
		}

		$_price = $product->get_sale_price();
		$_price_pre_order = get_post_meta( $product->get_id(), '_pre_order_price_discount', true );
		$price  = ! empty( $_price ) && $_price !== $_price_pre_order ? $_price : $price;

		return $price;
	}

	/**
	 * Edit the variation product price html for the pre-order.
	 *
	 */
	public function edit_variable_product_price_html( $price_html, $variation ) {
		if( ! $variation->is_type( 'variation' ) ) {
			return $price_html;
		}

		if ( ! Helper::is_pre_order_active( $variation ) ) {
			return $price_html;
		}
		
		$pre_order_price_discount = get_post_meta( $variation->get_id(), '_pre_order_price_discount', true );

		if( empty( $pre_order_price_discount ) ) {
			return $price_html;
		}

		if( empty( $variation->get_sale_price() ) ) {
			return $price_html;
		}
		
		if( ! empty( $variation->get_sale_price() ) ) {
			if( intval( $variation->get_sale_price() ) !== intval( $pre_order_price_discount ) ) {
				$price_html = wc_format_sale_price( wc_price(  $variation->get_sale_price() ), wc_price( $pre_order_price_discount ) );
			} else {
				$price_html = wc_format_sale_price( wc_price(  $variation->get_regular_price() ), wc_price( $pre_order_price_discount ) );
			}
		} else {
			if( intval( $variation->get_price() ) !== intval( $pre_order_price_discount ) ) {
				$price_html = wc_format_sale_price( wc_price( $variation->get_price() ), wc_price( $pre_order_price_discount ) );
			}
		}

		return $price_html;
	}

	/**
	 * Edit the product is on sale for the pre-order.
	 *
	 */
	public function product_is_on_sale( $on_sale, $product ) {
		$compare = false;
		
		if( $product->is_type( 'simple' ) && ! empty( get_post_meta( $product->get_id(), '_pre_order_price_discount', true ) ) ) {
			if( ! empty( $product->get_sale_price() ) ) {
				if( intval( $product->get_sale_price() ) !== intval( get_post_meta( $product->get_id(), '_pre_order_price_discount', true ) ) ) {
					$compare = true;
				}
			} else {
				if( intval( $product->get_price() ) !== intval( get_post_meta( $product->get_id(), '_pre_order_price_discount', true ) ) ) {
					$compare = true;
				}
			}
		}

		if ( $compare && Helper::is_pre_order_active( $product ) ) {
			return true;
		}

		return $on_sale;
	}

	public function change_out_of_stock_to_instock( $stock_status, $product ) {
		if ( Helper::is_pre_order_active( $product ) ) {
			if( 'outofstock' === $stock_status ) {
				$stock_status = 'instock';
			}
		}

		return $stock_status;
	}

	/**
	 * Adds order item meta
	 *
	 * @param int                   $item_id  Order item ID.
	 * @param WC_Order_Item_Product $item     Order item object.
	 * @param int                   $order_id Order ID.
	 */
	public function add_order_item_meta( $item_id, $item, $order_id ) {
		$order = wc_get_order( $order_id );

		if ( 'line_item' !== $item->get_type() || ! $order instanceof \WC_Order ) {
			return;
		}

		$product = $item->get_product();

		if ( Helper::is_pre_order_active( $product ) ) {
			if ( ! Helper::is_order_has_pre_order( $order ) ) {
				$order->update_meta_data( '_glozin_order_has_preorder', apply_filters( 'glozin_order_has_preorder', 'yes', $order, $product, $item ) );
				$order->update_meta_data( '_glozin_status', apply_filters( 'glozin_status', 'waiting', $order, $product, $item ) );
			}

			$item->update_meta_data( '_glozin_item_preorder', apply_filters( 'glozin_item_preorder', 'yes', $item, $product, $order ) );
			$item->update_meta_data( '_glozin_item_status', apply_filters( 'glozin_item_status', 'waiting', $item, $product, $order ) );
			$item->save();

			// Add the item to the order meta '_glozin_pre_order_items' to easily identify the pre-orders inside the order.
			$pre_order_items = get_post_meta( $order->get_id(), '_glozin_pre_order_items', true );
			if ( ! $pre_order_items ) {
				$pre_order_items = array();
			}
			$pre_order_items[ $item_id ] = 'waiting';
			$order->update_meta_data( '_glozin_pre_order_items', apply_filters( 'glozin_pre_order_items', $pre_order_items, $order, $product, $item ) );

			$order->add_order_note(
				apply_filters(
					'glozin_pre_ordered_order_note',
					sprintf(
					// translators: %s: item name.
						esc_html__( 'Item %s was pre-ordered', 'glozin-addons' ),
						$product->get_formatted_name()
					),
					$order,
					$product,
					$item
				)
			);

			$order->save();
		}
	}

	/**
	 * Add pre-order meta to the order item.
	 *
	 * @param string $html The HTML output.
	 * @param WC_Order_Item $item The WC_Order_Item object.
	 * @param array $args The arguments.
	 */
	public function display_item_meta( $html, $item, $args ) {
		if ( method_exists( $item, 'get_product' ) ) {
			if ( Helper::is_pre_order_active( $item->get_product() ) ) {
				$html .= Helper::print_pre_order_meta( $item->get_product() );
			}
		}

		return $html;
	}

	/**
	 * Add pre-order flag on Orders page (My account).
	 *
	 * @param WC_Order $order The WC_Order object.
	 */
	public function add_pre_order_button_on_orders_page( $order ) {
		$has_pre_order = false;

		if ( $order instanceof \WC_Order ) {
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( Helper::is_pre_order_active( $item->get_product() ) ) {
					$has_pre_order = true;
					break;
				}
			}

			if( $has_pre_order ) {
				echo wp_kses_post( wc_get_order_status_name( $order->get_status() ) );
				$label  = apply_filters( 'glozin_pre_order_status_my_account_orders_label', esc_html__( 'Includes pre-order item(s)', 'glozin-addons' ), $order );
				$output = apply_filters( 'glozin_pre_order_status_my_account_orders_output', '<br><mark>' . $label . '</mark>', $order, $label );
				echo wp_kses_post( $output );
			} else {
				echo wp_kses_post( wc_get_order_status_name( $order->get_status() ) );
			}
		}
	}

	/**
	 * Add the pre-orders endpoint.
	 */
	public function custom_add_preorders_endpoint() {
		add_rewrite_endpoint( 'pre-orders', EP_ROOT | EP_PAGES );
	}

	/**
	 * Set the endpoint in the menu list from My account dashboard.
	 *
	 * @param array $items List of My account menu items.
	 *
	 * @return array
	 */
	public function new_menu_items( $items ) {
		if ( apply_filters( 'glozin_count_customer_orders', count( Helper::get_orders_by_customer( get_current_user_id() ) ) > 0 ) ) {
			// Remove the logout menu item.
			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );

			// Insert your custom endpoint.
			$items['pre-orders'] = esc_html__( 'My Pre-Orders', 'glozin-addons' );

			// Insert back the logout item.
			$items['customer-logout'] = $logout;
		}
		return $items;
	}

	/**
	 * Display the endpoint content.
	 */
	public function endpoint_content() {
		$orders = Helper::get_orders_by_customer( get_current_user_id() );

		do_action( 'glozin_my_account_my_pre_orders_before_content', $orders );

		wc_get_template(
			'myaccount/pre-orders.php',
			array( 'orders' => $orders ),
			'',
			GLOZIN_ADDONS_DIR . 'modules/pre-order/templates/'
		);

		do_action( 'glozin_my_account_my_pre_orders_after_content', $orders );
	}
}