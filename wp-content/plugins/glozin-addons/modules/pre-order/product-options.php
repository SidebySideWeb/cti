<?php

namespace Glozin\Addons\Modules\Pre_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Settings
 */
class Product_Options {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		// Add pre-order options to simple products
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'pre_order_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_pre_order_options' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ) );

		// Add pre-order options to variable products
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'pre_order_variation_content' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_settings_fields' ), 10, 2 );
		add_filter( 'woocommerce_available_variation', array( $this, 'load_variation_settings_fields' ) );

		// Add label in order item
		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'add_label_in_order_item' ), 10, 3 );

		// Ajax function
		add_action( 'wp_ajax_glozin_pre_order_status_update', array( $this, 'update_status' ) );

		// Schedule pre-order
		add_action( 'woocommerce_scheduled_sales', array( $this, 'schedule_pre_order' ) );
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
			wp_enqueue_script( 'glozin-pre-order-admin-js', GLOZIN_ADDONS_URL . 'modules/pre-order/assets/admin/pre-order-admin.js', array( 'jquery' ), '20250313', true );
			wp_enqueue_style( 'glozin-pre-order-admin-css', GLOZIN_ADDONS_URL . 'modules/pre-order/assets/admin/pre-order-admin.css', array(), '20250313' );
		}
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function pre_order_tab( $tabs ) {
		$tabs['product_pre_order'] = [
			'label'    => esc_html__( 'Pre-Order', 'glozin-addons' ),
			'target'   => 'product_pre_order_data',
			'class'    => [ 'show_if_simple','pre_order_tab' ],
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_pre_order_options() {
		$status = get_post_meta( get_the_ID(), '_pre_order_status', true);
		$enable_label = esc_html__( 'Enable Pre-Order', 'glozin-addons' );
		$disable_label = esc_html__( 'Disable Pre-Order', 'glozin-addons' );
		$status_label = $status == 'yes' ? $enable_label : $disable_label;
		$pre_order_date = get_post_meta( get_the_ID(), '_pre_order_date', true );
		$pre_order_date = $pre_order_date ? date_i18n( 'Y-m-d', $pre_order_date ) : '';
		$pre_order_discount = get_post_meta( get_the_ID(), '_pre_order_price_discount', true );

		?>
		<div id="product_pre_order_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group">
				<div class="pre-order__switch-wrapper">
					<label class="pre-order__switch pre-order__switch--column <?php echo $status == 'yes' ? 'enable' : ''; ?>" data-post_id="<?php echo esc_attr( get_the_ID() ); ?>">
						<input type="checkbox" name="_pre_order_status" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
						<span class="switch"></span>
					</label>
					<div class="pre-order__switch-label" data-enable="<?php echo esc_attr( $enable_label ); ?>" data-disable="<?php echo esc_attr( $disable_label ); ?>">
						<?php echo esc_html( $status_label ); ?>
					</div>
				</div>
			</div>
			<div class="options_group pre-order__condition" <?php echo $status == 'yes' ? '' : 'style="display: none;"'; ?>>
				<p class="form-field">
					<label for="_pre_order_date"><?php esc_html_e( 'Availablity date and time', 'glozin-addons' ); ?></label>
					<input type="text" class="short" name="_pre_order_date" id="_pre_order_date" value="<?php echo esc_attr( $pre_order_date ); ?>" placeholder="YYYY-MM-DD" maxlength="10" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />
				</p>
				<p class="form-field">
					<?php 
						woocommerce_wp_text_input(
							array(
								'id'            => "_pre_order_price_discount",
								'name'          => "_pre_order_price_discount",
								'value'         => $pre_order_discount,
								'label'         => __( 'Discount on selling price', 'glozin-addons' ) . ' (' . get_woocommerce_currency_symbol() . ')',
								'data_type' => 'price',
							)
						);
					?>
				</p>
			</div>
		</div>
		<?php
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

		if ( isset( $_POST['_pre_order_status'] ) ) {
			update_post_meta( $post_id, '_pre_order_status', 'yes' );
		} else {
			update_post_meta( $post_id, '_pre_order_status', 'no' );
		}
		
		if ( isset( $_POST['_pre_order_date'] ) ) {
			update_post_meta( $post_id, '_pre_order_date', strtotime( $_POST['_pre_order_date'] ) );
		} else {
			update_post_meta( $post_id, '_pre_order_date', '' );
		}

		if ( isset( $_POST['_pre_order_price_discount'] ) ) {
			update_post_meta( $post_id, '_pre_order_price_discount', $_POST['_pre_order_price_discount'] );
		} else {
			update_post_meta( $post_id, '_pre_order_price_discount', '' );
		}
	}

	/**
	 * Call the template for the settings for the "Pre-order options" section inside the "Variations" tab (Edit product page).
	 *
	 * @param int     $loop           Position in the loop.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post data.
	 */
	public function pre_order_variation_content( $loop, $variation_data, $variation ) {
		$status = get_post_meta( $variation->ID, '_pre_order_status', true);
		$enable_label = esc_html__( 'Enable Pre-Order', 'glozin-addons' );
		$disable_label = esc_html__( 'Disable Pre-Order', 'glozin-addons' );
		$status_label = $status == 'yes' ? $enable_label : $disable_label;
		$pre_order_date = get_post_meta( $variation->ID, '_pre_order_date', true );
		$pre_order_date = $pre_order_date ? date_i18n( 'Y-m-d', $pre_order_date ) : '';
		$pre_order_discount = get_post_meta( $variation->ID, '_pre_order_price_discount', true );

		?>
		<div id="product_pre_order_data" class="product-variation-pre-order-data">
			<div class="options_group">
				<div class="pre-order__switch-wrapper">
					<label class="pre-order__switch pre-order__switch--column <?php echo $status == 'yes' ? 'enable' : ''; ?>" data-post_id="<?php echo esc_attr( $variation->ID ); ?>">
						<input type="checkbox" name="_pre_order_status[<?php echo esc_attr( $loop ); ?>]" <?php echo $status == 'yes' ? 'checked' : ''; ?> value="<?php echo esc_attr( $status ); ?>" />
						<span class="switch"></span>
					</label>
					<div class="pre-order__switch-label" data-enable="<?php echo esc_attr( $enable_label ); ?>" data-disable="<?php echo esc_attr( $disable_label ); ?>">
						<?php echo esc_html( $status_label ); ?>
					</div>
				</div>
			</div>
			<div class="options_group pre-order__condition" <?php echo $status == 'yes' ? '' : 'style="display: none;"'; ?>>
				<p class="form-field">
					<label for="_pre_order_date[<?php echo esc_attr( $loop ); ?>]"><?php esc_html_e( 'Availablity date and time', 'glozin-addons' ); ?></label>
					<input type="text" class="pre_order_date" name="_pre_order_date[<?php echo esc_attr( $loop ); ?>]" id="_pre_order_date<?php echo esc_attr( $loop ); ?>" value="<?php echo esc_attr( $pre_order_date ); ?>" placeholder="YYYY-MM-DD" maxlength="10" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />
				</p>
				<p class="form-field">
					<?php 
						woocommerce_wp_text_input(
							array(
								'id'            => "_pre_order_price_discount{$loop}",
								'name'          => "_pre_order_price_discount[{$loop}]",
								'value'         => $pre_order_discount,
								'label'         => __( 'Discount on selling price', 'glozin-addons' ) . ' (' . get_woocommerce_currency_symbol() . ')',
								'data_type' => 'price',
							)
						);
					?>
				</p>
			</div>
		</div>
		<?php
	}

	public function save_variation_settings_fields( $variation_id, $loop ) {
		$_pre_order_date = $_POST['_pre_order_date'][ $loop ];
		$_pre_order_price_discount = $_POST['_pre_order_price_discount'][ $loop ];

		if ( ! empty( $_pre_order_date ) ) {
			update_post_meta( $variation_id, '_pre_order_date', strtotime( $_pre_order_date ) );
		} else {
			update_post_meta( $variation_id, '_pre_order_date', '' );
		}

		if ( ! empty( $_pre_order_price_discount ) ) {
			update_post_meta( $variation_id, '_pre_order_price_discount', $_pre_order_price_discount );
		} else {
			update_post_meta( $variation_id, '_pre_order_price_discount', '' );
		}
	}

	public function load_variation_settings_fields( $variation ) {
		$variation['_pre_order_date'] = get_post_meta( $variation[ 'variation_id' ], '_pre_order_date', true );
		$variation['_pre_order_price_discount'] = get_post_meta( $variation[ 'variation_id' ], '_pre_order_price_discount', true );

		return $variation;
	}

	/**
	 * Add label in order item
	 *
	 */
	public function add_label_in_order_item( $item_id, $item, $product ) {
		if ( method_exists( $item, 'get_product' ) ) {
			if ( Helper::is_pre_order_active( $item->get_product() ) ) {
				echo Helper::print_pre_order_meta( $item->get_product() );
			}
		}
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
			update_post_meta( $post_id, '_pre_order_status', $status );
		}

		wp_send_json_success();
	}

	/**
	 * Remove pre-order data when pre-order is scheduled end
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function schedule_pre_order() {
		$paged      = 1;
		$per_page   = apply_filters( 'glozin_pre_order_schedule_batch_size', 1000 );

		do {
			$now  = time();
			$args = array(
				'post_type'      => array( 'product', 'product_variation' ),
				'posts_per_page' => $per_page,
				'paged'          => $paged,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_pre_order_date',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
					array(
						'key'     => '_pre_order_date',
						'value'   => $now,
						'compare' => '<',
						'type'    => 'NUMERIC',
					),
				),
				'fields' => 'ids',
			);

			$query = new \WP_Query( $args );

			if ( ! empty( $query->posts ) ) {
				foreach ( $query->posts as $product_id ) {
					if ( get_post_meta( $product_id, '_pre_order_status', true ) == 'yes' ) {
						update_post_meta( $product_id, '_pre_order_status', 'no' );
					}
				}
			}

			$paged++;

		} while ( $query->max_num_pages >= $paged );
		wp_reset_postdata();
	}
	
}
