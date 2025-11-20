<?php
namespace Glozin\Addons\Elementor\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Shoppable_Images {

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
		add_action( 'wc_ajax_load_shoppable_images_elementor', [ $this, 'ajax_shoppable_images' ] );
	}

	/**
	 * Ajax load shoppable images
	 */
	public function ajax_shoppable_images() {
		if ( empty( $_POST['shoppable_images_id'] ) ) {
			wp_send_json_error( esc_html__( 'No shoppable images id.', 'glozin-addons' ) );
			exit;
		}

		$elementor_instance = \Elementor\Plugin::instance();
		$output = $elementor_instance->frontend->get_builder_content_for_display( absint( $_POST['shoppable_images_id'] ), true );

		wp_send_json_success( $output );
	}
}