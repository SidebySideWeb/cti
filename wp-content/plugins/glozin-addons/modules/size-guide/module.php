<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Size_Guide;

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
		$this->add_actions();
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
			'Glozin\Addons\Modules\Size_Guide\Settings'    	=> GLOZIN_ADDONS_DIR . 'modules/size-guide/settings.php',
			'Glozin\Addons\Modules\Size_Guide\Frontend'    	=> GLOZIN_ADDONS_DIR . 'modules/size-guide/frontend.php',
		] );

	}


	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	*
	 * @return void
	 */
	protected function add_actions() {
		\Glozin\Addons\Modules\Size_Guide\Settings::instance();
		if ( get_option( 'glozin_size_guide', 'yes' ) == 'yes' && ! is_customize_preview() ) {
			\Glozin\Addons\Modules\Size_Guide\Frontend::instance();
		}
	}

}
