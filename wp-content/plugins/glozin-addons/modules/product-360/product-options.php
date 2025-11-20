<?php

namespace Glozin\Addons\Modules\Product_360;

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

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_360_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_product_360_options' ) );
		add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'save_product_data' ) );
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
			wp_enqueue_script( 'glozin_wc_product_360_js', GLOZIN_ADDONS_URL . 'modules/product-360/assets/admin/product-360-admin.js', array( 'jquery' ), '20240506', true );
			wp_enqueue_style( 'glozin_wc_product_360_css', GLOZIN_ADDONS_URL . 'modules/product-360/assets/admin/product-360-admin.css' );
		}
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_360_tab( $tabs ) {
		$tabs['product_product_360'] = [
			'label'    => esc_html__( 'Product 360', 'glozin-addons' ),
			'target'   => 'product_product_360_data',
			'class'    => [ 'product_360_tab' ],
			'priority' => 62,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_product_360_options() {
		$product_360_ids = get_post_meta( get_the_ID(), 'product_360_thumbnail_ids', true );
		$attachments = ! empty( $product_360_ids ) ? explode(',', $product_360_ids) : '';
		?>
		<div id="product_product_360_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group">
				<p class="form-field">
					<label><?php esc_html_e( 'Images', 'glozin-addons' ); ?></label>
					<span class="product-product-360__buttons">
						<a href="#" id="set-product_360-thumbnail" data-choose="<?php esc_attr_e( 'Upload Thumbnails', 'glozin-addons' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'glozin-addons' ); ?>" data-delete="<?php esc_attr_e( 'Delete thumbnail', 'glozin-addons' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'glozin-addons' ); ?>">
							<?php esc_html_e('Upload Thumbnails', 'glozin-addons'); ?>
						</a>
						<a href="#" id="delete-product-360-thumbnails" class="<?php echo ! empty( $attachments ) ? '' : 'hidden'; ?>">
							<?php esc_html_e('Delete all thumbnails', 'glozin-addons'); ?>
						</a>
					</span>
					<ul class="product-360__list-thumbnails">
					<?php
						if ( ! empty( $attachments ) ) {
							foreach ( $attachments as $attachment_id ) {
								$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );
								if ( empty( $attachment ) ) {
									continue;
								}
								?>
								<li class="thumbnail" data-attachment_id="<?php echo esc_attr( $attachment_id ); ?>">
									<?php echo $attachment; ?>
									<a href="#" class="delete tips" data-tip="<?php esc_attr_e( 'Delete thumbnail', 'glozin-addons' ); ?>"></a>
								</li>
								<?php
							}
						}
						?>
					</ul>
					<input type="hidden" id="product_360_thumbnail_ids" name="product_360_thumbnail_ids" value="<?php echo esc_attr($product_360_ids); ?>">
				</p>
			</div>
			<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'       => 'product_360_position',
						'label'    => esc_html__( 'Position', 'glozin-addons' ),
						'data_type'    => 'decimal',
					)
				);
				?>
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

		if ( isset( $_POST['product_360_thumbnail_ids'] ) ) {
			$woo_data = $_POST['product_360_thumbnail_ids'];
			update_post_meta( $post_id, 'product_360_thumbnail_ids', $woo_data );
		} else {
			delete_post_meta( $post_id, 'product_360_thumbnail_ids' );
		}

		if ( isset( $_POST['product_360_position'] ) ) {
			$woo_data = $_POST['product_360_position'];
			update_post_meta( $post_id, 'product_360_position', $woo_data );
		}
	}
}
