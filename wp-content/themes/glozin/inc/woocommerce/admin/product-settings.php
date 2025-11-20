<?php
/**
 * WooCommerce additional settings.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Admin;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Settings
 */
class Product_Settings {
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
		if ( ! function_exists( 'is_woocommerce' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'badges_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_badges_options' ) );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_attributes_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_attributes_options' ) );

		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'product_unit_measure_options' ) );
		add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'product_unit_measure_filter_edit' ) );
		add_action( 'woocommerce_product_quick_edit_end', array( $this, 'product_unit_measure_filter_quick_edit' ) );
		add_action( 'manage_product_posts_custom_column', array( $this, 'product_unit_measure_quick_edit_data' ), 99, 2 );

		add_action( 'woocommerce_product_quick_edit_save', array( $this, 'product_unit_measure_save_quick_edit' ), 10 );
		add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'product_unit_measure_save_quick_edit' ), 10 );

		add_action( 'save_post', array( $this, 'save_product_data' ) );

		// Atribute
		add_action( 'wp_ajax_glozin_wc_product_attributes', array( $this, 'wc_get_product_attributes' ) );
		add_action( 'wp_ajax_nopriv_glozin_wc_product_attributes', array( $this, 'wc_get_product_attributes' ) );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) && $screen->post_type == 'product' ) {
			wp_enqueue_script( 'glozin_wc_settings_js', get_template_directory_uri() . '/assets/js/backend/woocommerce.js', array( 'jquery' ), '20220318', true );
			wp_localize_script(
				'glozin_wc_settings_js',
				'glozin_wc_settings',
				array(
					'search_tag_nonce'   => wp_create_nonce( 'search-tags' ),
				)
			);
		}

		wp_enqueue_script( 'glozin_admin_edit', get_template_directory_uri() . '/assets/js/backend/admin-edit.js', array( 'jquery' ), '20220318', true );
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function badges_tab( $tabs ) {
		$tabs['product_badges'] = [
			'label'    => esc_html__( 'Badges', 'glozin' ),
			'target'   => 'product_badges_data',
			'class'    => [ 'product-badges-tab' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_badges_options() {
		?>
		<div id="product_badges_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group">
			<?php
				woocommerce_wp_checkbox( array(
					'id'          => '_is_new',
					'label'       => esc_html__( 'New product?', 'glozin' ),
					'description' => esc_html__( 'Enable to set this product as a new product. A "New" badge will be added to this product.', 'glozin' ),
				) );
			?>
			</div>
			<div class="options_group">
				<?php
					$post_custom = get_post_custom( get_the_ID());
					woocommerce_wp_textarea_input(
						array(
							'id'       => 'custom_badges_icon_image_html',
							'label'    => esc_html__( 'Custom Badge Icon', 'glozin' ),
							'desc_tip'    => true,
							'rows'       => '3',
							'value'    => isset( $post_custom['custom_badges_icon_image_html'][0] ) ? $post_custom['custom_badges_icon_image_html'][0] : '',
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'       => 'custom_badges_text',
							'label'    => esc_html__( 'Custom Badge Text', 'glozin' ),
							'desc_tip'    => true,
							'description' => esc_html__( 'Enter this optional to show your badges.', 'glozin' ),
						)
					);

					$bg_color = ( isset( $post_custom['custom_badges_bg'][0] ) ) ? $post_custom['custom_badges_bg'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'       => 'custom_badges_bg',
							'label'    => esc_html__( 'Custom Badge Background', 'glozin' ),
							'description' => esc_html__( 'Pick background color for your badge', 'glozin' ),
							'value'    => $bg_color,
						)
					);

					$color = ( isset( $post_custom['custom_badges_color'][0] ) ) ? $post_custom['custom_badges_color'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'       => 'custom_badges_color',
							'label'    => esc_html__( 'Custom Badge Color', 'glozin' ),
							'description' => esc_html__( 'Pick color for your badge', 'glozin' ),
							'value'    => $color,
						)
					);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_attributes_tab( $tabs ) {
		$tabs['product_attributes'] = [
			'label'    => esc_html__( 'Product Card Attributes', 'glozin' ),
			'target'   => 'product_attributes_data',
			'class'    => [ 'product_attributes_tab', 'show_if_variable' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_attributes_options() {
		?>
		<div id="product_attributes_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group product-attributes-compare" id="glozin-product-attributes">
			<?php
			self::get_product_attributes(get_the_ID());
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add product unit measure options
	 */
	public static function product_unit_measure_options() {
		woocommerce_wp_text_input(
			array(
				'id'          => 'unit_measure',
				'label'       => esc_html__( 'Unit of measure', 'glozin' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Enter units of measure for product quantities (e.g., "pieces," "kg"), displayed after the product price.', 'glozin' ),
				'wrapper_class' => 'show_if_simple show_if_external'
			)
		);
	}

	/**
	 * Add product unit measure filter edit
	 */
	public static function product_unit_measure_filter_edit() {
		?>
			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php echo esc_html__( 'Unit of measure', 'glozin' ); ?></span>
					<span class="input-text-wrap">
						<select class="change_measure change_to" name="change_measure">
							<?php
								$options = array(
									''  => __( '— No change —', 'glozin' ),
									'1' => __( 'Change to:', 'glozin' ),
								);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
							}
							?>
						</select>
					</span>
				</label>
				<label class="change-input">
					<input type="text" name="unit_measure" class="text unit_measure" value="" />
				</label>
			</div>
		<?php
	}

	/**
	 * Add product unit measure filter quick edit
	 */
	public static function product_unit_measure_filter_quick_edit() {
		?>
			<div class="inline-edit-group">
				<label>
					<span class="title"><?php echo esc_html__( 'Unit of measure', 'glozin' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="unit_measure" class="text unit_measure" value="" />
					</span>
				</label>
			</div>
		<?php
	}

	/**
	 * Assign value for quick edit data
	 *
	 * @param array $column
	 * @param integer $post_id
	 *
	 * @return void
	 */
	function product_unit_measure_quick_edit_data( $column, $post_id ) {
		switch ( $column ) {
			case 'name':
				?>
				<div class="hidden unit_measure_id_inline" id="unit_measure_id_inline_<?php echo esc_attr( $post_id ); ?>">
					<div id="unit_measure_id"><?php echo esc_html( maybe_unserialize( get_post_meta( $post_id, 'unit_measure', true ) ) ); ?></div>
				</div>
				<?php
				break;
			default:
				break;
		}
	}

	/**
	 * Add product unit measure save quick edit
	 */
	public static function product_unit_measure_save_quick_edit( $product ) {
		$post_id = $product->get_id();

		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( isset( $_REQUEST['unit_measure'] ) ) {
			$woo_data = $_REQUEST['unit_measure'];
			update_post_meta( $post_id, 'unit_measure', $woo_data );
		}
	}

	/**
	 * Save product data.
	 *
	 * @param int $post_id The post ID.
	 */
	public static function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_is_new'] ) ) {
			delete_post_meta( $post_id, '_is_new' );
		} else {
			update_post_meta( $post_id, '_is_new', 'yes' );
		}

		if ( isset( $_POST['custom_badges_icon_image_html'] ) ) {
			$woo_data =  sanitize_text_field( $_POST['custom_badges_icon_image_html'] );
			update_post_meta( $post_id, 'custom_badges_icon_image_html', $woo_data );
		}

		if ( isset( $_POST['custom_badges_text'] ) ) {
			$woo_data = sanitize_text_field( $_POST['custom_badges_text'] );
			update_post_meta( $post_id, 'custom_badges_text', $woo_data );
		}

		if ( isset( $_POST['custom_badges_bg'] ) ) {
			$woo_data = sanitize_text_field( $_POST['custom_badges_bg'] );
			update_post_meta( $post_id, 'custom_badges_bg', $woo_data );
		}

		if ( isset( $_POST['custom_badges_color'] ) ) {
			$woo_data = sanitize_text_field( $_POST['custom_badges_color'] );
			update_post_meta( $post_id, 'custom_badges_color', $woo_data );
		}

		if ( isset( $_POST['glozin_product_attribute'] ) ) {
			$woo_data = sanitize_text_field( $_POST['glozin_product_attribute'] );
			update_post_meta( $post_id, 'glozin_product_attribute', $woo_data );
		}

		if ( isset( $_POST['glozin_product_attribute_number'] ) ) {
			$woo_data = intval($_POST['glozin_product_attribute_number']);
			$woo_data = ! $woo_data ? '' : $woo_data;
			update_post_meta( $post_id, 'glozin_product_attribute_number', $woo_data );
		}

		if ( isset( $_POST['unit_measure'] ) ) {
			$woo_data = sanitize_text_field( $_POST['unit_measure'] );
			update_post_meta( $post_id, 'unit_measure', $woo_data );
		}
	}

	/**
	 * Get Product Attributes AJAX function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wc_get_product_attributes() {
		$post_id = sanitize_text_field( $_POST['post_id'] );

		if ( empty( $post_id ) ) {
			return;
		}
		ob_start();
		$this->get_product_attributes($post_id);
		$response = ob_get_clean();
		wp_send_json_success( $response );
		die();
	}

	/**
	 * Get Product Attributes function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_product_attributes ($post_id) {
		$product_object = wc_get_product( $post_id );
		if( ! $product_object ) {
			return;
		}
		$attributes = $product_object->get_attributes();

		if( ! $attributes ) {
			return;
		}
		$options         = array();
		$options['']     = esc_html__( 'Default', 'glozin' );
		$options['none'] = esc_html__( 'None', 'glozin' );
		foreach ( $attributes as $attribute ) {
			$options[ sanitize_title( $attribute['name'] ) ] = wc_attribute_label( $attribute['name'] );
		}
		woocommerce_wp_select(
			array(
				'id'       => 'glozin_product_attribute',
				'label'    => esc_html__( 'Primary Product Attribute', 'glozin' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Show the product attribute in the product card', 'glozin' ),
				'options'  => $options
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'       => 'glozin_product_attribute_number',
				'label'    => esc_html__( 'Primary Product Attribute Number', 'glozin' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Show number of the product attribute in the product card', 'glozin' ),
				'options'  => $options
			)
		);
		
	}
}
