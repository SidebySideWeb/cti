<?php
namespace Glozin\Addons\Modules\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Walker_Nav_Menu' ) ) {
	require_once ABSPATH . 'wp-includes/class-walker-nav-menu.php';
}

/**
 * Walker class for mega menu
 */
class Walker extends \Walker_Nav_Menu {
	/**
	 * Tells child items know it is in a mega menu or not
	 *
	 * @var bool
	 */
	protected $in_mega = false;

	/**
	 * Mega data of a mega menu
	 *
	 * @var array
	 */
	protected static $mega_data = array();

	/**
	 * Mega data of a column
	 *
	 * @var array
	 */
	protected $column_data = array();

	/**
	 * Get mega data of an item
	 *
	 * @return array
	 */
	public static function get_mega_data() {
		if ( is_null( self::$mega_data ) ) {
			self::$mega_data = new self();
		}

		return self::$mega_data;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see   Walker::start_lvl()
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param object $args   An object of wp_nav_menu() arguments. @see wp_nav_menu()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		// Don't render sub menu items if this is grid mega menu.
		if ( $this->in_mega && 'grid' == self::$mega_data['mega_mode'] ) {
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default class.
		$classes = array( 'sub-menu' );

		if ( ! $depth && $this->in_mega ) {
			$classes[] = 'mega-menu';
			$classes[] = 'mega-menu--' . self::$mega_data['mega_mode'];
		}

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 *
		 * @since 4.8.0
		 *
		 * @param array    $classes The CSS classes that are applied to the menu `<ul>` element.
		 * @param stdClass $args    An object of `wp_nav_menu()` arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		if ( ! $depth && $this->in_mega ) {
			$mega_style     = $this->get_mega_menu_css();
			$mega_container = $this->get_mega_container_attrs();

			$output .= "{$n}{$indent}<ul$class_names $mega_style>{$n}";
			$output .= "{$n}{$indent}<li$mega_container>{$n}";

			$output .= "{$n}{$indent}<ul class='mega-menu-main'>{$n}";
		} else {
			$output .= "{$n}{$indent}<ul$class_names>{$n}";
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see   Walker::end_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		// Don't render sub menu items if this is grid mega menu.
		if ( $this->in_mega && 'grid' == self::$mega_data['mega_mode'] ) {
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		if ( ! $depth && $this->in_mega ) {
			$output .= "$indent</ul></li></ul>{$n}";
		} else {
			$output .= "$indent</ul>{$n}";
		}
	}

	/**
	 * Starts the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		// Don't render sub menu items if this is grid mega menu.
		if ( $depth > 0 && $this->in_mega && 'grid' == self::$mega_data['mega_mode'] ) {
			return;
		}

		$item_mega = get_post_meta( $item->ID, '_menu_item_mega', true );
		$item_mega = array_replace_recursive( Module::default_settings(), (array) $item_mega );

		// Store mega data
		if ( ! $depth ) {
			$this->in_mega   = $item_mega['mega'];
			self::$mega_data = $item_mega;
		} elseif ( 1 == $depth ) {
			$this->column_data = $item_mega;
		}

		if ( ! $this->in_mega ) {
			$output .= parent::start_el( $output, $item, $depth, $args, $id );
			return;
		}

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		// Get mega data from post meta
		$item_css  = '';
		$item_atts = '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		// Adding classes for mega menu
		if ( $item_mega['mega'] && ! $depth ) {
			$classes[] = 'menu-item-mega';

			if ( self::$mega_data['position'] != 'left-aligned'  ) {
				$classes[] = 'menu-item-static';
			}

			if ( $item_mega['background']['image'] ) {
				$classes[] = 'menu-item-has-background';
			}

			$data_grid = get_post_meta( $item->ID, '_menu_item_mega_grid', true );

			if ( $data_grid ) {
				$classes[] = 'menu-item-has-children';

				if ( 'custom' == self::$mega_data['width'] ) {
					$classes[] = 'menu-item-has-container-custom';

					if ( self::$mega_data['position'] == 'left-aligned'  ) {
						$classes[] = 'menu-item-dropdown-left-aligned';
					}
				}
			}
		}

		// Add classes for columns
		if ( 1 == $depth && $this->in_mega ) {
			$classes[] = 'mega-sub-menu ' . $this->get_column_class( $item_mega['width'] );

			if ( 'hidden' == $item_mega['visible'] ) {
				$classes[]                 = 'hide-title';
				$item_mega['disable_link'] = true;
			} elseif ( 'none' == $item_mega['visible'] ) {
				$classes[] = 'hide-link';
			}

			if ( $item_mega['disable_link'] ) {
				$classes[] = 'link-disabled';
			}

			$item_css = $this->get_column_css();
		}

		/**
		 * Filters the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$item_id = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';

		$output .= $indent . '<li' . $item_id . $class_names . $item_atts . $item_css . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array    $atts   {
		 *                         The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type string    $title  Title attribute.
		 * @type string    $target Target attribute.
		 * @type string    $rel    The rel attribute.
		 * @type string    $href   The href attribute.
		 * }
		 *
		 * @param WP_Post  $item   The current menu item.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 * @param int      $depth  Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// Check if link is disable
		if ( $this->in_mega && $depth == 1 && $item_mega['disable_link'] ) {
			$link_open = '<span>';
		} else {
			$link_open = '<a' . $attributes . '>';
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of wp_nav_menu() arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		// Check if link is disable
		if ( $this->in_mega && $depth == 1 && $item_mega['disable_link'] ) {
			$link_close = '</span>';
		} else {
			$link_close = '</a>';
		}

		$item_output = $args->before;
		$item_output .= $link_open;
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= $link_close;
		$item_output .= $args->after;

		if ( 1 <= $depth && ! empty( $item_mega['content'] ) ) {
			$item_output .= '<div class="menu-item-content">' . do_shortcode( $item_mega['content'] ) . '</div>';
		}

		if ( 0 === $depth && $this->in_mega && 'grid' == self::$mega_data['mega_mode'] ) {
			$output .= $this->display_grid( $item, $args );
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker_Nav_Menu::end_el()
	 *
	 * @param string   $output      Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object Menu item data object. Not used.
	 * @param int      $depth       Depth of page. Not Used.
	 * @param stdClass $args        An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		// Don't render sub menu items if this is grid mega menu.
		if ( $depth > 1 && $this->in_mega && 'grid' == self::$mega_data['mega_mode'] ) {
			return;
		}

		parent::end_el( $output, $data_object, $depth, $args );
	}

	/**
	 * Display the grid content
	 *
	 * @param object $item Menu item data object.
	 * @param object $args An object of `wp_nav_menu()` arguments.
	 *
	 * @return string
	 */
	public function display_grid( $item, $args = null ) {
		$data = get_post_meta( $item->ID, '_menu_item_mega_grid', true );

		$output = '';
		$output = apply_filters( 'glozin_addons_mega_menu_grid_output', $output, $item, $args );

		if( !empty( $output ) ) {
			return $output;
		}

		if ( empty( $data ) || empty( $data['rows'] ) || empty( $data['columns'] ) ) {
			return;
		}

		$mega_container  = $this->get_mega_container_attrs();
		$this->grid_data = $data;


		$output  = '<ul class="sub-menu mega-menu mega-menu--grid" '. $this->get_mega_menu_css() .'>';
		$output .= '<li ' . $mega_container . '>';

		foreach ( $data['rows'] as $row ) {
			$output .= $this->display_grid_row( $row, $item, $args );
		}

		$output .= '</li>';
		$output .= '</ul>';

		$this->grid_data = null;

		return $output;
	}

	/**
	 * Display a single grid row
	 *
	 * @param array  $row   Row data
	 * @param object $item  Menu item object
	 * @param object $args  Menu item arguments
	 * @return void
	 */
	public function display_grid_row( $row, $item = null, $args = array() ) {
		if( ! isset( $this->grid_data['columns'] ) ) {
			return;
		}
		$columns = array_filter( $this->grid_data['columns'], function( $column_data ) use ( $row ) {
			return $column_data['row'] == $row['id'];
		} );

		$row_css = $this->get_css( $row );
		$output  = '<div class="mega-menu__row"' . $row_css . '>';

		foreach ( $columns as $column ) {
			$output .= $this->display_grid_column( $column, $item, $args );
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Display grid column.
	 *
	 * @param array  $column Column data
	 * @param object $item   Menu item object
	 * @param object $args   Menu item arguments
	 *
	 * @return string
	 */
	public function display_grid_column( $column, $item = null, $args = array() ) {
		if( ! isset( $this->grid_data['items'] ) ) {
			return;
		}
		$widgets = array_filter( $this->grid_data['items'], function( $item_data ) use (  $column ) {
			return $item_data['col'] == $column['id'];
		} );

		$column_css = $this->get_css( $column, false );

		if ( ! empty( $column['width'] ) ) {
			$column_css .= ( 'auto' == $column['width'] ) ? 'flex: 1;' : 'width:' . $column['width'] . '%;';
		}

		$output = '<ul class="mega-menu__column" style="' . $column_css . '">';

		foreach ( $widgets as $widget ) {
			$output .= $this->display_grid_widget( $widget, $item, $args );
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Display grid item widget
	 *
	 * @param array  $widget Widget data
	 * @param object $item   Menu item object
	 * @param object $args   Menu item arguments
	 *
	 * @return string
	 */
	public function display_grid_widget( $widget, $item = null, $args = null ) {
		$widget_object = null;
		$classes = array(
			'menu-item',
			'menu-item-' . $widget['id'],
		);

		$data = '';

		if ( $widget['is_widget'] && 'false' !== $widget['is_widget'] ) {
			$classes[]   = 'menu-item--widget';
			$widget_list = Module::get_item_widgets();

			if ( $widget['object'] ) {
				$classes[]   = 'menu-item--widget-' . $widget['object'];

				if ( $widget['object'] == 'heading' ) {
					$classes[]   = 'menu-item--type-' . $widget['type'];
				}

				if ( isset( $widget['alignment'] ) && ! empty( $widget['alignment'] ) && $widget['object'] == 'button' ) {
					$classes[]   = 'menu-item--align-' . $widget['alignment'];
				}

				if ( isset( $widget['spacing'] ) && ! empty( $widget['spacing'] ) && $widget['object'] == 'button' ) {
					$data = 'style=--gz-menu-button-item-spacing:' . $widget['spacing'] . 'px';
				}

				if ( $widget['object'] == 'custom-link' ) {
					if ( ! empty( $widget['badge_new'] ) || ! empty( $widget['badge_custom'] ) || ! empty( $widget['badge_hot'] ) ) {
						$classes[]   = 'menu-item--widget-badge';
					}
				}
			}

			if ( array_key_exists( $widget['object'], $widget_list ) ) {
				$clasname      = $widget_list[ $widget['object'] ];
				$widget_object = new $clasname( $widget );
			}
		}

		$output = '<li class="' . esc_attr( implode( ' ', $classes ) ) . '" '. esc_attr( $data ) .'>';

		if ( $widget_object ) {
			$classes[] = $widget['classes'];
			$output = '<li class="' . esc_attr( implode( ' ', $classes ) ) . '" '. esc_attr( $data ) .'>';

			ob_start();
			$widget_object->render();
			$output .= ob_get_clean();
		} else {
			$widget['post_type'] = 'nav_menu_item';
			$widget['post_title']= $widget['title'];
			$widget['ID']        = $widget['id'];
			$widget_item         = wp_setup_nav_menu_item( (object) $widget );
			$atts                = array();
			$atts  ['title']     = ! empty( $widget_item->attr_title ) ? $widget_item->attr_title : '';
			$atts  ['target']    = ! empty( $widget_item->target ) ? $widget_item->target : '';
			$atts  ['rel']       = ! empty( $widget_item->xfn ) ? $widget_item->xfn : '';
			$atts  ['href']      = ! empty( $widget_item->url ) ? $widget_item->url : '';
			$atts  ['classes']   = ! empty( $widget_item->classes ) ? $widget_item->classes : '';

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$output .= '<a ' . $attributes . '>';
			$output .= $args->link_before . $widget_item->title . $args->link_after;
			$output .= '</a>';
		}

		$output .= '</li>';
		return $output;
	}

	/**
	 * Get column class name
	 *
	 * @param string $width
	 *
	 * @return string
	 */
	public function get_column_class( $width = '25.00%' ) {
		$columns = array(
			'1_8' => '12.50%',
			'1_5' => '20.00%',
			'1_4' => '25.00%',
			'1_3' => '33.33%',
			'3_8' => '37.50%',
			'2_5' => '40.00%',
			'1_2' => '50.00%',
			'3_5' => '60.00%',
			'5_8' => '62.50%',
			'2_3' => '66.66%',
			'3_4' => '75.00%',
			'4_5' => '80.00%',
			'7_8' => '87.50%',
			'1_1' => '100.00%',
		);

		$column = array_search( $width, $columns );
		$column = false === $column ? '1_4' : $column;

		return 'col-' . $column;
	}

	/**
	 * Get inline style attribute
	 * Generate margin, padding, background properties.
	 *
	 * @return string
	 */
	public function get_column_css() {
		if ( ! $this->in_mega ) {
			return '';
		}

		return $this->get_css( $this->column_data );
	}

	/**
	 * Get inline css for mega menu container
	 *
	 * @return string
	 */
	public function get_mega_menu_css() {
		if ( ! $this->in_mega ) {
			return '';
		}

		$data = self::$mega_data;
		unset( $data['margin'] );
		unset( $data['padding'] );

		return $this->get_css( $data );
	}

	/**
	 * Generate the style attribute
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	protected function get_css( $data, $wrap = true ) {
		$props = array();

		if ( ! empty( $data['background'] ) ) {
			if ( $data['background']['color'] ) {
				$props['background-color'] = $data['background']['color'];
			}

			$image_url = '';

			if ( is_array( $data['background']['image'] ) && ( $data['background']['image']['id'] || $data['background']['image']['url'] ) ) {
				if ( $data['background']['image']['url'] ) {
					$image_url = $data['background']['image']['url'];
				} else {
					$image_url = wp_get_attachment_image_url( $data['background']['image']['id'], 'full' );
				}
			} elseif ( ! is_array( $data['background']['image'] ) && ! empty( $data['background']['image'] ) ) {
				$image_url = $data['background']['image'];
			}

			if ( $image_url ) {
				$props['background-image']      = 'url(' . esc_url( $image_url ) . ')';
				$props['background-attachment'] = $data['background']['attachment'];
				$props['background-repeat']     = $data['background']['repeat'];

				if ( $data['background']['size'] ) {
					$props['background-size'] = $data['background']['size'];

					if ( 'cover' == $data['background']['size'] ) {
						unset( $props['background-repeat'] );
					}
				}

				if ( $data['background']['position']['x'] == 'custom' ) {
					$position_x = $data['background']['position']['custom']['x'];
				} else {
					$position_x = $data['background']['position']['x'];
				}

				if ( $data['background']['position']['y'] == 'custom' ) {
					$position_y = $data['background']['position']['custom']['y'];
				} else {
					$position_y = $data['background']['position']['y'];
				}

				$props['background-position'] = $position_x . ' ' . $position_y;
			}
		}

		if ( ! empty( $data['padding'] ) ) {
			if ( ! empty( $data['padding']['top'] ) ) {
				$props['padding-top'] = $this->css_unit( $data['padding']['top'] );
			}

			if ( ! empty( $data['padding']['bottom'] ) ) {
				$props['padding-bottom'] = $this->css_unit( $data['padding']['bottom'] );
			}

			if ( ! empty( $data['padding']['left'] ) ) {
				$props['padding-inline-start'] = $this->css_unit( $data['padding']['left'] );
			}

			if ( ! empty( $data['padding']['right'] ) ) {
				$props['padding-inline-end'] = $this->css_unit( $data['padding']['right'] );
			}
		}

		if ( ! empty( $data['margin'] ) ) {
			if ( ! empty( $data['margin']['top'] ) ) {
				$props['margin-top'] = $this->css_unit( $data['margin']['top'] );
			}

			if ( ! empty( $data['margin']['bottom'] ) ) {
				$props['margin-bottom'] = $this->css_unit( $data['margin']['bottom'] );
			}

			if ( ! empty( $data['margin']['left'] ) ) {
				$props['margin-inline-start'] = $this->css_unit( $data['margin']['left'] );
			}

			if ( ! empty( $data['margin']['right'] ) ) {
				$props['margin-inline-end'] = $this->css_unit( $data['margin']['right'] );
			}
		}

		if ( ! empty( $data['border'] ) ) {
			if ( ! empty( $data['border']['type'] ) ) {
				$props['border-style'] = $this->css_unit( $data['border']['type'] );
			}

			if ( ! empty( $data['border']['color'] ) ) {
				$props['border-color'] = $this->css_unit( $data['border']['color'] );
			}

			if ( ! empty( $data['border']['width']['top'] ) ) {
				$props['border-top-width'] = $this->css_unit( $data['border']['width']['top'] );
			}

			if ( ! empty( $data['border']['width']['bottom'] ) ) {
				$props['border-bottom-width'] = $this->css_unit( $data['border']['width']['bottom'] );
			}

			if ( ! empty( $data['border']['width']['left'] ) ) {
				$props['border-inline-start-width'] = $this->css_unit( $data['border']['width']['left'] );
			}

			if ( ! empty( $data['border']['width']['right'] ) ) {
				$props['border-inline-end-width'] = $this->css_unit( $data['border']['width']['right'] );
			}
		}

		if ( isset( $data['custom_width'] ) && ! empty( $data['custom_width'] ) && 'custom' == self::$mega_data['width'] ) {
			$props['--gz-mega-menu-width'] = $this->css_unit( $data['custom_width'] );
		}

		if ( empty( $props ) ) {
			return '';
		}

		$style = '';
		foreach ( $props as $prop => $value ) {
			$style .= $prop . ':' . esc_attr( $value ) . ';';
		}

		if ( ! $wrap ) {
			return $style;
		}

		return ' style="' . $style . '"';
	}

	/**
	 * Get mega container tag attributes
	 *
	 * @return string
	 */
	protected function get_mega_container_attrs() {
		if ( ! $this->in_mega ) {
			return '';
		}

		$class = array( 'mega-menu-container' );

		if ( '' != self::$mega_data['width'] ) {
			$class[] = 'container-xxl';
			$class[] = self::$mega_data['width'] == 'container-fluid' ? 'full-width' : '';
			$attrs   = ' class="' . esc_attr( join( ' ', $class ) ) . '"';
		}

		return $attrs;
	}

	/**
	 * Get the correct unit for CSS properties.
	 *
	 * @param string $value
	 * @return string
	 */
	protected function css_unit( $value ) {
		$value = trim( $value );

		if ( is_numeric( $value ) ) {
			return $value . 'px';
		}

		return $value;
	}
}
