<?php

namespace Glozin\Addons\Modules\People_View_Fake;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'people_view_fake' ), 22 );
		add_action( 'glozin_people_view_fake_elementor', array( $this, 'people_view_fake' ), 15 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-people-view', GLOZIN_ADDONS_URL . 'modules/people-view-fake/assets/people-view-fake' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_enqueue_script('glozin-people-view', GLOZIN_ADDONS_URL . 'modules/people-view-fake/assets/people-view-fake' . $debug . '.js',  array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );
		$datas = array(
			'interval' => get_option( 'glozin_people_view_fake_interval', 6000 ),
			'from'     => get_option( 'glozin_people_view_fake_random_numbers_from', 1 ),
			'to'       => get_option( 'glozin_people_view_fake_random_numbers_to', 100 ),
		);

		wp_localize_script(
			'glozin-people-view', 'glozinPVF', $datas
		);
	}

	/**
	 * Get people view fake
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function people_view_fake() {
		$from 	= get_option( 'glozin_people_view_fake_random_numbers_form', 1 );
		$to   	= get_option( 'glozin_people_view_fake_random_numbers_to', 100 );
		?>
			<div class="glozin-people-view d-flex align-items-center gap-10">
				<span class="glozin-people-view__icon">
					<?php echo \Glozin\Addons\Helper::get_svg( 'eye' ); ?>
				</span>
				<span class="glozin-people-view__text text-dark"><span class="glozin-people-view__numbers"><?php echo rand( $from, $to ); ?></span><?php echo apply_filters( 'glozin_people_view_fake_text', esc_html__( 'peoples are viewing this right now', 'glozin-addons' ) );?></span>
			</div>
		<?php
	}
}