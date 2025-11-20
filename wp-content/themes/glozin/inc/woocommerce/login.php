<?php
/**
 * Hooks of Login.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Login template.
 */
class Login {
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
		add_action( 'wp_ajax_nopriv_login_modal_authenticate', array( $this, 'login_modal_authenticate' ) );
		add_action( 'wp_ajax_nopriv_register_modal_authenticate', array( $this, 'register_modal_authenticate' ) );
	}

	function login_modal_authenticate() {
		if ( isset( $_POST['username'], $_POST['password'] ) ) {
			$creds = array(
				'user_login'    => trim( wp_unslash( $_POST['username'] ) ),
				'user_password' => $_POST['password'],
				'remember'      => isset( $_POST['rememberme'] ),
			);

			// Apply WooCommerce filters
			$validation_error = new \WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

			if ( $validation_error->get_error_code() ) {
				wp_send_json_error( $validation_error->get_error_message() );
			}

			if ( empty( $creds['user_login'] ) ) {
				wp_send_json_error( esc_html__( 'Username is required.', 'glozin' ) );
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				wp_send_json_error( $user->get_error_message() );
			} else {
				wp_send_json_success( __( 'You have logged in successfully.', 'glozin' ) );
			}
		}
	}

	function register_modal_authenticate() {
		if ( isset( $_POST['email'] ) ) {
			$username = 'no' === get_option( 'woocommerce_registration_generate_username' ) && isset( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$password = 'no' === get_option( 'woocommerce_registration_generate_password' ) && isset( $_POST['password'] ) ? $_POST['password'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$email    = wp_unslash( $_POST['email'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$validation_error  = new \WP_Error();
			$validation_error  = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );
			$validation_errors = $validation_error->get_error_messages();

			if ( 1 === count( $validation_errors ) ) {
				wp_send_json_error( $validation_error->get_error_message() );
			} elseif ( $validation_errors ) {
				$notices = '';
				foreach ( $validation_errors as $message ) {
					$notices .= '<strong>' . __( 'Error:', 'glozin' ) . '</strong> ' . $message;
				}

				wp_send_json_error($notices);
			}

			$new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

			if ( is_wp_error( $new_customer ) ) {
				wp_send_json_error( $new_customer->get_error_message() );
			}

			if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) {
				wc_set_customer_auth_cookie( $new_customer );
			}

			if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) ) {
				wp_send_json_success( __( 'Your account was created successfully and a password has been sent to your email address.', 'glozin' ) );
			} else {
				wp_send_json_success( __( 'Your account was created successfully. Your login details have been sent to your email address.', 'glozin' ) );
			}
		}
	}

}