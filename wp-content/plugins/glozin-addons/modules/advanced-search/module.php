<?php
/**
 * Glozin Addons Modules functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\Addons\Modules\Advanced_Search;

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
			'Glozin\Addons\Modules\Advanced_Search\Settings'        => GLOZIN_ADDONS_DIR . 'modules/advanced-search/settings.php',
			'Glozin\Addons\Modules\Advanced_Search\AJAX_Search'        => GLOZIN_ADDONS_DIR . 'modules/advanced-search/ajax-search.php',
			'Glozin\Addons\Modules\Advanced_Search\Posts'        => GLOZIN_ADDONS_DIR . 'modules/advanced-search/posts.php',
			'Glozin\Addons\Modules\Advanced_Search\Taxonomies'        => GLOZIN_ADDONS_DIR . 'modules/advanced-search/taxonomies.php',
			'Glozin\Addons\Modules\Advanced_Search\Catalog'        => GLOZIN_ADDONS_DIR . 'modules/advanced-search/catalog.php',
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
			\Glozin\Addons\Modules\Advanced_Search\Settings::instance();
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
		if ( get_option( 'glozin_ajax_search', 'yes' ) == 'yes' ) {
			\Glozin\Addons\Modules\Advanced_Search\AJAX_Search::instance();
		}

		\Glozin\Addons\Modules\Advanced_Search\Catalog::instance();
	}

}
