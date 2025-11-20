<?php

namespace Glozin\Addons\Modules\Popup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Settings  {

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

	const POST_TYPE     = 'glozin_popup';
	const OPTION_NAME   = 'glozin_popup';
	const TAXONOMY_TAB_TYPE     = 'glozin_popup_type';


	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'glozin_get_sections_theme_features', array( $this, 'popup_section' ), 20, 2 );
		add_filter( 'glozin_get_settings_theme_features', array( $this, 'popup_settings' ), 20, 2 );

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		add_action( 'wp_ajax_glozin_save_popup_enable', array( $this, 'save_popup_enable' ) );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Add custom post type to screen ids
		add_filter('woocommerce_screen_ids', array( $this, 'wc_screen_ids' ) );

		add_action( 'wp_ajax_glozin_popup_ordering', array( $this, 'popup_ordering' ) );

		add_action( 'pre_get_posts', array( $this, 'popup_column_orderby' ) );
	}

	/**		
	 * Add popup section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function popup_section( $sections ) {
		$sections['popup'] = esc_html__( 'Popup', 'glozin-addons' );

		return $sections;
	}

	/**
	 * Add popup settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */	
	public function popup_settings( $settings, $section ) {
		if ( 'popup' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'glozin_popup_options',
				'title' => esc_html__( 'Popup', 'glozin-addons' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'glozin_popup_enable',
				'title'   => esc_html__( 'Enable popup', 'glozin-addons' ),
				'desc'    => esc_html__( 'Enable popup', 'glozin-addons' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			);


			$settings[] = array(
				'id'   => 'glozin_popup_options_end',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
	/**
	 * Add custom column to popups management screen
	 * Add Thumbnail column
     *
	 * @since 1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		$columns = array_merge( $columns, array(
			'popup_include_page' 	=> esc_html__( 'Include Pages', 'glozin-addons' ),
			'popup_exclude_page' 	=> esc_html__( 'Exclude Pages', 'glozin-addons' ),
			'popup_enabled' => esc_html__( 'Enabled', 'glozin-addons' ),
		) );

		return $columns;
	}

	/**
	 * Handle custom column display
     *
	 * @since 1.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_custom_columns( $column, $post_id ) {
		if( ! class_exists('\Elementor\Core\Settings\Manager') && ! method_exists('\Elementor\Core\Settings\Manager', 'get_settings_managers') ) {
			return;
		}
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_model = $page_settings_manager->get_model( $post_id );
		switch ( $column ) {
			case 'popup_enabled':
				$tab_enable_popup = $page_settings_model->get_settings( 'enable_popup' );
				$checked = ! empty($tab_enable_popup) ? 'checked="checked"' : '';
				echo '<div class="glozin-popup__toggle-button">';
				echo sprintf(
					'<input type="checkbox" id="popup_enabled_%1$s" class="glozin-popup__enabled" %2$s data-nonce="glozin_save_enabled_state_%1$s" data-popup-id="%1$s">',
					esc_attr( $post_id ),
					$checked
				);
				echo '<label for="glozin_popup_enable'. esc_attr( $post_id ) .'" aria-label="Switch to enable popup"></label>';
				echo '</div>';
				break;

			case 'popup_include_page':
				$page_ids = $page_settings_model->get_settings( 'popup_include_pages' );
				if( empty( $page_ids ) ) {
					echo esc_html__( 'All', 'glozin-addons' );
				} else {
					foreach( $page_ids as $page_id ) {
						echo get_the_title( $page_id ) . ', ';
					}
				}
				break;

			case 'popup_exclude_page':
				$page_ids = $page_settings_model->get_settings( 'popup_exclude_pages' );
				if( ! empty( $page_ids ) ) {
					foreach( $page_ids as $page_id ) {
						echo get_the_title( $page_id ) . ', ';
					}
				}
				break;
		}
	}

	/**
	 * Sets the enabled meta field to on or off
	 *
	 * @since 1.12.0
	 */
	public static function save_popup_enable() {
		if ( empty( $_POST['post_ID'] ) ) {
			wp_die( -1 );
		}

		$post_ID  = absint( $_POST['post_ID'] );
		$enabled = absint( $_POST['enabled'] ) == 1 ? 'yes' : '';
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_model = $page_settings_manager->get_model( $post_ID );
		$settings = $page_settings_model->get_settings();
		$settings['enable_popup'] = $enabled;
		$page_settings_manager->save_settings( $settings, $post_ID );
		$enabled = empty( $enabled ) ? 'no' : 'yes';
		update_post_meta( $post_ID, 'enable_popup',  $enabled );
		wp_send_json_success();
	}


	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function wc_screen_ids($screen_ids) {
		$screen_ids[] = 'glozin_popup';

		return $screen_ids;
	}

	/**
	 * Ajax request handling for product ordering.
	 *
	 * Based on Simple Page Ordering by 10up (https://wordpress.org/plugins/simple-page-ordering/).
	 */
	public static function popup_ordering() {
		global $wpdb;

		if ( empty( $_POST['id'] ) ) {
			wp_die( -1 );
		}

		$sorting_id  = absint( $_POST['id'] );
		$previd      = absint( isset( $_POST['previd'] ) ? $_POST['previd'] : 0 );
		$nextid      = absint( isset( $_POST['nextid'] ) ? $_POST['nextid'] : 0 );
		$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'glozin_popup' ORDER BY menu_order DESC" ), 'menu_order', 'ID' );
		$index       = count( $menu_orders ) + 1;

		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			}
			if ( $nextid === $id ) {
				$index --;
			}
			$index --;
			$menu_orders[ $id ] = $index;
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) );

			/**
			 * When a single product has gotten it's ordering updated.
			 * $id The product ID
			 * $index The new menu order
			*/
			do_action( 'glozin_after_single_popup_ordering', $id, $index );
		}

		if ( isset( $menu_orders[ $previd ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] - 1;
		} elseif ( isset( $menu_orders[ $nextid ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] + 1;
		} else {
			$menu_orders[ $sorting_id ] = 0;
		}


		$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) );

		do_action( 'glozin_after_popup_ordering', $sorting_id, $menu_orders );
		wp_send_json( $menu_orders );
	}

	/**
	 * Orderby popup
	 */
	function popup_column_orderby( $query ) {
		if( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if( $query->get('post_type') != self::POST_TYPE ) {
			return;
		}
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'DESC' );
	}


	/**
	 * Load scripts and style in admin area
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		$screen = get_current_screen();

		if ( in_array( $hook, array('edit.php', 'post-new.php', 'post.php' ) ) && self::POST_TYPE == $screen->post_type ) {
			wp_enqueue_style( 'glozin-popup', GLOZIN_ADDONS_URL . 'modules/popup/assets/admin/admin.css' );
			wp_enqueue_script( 'glozin-popup', GLOZIN_ADDONS_URL . 'modules/popup/assets/admin/admin.js', array( 'jquery', 'jquery-ui-sortable' ),'1.0', true );

		}

	}

}