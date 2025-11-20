<?php

namespace Glozin\Addons\Modules\Buy_X_Get_Y;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Meta_Box {

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

	const POST_TYPE = 'gz_buy_x_get_y';

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
		add_action( 'wp_ajax_glozin_buy_x_get_y_status_update', array( $this, 'update_status' ) );
		add_action( 'wp_ajax_glozin_get_quantity_products', array( $this, 'get_quantity_products' ) );
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
			wp_enqueue_style( 'glozin-buy-x-get-y-admin', GLOZIN_ADDONS_URL . 'modules/buy-x-get-y/assets/admin/buy-x-get-y-admin.css', [ 'woocommerce_admin_styles' ] );
			wp_enqueue_script( 'glozin-buy-x-get-y-admin', GLOZIN_ADDONS_URL . 'modules/buy-x-get-y/assets/admin/buy-x-get-y-admin.js', [ 'jquery', 'select2', 'wc-enhanced-select', 'jquery-ui-sortable' ], '1.0.0', true );

			$glozin_data = array(
				'ajax_admin_url' => admin_url('admin-ajax.php'),
			);
	
			wp_localize_script(
				'glozin-buy-x-get-y-admin', 'glozinBXGYadmin', $glozin_data
			);
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
			'configuration'      => esc_html__( 'Configuration', 'glozin-addons' ),
			'date'               => esc_html__( 'Date', 'glozin-addons' ),
			'buy_x_get_y_status' => esc_html__( 'Status', 'glozin-addons' ),
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
		$display = get_post_meta( $post_id, '_buy_x_get_y_display', true );
		$product_ids = maybe_unserialize( get_post_meta( $post_id, '_product_buy_x_get_y_ids', true ) );
		$terms       = get_the_terms( $post_id, 'product_cat' );
		$exclude_ids = maybe_unserialize( get_post_meta( $post_id, '_product_buy_x_get_y_exclude_ids', true ) );
		switch ( $column ) {
			case 'configuration':
				if( $display == 'products' ) {
					if( ! empty( $product_ids ) ) {
						echo '<strong>' . esc_html__( 'Products: ', 'glozin-addons' ) .'</strong>';
						$names = [];
						foreach( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							$names[] = $product->get_name();
						}

						echo implode( ', ', $names );
					} else {
						esc_html_e( 'Not configured yet', 'glozin-addons' );
					}
				} else {
					if ( ! is_wp_error($terms) && $terms && is_array( $terms ) ) {
						echo '<strong>' . esc_html__( 'Categories: ', 'glozin-addons' ) .'</strong>';
						$names = [];
						foreach ( $terms as $term ) {
							$names[] = $term->name;
						}

						echo implode( ', ', $names );

						if( ! empty( $exclude_ids ) ) {
							echo '<br /><strong>' . esc_html__( 'Exclude Products: ', 'glozin-addons' ) .'</strong>';
							$names = [];
							foreach( $exclude_ids as $product_id ) {
								$product = wc_get_product( $product_id );
								$names[] = $product->get_name();
							}
	
							echo implode( ', ', $names );
						}
					} else {
						esc_html_e( 'Not configured yet', 'glozin-addons' );
					}
				}
				break;
			case 'buy_x_get_y_status':
				$status = get_post_meta( $post_id, '_buy_x_get_y_status', true);
				?>
				<label class="buy-x-get-y__switch buy-x-get-y__switch--column <?php echo $status == 'yes' ? 'enable' : ''; ?>" data-post_id="<?php echo esc_attr( $post_id ); ?>">
					<input type="checkbox" name="_buy_x_get_y_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
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
		add_meta_box( 'glozin-buy-x-get-y-status', esc_html__( 'Status', 'glozin-addons' ), array( $this, 'buy_x_get_y_meta_box_status' ), self::POST_TYPE, 'side', 'default' );
		add_meta_box( 'glozin-buy-x-get-y', esc_html__( 'Configuration', 'glozin-addons' ), array( $this, 'buy_x_get_y_meta_box' ), self::POST_TYPE, 'advanced', 'high' );
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $post
	 */
	public function buy_x_get_y_meta_box_status( $post ) {
		$post_id = $post->ID;
		$status  = get_post_meta( $post_id, '_buy_x_get_y_status', true );
		?>
		<label class="buy-x-get-y__switch <?php echo $status == 'yes' ? 'enable' : ''; ?>">
			<input type="checkbox" name="_buy_x_get_y_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
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
	public function buy_x_get_y_meta_box( $post ) {
		$count    = 0;
		$display  = get_post_meta( $post->ID, '_buy_x_get_y_display', true );
		$cat_qty  = get_post_meta( $post->ID, '_cat_buy_x_get_y_product_qty', true );
		$items    = (array) get_post_meta( $post->ID, 'glozin_buy_x_get_y_items', true );
		$product_outofstocks = self::get_all_product_outofstock_ids();
		$product_outofstocks = ! empty( $product_outofstocks ) && is_array( $product_outofstocks ) ? implode( ',', $product_outofstocks ) : '';
		?>
		<div id="glozin-buy-x-get-y-source" class="glozin-buy-x-get-y-source" data-post_id="<?php echo esc_attr( $post->ID ); ?>">
			<p class="form-field">
				<label><?php esc_html_e('Displayed by', 'glozin-addons'); ?></label>
				<select name="_buy_x_get_y_display">
					<option value="products" <?php selected( 'products', $display ); ?>><?php esc_html_e( 'Products', 'glozin-addons' ); ?></option>
					<option value="categories" <?php selected( 'categories', $display ); ?>><?php esc_html_e( 'Categories', 'glozin-addons' ); ?></option>
				</select>
			</p>
			<p class="form-field glozin-buy-x-get-y--product <?php echo $display == 'products' ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Campaign Trigger Product', 'glozin-addons'); ?></label>
				<select class="wc-product-search product-buy-x-get-y-id" multiple="multiple" style="width: 50%;" name="_product_buy_x_get_y_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products" data-minimum_input_length="2" data-display_stock="true" data-exclude_type="grouped, external" data-exclude="<?php esc_attr_e( $product_outofstocks ); ?>">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post->ID, '_product_buy_x_get_y_ids', true ) );
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
			<p class="form-field glozin-buy-x-get-y--product <?php echo $display == 'products' && ! empty( $product_ids ) ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Product quantity', 'glozin-addons'); ?></label>
				<span class="glozin-product-quantitys">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post->ID, '_product_buy_x_get_y_ids', true ) );
					$product_qtys = get_post_meta( $post->ID, '_products_buy_x_get_y_product_qty', true );
					if ( $product_ids ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
						?>
							<span class="glozin-product-quantitys__item">
								<input type="number" min="1" class="glozin-product-quantity" name="_products_buy_x_get_y_product_qty[<?php echo esc_attr( $product_id ); ?>]" value="<?php echo isset( $product_qtys[$product_id] ) && ! empty( $product_qtys[$product_id] ) ? $product_qtys[$product_id] : 1; ?>"/>
								<strong><?php echo wp_kses_post( $product->get_formatted_name() ); ?></strong>
							</span>
						<?php
						}
					}
					?>
				</span>
			</p>
			<p class="form-field glozin-buy-x-get-y--categories <?php echo $display == 'categories' ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Campaign Trigger Categories', 'glozin-addons'); ?></label>
				<select class="wc-category-search" multiple="multiple" style="width: 50%;" id="categories-buy-x-get-y-id" name="_cat_buy_x_get_y_slugs[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_categories" data-minimum_input_length="2">
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
			<p class="form-field glozin-buy-x-get-y--categories <?php echo $display == 'categories' ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Product quantity', 'glozin-addons'); ?></label>
				<input type="number" min="1" class="glozin-product-quantity" name="_cat_buy_x_get_y_product_qty" value="<?php echo isset( $cat_qty ) && ! empty( $cat_qty ) ? $cat_qty : 1; ?>"/>
			</p>
			<p class="form-field glozin-buy-x-get-y--categories <?php echo $display == 'categories' ? '' : 'hidden'; ?>">
				<label><?php esc_html_e('Exclude Product', 'glozin-addons'); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="product-buy-x-get-y-exclude-id" name="_product_buy_x_get_y_exclude_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products" data-minimum_input_length="2" data-display_stock="true" data-exclude_type="grouped, external" data-exclude="<?php esc_attr_e( $product_outofstocks ); ?>">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post->ID, '_product_buy_x_get_y_exclude_ids', true ) );
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
		<div id="glozin-buy-x-get-y" class="glozin-buy-x-get-y">
			<label><?php esc_html_e( 'Select the product(s) to offer up for the campaign', 'glozin-addons' ); ?></label>
			<div class="glozin-buy-x-get-y__items">
				<div class="glozin-buy-x-get-y__heading">
					<label><?php esc_html_e( '#', 'glozin-addons' ); ?></label>
					<label><?php esc_html_e( 'Products to offer', 'glozin-addons' ); ?></label>
					<label><?php esc_html_e( 'Min Qty', 'glozin-addons' ); ?></label>
					<label><?php esc_html_e( 'Discount Type', 'glozin-addons' ); ?></label>
					<label><?php esc_html_e( 'Discount', 'glozin-addons' ); ?></label>
				</div>
				<?php foreach ( $items as $item ) : ?>
					<?php $class = $count == 0 ? 'hidden' : ''; ?>
					<div class="group-items <?php echo $count == 0 ? 'glozin-buy-x-get-y__group' : 'glozin-buy-x-get-y__group--clone'; ?>">
						<div class="group-items__item move"></div>
						<div class="group-items__item product-id">
							<select class="wc-product-search product-buy-x-get-y-id" style="width: 100%;" name="glozin_buy_x_get_y_items[<?php echo esc_attr( $count ); ?>][product_id]" data-allow_clear="true" data-display_stock="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'glozin-addons' ); ?>" data-action="woocommerce_json_search_products" data-minimum_input_length="2" data-exclude_type="grouped, external" data-exclude="<?php esc_attr_e( $product_outofstocks ); ?>">
								<?php
								if( ! empty( $item['product_id'] ) ) {
									$product = wc_get_product( $item['product_id'] );
									if ( is_object( $product ) ) {
										echo '<option value="' . esc_attr( $item['product_id'] ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
									}
								}
								?>
							</select>
						</div>
						<div class="group-items__item product-qty">
							<input type="number" min="1" name="glozin_buy_x_get_y_items[<?php echo esc_attr( $count ); ?>][product_qty]" value="<?php echo isset( $item['product_qty'] ) && ! empty( $item['product_qty'] ) ? $item['product_qty'] : 1; ?>"/>
						</div>
						<div class="group-items__item product-discount-type">
							<select name="glozin_buy_x_get_y_items[<?php echo esc_attr( $count ); ?>][product_discount_type]">
								<option value=""><?php esc_html_e( 'No discount', 'glozin-addons' );?></option>
								<option value="percent" <?php echo isset( $item['product_discount_type'] ) && $item['product_discount_type'] == 'percent' ? 'selected="selected"' : '';?>><?php esc_html_e( 'Percent', 'glozin-addons' );?></option>
								<option value="fixed_price" <?php echo isset( $item['product_discount_type'] ) && $item['product_discount_type'] == 'fixed_price' ? 'selected="selected"' : '';?>><?php esc_html_e( 'Fixed discount amount', 'glozin-addons' );?></option>
								<option value="free" <?php echo isset( $item['product_discount_type'] ) && $item['product_discount_type'] == 'free' ? 'selected="selected"' : '';?>><?php esc_html_e( 'Free', 'glozin-addons' );?></option>
                            </select>
						</div>
						<div class="group-items__item product-discount">
							<input type="number" name="glozin_buy_x_get_y_items[<?php echo esc_attr( $count ); ?>][product_discount]" value="<?php echo isset( $item['product_discount'] ) && ! empty( $item['product_discount'] ) ? $item['product_discount'] : 0; ?>" class="discount" <?php echo ! isset( $item['product_discount_type'] ) || empty( $item['product_discount_type'] ) || $item['product_discount_type'] == 'free' ? 'disabled' : ''; ?>/>
						</div>
						<button type="button" class="button glozin-buy-x-get-y__remove <?php echo esc_attr( $class ); ?>" style="margin-left: 10px;"><?php esc_html_e( 'Remove', 'glozin-addons' ); ?></button>
					</div>
				<?php $count++; endforeach; ?>
				<p class="form-field glozin-buy-x-get-y__button">
					<button type="button" class="button button-primary button-large glozin-buy-x-get-y__addnew"><?php esc_html_e( 'Add New Offer Product', 'glozin-addons' ); ?></button>
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

		if ( isset( $_POST['_buy_x_get_y_status'] ) ) {
			update_post_meta( $post_id, '_buy_x_get_y_status', 'yes' );
		} else {
			update_post_meta( $post_id, '_buy_x_get_y_status', 'no' );
		}

		if ( isset( $_POST['_buy_x_get_y_display'] ) ) {
			update_post_meta( $post_id, '_buy_x_get_y_display', $_POST['_buy_x_get_y_display'] );
		} else {
			update_post_meta( $post_id, '_buy_x_get_y_display', 'products' );
		}

		if ( isset( $_POST['_product_buy_x_get_y_ids'] ) ) {
			update_post_meta( $post_id, '_product_buy_x_get_y_ids', $_POST['_product_buy_x_get_y_ids'] );
		} else {
			update_post_meta( $post_id, '_product_buy_x_get_y_ids', [] );
		}

		if( isset( $_POST['_products_buy_x_get_y_product_qty'] ) ) {
			update_post_meta( $post_id, '_products_buy_x_get_y_product_qty', self::sanitize_array( $_POST['_products_buy_x_get_y_product_qty'] ) );
		}

		if ( isset( $_POST['_cat_buy_x_get_y_slugs'] ) ) {
			update_post_meta( $post_id, '_cat_buy_x_get_y_slugs', $_POST['_cat_buy_x_get_y_slugs'] );
		}

		$cat_ids = [];
		if ( isset($_POST['_cat_buy_x_get_y_slugs']) ) {
			$cat_slugs = $_POST['_cat_buy_x_get_y_slugs'];
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

		if ( isset( $_POST['_cat_buy_x_get_y_product_qty'] ) ) {
			update_post_meta( $post_id, '_cat_buy_x_get_y_product_qty', absint( $_POST['_cat_buy_x_get_y_product_qty'] ) );
		} else {
			update_post_meta( $post_id, '_cat_buy_x_get_y_product_qty', 1 );
		}

		if ( isset( $_POST['_product_buy_x_get_y_exclude_ids'] ) ) {
			update_post_meta( $post_id, '_product_buy_x_get_y_exclude_ids', $_POST['_product_buy_x_get_y_exclude_ids'] );
		} else {
			update_post_meta( $post_id, '_product_buy_x_get_y_exclude_ids', [] );
		}

		if( isset( $_POST['glozin_buy_x_get_y_items'] ) ) {
			update_post_meta( $post_id, 'glozin_buy_x_get_y_items', self::sanitize_array( $_POST['glozin_buy_x_get_y_items'] ) );
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
		$screen_ids[] = 'gz_buy_x_get_y';

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

		if( $column_name != 'buy_x_get_y_disable' ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e('Disable', 'glozin-addons'); ?></span>
					<span class="input-text-wrap"><input type="checkbox" name="_buy_x_get_y_status" class="inline-edit-checkbox" value=""></span>
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
			update_post_meta( $post_id, '_buy_x_get_y_status', $status );
		}

		wp_send_json_success();
	}

	/**
	 * Ajax get quantity products html
	 *
	 * @return void
	 */
	public function get_quantity_products() {
		if( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'glozin_get_quantity_products' ) {
			return;
		}

		ob_start();
		if ( ! empty( $_REQUEST['product_ids'] ) ) {
			$product_qtys = get_post_meta( $_REQUEST['post_id'], '_products_buy_x_get_y_product_qty', true );
			foreach ( $_REQUEST['product_ids'] as $product_id ) {
				$product = wc_get_product( $product_id );
			?>
				<span class="glozin-product-quantitys__item">
					<input type="number" min="1" class="glozin-product-quantity" name="_products_buy_x_get_y_product_qty[<?php echo esc_attr( $product_id ); ?>]" value="<?php echo isset( $product_qtys[$product_id] ) && ! empty( $product_qtys[$product_id] ) ? $product_qtys[$product_id] : 1; ?>"/>
					<strong><?php echo wp_kses_post( $product->get_formatted_name() ); ?></strong>
				</span>
			<?php
			}
		}
		$output = ob_get_clean();

		wp_send_json_success( $output );
	}

	/**
	 * Get all product out of stock ids
	 *
	 * @return array
	 */
	public function get_all_product_outofstock_ids() {
		$ids = [];
		$products = wc_get_products(array(
			'status'       => 'publish',
			'stock_status' => 'outofstock',
			'limit'        => -1,
		));

		foreach ($products as $product) {
			$ids[] = $product->get_id();
		}

		return $ids;
	}
}