<?php
/**
 * Posts functions and definitions.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Catalog;

use Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts initial
 *
 */
class Sidebar {
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
		// Sidebar
		add_filter( 'glozin_primary_sidebar_id', array( $this, 'sidebar_id' ), 10 );
		add_action( 'dynamic_sidebar_before', array( $this, 'catalog_sidebar_before_content' ) );
		add_action( 'dynamic_sidebar_after', array( $this, 'catalog_sidebar_after_content' ) );
	}

	/**
	 * Sidebar ID
	 *
	 * @return void
	 */
	public function sidebar_id( $sidebarID ) {
		if ( is_active_sidebar( 'catalog-filters-sidebar' ) ) {
			$sidebarID = 'filter-sidebar-panel';
		}

		return $sidebarID;
	}

	/**
	 * Add modal content before Widget Content
	 *
	 * @since 1.0.0
	 *
	 * @param $index
	 *
	 * @return void
	 */
	public function catalog_sidebar_before_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'catalog-filters-sidebar' ) {
			return;
		}

		if ( ! apply_filters( 'glozin_get_catalog_sidebar_before_content', true ) ) {
			return;
		}

		$classes = 'sidebar__button-close gz-button gz-button-icon gz-button-text position-absolute top-13 end-20 z-1';
		$classes .= Helper::get_option( 'product_filter_type' ) !== 'popup' ? ' d-none-xl' : '';

		?>
		<div class="sidebar__backdrop"></div>
        <div class="sidebar__container">
			<?php echo \Glozin\Icon::get_svg( 'close', 'ui', array( 'class' => $classes ) ); ?>
			<div class="sidebar__header d-none-xl h5 m-0 px-30 py-23 position-relative border-bottom d-flex align-items-center justify-content-between">
				<?php echo esc_html__( 'Filter', 'glozin' ); ?>
			</div>
			<div class="sidebar__content">
		<?php

	}

	/**
	 * Change blog sidebar after content
	 *
	 * @since 1.0.0
	 *
	 * @param $index
	 *
	 * @return void
	 */
	public function catalog_sidebar_after_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'catalog-filters-sidebar' ) {
			return;
		}

		if ( ! apply_filters( 'glozin_get_catalog_sidebar_before_content', true ) ) {
			return;
		}

		?>
        	</div>
        </div>
		<?php

	}

}