<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Linked_Variant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Module {

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
		$this->includes();
		$this->actions();
		add_action('admin_init', array( $this, 'settings'));
	}

	/**
	 * Includes files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		\Glozin\Addons\Auto_Loader::register( [
			'Glozin\Addons\Modules\Linked_Variant\Post_Type' => GLOZIN_ADDONS_DIR . 'modules/linked-variant/post-type.php',
			'Glozin\Addons\Modules\Linked_Variant\Meta_Box'  => GLOZIN_ADDONS_DIR . 'modules/linked-variant/meta-box.php',
			'Glozin\Addons\Modules\Linked_Variant\Frontend'  => GLOZIN_ADDONS_DIR . 'modules/linked-variant/frontend.php',
			'Glozin\Addons\Modules\Linked_Variant\Settings'  => GLOZIN_ADDONS_DIR . 'modules/linked-variant/settings.php',
		] );
	}

	/**
	 * Settings
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings() {
		if ( is_admin() ) {
			\Glozin\Addons\Modules\Linked_Variant\Settings::instance();
			if ( get_option( 'glozin_linked_variant' ) == 'yes' ) {
				\Glozin\Addons\Modules\Linked_Variant\Meta_Box::instance();
			}
		}
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function actions() {
		if ( get_option( 'glozin_linked_variant' ) == 'yes' ) {
			\Glozin\Addons\Modules\Linked_Variant\Post_Type::instance();
			\Glozin\Addons\Modules\Linked_Variant\Frontend::instance();
		}

	}

}
