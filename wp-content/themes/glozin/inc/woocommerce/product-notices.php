<?php
/**
 * WooCommerce Notices template hooks.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of WooCommerce Notices
 */

class Product_Notices {
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
        add_filter( 'glozin_wp_script_data', array( $this, 'notices_script_data' ) );
   }

    /**
    * Get notices script data
    *
    * @since 1.0.0
    *
    * @param $data
    *
    * @return array
    */
    public function notices_script_data( $data ) {
        $data['added_to_cart_notice'] = array(
            'added_to_cart_notice_layout' => Helper::get_option( 'added_to_cart_notice' ),
        );

        if ( intval( Helper::get_option( 'added_to_wishlist_notice' ) ) && shortcode_exists( 'wcboost_wishlist_button' ) ) {
            if( shortcode_exists( 'wcboost_wishlist_button' ) ) {
                $url = wc_get_page_permalink( 'wishlist' );
            }

            $data['added_to_wishlist_notice'] = array(
                'added_to_wishlist_text'    => esc_html__( 'has been added to your wishlist.', 'glozin' ),
                'added_to_wishlist_texts'   => esc_html__( 'have been added to your wishlist.', 'glozin' ),
                'wishlist_view_text'        => esc_html__( 'View Wishlist', 'glozin' ),
                'wishlist_view_link'        => esc_url( $url ),
                'wishlist_notice_auto_hide' => intval( Helper::get_option( 'wishlist_notice_auto_hide' ) ) > 0 ? intval( Helper::get_option( 'wishlist_notice_auto_hide' ) ) * 1000 : 0,
            );
        }

        if ( intval( Helper::get_option( 'added_to_compare_notice' ) ) && function_exists( 'wcboost_products_compare' ) && get_option( 'wcboost_products_compare_added_behavior' ) != 'popup' ) {
            $url = wc_get_page_permalink( 'compare' );

            $data['added_to_compare_notice'] = array(
                'added_to_compare_text'    => esc_html__( 'has been added to your compare.', 'glozin' ),
                'added_to_compare_texts'   => esc_html__( 'have been added to your compare.', 'glozin' ),
                'compare_view_text'        => esc_html__( 'View Compare', 'glozin' ),
                'compare_view_link'        => esc_url( $url ),
                'compare_notice_auto_hide' => intval( Helper::get_option( 'compare_notice_auto_hide' ) ) > 0 ? intval( Helper::get_option( 'compare_notice_auto_hide' ) ) * 1000 : 0,
            );
        }

        return $data;
    }
}
