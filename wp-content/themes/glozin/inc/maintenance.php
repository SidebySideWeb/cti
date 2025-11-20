<?php
/**
 * Glozin maintenance functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Glozin
 */

namespace Glozin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Glozin maintenance
 */
class Maintenance {
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
		add_action( 'template_redirect', array( $this, 'maintenance_redirect' ), 1 );
	}

	/**
	 * Redirect to the target page if the maintenance mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maintenance_redirect() {
		if ( ! Helper::get_option( 'maintenance_enable' ) ) {
			return;
		}

		if ( current_user_can( 'super admin' ) || current_user_can( 'administrator' ) ) {
			return;
		}

		$mode     = Helper::get_option( 'maintenance_mode' );
		$page_id  = Helper::get_option( 'maintenance_page' );
		$code     = 'maintenance' == $mode ? 503 : 200;
		$page_url = $page_id ? get_page_link( $page_id ) : '';

		// Use default message.
		if ( ! $page_id || ! $page_url ) {
			if ( 'coming_soon' == $mode ) {
				$message = sprintf( '<h1>%s</h1><p>%s</p>', esc_html__( 'Coming Soon', 'glozin' ), esc_html__( 'Our website is under construction. We will be here soon with our new awesome site.', 'glozin' ) );
			} else {
				$message = sprintf( '<h1>%s</h1><p>%s</p>', esc_html__( 'Website Under Maintenance', 'glozin' ), esc_html__( 'Our website is currently undergoing scheduled maintenance. Please check back soon.', 'glozin' ) );
			}

			wp_die( $message, get_bloginfo( 'name' ), array( 'response' => $code ) );
		}

		// Add body classes.
		add_filter( 'body_class', array( $this, 'maintenance_page_body_class' ) );

		// Redirect to the correct page.
		if ( ! is_page( $page_id ) ) {
			wp_redirect( $page_url );
			exit;
		} else {
			if ( ! headers_sent() ) {
				status_header( $code );
			}

			add_filter( 'glozin_get_header_layout', '__return_false' );
			add_filter( 'glozin_get_header_mobile_layout', '__return_false' );
			add_filter( 'glozin_get_topbar', '__return_false' );
			add_filter( 'glozin_get_campaign_bar', '__return_false' );
			add_filter( 'glozin_get_popup', '__return_false' );
			add_filter( 'glozin_get_footer_builder', '__return_false' );
			add_filter( 'glozin_ask_question_content', '__return_false' );
			add_filter( 'glozin_product_share_content', '__return_false' );
			add_filter( 'glozin_get_back_to_top', '__return_false' );
			add_filter( 'glozin_modals_items', '__return_false' );
			add_filter( 'glozin_panel_items', '__return_false' );

			if( class_exists( 'WCBoost\ProductsCompare\Frontend' ) ) {
				remove_action( 'wp_footer', array( \WCBoost\ProductsCompare\Frontend::instance(), 'compare_popup' ) );
			}
		}
	}

	/**
	 * Add classes for maintenance mode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function maintenance_page_body_class( $classes ) {
		if ( ! Helper::get_option( 'maintenance_enable' ) ) {
			return $classes;
		}

		if ( current_user_can( 'super admin' ) ) {
			return $classes;
		}

		$classes[] = 'maintenance-mode';

		if ( $this->is_maintenance_page() ) {
			$classes[] = 'maintenance-page';
		}

		return $classes;
	}


	/**
	 * Check is maintenance page
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_maintenance_page() {
		if ( ! Helper::get_option( 'maintenance_enable' ) ) {
			return false;
		}

		if ( current_user_can( 'super admin' ) ) {
			return false;
		}

		$page_id = Helper::get_option( 'maintenance_page' );

		if ( ! $page_id ) {
			return false;
		}

		return is_page( $page_id );
	}
}
