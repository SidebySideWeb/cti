<?php
/**
 * Hooks of Account.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Account template.
 */
class My_Account {
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
		add_filter('glozin_site_content_container_class', array( $this, 'site_content_container_class' ));

		add_filter('get_the_archive_title', array( $this, 'page_header_title' ), 40);

		add_filter('body_class', array( $this, 'body_class' ));
	}

	public function site_content_container_class( $classes ) {
		$classes = 'container';

		return $classes;
	}

	/**
	 * Page Title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function page_header_title($title) {
		if( is_user_logged_in() ) {
			return $title;
		}

		if( function_exists('is_lost_password_page') && is_lost_password_page() ) {
			return esc_html__('Lost Password', 'glozin');
		}

		$mode = $_GET && isset( $_GET['mode'] ) ? $_GET['mode'] : '';
		if( $mode == 'register' ) {
			$title = esc_html__('Register', 'glozin');
		} elseif( empty( $mode ) || $mode == 'login' ) {
			$title = esc_html__('Login', 'glozin');
		}

		return $title;
	}

	/**
	 * Body Class
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function body_class( $classes ) {
		$mode = $_GET && isset( $_GET['mode'] ) ? $_GET['mode'] : '';
		if( $mode == 'register' ) {
			$classes[] = 'woocommerce-account-register';
		}

		return $classes;
	}
}
