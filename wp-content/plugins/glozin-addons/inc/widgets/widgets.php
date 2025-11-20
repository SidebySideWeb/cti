<?php
/**
 * Load and register widgets
 *
 * @package Glozin
 */

namespace Glozin\Addons;
/**
 * Glozin theme init
 */
class Widgets {

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
		// Include plugin files
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}


	/**
	 * Register widgets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function register_widgets() {
		$this->includes();
		$this->add_actions();
	}

	/**
	 * Include Files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function includes() {
		\Glozin\Addons\Auto_Loader::register( [
			'Glozin\Addons\Widgets\Recent_Posts_Widget' => GLOZIN_ADDONS_DIR . 'inc/widgets/recent-posts.php',
			'Glozin\Addons\Widgets\Social_Links'        => GLOZIN_ADDONS_DIR . 'inc/widgets/socials.php',
			'Glozin\Addons\Widgets\Products_List'      => GLOZIN_ADDONS_DIR . 'inc/widgets/products-list.php',
		] );
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_actions() {
		register_widget( new \Glozin\Addons\Widgets\Recent_Posts_Widget() );
		register_widget( new \Glozin\Addons\Widgets\Social_Links() );
		register_widget( new \Glozin\Addons\Widgets\Products_List() );
	}

}