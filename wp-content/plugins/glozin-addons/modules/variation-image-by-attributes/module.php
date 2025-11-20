<?php
/**
 * Variation Image By Attributes
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Variation_Image_By_Attributes;

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
			'Glozin\Addons\Modules\Variation_Image_By_Attributes\Settings'   => GLOZIN_ADDONS_DIR . 'modules/variation-image-by-attributes/settings.php',
			'Glozin\Addons\Modules\Variation_Image_By_Attributes\Frontend'   => GLOZIN_ADDONS_DIR . 'modules/variation-image-by-attributes/frontend.php',
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
			\Glozin\Addons\Modules\Variation_Image_By_Attributes\Settings::instance();
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
		if ( get_option( 'glozin_variation_image_by_attributes' ) == 'yes' ) {
			\Glozin\Addons\Modules\Variation_Image_By_Attributes\Frontend::instance();
		}
	}

}
