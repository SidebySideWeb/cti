<?php

namespace Glozin\Addons\Modules\Dynamic_Pricing_Discounts;

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

	const POST_TYPE = 'gz_pricing_discount';

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

		// Ajax function
		add_action( 'wp_ajax_glozin_dynamic_pricing_discount_status_update', array( $this, 'update_status' ) );
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
			wp_enqueue_media();
			wp_enqueue_style( 'glozin-dynamic-pricing-discounts-admin', GLOZIN_ADDONS_URL . 'modules/dynamic-pricing-discounts/assets/admin/dynamic-pricing-discounts-admin.css', [ 'woocommerce_admin_styles' ] );
			wp_enqueue_script( 'glozin-dynamic-pricing-discounts-admin', GLOZIN_ADDONS_URL . 'modules/dynamic-pricing-discounts/assets/admin/dynamic-pricing-discounts-admin.js', [ 'jquery', 'select2', 'wc-enhanced-select' ], '1.0.0', true );
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
		$columns = array_merge( $columns, array(
			'pricing_discount_status' => esc_html__( 'Status', 'glozin-addons' ),
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
		switch ( $column ) {
			case 'pricing_discount_status':
				$status = get_post_meta( $post_id, '_pricing_discount_status', true);
				?>
				<label class="dynamic-pricing-discount__switch dynamic-pricing-discount__switch--column <?php echo $status == 'yes' ? 'enable' : ''; ?>" data-post_id="<?php echo esc_attr( $post_id ); ?>">
					<input type="checkbox" name="_pricing_discount_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
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
		add_meta_box( 'glozin-dynamic-pricing-discount-status', esc_html__( 'Status', 'glozin-addons' ), array( $this, 'pricing_discount_meta_box_status' ), self::POST_TYPE, 'side', 'default' );
		add_meta_box( 'glozin-dynamic-pricing-discount', esc_html__( 'Dynamic Pricing & Discounts', 'glozin-addons' ), array( $this, 'pricing_discount_meta_box' ), self::POST_TYPE, 'advanced', 'high' );
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $post
	 */
	public function pricing_discount_meta_box_status( $post ) {
		$post_id = $post->ID;
		$status  = get_post_meta( $post_id, '_pricing_discount_status', true );
		?>
		<label class="dynamic-pricing-discount__switch <?php echo $status == 'yes' ? 'enable' : ''; ?>">
			<input type="checkbox" name="_pricing_discount_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
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
	public function pricing_discount_meta_box( $post ) {
		$count   = 0;
		$layout = get_post_meta( $post->ID, '_dynamic_pricing_discounts_layout', true );
		$display = get_post_meta( $post->ID, '_dynamic_pricing_discounts_display', true );
		$items   = (array) get_post_meta( $post->ID, 'glozin_dynamic_pricing_discounts_items', true );
		?>
		<div id="glozin-dynamic-pricing-discounts-display" class="glozin-dynamic-pricing-discounts-display">
			<p class="form-field">
				<label><?php esc_html_e('Layout', 'glozin-addons'); ?></label>
				<select name="_dynamic_pricing_discounts_layout">
					<option value="list" <?php selected( 'list', $layout ); ?>><?php esc_html_e( 'List', 'glozin-addons' ); ?></option>
					<option value="grid" <?php selected( 'grid', $layout ); ?>><?php esc_html_e( 'Grid', 'glozin-addons' ); ?></option>
				</select>
			</p>
			<p class="form-field">
				<label><?php esc_html_e('Displayed by', 'glozin-addons'); ?></label>
				<select name="_dynamic_pricing_discounts_display">
					<option value="products" <?php selected( 'products', $display ); ?>><?php esc_html_e( 'Products', 'glozin-addons' ); ?></option>
					<option value="categories" <?php selected( 'categories', $display ); ?>><?php esc_html_e( 'Categories', 'glozin-addons' ); ?></option>
				</select>
			</p>
			<p class="form-field glozin-dynamic-pricing-discounts--product <?php echo $display == 'products' ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Products', 'glozin-addons'); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="product-pricing-discount-id" name="_product_pricing_discount_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products" data-minimum_input_length="2">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post->ID, '_product_pricing_discount_ids', true ) );
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
			<p class="form-field glozin-dynamic-pricing-discounts--categories <?php echo $display == 'categories' ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Categories', 'glozin-addons'); ?></label>
				<select class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-pricing-discount-categories" name="_product_pricing_discount_cat_slugs[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_categories" data-minimum_input_length="2">
					<?php
					$terms = get_the_terms( $post->ID, 'product_cat' );
					if ( ! is_wp_error($terms) && $terms && is_array( $terms ) ) {
						foreach ( $terms as $term ) {
							echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $term->name ) . '</option>';
						}
					}
					?>
				</select>
			</p>
		</div>
		<hr />
		<div id="glozin-dynamic-pricing-discounts" class="glozin-dynamic-pricing-discounts">
			<label><?php esc_html_e( 'Configuration', 'glozin-addons' ); ?></label>
			<div class="glozin-dynamic-pricing-discounts__items">
				<h3><?php esc_html_e( 'Discounts', 'glozin-addons' ); ?></h3>
				<?php foreach ( $items as $item ) : ?>
					<?php $class = $count == 0 ? 'hidden' : ''; ?>
					<div class="group-items <?php echo $count == 0 ? 'glozin-dynamic-pricing-discount__group' : 'glozin-dynamic-pricing-discount__group--clone'; ?>">
						<p class="form-field form-field--layout <?php echo $layout !== 'grid' ? 'hidden' : ''; ?>">
							<label>
								<input id="dynamic_pricing_discounts_popular" type="checkbox" name="dynamic_pricing_discounts_popular[<?php echo esc_attr($count); ?>]" <?php echo ! empty( $item['popular'] ) && $item['popular'] == 'yes' ? 'checked' : ''; ?> value="yes">
								<?php esc_html_e( 'Most popular', 'glozin-addons' ); ?>
							</label>
						</p>
						<p class="form-field form-field--layout form-field__thumbnail <?php echo $layout !== 'grid' ? 'hidden' : ''; ?>">
							<span class="glozin-dynamic-pricing-discount__thumbnail <?php echo ! empty( $item['thumbnail_id'] ) ? 'has-thumnail' : ''; ?>">
								<a href="#" id="set-thumbnail">
									<?php if( ! empty( $item['thumbnail_id'] ) ) : ?>
										<?php echo wp_get_attachment_image( $item['thumbnail_id'], 'thumbnail' ); ?>
									<?php else : ?>
										<?php esc_html_e('Set thumbnail', 'glozin-addons'); ?>
									<?php endif; ?>
								</a>
								<a href="#" id="remove-thumbnail" class="<?php echo ! empty( $item['thumbnail_id'] ) ? '' : 'hidden'; ?>" data-set-text="<?php esc_attr_e('Set thumbnail', 'glozin-addons'); ?>">
									<?php esc_html_e('Remove', 'glozin-addons'); ?>
								</a>
							</span>
							<input type="hidden" id="dynamic_pricing_discounts_thumbnail_id" name="dynamic_pricing_discounts_thumbnail_id[]" value="<?php echo ! empty( $item['thumbnail_id'] ) ? esc_attr( $item['thumbnail_id'] ) : ''; ?>">
						</p>
						<p class="form-field form-field--layout <?php echo $layout !== 'grid' ? 'hidden' : ''; ?>">
							<label><?php esc_html_e( 'Unit', 'glozin-addons' ); ?></label>
							<input id="dynamic_pricing_discounts_unit" type="text" name="glozin_dynamic_pricing_discounts_unit[]" value="<?php echo ! empty( $item['unit'] ) ? wp_kses_post( $item['unit'] ) : ''; ?>">
						</p>
						<p class="form-field">
							<label><?php esc_html_e( 'Min', 'glozin-addons' ); ?></label>
							<input id="dynamic_pricing_discounts_from" type="number" name="glozin_dynamic_pricing_discounts_from[]" value="<?php echo wp_kses_post( $item['from'] ); ?>">
						</p>
						<p class="form-field">
							<label><?php esc_html_e( 'Max', 'glozin-addons' ); ?></label>
							<input id="dynamic_pricing_discounts_to" type="number" name="glozin_dynamic_pricing_discounts_to[]" value="<?php echo wp_kses_post( $item['to'] ); ?>">
						</p>
						<p class="form-field">
							<label><?php esc_html_e( 'apply', 'glozin-addons' ); ?></label>
							<input id="dynamic_pricing_discounts_discount" type="number" name="glozin_dynamic_pricing_discounts_discount[]" value="<?php echo wp_kses_post( $item['discount'] ); ?>">
							<label><?php esc_html_e( '(%)', 'glozin-addons' ); ?></label>
						</p>
						<button type="button" class="button glozin-dynamic-pricing-discount__remove <?php echo esc_attr( $class ); ?>" style="margin-left: 10px;"><?php esc_html_e( 'Remove', 'glozin-addons' ); ?></button>
					</div>
				<?php $count++; endforeach; ?>
				<p class="form-field glozin-dynamic-pricing-discount__button">
					<button type="button" class="button button-primary button-large glozin-dynamic-pricing-discount__addnew"><?php esc_html_e( 'Add new', 'glozin-addons' ); ?></button>
				</p>
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

		if ( isset( $_POST['_pricing_discount_status'] ) ) {
			update_post_meta( $post_id, '_pricing_discount_status', 'yes' );
		} else {
			update_post_meta( $post_id, '_pricing_discount_status', 'no' );
		}

		if ( isset( $_POST['_dynamic_pricing_discounts_layout'] ) ) {
			update_post_meta( $post_id, '_dynamic_pricing_discounts_layout', $_POST['_dynamic_pricing_discounts_layout'] );
		} else {
			update_post_meta( $post_id, '_dynamic_pricing_discounts_layout', 'list' );
		}

		if ( isset( $_POST['_dynamic_pricing_discounts_display'] ) ) {
			update_post_meta( $post_id, '_dynamic_pricing_discounts_display', $_POST['_dynamic_pricing_discounts_display'] );
		} else {
			update_post_meta( $post_id, '_dynamic_pricing_discounts_display', 'products' );
		}

		if ( isset( $_POST['_product_pricing_discount_ids'] ) ) {
			update_post_meta( $post_id, '_product_pricing_discount_ids', $_POST['_product_pricing_discount_ids'] );
		} else {
			update_post_meta( $post_id, '_product_pricing_discount_ids', [] );
		}

		if ( isset( $_POST['_product_pricing_discount_cat_slugs'] ) ) {
			update_post_meta( $post_id, '_product_pricing_discount_cat_slugs', $_POST['_product_pricing_discount_cat_slugs'] );
		}

		$cat_ids = [];
		if ( isset($_POST['_product_pricing_discount_cat_slugs']) ) {
			$cat_slugs = $_POST['_product_pricing_discount_cat_slugs'];
			foreach( $cat_slugs as $value => $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				if( ! is_wp_error( $term ) && $term ) {
					$cat_ids[] = $term->term_id;
				}
			}
		}

		if( $cat_ids ) {
			wp_set_post_terms( $post_id, $cat_ids, 'product_cat' );
		}

		$items = [];
		if ( isset( $_POST['glozin_dynamic_pricing_discounts_to'] ) ) {
			$count = 0;
			foreach( ( array ) $_POST['glozin_dynamic_pricing_discounts_to'] as $to ) {
				$items[] = [
					'popular'      => ! empty( $_POST['dynamic_pricing_discounts_popular'][ $count ] ) ? $_POST['dynamic_pricing_discounts_popular'][ $count ] : 'no',
					'thumbnail_id' => ! empty( $_POST['dynamic_pricing_discounts_thumbnail_id'][ $count ] ) ? $_POST['dynamic_pricing_discounts_thumbnail_id'][ $count ] : '',
					'unit'     => ! empty( $_POST['glozin_dynamic_pricing_discounts_unit'][ $count ] ) ? $_POST['glozin_dynamic_pricing_discounts_unit'][ $count ] : '',
					'from'     => ! empty( $_POST['glozin_dynamic_pricing_discounts_from'][ $count ] ) ? intval( $_POST['glozin_dynamic_pricing_discounts_from'][ $count ] ) : 0,
					'to'       => ! empty( $to ) ? intval( $to ) : 0,
					'discount' => ! empty( $_POST['glozin_dynamic_pricing_discounts_discount'][ $count ] ) ? intval( $_POST['glozin_dynamic_pricing_discounts_discount'][ $count ] ) : 0,
                ];

				$count++;
			}
		} else {
			$items[] = [
				'popular'      => 'no',
				'thumbnail_id' => '',
				'unit'         => '',
				'from'         => 0,
				'to'           => 0,
				'discount'     => 0,
			];
		}

		update_post_meta( $post_id, 'glozin_dynamic_pricing_discounts_items', $items );
	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function wc_screen_ids($screen_ids) {
		$screen_ids[] = 'gz_pricing_discount';

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

		if( $column_name != 'pricing_discount_disable' ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e('Disable', 'glozin-addons'); ?></span>
					<span class="input-text-wrap"><input type="checkbox" name="_pricing_discount_status" class="inline-edit-checkbox" value=""></span>
				</label>
			</div>
		</fieldset>
		<?php
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
			update_post_meta( $post_id, '_pricing_discount_status', $status );
		}

		wp_send_json_success();
	}
}