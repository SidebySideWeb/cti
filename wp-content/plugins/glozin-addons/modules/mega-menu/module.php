<?php
/**
 * Customize and add more data into menu items.
 */

namespace Glozin\Addons\Modules\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main module class for mega menu.
 */
class Module {
	/**
	 * Modal screen of mega menu settings
	 *
	 * @var array
	 */
	public $modals = array();

	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init();
	}

	/**
	 * Load core files
	 */
	public function includes() {
		require_once 'widgets/widget-base.php';
		require_once 'widgets/image.php';
		require_once 'widgets/banner.php';
		require_once 'widgets/html.php';
		require_once 'widgets/heading.php';
		require_once 'widgets/button.php';
		require_once 'widgets/custom-link.php';
		require_once 'widgets/product.php';

		require_once 'walker-edit.php';
		require_once 'walker.php';
	}

	/**
	 * Initialize hooks
	 */
	protected function init() {
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_settings_link' ), 10, 2 );
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_nav_menu_item' ), 20 );
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_walker' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_head-nav-menus.php', array( $this, 'meta_boxes' ) );
		add_action( 'admin_footer-nav-menus.php', array( $this, 'modal' ) );
		add_action( 'admin_footer-nav-menus.php', array( $this, 'templates' ) );
		add_action( 'wp_ajax_glozin_addons_megamenu_save', array( $this, 'save_menu_item_data' ) );
		add_action( 'wp_ajax_glozin_addons_megamenu_get_grid_items', array( $this, 'get_menu_items' ) );
		add_action( 'wp_ajax_glozin_addons_megamenu_update_grid', array( $this, 'save_menu_grid_data' ) );
		add_action( 'wp_ajax_glozin_addons_megamenu_update_tab', array( $this, 'save_menu_tab_data' ) );

		add_filter( 'nav_menu_css_class', array( $this, 'add_menu_icon_css_class' ), 10, 2 );
		add_filter( 'nav_menu_item_title', array( $this, 'add_menu_icon' ), 10, 4 );
		add_filter( 'nav_menu_item_title', array( $this, 'add_menu_badges' ), 10, 4 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add the mega menu settings link to the menu item.
	 *
	 * @param int $item_id
	 */
	public function add_settings_link( $item_id ) {
		$mega_data = get_post_meta( $item_id, '_menu_item_mega', true );
		$mega_data = array_replace_recursive( self::default_settings(), (array) $mega_data );
		$grid_data = get_post_meta( $item_id, '_menu_item_mega_grid', true );
		$grid_data = $grid_data ? $grid_data : array();
		$tab_data  = get_post_meta( $item_id, '_menu_item_mega_tab', true );
		$tab_data  = $tab_data ? $tab_data : array();

		$mega_content = $mega_data['content'];
		unset( $mega_data['content'] );
		?>
		<fieldset class="field-mega-options hide-if-no-js description-wide">
			<span class="field-move-visual-label" aria-hidden="true"><?php echo esc_html( _x( 'Options', 'Mega menu options', 'glozin-addons' ) ) ?></span>
			<span class="hidden mega-data" aria-hidden="true" data-mega="<?php echo esc_attr( json_encode( $mega_data ) ) ?>"><?php echo trim( $mega_content ); ?></span>
			<span class="hidden mega-data-gridbuilder" aria-hidden="true" data-griddata="<?php echo esc_attr( json_encode( $grid_data ) ); ?>"></span>
			<span class="hidden tab-data-gridbuilder" aria-hidden="true" data-tabdata="<?php echo esc_attr( json_encode( $tab_data ) ); ?>"></span>
			<button type="button" class="item-config-mega button-link"><?php esc_html_e( 'Mega Menu', 'glozin-addons' ) ?></button>
			<button type="button" class="item-config-icon button-link"><?php esc_html_e( 'Icon', 'glozin-addons' ) ?></button>
		</fieldset>
		<?php
	}

	/**
	 * Setup data for custom menu items.
	 * Add the [M] to mega menu items.
	 *
	 * @param object $menu_item
	 *
	 * @return object
	 */
	public function setup_nav_menu_item( $menu_item ) {
		// Setup data for custom menu items.
		if ( 0 === strpos( $menu_item->type, 'megamenu_item__' ) ) {
			$menu_item->megamenu_type = str_replace( 'megamenu_item__', '', $menu_item->type );
			$menu_item->object = 'megamenu';
			$menu_item->type = 'custom';
			$menu_item->type_label = __( 'Mega Item', 'glozin-addons' );
		}

		// Add the [M] to mega menu items.
		if ( is_admin() ) {
			$mega_data = get_post_meta( $menu_item->ID, '_menu_item_mega', true );

			if ( ! empty( $mega_data['mega'] ) ) {
				$menu_item->type_label .= ' [M]';
			}
		}

		return $menu_item;
	}

	/**
	 * Change the Walker class used when adding nav menu items.
	 *
	 * @param string $walker
	 * @return string
	 */
	public function edit_nav_walker( $walker ) {
		$walker = __NAMESPACE__ . '\Walker_Edit';

		return $walker;
	}

	/**
	 * Load scripts on Menus page only
	 *
	 * @param string $hook
	 */
	public function scripts( $hook ) {
		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		// Get assets URL.
		$assets_url = plugin_dir_url( dirname( __FILE__ ) ) . basename( dirname( __FILE__ ) );

		wp_register_style( 'glozin-addons-mega-menu-admin', $assets_url . '/css/mega-menu.css', array(
			'media-views',
			'wp-color-picker',
		), '1.0' );
		wp_enqueue_style( 'glozin-addons-mega-menu-admin' );

		wp_register_script( 'glozin-addons-mega-menu-admin', $assets_url . '/js/mega-menu.js', array(
			'jquery',
			'jquery-ui-resizable',
			'wp-util',
			'wp-color-picker',
		), '1.0', true );
		wp_enqueue_media();
		wp_enqueue_script( 'glozin-addons-mega-menu-admin' );

		wp_enqueue_script( 'glozin-addons-menu-item', $assets_url . '/js/menu-item.js', array( 'jquery' ),'1.0', true );

		wp_localize_script( 'glozin-addons-mega-menu-admin', 'glozinAddonsMegaMenuConfig', array(
			'templates' => $this->get_modal_templates(),
			'l10n' => array(
				'close_confirm'       => esc_html__( 'Your changes are not saved. Do you want to leave?', 'glozin-addons' ),
				'enable_mega_message' => esc_html__( 'You need to enable the mega menu first', 'glozin-addons' ),
				'width_auto'          => esc_html__( 'Auto', 'glozin-addons' ),
			),
		) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Get assets URL.
		$assets_url = plugin_dir_url( dirname( __FILE__ ) ) . basename( dirname( __FILE__ ) );

		wp_enqueue_script( 'mega-menu-script', $assets_url . '/js/scripts.js', array( 'jquery' ), GLOZIN_ADDONS_VER, true );

	}

	/**
	 * Add custom nav meta box.
	 *
	 * Adapted from http://www.johnmorrisonline.com/how-to-add-a-fully-functional-custom-meta-box-to-wordpress-navigation-menus/.
	 */
	public function meta_boxes() {
		add_meta_box( 'glozin_addons_mega_menu_items', __( 'Mega Menu Items', 'glozin-addons' ), array( $this, 'nav_menu_items' ), 'nav-menus', 'side', 'low' );
	}

	/**
	 * Output mega menu item list.
	 */
	public function nav_menu_items() {
		$items = self::get_item_widgets();
		?>
		<div id="posttype-megamenu_item" class="posttypediv">
			<div id="tabs-panel-posttype-megamenu_item" class="tabs-panel tabs-panel-active" role="region" aria-label="<?php esc_attr_e( 'Mega Menu Item List', 'glozin-addons' ) ?>" tabindex="0">
				<ul id="megamenu_item-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = -1;
					foreach ( $items as $key => $classname ) :
						$object = new $classname;
						?>
						<li>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $object->get_label() ); ?>
							</label>
							<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="megamenu_item__<?php echo esc_attr( $key ) ?>" />
							<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_attr( $object->get_label() ); ?>" />
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="#" />
							<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" value="" />
						</li>
						<?php
						$i--;
					endforeach;
					?>
				</ul>
			</div>
			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<button type="submit" class="button submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'glozin-addons' ); ?>" name="add-post-type-menu-item" id="submit-posttype-megamenu_item" disabled><?php esc_html_e( 'Add to Menu', 'glozin-addons' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Get the list of mega menu items.
	 *
	 * @return array
	 */
	public static function get_item_widgets() {
		return apply_filters( 'glozin_addons_mega_menu_items', array(
			'image' 			=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\Image',
			'banner' 			=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\Banner',
			'html'  			=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\HTML',
			'heading'  			=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\Heading',
			'button'  			=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\Button',
			'custom-link'  		=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\Custom_Link',
			'product'  			=> '\Glozin\Addons\Modules\Mega_Menu\Widgets\Product',
		) );
	}

	/**
	 * Prints HTML of modal on footer
	 * @todo Remove this method after testing
	 */
	public function modal_odl() {
		?>
		<div id="megamenu-modal" class="megamenu-modal">
			<div class="megamenu-modal__modal media-modal wp-core-ui" tabindex="0">
				<button type="button" class="media-modal-close megamenu-modal__close">
					<span class="media-modal-icon"><span class="screen-reader-text"><?php esc_html_e( 'Close', 'glozin-addons' ) ?></span></span>
				</button>
				<div class="media-modal-content">
					<div class="media-frame-menu megamenu-modal__frame-menu"></div>
					<div class="media-frame-title megamenu-modal__title"></div>
					<div class="media-frame-content">
						<div class="megamenu-modal__content"></div>
					</div>
					<div class="media-frame-toolbar">
						<div class="megamenu-modal__toolbar media-toolbar">
							<button type="button" class="megamenu-modal__save button media-button button-primary button-large" disabled><?php esc_html_e( 'Save Changes', 'glozin-addons' ) ?></button>
							<button type="button" class="megamenu-modal__cancel button media-button button-secondary button-large"><?php esc_html_e( 'Cancel', 'glozin-addons' ) ?></button>
							<span class="spinner"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="media-modal-backdrop megamenu-modal__backdrop"></div>
		</div>
		<?php
	}

	/**
	 * Prints HTML of modal on footer
	 */
	public function modal() {
		?>
		<div id="megamenu-modal" class="megamenu-modal">
			<div class="megamenu-modal__modal wp-core-ui" tabindex="0">
				<button type="button" class="media-modal-close megamenu-modal__close">
					<span class="media-modal-icon"><span class="screen-reader-text"><?php esc_html_e( 'Close', 'glozin-addons' ) ?></span></span>
				</button>
				<div class="megamenu-modal__frame">
					<div class="megamenu-modal__frame-menu"></div>
					<div class="megamenu-modal__title"></div>
					<div class="megamenu-modal__frame-content">
						<div class="megamenu-modal__content"></div>
					</div>
					<div class="megamenu-modal__toolbar">
						<button type="button" class="megamenu-modal__save button button-primary button-large" disabled><?php esc_html_e( 'Save Changes', 'glozin-addons' ) ?></button>
						<button type="button" class="megamenu-modal__cancel button button-secondary button-large"><?php esc_html_e( 'Cancel', 'glozin-addons' ) ?></button>
						<span class="spinner"></span>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Prints underscore template on footer
	 */
	public function templates() {
		$path = plugin_dir_path( __FILE__ );

		foreach ( $this->get_modal_templates() as $template ) {
			$file = $path . 'views/' . $template . '.php';

			if ( ! file_exists( $file ) ) {
				continue;
			}
			?>
			<script type="text/html" id="tmpl-megamenu__<?php echo esc_attr( $template ) ?>">
				<?php include( $file ); ?>
			</script>
			<?php
		}
	}

	/**
	 * Ajax function to save menu item data
	 */
	public function save_menu_item_data() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
			return;
		}

		$_POST['data'] = stripslashes_deep( $_POST['data'] );
		parse_str( $_POST['data'], $data );
		$updated = $data;

		// Sanitize svg.
		if ( ! empty( $data['icon_svg'] ) ) {
			$data['icon_svg'] = \Glozin\Addons\Helper::sanitize_svg( $data['icon_svg'] );
		}

		// Save menu item data
		foreach ( $data['menu-item-mega'] as $id => $meta ) {
			$old_meta = get_post_meta( $id, '_menu_item_mega', true );
			$old_meta = array_replace_recursive( self::default_settings(), (array) $old_meta );
			$meta     = array_replace_recursive( $old_meta, $meta );

			$updated['menu-item-mega'][ $id ] = $meta;

			update_post_meta( $id, '_menu_item_mega', $meta );
		}

		wp_send_json_success( $updated );
	}

	/**
	 * Ajax function to save grid builder data.
	 */
	public function save_menu_grid_data() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
			return;
		}

		$item_id = intval( $_POST['id'] );
		$data    = maybe_unserialize( $_POST['data'] );

		update_post_meta( $item_id, '_menu_item_mega_grid', $data );

		wp_send_json_success();
	}

	/**
	 * Ajax function to save grid builder data.
	 */
	public function save_menu_tab_data() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
			return;
		}

		$item_id = intval( $_POST['id'] );
		$data    = maybe_unserialize( $_POST['data'] );

		update_post_meta( $item_id, '_menu_item_mega_tab', $data );

		wp_send_json_success();
	}

	/**
	 * Ajax function to get HTML of menu items.
	 */
	public function get_menu_items() {
		$items = $_POST['items'];
		$i     = 0;

		$menu_items = array();

		while ( $i < count( $items ) ) {
			$menu_item = $items[ $i ];
			$menu_item['is_widget'] = ( 'true' === $menu_item['is_widget'] || '1' === $menu_item['is_widget'] || true === $menu_item['is_widget'] );

			$menu_obj     = (object) $menu_item;
			$menu_obj->ID = $menu_obj->id;

			if ( ! $menu_obj->is_widget ) {
				$menu_obj->post_type = 'nav_menu_item';
				$menu_obj            = wp_setup_nav_menu_item( $menu_obj );
			} else {
				$menu_obj->type          = 'custom';
				$menu_obj->type_label    = __( 'Mega Item', 'glozin-addons' );
				$menu_obj->megamenu_type = $menu_obj->object;
				$menu_obj->object        = 'megamenu';
				$menu_obj->_options      = $menu_item;
			}

			$menu_obj->title = empty( $menu_obj->title ) ? $menu_item['title'] : $menu_obj->title;

			if ( $menu_obj->megamenu_type == 'banner' ) {
				$menu_obj->button_text = empty( $menu_obj->button_text ) ? $menu_item['button_text'] : $menu_obj->button_text;
				$menu_obj->title = ! empty( $menu_obj->title ) ? $menu_obj->title : $menu_obj->button_text;
			}

			$menu_obj->label = $menu_obj->title;

			$menu_items[]    = $menu_obj;
			$i++;
		}

		/** This filter is documented in wp-admin/includes/nav-menu.php */
		$walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', 0 );

		if ( ! class_exists( $walker_class_name ) ) {
			wp_die( 0 );
		}

		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after'       => '',
				'before'      => '',
				'link_after'  => '',
				'link_before' => '',
				'walker'      => new $walker_class_name,
			);

			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}
	}

	/**
	 * Get modal templates
	 *
	 * @return array
	 */
	public function get_modal_templates() {
		return array(
			'menu',
			'title',
			'mega',
			'mega-grid-row',
			'mega-grid-column',
			'mega-grid-options',
			'design',
			'icon',
			'badges',
			'content',
			'design',
			'settings',
			'tab-content',
		);
	}

	/**
	 * Define default mega menu settings
	 */
	public static function default_settings() {
		return apply_filters(
			'glozin_addons_mega_menu_default',
			array(
				'mega'         	=> false,
				'mega_mode'    	=> 'default',
				'icon_type'    	=> 'fontawesome',
				'icon_position' => 'left',
				'icon'         	=> '',
				'icon_image'   	=> '',
				'icon_image_id' => '',
				'icon_svg'     	=> '',
				'badges_text'  	=> '',
				'badges_background' => '',
				'badges_color'  => '',
				'visible'      	=> 'visible',
				'disable_link' 	=> false,
				'content'      	=> '',
				'width'        	=> 'container',
				'custom_width' 	=> '1140px',
				'position' 		=> 'centered',
				'padding'      	=> array(
					'top'    => '',
					'bottom' => '',
					'left'   => '',
					'right'  => '',
				),
				'margin'       => array(
					'top'    => '',
					'bottom' => '',
					'left'   => '',
					'right'  => '',
				),
				'background'   => array(
					'image'      => '',
					'color'      => '',
					'attachment' => 'scroll',
					'size'       => '',
					'repeat'     => 'no-repeat',
					'position'   => array(
						'x'      => 'left',
						'y'      => 'top',
						'custom' => array(
							'x' => '',
							'y' => '',
						),
					),
				),
				'border'   => array(
					'type'      => '',
					'color'     => '',
					'width' => array(
						'top' => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					),
				),
			)
		);
	}

	/**
	 * Get default row options for the grid builder
	 */
	public static function default_row_options() {
		return array(
			'padding' => array(
				'top'    => '',
				'bottom' => '',
				'left'   => '',
				'right'  => '',
			),
			'margin' => array(
				'top'    => '',
				'bottom' => '',
			),
			'background' => array(
				'image'      => array(
					'id'  => '',
					'url' => '',
				),
				'color'      => '',
				'repeat'     => 'repeat',
				'attachment' => 'scroll',
				'size'       => '',
				'position'   => array(
					'x'        => 'center',
					'y'        => 'center',
					'custom_x' => '',
					'custom_y' => '',
				)
			),
			'border'   => array(
				'type'      => '',
				'color'     => '',
				'width' => array(
					'top' => '',
					'bottom' => '',
					'left' => '',
					'right' => '',
				),
			),
		);
	}

	/**
	 * Get default column options for the grid builder
	 */
	public static function default_column_options() {
		return array(
			'width'   => 'auto',
			'padding' => array(
				'top'    => '',
				'bottom' => '',
				'left'   => '',
				'right'  => '',
			),
			'background' => array(
				'image'      => array(
					'id'  => '',
					'url' => '',
				),
				'color'      => '',
				'repeat'     => 'repeat',
				'attachment' => 'scroll',
				'size'       => '',
				'position'   => array(
					'x'        => 'center',
					'y'        => 'center',
					'custom_x' => '',
					'custom_y' => '',
				)
			),
			'border'   => array(
				'type'      => '',
				'color'     => '',
				'width' => array(
					'top' => '',
					'bottom' => '',
					'left' => '',
					'right' => '',
				),
			),
		);
	}

	/**
	 * Add a class of menu with icon.
	 *
	 * @param array $classes
	 * @param object $item
	 * @return array
	 */
	public function add_menu_icon_css_class( $classes, $item ) {
		$icon = $this->get_menu_icon( $item );

		if ( $icon ) {
			$classes[] = 'menu-item-has-icon';
		}

		return $classes;
	}

	/**
	 * Add icon before the menu title
	 *
	 * @param string $title
	 * @param object $item
	 * @return string
	 */
	public function add_menu_icon( $title, $item, $args, $depth ) {
		$data  = get_post_meta( $item->ID, '_menu_item_mega', true );
		$data  = array_replace_recursive( self::default_settings(), (array) $data );
		$icon = $this->get_menu_icon( $item );

		if ( $args->theme_location == 'category-menu' && $depth == 0 ) {
			$title = '<span class="menu-item-text">'. $title .'</span>';
		}

		if ( $icon ) {
			if ( $data['icon_position'] == 'left' ) {
				$title =  $icon . $title ;
			} elseif ( $data['icon_position'] == 'right' ) {
				$title =  $title . $icon;
			}
		}

		return $title;
	}

	/**
	 * Add icon before the menu title
	 *
	 * @param string $title
	 * @param object $item
	 * @return string
	 */
	public function add_menu_badges( $title, $item, $args, $depth ) {
		if ( $depth != 0 ) {
			return $title;
		}

		$data  = get_post_meta( $item->ID, '_menu_item_mega', true );
		$data  = array_replace_recursive( self::default_settings(), (array) $data );

		if ( $data['badges_text'] ) {
			$badge_bg = $data['badges_background'] ? $data['badges_background'] : '';
			$badge_color = $data['badges_color'] ? $data['badges_color'] : '';
			$badge_style = $badge_bg || $badge_color ? 'style="--gz-menu-item-badge-bg: ' . $badge_bg . '; --gz-menu-item-badge-color: ' . $badge_color . ';"' : '';
			$title = $title . '<span class="menu-item-badge fs-9 text-center fw-semibold text-uppercase text-nowrap lh-1 position-relative position-absolute-xl" ' . $badge_style . '>' . $data['badges_text'] . '</span>';
		}

		return $title;
	}

	/**
	 * Get menu icon
	 *
	 * @param object $item
	 * @return string
	 */
	public function get_menu_icon( $item ) {
		$data  = get_post_meta( $item->ID, '_menu_item_mega', true );
		$data  = array_replace_recursive( self::default_settings(), (array) $data );
		$icon  = '';

		switch ( $data['icon_type'] ) {
			case 'theme':
				if ( ! empty( $data['icon'] ) ) {
					$icon = \Glozin\Addons\Helper::get_svg( $data['icon'], 'social', array( 'class' => 'menu-item-icon gz-socials-button gz-socials--' . $data['icon'] ) );
				}
				break;

			case 'image':
				if ( ! empty( $data['icon_image'] ) ) {

					$image_html = '';
					if ( ! empty( $data['icon_image_id'] ) ) {
						$image_html = wp_get_attachment_image( $data['icon_image_id'] );
					} else {
						$info = pathinfo( $data['icon_image'] );
						if ( ! empty( $info['extension'] ) ) {
							$name = basename( $data['icon_image'], '.' . $info['extension'] );
							$image_html = '<img src="' . esc_url( $data['icon_image'] ) . '" alt="' . esc_attr( $name ) . '"/>';
						}
					}

					$icon = '<span class="menu-item-icon menu-icon-item--image">'. $image_html .'</span>';
				}
				break;

			case 'svg':
				if ( ! empty( $data['icon_svg'] ) ) {
					$icon = '<span class="menu-item-icon menu-icon-item--svg menu-icon-item-position--'. $data['icon_position'] .'">' . $data['icon_svg'] . '</span>';
				}
				break;
		}

		return $icon;
	}
}
