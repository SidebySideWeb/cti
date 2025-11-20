<?php
/**
 * Product Category settings
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Admin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Category settings
 */
class Category_Settings {
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
	 * Placeholder image
	 *
	 * @since 1.0.0
	 * @var $placeholder_img_src
	 */
	public $placeholder_img_src;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Register custom post type and custom taxonomy
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		$this->placeholder_img_src = get_template_directory_uri() . '/images/placeholder.png';

		// Add form Category
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ), 30 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 20 );

		// Save fields
		add_action( 'created_term', array( $this, 'save_category_fields' ), 20, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 20, 3 );
	}

	/**
	 * Register admin scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ( $hook == 'edit-tags.php' &&  $screen->taxonomy == 'product_cat' ) || ( $hook == 'term.php' &&  $screen->taxonomy == 'product_cat' ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'glozin_product_cat_js', get_template_directory_uri() . "/assets/js/backend/product-cat.js", array( 'jquery' ), '20250329', true );
		}
	}

	/**
	 * Category thumbnail fields.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_category_fields() {
		?>
		<hr/>
		<div class="form-field">
            <label><?php esc_html_e( 'Page Header Background', 'glozin' ); ?></label>

            <div id="gz_page_header_bg" class="gz-page-header-bg">
                <ul class="gz-cat-page-header-bg"></ul>
                <input type="hidden" id="gz_page_header_bg_id" class="gz_page_header_bg_id" name="gz_page_header_bg_id"/>
                <button type="button"
                        data-delete="<?php esc_attr_e( 'Delete image', 'glozin' ); ?>"
                        data-text="<?php esc_attr_e( 'Delete', 'glozin' ); ?>"
                        class="upload_images_button button"><?php esc_html_e( 'Upload/Add Image', 'glozin' ); ?></button>
            </div>
            <div class="clear"></div>
        </div>
		<?php if(\Glozin\Helper::get_option('product_grid_banner')) : ?>
			<hr/>
			<div class="form-field">
				<label><?php esc_html_e( 'Product Grid Banner', 'glozin' ); ?></label>

				<div id="gz_cat_product_banner" class="gz-cat-shop-banner">
					<ul class="gz-cat-images"></ul>
					<input type="hidden" id="gz_cat_product_banner_id" class="gz_cat_product_banner_id" name="gz_cat_product_banner_id"/>
					<button type="button" data-multiple="1"
							data-delete="<?php esc_attr_e( 'Delete image', 'glozin' ); ?>"
							data-text="<?php esc_attr_e( 'Delete', 'glozin' ); ?>"
							class="upload_images_button button"><?php esc_html_e( 'Upload/Add Image', 'glozin' ); ?></button>
				</div>
				<div class="clear"></div>
			</div>

			<div class="form-field">
				<label><?php esc_html_e( 'Product Grid Banner Link', 'glozin' ); ?></label>

				<div class="gz-cat-shop-banner-link">
					<input type="text" id="gz_cat_product_banner_link" name="gz_cat_product_banner_link" />
				</div>
				<div class="clear"></div>
			</div>
			<div class="form-field">
				<label><?php esc_html_e( 'Product Grid Banner Position', 'glozin' ); ?></label>

				<div class="gz-cat-shop-banner-position">
					<input type="number" id="gz_cat_product_banner_position" name="gz_cat_product_banner_position" />
				</div>
				<div class="clear"></div>
			</div>
			<?php
		endif;
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $term Term (category) being edited
     *
	 * @return void
	 */
	public function edit_category_fields( $term ) {
		$banners_id        = get_term_meta( $term->term_id, 'gz_cat_product_banner_id', true );
		$banners_link      = get_term_meta( $term->term_id, 'gz_cat_product_banner_link', true );
		$page_header_bg_id = get_term_meta( $term->term_id, 'gz_page_header_bg_id', true );
		$banners_position  = get_term_meta( $term->term_id, 'gz_cat_product_banner_position', true );
		?>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Page Header Background', 'glozin' ); ?></label></th>
            <td>
                <div id="gz_page_header_bg" class="gz-page-header-bg">
                    <ul class="gz-cat-page-header-bg">
						<?php

						if ( $page_header_bg_id ) {
							$image = wp_get_attachment_thumb_url( $page_header_bg_id );
							?>
							<li class="image" data-attachment_id="<?php echo esc_attr( $page_header_bg_id ); ?>">
								<img src="<?php echo esc_url( $image ); ?>" width="100px" height="100px"/>
								<ul class="actions">
									<li>
										<a href="#" class="delete"
											title="<?php esc_attr_e( 'Delete image', 'glozin' ); ?>"><?php esc_html_e( 'Delete', 'glozin' ); ?></a>
									</li>
								</ul>
							</li>
							<?php
						}
						?>
                    </ul>
                    <input type="hidden" id="gz_page_header_bg_id" class="gz_page_header_bg_id" name="gz_page_header_bg_id"
                           value="<?php echo esc_attr( $page_header_bg_id ); ?>"/>
                    <button type="button"
                            data-delete="<?php esc_attr_e( 'Delete image', 'glozin' ); ?>"
                            data-text="<?php esc_attr_e( 'Delete', 'glozin' ); ?>"
                            class="upload_images_button button"><?php esc_html_e( 'Upload/Add Image', 'glozin' ); ?></button>
                </div>
                <div class="clear"></div>
            </td>
        </tr>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<?php if(\Glozin\Helper::get_option('product_grid_banner')) : ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Product Grid Banner', 'glozin' ); ?></label></th>
            <td>
                <div id="gz_cat_product_banner" class="gz-cat-shop-banner">
                    <ul class="gz-cat-image">
						<?php

						if ( $banners_id ) {
							$image = wp_get_attachment_thumb_url( $banners_id );
							if ( ! empty( $image ) ) { ?>
								<li class="image" data-attachment_id="<?php echo esc_attr( $banners_id ); ?>">
									<img src="<?php echo esc_url( $image ); ?>" width="100px" height="100px"/>
									<ul class="actions">
										<li>
											<a href="#" class="delete"
												title="<?php esc_attr_e( 'Delete image', 'glozin' ); ?>"><?php esc_html_e( 'Delete', 'glozin' ); ?></a>
										</li>
									</ul>
								</li>
							<?php } 
						} ?>
                    </ul>
                    <input type="hidden" id="gz_cat_product_banner_id" class="gz_cat_product_banner_id" name="gz_cat_product_banner_id"
                           value="<?php echo esc_attr( $banners_id ); ?>"/>
                    <button type="button" data-multiple="1"
                            data-delete="<?php esc_attr_e( 'Delete image', 'glozin' ); ?>"
                            data-text="<?php esc_attr_e( 'Delete', 'glozin' ); ?>"
                            class="upload_images_button button"><?php esc_html_e( 'Upload/Add Image', 'glozin' ); ?></button>
                </div>
                <div class="clear"></div>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Product Grid Banner Link', 'glozin' ); ?></label></th>
            <td>
                <div class="gz-cat-shop-banner-link">
                    <input type="text" id="gz_cat_product_banner_link" name="gz_cat_product_banner_link" value="<?php echo esc_url( $banners_link ); ?>" />
                </div>
                <div class="clear"></div>
            </td>
        </tr>	
		<tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Product Grid Banner Position', 'glozin' ); ?></label></th>
            <td>
                <div class="gz-cat-shop-banner-position">
                    <input type="number" id="gz_cat_product_banner_position" name="gz_cat_product_banner_position" value="<?php echo esc_attr( $banners_position ); ?>" />
                </div>
			</td>
		</tr>
		<?php
		endif;
	}

	/**
	 * Save Category fields
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
     *
	 * @return void
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'product_cat' === $taxonomy && function_exists( 'update_term_meta' ) ) {
			if ( isset( $_POST['gz_cat_product_banner_id'] ) ) {
				update_term_meta( $term_id, 'gz_cat_product_banner_id', sanitize_text_field( $_POST['gz_cat_product_banner_id'] ) );
			}

			if ( isset( $_POST['gz_cat_product_banner_link'] ) ) {
				update_term_meta( $term_id, 'gz_cat_product_banner_link', sanitize_text_field( $_POST['gz_cat_product_banner_link'] ) );
			}

			if ( isset( $_POST['gz_cat_product_banner_position'] ) ) {
				update_term_meta( $term_id, 'gz_cat_product_banner_position', sanitize_text_field( $_POST['gz_cat_product_banner_position'] ) );
			}

			if ( isset( $_POST['gz_page_header_bg_id'] ) ) {
				update_term_meta( $term_id, 'gz_page_header_bg_id', sanitize_text_field( $_POST['gz_page_header_bg_id'] ) );
			}
		}
		
	}
}
