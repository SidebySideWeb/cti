<?php

namespace Glozin\Addons\Modules\Linked_Variant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Meta_Box  {

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

	const POST_TYPE = 'em_linked_variant';

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'clear_linked_variant_cache' ) );
		add_action( 'before_delete_post', array( $this, 'clear_linked_variant_cache' ) );

		// Ajax function
		add_action( 'wp_ajax_glozin_linked_variant_status_update', array( $this, 'update_status' ) );
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
			wp_enqueue_style( 'glozin-linked-variant-admin', GLOZIN_ADDONS_URL . 'modules/linked-variant/assets/admin/linked-variant-admin.css', [ 'woocommerce_admin_styles' ] );
			wp_enqueue_script( 'glozin-linked-variant-admin', GLOZIN_ADDONS_URL . 'modules/linked-variant/assets/admin/linked-variant-admin.js', [ 'jquery', 'select2', 'wc-enhanced-select', 'jquery-ui-sortable' ], GLOZIN_ADDONS_VER, true );
		}
	}

	/**
	 * Add custom column to product tabss management screen
	 * Add Thumbnail column
     *
	 * @since 1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		unset( $columns['date' ] );
		$columns = array_merge( $columns, array(
			'configuration'         => esc_html__( 'Configuration', 'glozin-addons' ),
			'date'                  => esc_html__( 'Date', 'glozin-addons' ),
			'linked_variant_status' => esc_html__( 'Status', 'glozin-addons' ),
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
		$product_ids = maybe_unserialize( get_post_meta( $post_id, '_product_linked_variant_ids', true ) );
		$terms       = get_the_terms( $post_id, 'product_cat' );
		$items       = get_post_meta( $post_id, 'glozin_linked_variant_items', true );
		switch ( $column ) {
			case 'configuration':
				echo esc_html__( 'Products:', 'glozin-addons' );
				if( ! empty( $product_ids ) ) {
					$names = [];
					foreach( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						$names[] = $product->get_name();
					}

					echo implode( ', ', $names );
				}

				if( ! empty( $items['attributes'] ) ) {
					echo '<br/>';
					$attr_names = [];
					foreach ( $items['attributes'] as $id ) {
						if ( $attr = wc_get_attribute( absint( str_replace( 'id:', '', $id ) ) ) ) {
							$attr_names[] = $attr->name;
						}
					}
                    echo esc_html__( 'Attributes:', 'glozin-addons' ) . ' ' . implode( ', ', $attr_names );
				}
				break;
			case 'linked_variant_status':
				$status = get_post_meta( $post_id, '_linked_variant_status', true);
				?>
				<label class="linked-variant__switch linked-variant__switch--column <?php echo $status == 'yes' ? 'enable' : ''; ?>" data-post_id="<?php echo esc_attr( $post_id ); ?>">
					<input type="checkbox" name="_linked_variant_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
					<span class="switch"></span>
				</label>
				<?php
				break;
		}
	}

	/**
	 * Add meta boxes
	 *
	 * @param object $post
	 */
	public function meta_boxes( $post ) {
		add_meta_box( 'glozin-linked-variant-status', esc_html__( 'Status', 'glozin-addons' ), array( $this, 'linked_variant_meta_box_status' ), self::POST_TYPE, 'side', 'default' );
		add_meta_box( 'glozin-linked-variant', esc_html__( 'Configuration', 'glozin-addons' ), array( $this, 'linked_variant_meta_box' ), self::POST_TYPE, 'advanced', 'high' );
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $post
	 */
	public function linked_variant_meta_box_status( $post ) {
		$post_id = $post->ID;
		$status  = get_post_meta( $post_id, '_linked_variant_status', true );
		?>
		<label class="linked-variant__switch <?php echo $status == 'yes' ? 'enable' : ''; ?>">
			<input type="checkbox" name="_linked_variant_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
			<span class="switch"></span>
			<span class="text" data-enable="<?php echo esc_html( 'Enable', 'glozin-addons' ); ?>" data-disable="<?php echo esc_html( 'Disable', 'glozin-addons' ); ?>"><?php echo $status == 'yes' ? esc_html( 'Enable', 'glozin-addons' ) : esc_html( 'Disable', 'glozin-addons' ); ?></span>
		</label>
		<?php
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $post
	 */
	public function linked_variant_meta_box( $post ) {
		$items            = get_post_meta( $post->ID, 'glozin_linked_variant_items', true );
		$items_attributes = ! empty( $items['attributes'] ) ? $items['attributes']: [];
		$items_images     = ! empty( $items['images'] ) ? $items['images']        : [];
		$items_shape      = ! empty( $items['shape'] ) ? $items['shape']          : '';
		$items_size       = ! empty( $items['size'] ) ? $items['size']            : [];
		?>
		<div id="glozin-linked-variant-source" class="glozin-linked-variant-source">
			<p class="form-field glozin-linked-variant--product">
				<label><?php esc_html_e('Products', 'glozin-addons'); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="product-pricing-discount-id" name="_product_linked_variant_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products" data-minimum_input_length="2" data-exclude_type="variable, grouped, virtual, downloadable">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post->ID, '_product_linked_variant_ids', true ) );
					if ( $product_ids ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					}
					?>
				</select>
			</p>
		</div>
		<hr />
		<div id="glozin-linked-variant" class="glozin-linked-variant">
			<label><?php esc_html_e( 'Linked by (attributes)', 'glozin-addons' ); ?></label>
			<div class="glozin-linked-variant__items">
			<?php
				$wc_attributes = wc_get_attribute_taxonomies();
				$attributes    = [];

				foreach ( $wc_attributes as $wc_attribute ) {
					$attributes[ 'id:' . $wc_attribute->attribute_id ] = $wc_attribute->attribute_label;
				}

				$saved_attributes = [];
				foreach ( $items_attributes as $attr ) {
					$saved_attributes[$attr] = $attributes[$attr];
				}

				$merge_attributes = array_merge( $saved_attributes, $attributes );
				if( $merge_attributes ) {
					foreach ($merge_attributes as $id => $label ) :
				?>
					<div class="glozin-linked-variant__attribute">
						<span class="move"></span>
						<span class="checkbox">
							<label>
								<input type="checkbox" name="glozin_linked_variant_items[attributes][]" value="<?php echo esc_attr( $id );?>" <?php echo isset( $items_attributes ) && is_array( $items_attributes ) && in_array( $id, $items_attributes )? 'checked' : '';?> />
								<?php echo esc_html( $label ); ?>
							</label>
						</span>
						<span class="image">
							<label>
								<input type="checkbox" name="glozin_linked_variant_items[images][]" value="<?php echo esc_attr( $id );?>" <?php echo isset( $items_images ) && is_array( $items_images ) && in_array( $id, $items_images )? 'checked' : '';?> />
								<?php echo esc_html__( 'Show Images', 'glozin-addons' ); ?>
							</label>
						</span>
						<span class="size">
							<label><?php esc_html_e( 'Size(px):', 'glozin-addons' ); ?></label>
							<input type="number" name="glozin_linked_variant_items[size][<?php echo esc_attr( $id ); ?>][width]" value="<?php echo isset( $items_size ) && ! empty( $items_size[$id]['width'] ) ? $items_size[$id]['width'] : ''; ?>" placeholder="<?php esc_attr_e( 'Width', 'glozin-addons' ); ?>"/> x
							<input type="number" name="glozin_linked_variant_items[size][<?php echo esc_attr( $id ); ?>][height]" value="<?php echo isset( $items_size ) && ! empty( $items_size[$id]['height'] ) ? $items_size[$id]['height'] : ''; ?>" placeholder="<?php esc_attr_e( 'Height', 'glozin-addons' ); ?>" />
						</span>
						<span class="shape">
							<select name="glozin_linked_variant_items[shape][<?php echo esc_attr( $id ); ?>]">
								<option value=""><?php esc_html_e( 'Select Shape', 'glozin-addons' ); ?></option>
								<option value="round" <?php echo isset( $items_shape[$id] ) && $items_shape[$id] == 'round' ? 'selected="selected"' : '';?>><?php esc_html_e( 'Circle', 'glozin-addons' );?></option>
                                <option value="rounded" <?php echo isset( $items_shape[$id] ) && $items_shape[$id] == 'rounded' ? 'selected="selected"' : '';?>><?php esc_html_e( 'Rounded corners', 'glozin-addons' );?></option>
                                <option value="square" <?php echo isset( $items_shape[$id] ) && $items_shape[$id] == 'square' ? 'selected="selected"' : '';?>><?php esc_html_e( 'Square', 'glozin-addons' );?></option>
							</select>
						</span>
					</div>
				<?php
					endforeach;
				} else {
					echo '<div class="glozin-linked-variant__attribute--empty">'. esc_html__( 'No attributes.', 'glozin-addons' ) .'</div>';
				}
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box content.
     *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 * @param object $post
     *
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		// If not the flex post.
		if ( self::POST_TYPE != $post->post_type ) {
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

		$this->clear_linked_variant_cache();

		if ( isset( $_POST['_linked_variant_status'] ) ) {
			update_post_meta( $post_id, '_linked_variant_status', 'yes' );
		} else {
			update_post_meta( $post_id, '_linked_variant_status', 'no' );
		}

		if ( isset( $_POST['_product_linked_variant_ids'] ) ) {
			update_post_meta( $post_id, '_product_linked_variant_ids', $_POST['_product_linked_variant_ids'] );
		} else {
			update_post_meta( $post_id, '_product_linked_variant_ids', [] );
		}

		if( isset( $_POST['glozin_linked_variant_items'] ) ) {
			update_post_meta( $post_id, 'glozin_linked_variant_items', self::sanitize_array( $_POST['glozin_linked_variant_items'] ) );
		}
	}

	public function sanitize_array( $arr ) {
		foreach ( (array) $arr as $k => $v ) {
			if ( is_array( $v ) ) {
				$arr[ $k ] = self::sanitize_array( $v );
			} else {
				$arr[ $k ] = sanitize_text_field( $v );
			}
		}

		return $arr;
	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function wc_screen_ids($screen_ids) {
		$screen_ids[] = 'em_linked_variant';

		return $screen_ids;
	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function edit_custom_box( $column_name, $post_type ) {
		if( $post_type != self::POST_TYPE ) {
			return;
		}

		if( $column_name != 'linked_variant_disable' ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e('Disable', 'glozin-addons'); ?></span>
					<span class="input-text-wrap"><input type="checkbox" name="_linked_variant_status" class="inline-edit-checkbox" value=""></span>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Clear dynamic pricing discount post ids
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear_linked_variant_cache() {
		delete_transient( 'glozin_post_linked_variant' );
	}

	/**
	 * Ajax change status of post
	 *
	 * @return void
	 */
	public static function update_status() {
		$status  = isset( $_POST['status'] ) ? strtolower( $_POST['status'] ) : false;
		$post_id = absint( $_POST['post_id'] );

		if ( $post_id && $status ) {
			update_post_meta( $post_id, '_linked_variant_status', $status );
		}

		wp_send_json_success();
	}
}