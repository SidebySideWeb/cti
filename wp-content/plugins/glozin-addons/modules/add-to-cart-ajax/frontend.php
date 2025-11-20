<?php

namespace Glozin\Addons\Modules\Add_To_Cart_Ajax;

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 25 );

        add_action( 'wc_ajax_glozin_add_to_cart_single_ajax', array( $this, 'add_to_cart_action' ) );
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
        wp_enqueue_script('glozin-add-to-cart-ajax', GLOZIN_ADDONS_URL . 'modules/add-to-cart-ajax/assets/add-to-cart-ajax' . $debug . '.js',  array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );

        $data = array(
            'add_to_cart_ajax' => get_option( 'add_to_cart_ajax', 'yes' ),
            'view_cart_text'   => esc_html__( 'View cart', 'glozin-addons' ),
            'view_cart_link'   => esc_url( wc_get_cart_url() )
        );

        wp_localize_script(
            'glozin-add-to-cart-ajax', 'glozinATCA', $data
        );
	}

    /**
	 * Add to cart action.
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false) URL to redirect to.
	 */
	public static function add_to_cart_action( $url = false ) {
		if ( ! isset( $_REQUEST['glozin-add-to-cart-ajax'] ) || ! is_numeric( wp_unslash( $_REQUEST['glozin-add-to-cart-ajax'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		wc_nocache_headers();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( wp_unslash( $_REQUEST['glozin-add-to-cart-ajax'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );

		if ( 'variable' === $add_to_cart_handler || 'variation' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_variable( $product_id );
		} elseif ( 'grouped' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_grouped( $product_id );
		} elseif ( has_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler ) ) {
			do_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler, $url ); // Custom handler.
		} else {
			$was_added_to_cart = self::add_to_cart_handler_simple( $product_id );
		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 === wc_notice_count( 'error' ) ) {
			do_action( 'glozin_ajax_add_to_cart', $was_added_to_cart );
			
			$data = self::get_refreshed_fragments();
			$url = apply_filters( 'woocommerce_add_to_cart_redirect', $url, $adding_to_cart );
			
			if ( empty($url) && 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				$url = wc_get_cart_url();
			}

			if( $url ) {
				$data['url'] = $url;
			}

			wp_send_json( $data );
		} else {
            $notice = WC()->session->get( 'wc_notices', array() )['error'][0]['notice'];
			wc_clear_notices();
			wp_send_json(
				array( 'error' =>  $notice )
			);
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
		return $data;
	}

	/**
	 * Handle adding simple products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_simple( $product_id ) {
		$quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && false !== $cart_key = WC()->cart->add_to_cart( $product_id, $quantity ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			return $cart_key;
		}
		return false;
	}

	/**
	 * Handle adding grouped products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_grouped( $product_id ) {
		$was_added_to_cart = false;
		$added_to_cart     = array();
		$items             = isset( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ? wp_unslash( $_REQUEST['quantity'] ) : array(); // phpcs: ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$variation_ids     = isset( $_REQUEST['variation_ids'] ) && is_array( $_REQUEST['variation_ids'] ) ? wp_unslash( $_REQUEST['variation_ids'] ) : array();
		$variation_id      = 0;
		$variation         = array();


		if ( ! empty( $items ) ) {
			$quantity_set = false;

			foreach ( $items as $item => $quantity ) {
				$quantity = wc_stock_amount( $quantity );
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation.
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

				$_product = null;
				// Support for grouped product with variation.
				if( ! empty( $variation_ids[$item] ) ) {
					$variation_id = $item;
					$_product     = wc_get_product( $variation_id );
					$item         = $_product->get_parent_id();
					$variation    = ! empty( $_REQUEST['attributes'][ $variation_id ] ) ? (array) json_decode( stripslashes( $_REQUEST['attributes'][ $variation_id ] ) ) : array();
					$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
				} else {
					$_product = wc_get_product( $item );
				}


				if( empty( $_product ) || ! $_product->is_in_stock() ) {
					continue;
				}

				// Suppress total recalculation until finished.
				remove_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );

				if ( $passed_validation && false !== WC()->cart->add_to_cart( $item, $quantity, $variation_id, $variation ) ) {
					$was_added_to_cart      = true;
					$added_to_cart[ $item ] = $quantity;
				}

				add_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' ), 'error' );
			} elseif ( $was_added_to_cart ) {
				wc_add_to_cart_message( $added_to_cart );
				WC()->cart->calculate_totals();
				return true;
			}
		} elseif ( $product_id ) {
			/* Link on product archives */
			wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'woocommerce' ), 'error' );
		}
		return false;
	}

	/**
	 * Handle adding variable products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @throws Exception If add to cart fails.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_variable( $product_id ) {
		$variation_id = empty( $_REQUEST['variation_id'] ) ? '' : absint( wp_unslash( $_REQUEST['variation_id'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$quantity     = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$variations   = array();

		$product = wc_get_product( $product_id );

		if ( isset( $_REQUEST['glozin_pbt_add_to_cart'] ) ) {
			$variations = json_decode( stripslashes( $_REQUEST['glozin_variation_attrs'] ) );
			$variations = (array) json_decode( $variations->$variation_id );
		} else {
			foreach ( $_REQUEST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( 'attribute_' !== substr( $key, 0, 10 ) ) {
					continue;
				}

				$variations[ sanitize_title( wp_unslash( $key ) ) ] = wp_unslash( $value );
			}
		}

		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		if ( ! $passed_validation ) {
			return false;
		}

		// Prevent parent variable product from being added to cart.
		if ( empty( $variation_id ) && $product && $product->is_type( 'variable' ) ) {
			/* translators: 1: product link, 2: product name */
			wc_add_notice( sprintf( __( 'Please choose product options by visiting <a href="%1$s" title="%2$s">%2$s</a>.', 'woocommerce' ), esc_url( get_permalink( $product_id ) ), esc_html( $product->get_name() ) ), 'error' );

			return false;
		}

		if ( false !== $cart_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			return $cart_key;
		}

		return false;
	}
}