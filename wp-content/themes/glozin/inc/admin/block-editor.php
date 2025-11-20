<?php
/**
 * Block Editor functions
 *
 * @package Glozin
 */

namespace Glozin\Admin;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Block Editor
 *
 */
class Block_Editor {
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
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_style' ) );
	}

	/**
	 * Enqueue editor styles for Gutenberg
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 */
	public function block_editor_styles() {
		wp_enqueue_style( 'glozin-block-editor-style', get_template_directory_uri() . '/assets/css/editor-blocks.css' );
	}

	/**
	 * Load admin style
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 */
	public function load_admin_style() {
		wp_enqueue_style( 'glozin-admin-style', get_template_directory_uri() . '/assets/css/glozin-admin.css' );
	}
}
