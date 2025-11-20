<?php
/**
 * Helper init
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Live_Sales_Notification;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper
 */
class Helper {

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
	 * Get popup html
	 *
	 * @return void
	 */
	public static function get_popup_html( $product ) {
        if( empty( $product ) || ! is_array( $product ) ) {
			return;
		}

		ob_start();

		wc_get_template(
			'live-sales-notification.php',
			array(
				'product'               => $product,
				'progress_bar_duration' => get_option( 'glozin_live_sales_notification_time_keep_opened', 6000 ),
			),
			'',
			GLOZIN_ADDONS_DIR . 'modules/live-sales-notification/templates/'
		);

		$output = ob_get_clean();

		return $output;
    }

	/**
	 * Format product object
	 *
	 * @return void
	 */
	public static function format_product_obj($product, $order) {
        if( ! is_object( $product ) ) {
			return;
		}

        if( ! ( is_object($order) && method_exists( $order, 'get_billing_first_name' ) && method_exists( $order, 'get_billing_city' ) && method_exists( $order, 'get_billing_state' ) && method_exists( $order, 'get_billing_country' ) && method_exists( $order, 'get_date_created' ) ) ) {
			return;
		}

        $link      = self::product_link( $product );
        $thumbnail = $product->get_image('woocommerce_gallery_thumbnail');

		$address_args = array();
		$time_passed = '';
		$time_type = array(
						'seconds' => esc_html__( 'seconds', 'glozin-addons' ),
						'minutes' => esc_html__( 'minutes', 'glozin-addons' ),
						'hours'   => esc_html__( 'hours', 'glozin-addons' ),
					);
		$time_passed_type = $time_type[get_option( 'glozin_live_sales_notification_time_passed_type')];

		$city    = $order->get_billing_city();
		$state   = isset(WC()->countries->countries[$order->get_billing_country()]) ? WC()->countries->get_states($order->get_billing_country())[$order->get_billing_state()] : $order->get_billing_state();
		$country = isset(WC()->countries->countries[$order->get_billing_country()]) ? WC()->countries->countries[$order->get_billing_country()] : $order->get_billing_country();

		if( ! empty( $city ) ) {
			$address_args['city'] = $city;
		}

		if( ! empty( $state ) ) {
			$address_args['state'] = $state;
		}

		if( ! empty( $country ) ) {
			$address_args['country'] = $country;
		}

		$address = ! empty( $address_args ) ? implode( ', ', $address_args ) : '';

		if ( $time_passed_type == 'hour' ) {
			$time_passed = rand( 1, 24 );
		} else {
			$time_passed = rand( 1, 59 );
		}

        $formated_product = array(
            'product_id'    	 => $product->get_id(),
            'product_name'  	 => self::get_title( $product->get_id() ),
            'product_thumb' 	 => $thumbnail,
            'product_link'  	 => $link,
            'first_name'    	 => $order->get_billing_first_name(),
			'address'	         => $address,
            'time'          	 => $order->get_date_created()->date( 'G:i' ),
            'date'          	 => $order->get_date_created()->date( 'Y/m/d' ),
			'time_passed'	     => $time_passed,
			'time_passed_type'	 => $time_passed_type,
        );

        return $formated_product;
    }

	/**
	 * Product link
	 *
	 * @return void
	 */
	public static function product_link( $product ) {
        if( $product->is_type('external') ) {
            $link = $product->get_product_url();

            if( empty($link) ) {
                $link = $product->get_permalink();
            }
        } else {
            $link = $product->get_permalink( );
        }

        return apply_filters( 'glozin_live_sales_notification_product_link', $link, $product );
    }

	/**
	 * Get Title
	 *
	 * @return void
	 */
    public static function get_title( $product_id ) {
        return get_the_title($product_id);
    }
}
