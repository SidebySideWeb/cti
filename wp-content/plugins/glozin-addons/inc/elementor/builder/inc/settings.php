<?php

namespace Glozin\Addons\Elementor\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Glozin\Addons\Elementor\Builder\Helper;

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

	const POST_TYPE     = 'glozin_builder';
	const OPTION_NAME   = 'glozin_builder';
	const TAXONOMY_TYPE = 'glozin_builder_type';
	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 50 );
		add_action( 'admin_init', array( $this, 'register_theme_builder_settings' ) );

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		add_action( 'wp_ajax_glozin_save_builder_enable', array( $this, 'save_builder_enable' ) );
		add_action( 'wp_ajax_glozin_builder_template_type', array( $this, 'builder_template_type' ) );

		// Sortable columns
		add_action( 'wp_ajax_glozin_sortable_builder', array( $this, 'sortable_builder' ) );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 99, 2 );

		// Add custom post type to screen ids
		add_filter('woocommerce_screen_ids', array( $this, 'wc_screen_ids' ) );

		add_filter( 'views_edit-' . self::POST_TYPE, array($this, 'admin_print_tabs' ) );

		add_filter( 'parse_query', array( $this, 'query_filter' ) );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Add meta boxes.
		add_action( 'save_post', array( $this, 'delete_builder_cache' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'delete_builder_cache' ), 10, 1 );
		add_action( 'before_delete_post', array( $this, 'delete_builder_cache' ), 10, 1 );

		add_action( 'admin_footer', array( $this, 'template_type_popup' ) );
	}

	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_submenu_page(
			'glozin_dashboard',
			esc_html__( 'Theme Builder', 'glozin-addons' ),
			esc_html__( 'Theme Builder', 'glozin-addons' ),
			'manage_options',
			'theme_builder_settings',
			array($this, 'theme_builder_settings')
		);
	}

	/**
	 * Register theme builder settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function theme_builder_settings() {
		echo '<form action="options.php" method="post">';
		settings_fields("theme_builder_settings");
		do_settings_sections( 'theme_builder_settings' );
		submit_button();
		echo '</form>';
	}

	/**
	 * Register glozin builder settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_theme_builder_settings( $columns ) {
		add_settings_section(
			'glozin_builder_section',
			'',
			array($this, 'glozin_builder_section_html'),
			'theme_builder_settings'
		);
		register_setting(
			'theme_builder_settings',
			'glozin_product_builder_enable',
			'string'
		);
		add_settings_field(
			'glozin_product_builder_enable',
			esc_html__( 'Single Product Builder', 'glozin-addons' ),
			array( $this, 'glozin_single_builder_enable_field_html' ),
			'theme_builder_settings',
			'glozin_builder_section'
		);
		register_setting(
			'theme_builder_settings',
			'glozin_product_archive_builder_enable',
			'string'
		);
		add_settings_field(
			'glozin_product_archive_builder_enable',
			esc_html__( 'Product Archive Builder', 'glozin-addons' ),
			array( $this, 'glozin_archive_builder_enable_field_html' ),
			'theme_builder_settings',
			'glozin_builder_section'
		);
		/* register_setting(
			'theme_builder_settings',
			'glozin_cart_page_builder_enable',
			'string'
		);
		add_settings_field(
			'glozin_cart_page_builder_enable',
			esc_html__( 'Cart Page Builder', 'glozin-addons' ),
			array( $this, 'glozin_cart_builder_enable_field_html' ),
			'theme_builder_settings',
			'glozin_builder_section'
		);
		register_setting(
			'theme_builder_settings',
			'glozin_checkout_page_builder_enable',
			'string'
		);
		add_settings_field(
			'glozin_checkout_page_builder_enable',
			esc_html__( 'Checkout Page Builder', 'glozin-addons' ),
			array( $this, 'glozin_checkout_builder_enable_field_html' ),
			'theme_builder_settings',
			'glozin_builder_section'
		); */
		register_setting(
			'theme_builder_settings',
			'glozin_404_page_builder_enable',
			'string'
		);
		add_settings_field(
			'glozin_404_page_builder_enable',
			esc_html__( '404 Page Builder', 'glozin-addons' ),
			array( $this, 'glozin_404_builder_enable_field_html' ),
			'theme_builder_settings',
			'glozin_builder_section'
		);
	}

	public function glozin_single_builder_enable_field_html() {
		$default = 1;
		$enable = get_option( 'glozin_product_builder_enable' );
		$disable = '';
		$notice = '';
		if ( class_exists('\Elementor\Plugin') && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$disable = ! $enable ? 'disabled' : '';
			$notice = sprintf('<p>%s</p>', esc_html('In order to use Glozin Builder, first you need to active Container in Elementor. Go to Elementor > Settings > Features > Container to select active option.', 'glozin-addons'));
			$notice .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=elementor#tab-experiments')), esc_html('Active Elementor Container', 'glozin-addons'));
		}
		?>
		<input id="glozin_product_builder_enable_checkbox" type="checkbox" name="glozin_product_builder_enable" <?php checked( $default, $enable ); ?> <?php echo esc_attr( $disable ); ?> value="<?php echo $default; ?>">
		<label for="glozin_product_builder_enable_checkbox"><?php esc_html_e('Enable', 'glozin-addons'); ?></label>
		<?php
		echo $notice;
		?>
		<?php
	}

	public function glozin_archive_builder_enable_field_html() {
		$default = 1;
		$enable = get_option( 'glozin_product_archive_builder_enable' );
		$disable = '';
		$notice = '';
		if ( class_exists('\Elementor\Plugin') && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$disable = ! $enable ? 'disabled' : '';
			$notice = sprintf('<p>%s</p>', esc_html('In order to use Glozin Builder, first you need to active Container in Elementor. Go to Elementor > Settings > Features > Container to select active option.', 'glozin-addons'));
			$notice .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=elementor#tab-experiments')), esc_html('Active Elementor Container', 'glozin-addons'));
		}
		?>
		<input id="glozin_product_archive_builder_enable_checkbox" type="checkbox" name="glozin_product_archive_builder_enable" <?php checked( $default, $enable ); ?> <?php echo esc_attr( $disable ); ?> value="<?php echo $default; ?>">
		<label for="glozin_product_archive_builder_enable_checkbox"><?php esc_html_e('Enable', 'glozin-addons'); ?></label>
		<?php
		echo $notice;
		?>
		<?php
	}

	public function glozin_cart_builder_enable_field_html() {
		$default = 1;
		$enable = get_option( 'glozin_cart_page_builder_enable' );
		$disable = '';
		$notice = '';
		if ( class_exists('\Elementor\Plugin') && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$disable = ! $enable ? 'disabled' : '';
			$notice = sprintf('<p>%s</p>', esc_html('In order to use Glozin Builder, first you need to active Container in Elementor. Go to Elementor > Settings > Features > Container to select active option.', 'glozin-addons'));
			$notice .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=elementor#tab-experiments')), esc_html('Active Elementor Container', 'glozin-addons'));
		}
		?>
		<p>
			<input id="glozin_cart_page_builder_enable_checkbox" type="checkbox" name="glozin_cart_page_builder_enable" <?php checked( $default, $enable ); ?> <?php echo esc_attr( $disable ); ?> value="<?php echo $default; ?>">
			<label for="glozin_cart_page_builder_enable_checkbox"><?php esc_html_e('Enable', 'glozin-addons'); ?></label>
		</p>
		<?php
		echo $notice;
	}

	public function glozin_checkout_builder_enable_field_html() {
		$default = 1;
		$enable = get_option( 'glozin_checkout_page_builder_enable' );
		$disable = '';
		$notice = '';
		if ( class_exists('\Elementor\Plugin') && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$disable = ! $enable ? 'disabled' : '';
			$notice = sprintf('<p>%s</p>', esc_html('In order to use Glozin Builder, first you need to active Container in Elementor. Go to Elementor > Settings > Features > Container to select active option.', 'glozin-addons'));
			$notice .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=elementor#tab-experiments')), esc_html('Active Elementor Container', 'glozin-addons'));
		}
		?>
		<p>
			<input id="glozin_checkout_page_builder_enable_checkbox" type="checkbox" name="glozin_checkout_page_builder_enable" <?php checked( $default, $enable ); ?> <?php echo esc_attr( $disable ); ?> value="<?php echo $default; ?>">
			<label for="glozin_checkout_page_builder_enable_checkbox"><?php esc_html_e('Enable', 'glozin-addons'); ?></label>
		</p>
		<?php
		echo $notice;
	}

	public function glozin_404_builder_enable_field_html() {
		$default = 1;
		$enable = get_option( 'glozin_404_page_builder_enable' );
		$disable = '';
		$notice = '';
		if ( class_exists('\Elementor\Plugin') && ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$disable = ! $enable ? 'disabled' : '';
			$notice = sprintf('<p>%s</p>', esc_html('In order to use Glozin Builder, first you need to active Container in Elementor. Go to Elementor > Settings > Features > Container to select active option.', 'glozin-addons'));
			$notice .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=elementor#tab-experiments')), esc_html('Active Elementor Container', 'glozin-addons'));
		}
		?>
		<p>
			<input id="glozin_404_page_builder_enable_checkbox" type="checkbox" name="glozin_404_page_builder_enable" <?php checked( $default, $enable ); ?> <?php echo esc_attr( $disable ); ?> value="<?php echo $default; ?>">
			<label for="glozin_404_page_builder_enable_checkbox"><?php esc_html_e('Enable', 'glozin-addons'); ?></label>
		</p>
		<?php
		echo $notice;
	}

	public function glozin_builder_section_html() {
		echo '<hr/><h2>' . esc_html__('Glozin Builder', 'glozin-addons') . '</h2>';
	}

	/**
	 * Add custom column to builders management screen
	 * Add Thumbnail column
     *
	 * @since 1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		$new_columns = array_merge( $columns, array(
			'builder_type'   => esc_html__( 'Type', 'glozin-addons' ),
			'include_pages'  => esc_html__( 'Include', 'glozin-addons' ),
			'exclude_pages'  => esc_html__( 'Exclude', 'glozin-addons' ),
			'enable_builder' => esc_html__( 'Enable Builder', 'glozin-addons' ),
		) );

		unset( $new_columns['date'] );

		if ( isset( $columns['date'] ) ) {
			$new_columns['date'] = $columns['date'];
			unset( $columns['date'] );
		}

		return array_merge( $new_columns, $columns );
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
		$terms = get_the_terms( $post_id, self::TAXONOMY_TYPE );
		$settings = Helper::get_template_settings( $post_id );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return;
		}
		$checked = false;
		$type = '';
		foreach ( $terms as $term ) {
			if( $term->slug == 'enable' ) {
				$checked = true;
			} elseif( $term->slug == 'footer' ) {
				$type = 'footer';
			} elseif( $term->slug == 'navigation_bar' ) {
				$type = 'navigation_bar';
			} elseif( $term->slug == 'product' ) {
				$type = 'product';
			} elseif( $term->slug == 'archive' ) {
				$type = 'archive';
			} elseif( $term->slug == 'cart_page' ) {
				$type = 'cart_page';
			} elseif( $term->slug == 'checkout_page' ) {
				$type = 'checkout_page';
			} elseif( $term->slug == '404_page' ) {
				$type = '404_page';
			}
		}
		switch ( $column ) {
			case 'builder_type':
				if( $type == 'footer' ) {
					esc_html_e('Footer', 'glozin-addons');
				}

				if( $type == 'navigation_bar' ) {
					esc_html_e('Navigation Bar', 'glozin-addons');
				}

				if( $type == 'product' ) {
					esc_html_e('Product', 'glozin-addons');
				}

				if( $type == 'archive' ) {
					esc_html_e('Archive', 'glozin-addons');
				}

				if( $type == 'cart_page' ) {
					esc_html_e('Cart Page', 'glozin-addons');
				}

				if( $type == 'checkout_page' ) {
					esc_html_e('Checkout Page', 'glozin-addons');
				}

				if( $type == '404_page' ) {
					esc_html_e('404 Page', 'glozin-addons');
				}
				break;
			case 'include_pages':
				if( $type == 'footer' || $type == 'product' || $type == 'archive' || $type == 'navigation_bar' ) {
					$text = $type == 'product' ? esc_html__( 'Products: ', 'glozin-addons' ) : esc_html__( 'Pages: ', 'glozin-addons' );

					if( empty( $settings['include_page_ids'] ) ) {
						echo '<strong>'. $text .'</strong>' . esc_html__( 'All', 'glozin-addons' );
					} else {
						$args = [];
						foreach( array_filter( explode( ',', $settings['include_page_ids'] ) ) as $id ) {
							$args[] = get_the_title( $id );
						}

						echo '<strong>'. $text .'</strong>' . implode( ', ', $args );
					}

					if( $type == 'product' && ! empty( $settings['product_cat_include'] ) ) {
						$args = [];
						foreach( array_filter( explode( ',', $settings['product_cat_include'] ) ) as $slug ) {
							$args[] = get_term_by( 'slug', $slug, 'product_cat' )->name;
						}

						echo '<br/><strong>'. esc_html__( 'Categories: ', 'glozin-addons' ) .'</strong>' . implode( ', ', $args );
					}
				}
				break;
			case 'exclude_pages':
				if( ! empty( $settings['exclude_page_ids'] ) ) {
					$args = [];
					foreach( array_filter( explode( ',', $settings['exclude_page_ids'] ) ) as $id ) {
						$args[] = get_the_title( $id );
					}

					echo implode( ', ', $args );
				}
				break;
			case 'enable_builder':
				$checked = $checked ? 'checked="checked"' : '';
				echo '<div class="glozin-builder__toggle-button">';
				echo sprintf(
					'<input type="checkbox" id="builder_enabled_%1$s" class="glozin-builder__enabled" %2$s data-nonce="glozin_save_enabled_state_%1$s" data-builder-id="%1$s">',
					esc_attr( $post_id ),
					$checked
				);
				echo '<label for="glozin_builder_enable'. esc_attr( $post_id ) .'" aria-label="Switch to enable builder"></label>';
				echo '</div>';
				break;
		}
	}

	/**
	 * Sets the enabled meta field to on or off
	 *
	 */
	public static function save_builder_enable() {
		if ( empty( $_POST['post_ID'] ) ) {
			wp_die( -1 );
		}

		$post_ID  = absint( $_POST['post_ID'] );
		$enabled = absint( $_POST['enabled'] ) == 1 ? 'yes' : 'no';
		$terms = get_the_terms( $post_ID, self::TAXONOMY_TYPE );
		$new_builder_type = '';
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				if( $term->slug == 'enable' && $enabled == 'no' ) {
					continue;
				}

				$new_builder_type .= !empty( $new_builder_type ) ? ',' : '';
				$new_builder_type .= $term->slug;
			}

			if( $enabled == 'yes' ) {
				$new_builder_type .= !empty( $new_builder_type ) ? ',' : '';
				$new_builder_type .= 'enable';
			}
		}

		wp_set_post_terms( $post_ID, $new_builder_type, self::TAXONOMY_TYPE );
		do_action('glozin_after_builder_enable');
		wp_send_json_success();
	}

	public function builder_template_type() {
		if ( ! isset( $_POST ) ) {
			$errormessage = array(
				'message'  => __('Post request dose not found','glozin-addons')
			);
			wp_send_json_error( $errormessage );
		}

		if( !(current_user_can('manage_options') || current_user_can('edit_others_posts')) ){
			$errormessage = array(
				'message'  => __('You are unauthorize to create a template','glozin-addons')
			);
			wp_send_json_error( $errormessage );
		}

		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'glozin_buider_new_template' ) ) {
			$errormessage = array(
				'message'  => __('Nonce Varification Faild','glozin-addons')
			);
			wp_send_json_error( $errormessage );
		}

		$post_title 		= ! empty( $_POST['post_title'] ) ? sanitize_text_field( $_POST['post_title'] ) : '';
		$template_type 		= ! empty( $_POST['template_type'] ) ? sanitize_text_field( $_POST['template_type'] ) : '';
		$enable_builder 		= ! empty( $_POST['enable_builder'] ) ? sanitize_text_field( $_POST['enable_builder'] ) : '';

		$args = [
			'template_type'  => $template_type,
			'post_title'   => $post_title,
			'enable_builder' => $enable_builder
		];
		$this->insert_template( $args );
	}

	public function insert_template($args) {
		$args['post_type'] = self::POST_TYPE;
		$args['post_status'] = 'publish';
		$new_post_id = wp_insert_post( $args );

		if( $new_post_id ) {
			$template_type = $args['template_type'];
			if( !empty( $args['enable_builder'] ) && $args['enable_builder'] == 'yes' ) {
				$template_type .= ', enable';

				do_action('glozin_after_builder_enable');
			}
			wp_set_post_terms( $new_post_id, $template_type, self::TAXONOMY_TYPE );

			$response = array(
				'id'       => $new_post_id,
			);

			wp_send_json_success( $response );
		} else {
			$errormessage = array(
				'message'  => __('There is an error while create a template. Try it again','glozin-addons')
			);
			wp_send_json_error( $errormessage );
		}
	}

	/**
	 * Sortable
	 *
	 * @return void
	 */
	public function sortable_builder() {
		global $wpdb, $userdata;

		$paged = filter_var( sanitize_text_field( wp_unslash( $_POST['paged'] ) ), FILTER_SANITIZE_NUMBER_INT );
		parse_str( sanitize_text_field( wp_unslash( $_POST['order'] ) ), $data );

		if ( ! is_array($data) || count($data) < 1 ) {
			die();
		}

		//retrieve a list of all objects
		$mysql_query = $wpdb->prepare("SELECT ID FROM ". $wpdb->posts ." WHERE post_type = %s AND post_status IN ('publish', 'pending', 'draft', 'private', 'future', 'inherit') ORDER BY menu_order, post_date DESC", self::POST_TYPE );

		$results = $wpdb->get_results($mysql_query);

		if ( ! is_array($results) || count($results) < 1 ) {
			die();
		}

		//create the list of ID's
		$ids = array();
		foreach( $results as $result ) {
			$ids[] = absint( $result->ID );
		}

		$per_page = get_user_meta( $userdata->ID ,'edit_' .  self::POST_TYPE  .'_per_page', TRUE );
		$per_page = apply_filters( 'edit_{' . self::POST_TYPE . '}_per_page', $per_page );

		if( empty( $per_page ) ) {
			$per_page = 20;
		}

		$edit_start_at = ( $paged * $per_page ) - $per_page;
		$index         = 0;
		for( $i = $edit_start_at; $i < ( $edit_start_at + $per_page ); $i++ ) {
			if( ! isset( $ids[$i] ) ) {
				break;
			}

			$ids[$i] = absint( $data['post'][$index] );
			$index++;
		}

		//update the menu_order within database
		foreach( $ids as $menu_order => $id ) {
			$data = array(
					'menu_order' => $menu_order
				);

			$wpdb->update( $wpdb->posts, $data, array( 'ID' => $id ) );

			clean_post_cache( $id );
		}

		wp_send_json_success();
	}

	/**
	 * Posts OrderBy filter
	 *
	 * @param mixed $orderBy
	 * @param mixed $query
	 */
	public function posts_orderby( $orderBy, $query ) {
		global $wpdb;

		if ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'glozin_builder' ) {
			if( trim( $orderBy ) == '' ) {
				$orderBy = "{$wpdb->posts}.menu_order";
			} else {
				$orderBy = "{$wpdb->posts}.menu_order, " . $orderBy;
			}
		}

		return $orderBy;
	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function wc_screen_ids($screen_ids) {
		$screen_ids[] = 'glozin_builder';

		return $screen_ids;
	}

	public function admin_scripts( $hook ) {
		$screen = get_current_screen();

		if ( in_array( $hook, array('edit.php', 'post-new.php', 'post.php' ) ) && self::POST_TYPE == $screen->post_type ) {
			wp_enqueue_style( 'glozin-builder', GLOZIN_ADDONS_URL . 'inc/elementor/builder/assets/css/admin.css' );
			wp_enqueue_script( 'glozin-builder', GLOZIN_ADDONS_URL . 'inc/elementor/builder/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ),'1.0', true );
		}
	}

    public function admin_print_tabs( $views ) {
		$active_class = 'nav-tab-active';
		$current_type = '';
		if( isset( $_GET['template_type'] ) ){
			$active_class = '';
			$current_type = sanitize_key( $_GET['template_type'] );
		}
        ?>
            <div id="glozin-template-tabs-wrapper" class="nav-tab-wrapper">
				<div class="glozin-menu-area">
					<a class="nav-tab <?php echo esc_attr($active_class); ?>" href="edit.php?post_type=<?php echo esc_attr(self::POST_TYPE); ?>"><?php echo __('All','glozin-addons');?></a>
					<?php
						foreach( self::get_template_type() as $tabkey => $tabname ){
							$active_class = ( $current_type == $tabkey ? 'nav-tab-active' : '' );
							echo '<a class="nav-tab ' . $active_class . '" href="edit.php?post_type=' . self::POST_TYPE . '&template_type=' .$tabkey .'">' . $tabname . '</a>';
						}
					?>
				</div>
            </div>
        <?php
		return $views;
    }

	public function query_filter( \WP_Query $query ) {
		if ( ! is_admin() || ! $this->is_current_screen() ) {
			return;
		}

		if( isset( $_GET['template_type'] ) && $_GET['template_type'] != '' && $_GET['template_type'] != 'all') {
			$type = sanitize_key( $_GET['template_type'] ) ;
			$taxquery = array(
				array(
					'taxonomy' => self::TAXONOMY_TYPE,
					'field' => 'slug',
					'terms' => array($type),
					'operator'=> 'IN'
				)
			);
			$query->set('tax_query', $taxquery);
		}
	}


	private function is_current_screen() {
		global $pagenow, $typenow;
		return 'edit.php' === $pagenow && self::POST_TYPE === $typenow;
	}

	public static function get_template_type(){
		$args = array(
			'footer'        => esc_html__('Footer','glozin-addons'),
		);

		$args['navigation_bar'] = esc_html__('Navigation Bar','glozin-addons');

		if( get_option( 'glozin_product_builder_enable', false ) ) {
			$args['product'] = esc_html__('Single Product','glozin-addons');
		}

		if( get_option( 'glozin_product_archive_builder_enable', false ) ) {
			$args['archive'] = esc_html__('Product Archive','glozin-addons');
		}

		if( get_option( 'glozin_cart_page_builder_enable', false ) ) {
			$args['cart_page'] = esc_html__('Cart Page','glozin-addons');
		}

		if( get_option( 'glozin_checkout_page_builder_enable', false ) ) {
			$args['checkout_page'] = esc_html__('Checkout Page','glozin-addons');
		}

		if( get_option( 'glozin_404_page_builder_enable', false ) ) {
			$args['404_page'] = esc_html__('404 Page','glozin-addons');
		}

		return $args;
	}

	/**
	 * Clear popup ids
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function delete_builder_cache($post_id) {
		$terms = get_the_terms( $post_id, self::TAXONOMY_TYPE );
		$terms = ! is_wp_error( $terms ) &&  $terms ? wp_list_pluck($terms, 'slug') : '';
	}

	public function template_type_popup() {
		if( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'glozin_builder' ){
			include_once(GLOZIN_ADDONS_DIR . 'inc/elementor/builder/templates/template_type_popup.php' );
		}
    }
}