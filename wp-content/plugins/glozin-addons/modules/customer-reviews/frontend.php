<?php

namespace Glozin\Addons\Modules\Customer_Reviews;

use Glozin\Addons\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	private $limit = 5000000;
	private $size = 3;


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

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->limit = get_option( 'glozin_customer_reviews_upload_limit', 5 );
		$this->size  = 1024 * 1024 * get_option( 'glozin_customer_reviews_upload_size', 25 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action('woocommerce_review_after_comment_text', array( $this, 'display_review_attachments' ) );

		add_action( 'woocommerce_product_review_comment_form_args', array( $this, 'custom_fields_attachment' ) );
		add_action( 'comment_post', array( $this, 'handle_file_upload_in_comment' ), 10, 2 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'glozin-customer-reviews', GLOZIN_ADDONS_URL . 'modules/customer-reviews/assets/customer-reviews' . $debug . '.css', array(), GLOZIN_ADDONS_VER );
		wp_enqueue_script( 'glozin-customer-reviews', GLOZIN_ADDONS_URL . 'modules/customer-reviews/assets/customer-reviews' . $debug . '.js',  array('jquery'), GLOZIN_ADDONS_VER, array('strategy' => 'defer') );

		if( $this->upload_video() ) {
			$label = sprintf( __( 'Upload images or videos (Max %d files)', 'glozin-addons' ), intval( $this->limit ) );
			$message = sprintf( __( 'Upload up to %d images or videos', 'glozin-addons' ), intval( $this->limit ) );
			$file_type = __( 'Error: accepted file types are PNG, JPG, JPEG, GIF, MP4, MPEG, OGG, WEBM, MOV, AVI', 'glozin-addons' );
		} else {
			$label = sprintf( __( 'Upload images (Max %d files)', 'glozin-addons' ), intval( $this->limit ) );
			$message = sprintf( __( 'Upload up to %d images', 'glozin-addons' ), intval( $this->limit ) );
			$file_type = __( 'Error: accepted file types are PNG, JPG, JPEG, GIF', 'glozin-addons' );
		}

		wp_localize_script(
			'glozin-customer-reviews',
			'glozinCRA',
			array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'upload_video' => $this->upload_video(),
				'limit'    	   => intval( $this->limit ),
				'size' 		   => intval( $this->size ),
				'label'        => $label,
				'message'      => $message,
				'error'        => array(
					'file_type'   => $file_type,
					'too_many'    => sprintf( __( 'Error: You tried to upload too many files. The maximum number of files that can be uploaded is %d.', 'glozin-addons' ), intval( $this->limit ) ),
					'file_size'   => sprintf( __( 'The file cannot be uploaded because its size exceeds the limit of %d MB', 'glozin-addons' ), intval( $this->size / 1024 / 1024 ) ),
				),
			)
		);
	}

	public function display_review_attachments( $comment ) {
		$attachment_ids = get_comment_meta( $comment->comment_ID, 'glozin_customer_reviews_upload_files', true );
		$thumbnail_url = ! empty( wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' ) ) ? wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' )[0] : wc_placeholder_img_src();
		$thumbnail_url = ! empty( $thumbnail_url ) ? $thumbnail_url : wc_placeholder_img_src();

		if( ! $comment->comment_approved ) {
			return;
		}
		
		if ( empty( $attachment_ids[0] ) ) {
			return;
		}
		
		?>
		<div class="glozin-customer-reviews__attachments">
			<?php foreach ($attachment_ids as $attachment_id) :
				$type = wp_attachment_is( 'video', $attachment_id ) ? 'video' : 'image';

				if( $type == 'video' && ! $this->upload_video() ) {
					continue;
				}

				?>
				<div class="glozin-customer-reviews__attachment" data-type="<?php echo esc_attr( $type ); ?>">
					<?php echo $type == 'image' ? wp_get_attachment_link( $attachment_id, 'full' ) : '<a href="'. esc_url( wp_get_attachment_url( $attachment_id ) ) .'"><img src="'.  esc_url( $thumbnail_url ) .'" atl="'. esc_attr( $comment->comment_author ) .'" />'. \Glozin\Addons\Helper::get_svg( 'play', 'ui', 'class=glozin-customer-reviews__play' ) .'</a>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	public function custom_fields_attachment( $comment_form ) {
		if( ! apply_filters( 'glozin_customer_reviews_custom_fields_attachment_enabled', true ) ) {
			return $comment_form;
		}

		$post_id = get_the_ID();
		$thumbnail_url = ! empty( wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' ) ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' )[0] : wc_placeholder_img_src();
		$thumbnail_url = ! empty( $thumbnail_url ) ? $thumbnail_url : wc_placeholder_img_src();

		if( $this->upload_video() ) {
			$label = sprintf( __( 'Upload images or videos (Max %d files)', 'glozin-addons' ), intval( $this->limit ) );
			$accept = 'image/*, video/*';
		} else {
			$label = sprintf( __( 'Upload images (Max %d files)', 'glozin-addons' ), intval( $this->limit ) );
			$accept = 'image/*';
		}

		$comment_form['comment_field'] .= apply_filters( 'glozin_customer_reviews_custom_fields_attachment_html',
												'<div class="glozin-customer-reviews">
													<label for="glozin_customer_reviews_files" class="glozin-customer-reviews__message">'. $label .'</label>
													<div class="position-relative">
														<input type="file" accept="'. esc_attr( $accept ) .'" multiple="multiple" name="glozin_customer_reviews_files[]" id="glozin-customer-reviews-files" class="w-100 h-100 py-50" data-nonce="'. wp_create_nonce( 'glozin-customer-reviews-upload-frontend' ) .'" data-postid="'. esc_attr( $post_id ) .'" />
														<div class="position-absolute z-1 pe-none start-0 end-0 top-0 bottom-0 bg-light border-dashed d-flex justify-content-center align-items-center rounded-20">'.\Glozin\Addons\Helper::get_svg( 'cloud', 'ui', [ 'class' => 'fs-40'] ).'</div>
													</div>
													<div class="glozin-customer-reviews__items"></div>
													<input type="hidden" name="thumbnail_url" value="'. esc_url( $thumbnail_url ) .'" />
												</div>'
											);

		return apply_filters( 'glozin_customer_reviews_custom_fields_attachment', $comment_form );
	}

	public function handle_file_upload_in_comment( $comment_id, $comment ) {
		if( ! empty( $_FILES['glozin_customer_reviews_files']['name'][0] ) ) {
			$files = $_FILES['glozin_customer_reviews_files'];
		
			if( count( $files['name'] ) > $this->limit ) {
				$commentdata = get_comment( $comment_id );
				$message = '<div class="wp-die-message">' . sprintf( __( 'Error: You tried to upload too many files. The maximum number of files that you can upload is %d.', 'glozin-addons' ), $this->limit ) . '</div>';
				$message .= '<p>' . sprintf( __( 'Go back to: %s', 'glozin-addons' ), '<a href="' . get_permalink( $commentdata->comment_post_ID ) . '">' . get_the_title( $commentdata->comment_post_ID ) . '</a>' ) . '</p>';
				wp_die( $message );
				return;
			}

			$args_id = array();
			foreach ( $files['name'] as $key => $filename ) {
				if ( $files['error'][$key] === UPLOAD_ERR_OK ) {
					// Move the uploaded file to a directory
					$file = array(
						'name'     => $files['name'][$key],
						'type'     => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error'    => $files['error'][$key],
						'size'     => $files['size'][$key],
					);

					$_FILES = array( 'glozin_customer_reviews_files' => $file );

					foreach ( $_FILES as $file => $array ) {
						$attachment_id = $this->insert_attachment( $file, $comment_id );
						
						// Check if the upload was successful
						if ( is_wp_error( $attachment_id ) ) {
							// Handle error (optional)
							wp_die(__('File upload error: ', 'woocommerce') . $attachment_id->get_error_message());
						} else {
							// Store the attachment URL
							array_push( $args_id, $attachment_id );
						}
					}
				}
			}
	
			 // Save all uploaded file URLs as comment meta
			 if ( ! empty( $args_id )) {
				add_comment_meta($comment_id, 'glozin_customer_reviews_upload_files', $args_id);
			}
		}
	}

	/**
	 * Add attachment to media library
	 *
	 * @param   int    $postId
	 * @param   string $fileHandler
	 *
	 * @return  void
	 *
	 * @since  1.0
	 * @author Lorenzo Giuffrida
	 */
	public function insert_attachment( $fileHandler, $postId ) {	
		require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		
		return media_handle_upload( $fileHandler, $postId );
	}

	public function upload_video() {
		if( get_option( 'glozin_customer_reviews_upload_video' ) == 'yes' ) {
			return true;
		}

		return false;
	}
}