<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Popup;

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
		add_action('template_redirect', array( $this, 'content'));
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
			'Glozin\Addons\Modules\Popup\FrontEnd'           => GLOZIN_ADDONS_DIR . 'modules/popup/frontend.php',
			'Glozin\Addons\Modules\Popup\Settings'           => GLOZIN_ADDONS_DIR . 'modules/popup/settings.php',
			'Glozin\Addons\Modules\Popup\Elementor_Settings' => GLOZIN_ADDONS_DIR . 'modules/popup/elementor-settings.php',
			'Glozin\Addons\Modules\Popup\Post_Type'          => GLOZIN_ADDONS_DIR . 'modules/popup/post-type.php',
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
		if( is_admin() ) {
			\Glozin\Addons\Modules\Popup\Settings::instance();
			if( class_exists('Elementor\Core\Base\Module') ) {
				\Glozin\Addons\Modules\Popup\Elementor_Settings::instance();
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
		if( get_option('glozin_popup_enable', 'yes') == 'yes' ) {
			\Glozin\Addons\Modules\Popup\Post_Type::instance();
		}
	}

	/**
	 * Single Product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function content() {
		if ( is_customize_preview() || get_option('glozin_popup_enable', 'yes') !== 'yes' ) {
			return;
		}

		\Glozin\Addons\Modules\Popup\FrontEnd::instance();
	}
}
