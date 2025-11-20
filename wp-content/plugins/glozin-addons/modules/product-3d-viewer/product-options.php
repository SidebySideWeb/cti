<?php

namespace Glozin\Addons\Modules\Product_3D_Viewer;

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

		add_filter( 'upload_mimes', [ $this, 'upload_mimes' ] );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'add_allow_upload_extension_exception'], 10, 5 );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_3d_viewer_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_product_3d_viewer_options' ) );
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
			wp_enqueue_script( 'glozin_wc_product_3d_viewer_js', GLOZIN_ADDONS_URL . 'modules/product-3d-viewer/assets/admin/product-3d-viewer-admin.js', array( 'jquery' ), '20240506', true );
		}
	}

	/**
	 * Mime types
	 *
	 * @return array
	 */
	public function upload_mimes( $mimes ) {
        $mimes['glb']  = 'model/gltf-binary';
        $mimes['gltf'] = 'model/gltf-binary';

        return $mimes;
    }

	/**
	 * Allowed MIME types
	 *
	 * @return void
	 */
	function add_allow_upload_extension_exception( $data, $file, $filename, $mimes, $real_mime=null ) {
        $f_sp        = explode(".", $filename);
        $f_exp_count = count ($f_sp);

        if($f_exp_count <= 1){
            return $data;
        }else{
            $f_name = $f_sp[0];
            $ext    = $f_sp[$f_exp_count - 1];
        }

        $extendedMimes = [
            'glb' => 'model/gltf-binary',
            'gltf' => 'model/gltf-binary',
        ];

        if( isset( $extendedMimes[$ext] ) ) {
            $type = $extendedMimes[$ext];
            $proper_filename = '';
            return compact( 'ext', 'type', 'proper_filename' );
        }

        return $data;
    }

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_3d_viewer_tab( $tabs ) {
		$tabs['product_product_3d_viewer'] = [
			'label'    => esc_html__( 'Product 3D Viewer', 'glozin-addons' ),
			'target'   => 'product_product_3d_viewer_data',
			'class'    => [ 'product_3d_viewer_tab' ],
			'priority' => 62,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_product_3d_viewer_options() {
		$product_3d_viewer_id = get_post_meta( get_the_ID(), 'product_3d_viewer_thumbnail_id', true );
		$attachment = wp_get_attachment_image( $product_3d_viewer_id, 'thumbnail' );
		$remove_class = $product_3d_viewer_id ? '' : 'hidden';
		?>
		<div id="product_product_3d_viewer_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group">
				<p class=" form-field">
					<label><?php esc_html_e( 'Thumbnail', 'glozin-addons' ); ?></label>
					<span class="hide-if-no-js">
						<a href="#" id="set-product_3d_viewer-thumbnail">
							<?php if( $product_3d_viewer_id ) : ?>
								<?php echo $attachment; ?>
							<?php else : ?>
								<?php esc_html_e('Set thumbnail', 'glozin-addons'); ?>
							<?php endif; ?>
						</a>
						<br/>
						<a href="#" id="remove-product_3d_viewer-thumbnail" class="<?php echo esc_attr($remove_class); ?>" data-set-text="<?php esc_attr_e('Set thumbnail', 'glozin-addons'); ?>">
							<?php esc_html_e('Remove thumbnail', 'glozin-addons'); ?>
						</a>
					</span>
					</span>
					<input type="hidden" id="product_3d_viewer_thumbnail_id" name="product_3d_viewer_thumbnail_id" value="<?php echo esc_attr($product_3d_viewer_id); ?>">
				</p>
			</div>
			<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'       => 'product_3d_viewer_url',
						'label'    => esc_html__( '3D URL', 'glozin-addons' ),
						'data_type'    => 'url',
						'desc_tip'    => true,
						'description' => esc_html__( 'Enter the gltf/glb file url', 'glozin-addons' ),
					)
				);
				?>
			</div>
			<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'       => 'product_3d_viewer_position',
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

		if ( isset( $_POST['product_3d_viewer_thumbnail_id'] ) ) {
			$woo_data = $_POST['product_3d_viewer_thumbnail_id'];
			update_post_meta( $post_id, 'product_3d_viewer_thumbnail_id', $woo_data );
		}

		if ( isset( $_POST['product_3d_viewer_position'] ) ) {
			$woo_data = $_POST['product_3d_viewer_position'];
			update_post_meta( $post_id, 'product_3d_viewer_position', $woo_data );
		}

		if ( isset( $_POST['product_3d_viewer_url'] ) ) {
			$woo_data = $_POST['product_3d_viewer_url'];
			update_post_meta( $post_id, 'product_3d_viewer_url', $woo_data );
		}

	}
}
