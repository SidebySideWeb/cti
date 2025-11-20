<?php

namespace Glozin\Addons\Modules\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Walker_Nav_Menu_Edit' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-walker-nav-menu-edit.php';
}

class Walker_Edit extends \Walker_Nav_Menu_Edit {
	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu_Edit::start_el()
	 *
	 * @global int $_wp_nav_menu_max_depth
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item. Used for padding.
	 * @param stdClass $args              Not used.
	 * @param int      $current_object_id Optional. ID of the current menu item. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		if ( 'custom' !== $data_object->type || 'megamenu' !== $data_object->object || empty( $data_object->megamenu_type ) ) {
			$data_object->classes = is_string( $data_object->classes ) ? (array) $data_object->classes : $data_object->classes;
			parent::start_el( $output, $data_object, $depth, $args, $current_object_id );
			return;
		}

		$menu_item    = $data_object;
		$item_id      = esc_attr( $menu_item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $menu_item->object ),
			'menu-item-widget-' . esc_attr( $menu_item->megamenu_type ),
			'menu-item-edit-inactive',
		);

		if ( isset( $menu_item->post_status ) && 'draft' === $menu_item->post_status ) {
			$classes[] = 'pending';
		}

		$title  = ! empty( $menu_item->label ) ? $menu_item->label : $menu_item->title;
		$data   = $data_object->_options ? $data_object->_options : array();
		$widget = $this->get_widget_object( $data_object->megamenu_type, $data );

		ob_start();
		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>" data-id="<?php echo esc_attr( $item_id ); ?>">
			<div class="menu-item-bar">
				<div class="menu-item-handle">
					<label class="item-title" for="menu-item-checkbox-<?php echo $item_id; ?>">
						<input id="menu-item-checkbox-<?php echo $item_id; ?>" type="checkbox" class="menu-item-checkbox" data-menu-item-id="<?php echo $item_id; ?>" disabled="disabled" />
						<span class="menu-item-title"><?php echo esc_html( $title ); ?></span>
						<span class="is-submenu" style="display: none;"><?php _e( 'sub item', 'glozin-addons' ); ?></span>
					</label>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $menu_item->type_label ); ?></span>
						<?php
						if ( isset( $_GET['edit-menu-item'] ) && $item_id === $_GET['edit-menu-item'] ) {
							$edit_url = admin_url( 'nav-menus.php' );
						} else {
							$edit_url = add_query_arg(
								array(
									'edit-menu-item' => $item_id,
								),
								remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) )
							);
						}

						printf(
							'<a class="item-edit" id="edit-%s" href="%s" aria-label="%s"><span class="screen-reader-text">%s</span></a>',
							$item_id,
							$edit_url,
							esc_attr__( 'Edit menu item', 'glozin-addons' ),
							__( 'Edit', 'glozin-addons' )
						);
						?>
					</span>
				</div>
			</div>

			<div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
				<?php
				if ( $widget ) {
					$widget->form();

					// Always include the default "classes" field.
					$widget->print_control( array(
						'type'        => 'text',
						'label'       => __( 'CSS Classes (optional)', 'glozin-addons' ),
						'name'        => 'classes',
						'class'       => 'field-css-classes',
						'description' => '',
						'value'       => $widget->get_data( 'classes' ),
					) );
				}
				?>

				<div class="menu-item-actions description-wide submitbox">
					<?php
					printf(
						'<a class="item-delete submitdelete deletion" id="delete-%s" href="%s">%s</a>',
						$item_id,
						wp_nonce_url(
							add_query_arg(
								array(
									'action'    => 'delete-menu-item',
									'menu-item' => $item_id,
								),
								admin_url( 'nav-menus.php' )
							),
							'delete-menu_item_' . $item_id
						),
						__( 'Remove', 'glozin-addons' )
					);
					?>
					<span class="meta-sep hide-if-no-js"> | </span>
					<?php
					printf(
						'<a class="item-cancel submitcancel hide-if-no-js" id="cancel-%s" href="%s#menu-item-settings-%s">%s</a>',
						$item_id,
						esc_url(
							add_query_arg(
								array(
									'edit-menu-item' => $item_id,
									'cancel'         => time(),
								),
								admin_url( 'nav-menus.php' )
							)
						),
						$item_id,
						__( 'Cancel', 'glozin-addons' )
					);
					?>
				</div>

				<?php
				if ( $widget ) {
					$widget->print_control( array(
						'type'  => 'hidden',
						'name'  => 'id',
						'value' => $item_id,
					) );

					$widget->print_control( array(
						'type'  => 'hidden',
						'name'  => 'type',
						'value' => $widget->get_name(),
					) );

					if ( ! $widget->has_control( 'title' ) ) {
						$widget->print_control( array(
							'type'  => 'hidden',
							'name'  => 'title',
							'value' => $widget->get_label(),
						) );
					}
				}
				?>
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

	/**
	 * Get the widget object.
	 *
	 * @param string $type
	 * @param array $data
	 *
	 * @return object|false
	 */
	public function get_widget_object( $type, $data = array() ) {
		$widgets = Module::get_item_widgets();

		if ( ! isset( $widgets[ $type ] ) ) {
			return false;
		}

		$classname = $widgets[ $type ];

		if ( ! class_exists( $classname ) ) {
			return false;
		}

		return new $classname( $data );
	}
}
