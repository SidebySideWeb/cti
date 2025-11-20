<?php

namespace Glozin\Addons\Modules\Model_Sizing;

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

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'model_sizing_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( $this, 'model_sizing_options' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ) );
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
			wp_enqueue_script( 'glozin_wc_model_sizing_js', GLOZIN_ADDONS_URL . 'modules/model-sizing/assets/admin/model-sizing-admin.js', array( 'jquery' ), '20240506', true );
			wp_enqueue_style( 'glozin_wc_model_sizing_css', GLOZIN_ADDONS_URL . 'modules/model-sizing/assets/admin/model-sizing-admin.css' );
		}
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function model_sizing_tab( $tabs ) {
		$tabs['product_model_sizing'] = [
			'label'    => esc_html__( "Model's Sizing", 'glozin-addons' ),
			'target'   => 'product_model_sizing_data',
			'class'    => [ 'model_sizing_tab' ],
			'priority' => 62,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function model_sizing_options() {
		$model_sizing_id = get_post_meta( get_the_ID(), 'model_sizing_thumbnail_id', true );
		$attachment = wp_get_attachment_image( $model_sizing_id, 'thumbnail' );
		$remove_class = $model_sizing_id ? '' : 'hidden';
		$informations_custom = get_post_meta( get_the_ID(), 'model_sizing_informations_custom', true );
		?>
		<div id="product_model_sizing_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
		<div class="options_group">
				<p class=" form-field">
					<label><?php esc_html_e( 'Thumbnail', 'glozin-addons' ); ?></label>
					<span class="hide-if-no-js">
						<a href="#" id="set-model_sizing-thumbnail">
							<?php if( $model_sizing_id ) : ?>
								<?php echo $attachment; ?>
							<?php else : ?>
								<?php esc_html_e('Set thumbnail', 'glozin-addons'); ?>
							<?php endif; ?>
						</a>
						<br/>
						<a href="#" id="remove-model_sizing-thumbnail" class="<?php echo esc_attr($remove_class); ?>" data-set-text="<?php esc_attr_e('Set thumbnail', 'glozin-addons'); ?>">
							<?php esc_html_e('Remove thumbnail', 'glozin-addons'); ?>
						</a>
					</span>
					</span>
					<input type="hidden" id="model_sizing_thumbnail_id" name="model_sizing_thumbnail_id" value="<?php echo esc_attr($model_sizing_id); ?>">
				</p>
			</div>
			<div class="options_group">
				<?php
					woocommerce_wp_text_input(
						array(
							'id'       => 'model_sizing_wearing',
							'label'    => esc_html__( 'Model is Wearing', 'glozin-addons' ),
							'data_type'    => 'text',
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'       => 'model_sizing_height',
							'label'    => esc_html__( 'Height', 'glozin-addons' ),
							'data_type'    => 'text',
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'       => 'model_sizing_weight',
							'label'    => esc_html__( 'Weight', 'glozin-addons' ),
							'data_type'    => 'text',
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'       => 'model_sizing_shoulder_width',
							'label'    => esc_html__( 'Shoulder width', 'glozin-addons' ),
							'data_type'    => 'text',
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'       => 'model_sizing_bust_waist_hip',
							'label'    => esc_html__( 'Bust/waist/hip', 'glozin-addons' ),
							'data_type'    => 'text',
						)
					);
				?>
			</div>
			<div class="options_group">
				<h4 class="custom-information-item__title"><?php esc_html_e( 'Custom informations', 'glozin-addons' ); ?></h4>
				<?php if( ! empty( $informations_custom ) ) : ?>
					<?php foreach( (array) $informations_custom as $key => $information ) : ?>
						<div class="custom-information-item" data-remove="<?php esc_attr_e( 'Remove', 'glozin-addons' ); ?>">
							<input type="text" name="model_sizing_informations_custom_label[]" value="<?php echo esc_attr( $information['label'] ); ?>">
							<input type="text" name="model_sizing_informations_custom_value[]" value="<?php echo esc_attr( $information['value'] ); ?>">
							<?php if( $key > 0 ) : ?>
								<button type="button" class="button remove-custom-information">
									<?php esc_html_e( 'Remove', 'glozin-addons' ); ?>
								</button>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="custom-information-item" data-remove="<?php esc_attr_e( 'Remove', 'glozin-addons' ); ?>">
						<input type="text" name="model_sizing_informations_custom_label[]" value="">
						<input type="text" name="model_sizing_informations_custom_value[]" value="">
					</div>
				<?php endif; ?>
				<button type="button" class="button add-custom-information">
					<?php esc_html_e( 'Add custom information', 'glozin-addons' ); ?>
				</button>
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

		if ( isset( $_POST['model_sizing_thumbnail_id'] ) ) {
			$woo_data = $_POST['model_sizing_thumbnail_id'];
			update_post_meta( $post_id, 'model_sizing_thumbnail_id', $woo_data );
		}

		if ( isset( $_POST['model_sizing_wearing'] ) ) {
			$woo_data = $_POST['model_sizing_wearing'];
			update_post_meta( $post_id, 'model_sizing_wearing', $woo_data );
		} else {
			update_post_meta( $post_id, 'model_sizing_wearing', '' );
		}

		if ( isset( $_POST['model_sizing_height'] ) ) {
			$woo_data = $_POST['model_sizing_height'];
			update_post_meta( $post_id, 'model_sizing_height', $woo_data );
		} else {
			update_post_meta( $post_id, 'model_sizing_height', '' );
		}

		if ( isset( $_POST['model_sizing_weight'] ) ) {
			$woo_data = $_POST['model_sizing_weight'];
			update_post_meta( $post_id, 'model_sizing_weight', $woo_data );
		} else {
			update_post_meta( $post_id, 'model_sizing_weight', '' );
		}

		if ( isset( $_POST['model_sizing_shoulder_width'] ) ) {
			$woo_data = $_POST['model_sizing_shoulder_width'];
			update_post_meta( $post_id, 'model_sizing_shoulder_width', $woo_data );
		} else {
			update_post_meta( $post_id, 'model_sizing_shoulder_width', '' );
		}

		if ( isset( $_POST['model_sizing_bust_waist_hip'] ) ) {
			$woo_data = $_POST['model_sizing_bust_waist_hip'];
			update_post_meta( $post_id, 'model_sizing_bust_waist_hip', $woo_data );
		} else {
			update_post_meta( $post_id, 'model_sizing_bust_waist_hip', '' );
		}

		$args = [];
		if ( isset( $_POST['model_sizing_informations_custom_value'] ) && isset( $_POST['model_sizing_informations_custom_label'] ) ) {
			foreach( $_POST['model_sizing_informations_custom_value'] as $key => $value ) {
				$args[] = [
					'value' => $value,
					'label' => $_POST['model_sizing_informations_custom_label'][$key],
				];
			}
		}

		update_post_meta( $post_id, 'model_sizing_informations_custom', $args );
	}
}
