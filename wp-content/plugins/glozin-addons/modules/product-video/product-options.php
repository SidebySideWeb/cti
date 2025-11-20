<?php

namespace Glozin\Addons\Modules\Product_Video;

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

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_video_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_product_video_options' ) );
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
			wp_enqueue_script( 'glozin_wc_product_video_js', GLOZIN_ADDONS_URL . 'modules/product-video/assets/admin/product-video-admin.js', array( 'jquery' ), '20240506', true );
		}
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_video_tab( $tabs ) {
		$tabs['product_product_video'] = [
			'label'    => esc_html__( 'Video', 'glozin-addons' ),
			'target'   => 'product_product_video_data',
			'class'    => [ 'product_video_tab' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_product_video_options() {
		$video_id = get_post_meta(get_the_ID(), 'video_thumbnail_id', true);
		$attachment = wp_get_attachment_image( $video_id, 'thumbnail' );
		$remove_class = $video_id ? '' : 'hidden';
		?>
		<div id="product_product_video_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group">
				<p class=" form-field">
					<label><?php esc_html_e( 'Video Thumbnail', 'glozin-addons' ); ?></label>
					<span class="hide-if-no-js">
						<a href="#" id="set-video-thumbnail">
							<?php if( $video_id ) : ?>
								<?php echo $attachment; ?>
							<?php else : ?>
								<?php esc_html_e('Set video thumbnail', 'glozin-addons'); ?>
							<?php endif; ?>
						</a>
						<br/>
						<a href="#" id="remove-video-thumbnail" class="<?php echo esc_attr($remove_class); ?>" data-set-text="<?php esc_attr_e('Set video thumbnail', 'glozin-addons'); ?>">
							<?php esc_html_e('Remove video thumbnail', 'glozin-addons'); ?>
						</a>
					</span>
					</span>
					<input type="hidden" id="video_thumbnail_id" name="video_thumbnail_id" value="<?php echo esc_attr($video_id); ?>">
				</p>
			</div>
			<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'       => 'video_url',
						'label'    => esc_html__( 'Video URL', 'glozin-addons' ),
						'data_type'    => 'url',
						'desc_tip'    => true,
						'description' => esc_html__( 'Enter URL of Youtube or Vimeo or specific filetypes such as mp4, webm, ogv.', 'glozin-addons' ),
					)
				);
				?>
			</div>
			<div class="options_group">
				<p class="form-field">
					<label style="width: auto;"><strong><?php echo esc_html__( 'Product Card Options', 'glozin-addons' ); ?></strong></label>
				</p>
				<?php
				$value = get_post_meta(get_the_ID(), 'video_show_on_product_card', true);

				woocommerce_wp_checkbox(
					array(
						'id'       => 'video_show_on_product_card',
						'label'    => esc_html__( 'Enable', 'glozin-addons' ),
						'value'	=> $value ? $value : '',
						'unchecked_value' => ''
					)
				);
				?>
				<?php
				$value = get_post_meta(get_the_ID(), 'video_product_card_autoplay', true);

				woocommerce_wp_checkbox(
					array(
						'id'       => 'video_product_card_autoplay',
						'label'    => esc_html__( 'Video Autoplay', 'glozin-addons' ),
						'value'	=> $value ? $value : '',
						'unchecked_value' => ''
					)
				);
				?>
			</div>
			<div class="options_group">
				<p class="form-field">
					<label style="width: auto;"><strong><?php echo esc_html__( 'Single Product Options', 'glozin-addons' ); ?></strong></label>
				</p>
				<?php
				$value = get_post_meta(get_the_ID(), 'video_show_on_single_product', true);

				woocommerce_wp_checkbox(
					array(
						'id'       => 'video_show_on_single_product',
						'label'    => esc_html__( 'Enable', 'glozin-addons' ),
						'value'	=> $value ? $value : '',
						'unchecked_value' => ''
					)
				);
				?>
				<?php
				woocommerce_wp_text_input(
					array(
						'id'       => 'video_position',
						'label'    => esc_html__( 'Video Position', 'glozin-addons' ),
						'data_type'    => 'decimal',
					)
				);
				?>
				<?php
				$value = get_post_meta(get_the_ID(), 'video_autoplay', true);

				woocommerce_wp_checkbox(
					array(
						'id'       => 'video_autoplay',
						'label'    => esc_html__( 'Video Autoplay', 'glozin-addons' ),
						'value'	=> $value ? $value : '',
						'unchecked_value' => ''
					)
				);
				?>
				<?php
				$value = get_post_meta(get_the_ID(), 'hide_video_controls', true);

				woocommerce_wp_checkbox(
					array(
						'id'       => 'hide_video_controls',
						'label'    => esc_html__( 'Hide Video Controls', 'glozin-addons' ),
						'value'	=> $value ? $value : '',
						'unchecked_value' => ''
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

		if ( isset( $_POST['video_thumbnail_id'] ) ) {
			$woo_data = $_POST['video_thumbnail_id'];
			update_post_meta( $post_id, 'video_thumbnail_id', $woo_data );
		}

		if ( isset( $_POST['video_position'] ) ) {
			$woo_data = $_POST['video_position'];
			update_post_meta( $post_id, 'video_position', $woo_data );
		}

		if ( isset( $_POST['video_url'] ) ) {
			$woo_data = $_POST['video_url'];
			update_post_meta( $post_id, 'video_url', $woo_data );
		}

		if ( isset( $_POST['video_autoplay'] ) ) {
			$woo_data = $_POST['video_autoplay'];
			update_post_meta( $post_id, 'video_autoplay', $woo_data );
		}

		if ( isset( $_POST['hide_video_controls'] ) ) {
			$woo_data = $_POST['hide_video_controls'];
			update_post_meta( $post_id, 'hide_video_controls', $woo_data );
		}

		if ( isset( $_POST['video_show_on_single_product'] ) ) {
			$woo_data = $_POST['video_show_on_single_product'];
			update_post_meta( $post_id, 'video_show_on_single_product', $woo_data );
		}

		if ( isset( $_POST['video_show_on_product_card'] ) ) {
			$woo_data = $_POST['video_show_on_product_card'];
			update_post_meta( $post_id, 'video_show_on_product_card', $woo_data );
		}

		if ( isset( $_POST['video_product_card_autoplay'] ) ) {
			$woo_data = $_POST['video_product_card_autoplay'];
			update_post_meta( $post_id, 'video_product_card_autoplay', $woo_data );
		}
		
		
	}
}
