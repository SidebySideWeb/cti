<?php
namespace Glozin\Addons\Modules\Customer_Reviews;

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

	const POST_TYPE = 'comment';

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

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );

		// Ajax function
		add_action( 'wp_ajax_glozin_customer_reviews_upload', array( $this, 'glozin_customer_reviews_upload' ) );
		add_action( 'wp_ajax_nopriv_glozin_customer_reviews_upload', array( $this, 'glozin_customer_reviews_upload' ) );

		add_action( 'wp_ajax_glozin_customer_reviews_detach', array( $this, 'glozin_customer_reviews_detach' ) );
		add_action( 'wp_ajax_nopriv_glozin_customer_reviews_detach', array( $this, 'glozin_customer_reviews_detach' ) );
	}

	/**
	 * Load scripts and style in admin area
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		if ( in_array( $hook, array( 'comment.php' ) ) ) {
			wp_enqueue_style( 'glozin-customer-reviews-admin', GLOZIN_ADDONS_URL . 'modules/customer-reviews/assets/admin/customer-reviews-admin.css' );
			wp_enqueue_script( 'glozin-customer-reviews-admin', GLOZIN_ADDONS_URL . 'modules/customer-reviews/assets/admin/customer-reviews-admin.js', [ 'jquery' ], GLOZIN_ADDONS_VER, true );

			if( $this->upload_video() ) {
				$file_type = __( 'Error: accepted file types are PNG, JPG, JPEG, GIF, MP4, MPEG, OGG, WEBM, MOV, AVI', 'glozin-addons' );
			} else {
				$file_type = __( 'Error: accepted file types are PNG, JPG, JPEG, GIF', 'glozin-addons' );
			}

			wp_localize_script(
				'glozin-customer-reviews-admin',
				'glozinCRA',
				array(
					'ajax_url'           => admin_url( 'admin-ajax.php' ),
					'upload_video'       => $this->upload_video(),
					'file_type'          => $file_type,
					'uploading'          => __( 'Uploading...', 'glozin-addons' ),
					'detach_yes'         => __( 'Yes', 'glozin-addons' ),
					'detach_no'          => __( 'No', 'glozin-addons' ),
					'cancel'             => __( 'Cancel', 'glozin-addons' ),
					'downloading'        => __( 'Downloading...', 'glozin-addons' ),
					'try_again'          => __( 'Try again', 'glozin-addons' ),
					'ok'                 => __( 'OK', 'glozin-addons' ),
					'cancelling'         => __( 'Cancelling...', 'glozin-addons' ),
					'download_cancelled' => __( 'Downloading of media file(s) was cancelled.', 'glozin-addons' )
				)
			);
		}
	}

	/**
	 * Add meta boxes
	 *
	 * @param object $post
	 */
	public function meta_boxes( $post ) {
		add_meta_box( 'glozin-customer-reviews-upload', esc_html__( 'Uploaded Media', 'glozin-addons' ), array( $this, 'customer_reviews_upload_meta_box' ), self::POST_TYPE, 'normal', 'default' );
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $comment
	 */
	public function customer_reviews_upload_meta_box( $comment ) {
		$author  = $comment->comment_author;
		$files   = get_comment_meta( $comment->comment_ID, 'glozin_customer_reviews_upload_files' );
		$files	 = ! empty( $files[0] ) ? $files[0] : [];

		if( $this->upload_video() ) {
			$label = esc_html__( 'Upload images or videos', 'glozin-addons' );
			$accept = 'image/*, video/*';
		} else {
			$label = esc_html__( 'Upload images', 'glozin-addons' );
			$accept = 'image/*';
		}
	?>
		<div class="glozin-customer-reviews__items">
		<?php foreach ( $files as $key => $id ) {
				$type = wp_attachment_is( 'video', $id ) ? 'video' : 'image';

				if( $type == 'video' && ! $this->upload_video() ) {
					continue;
				}
				?>
				<div class="glozin-customer-reviews__item glozin-customer-reviews__item-<?php echo esc_attr( $id ); ?>" data-type="<?php echo esc_attr( $type ); ?>">
					<?php if ( $type == 'video' ) : ?>
						<video preload="metadata" class="glozin-video" src="<?php echo esc_url( wp_get_attachment_url( $id ) ); ?>"></video>
						<?php echo \Glozin\Addons\Helper::get_svg( 'play', 'ui', 'class=glozin-customer-reviews__play' ); ?>
					<?php else: ?>
						<img src="<?php echo esc_url( wp_get_attachment_url( $id ) ); ?>" alt="<?php echo esc_attr( $author ); ?>">
					<?php endif; ?>
					<div class="glozin-customer-reviews__bg"></div>
					<?php echo \Glozin\Addons\Helper::get_svg( 'close', 'ui', 'class=glozin-customer-reviews__detach' ); ?>
					<div class="glozin-customer-reviews__condition hidden">
						<span class="yes" data-nonce="<?php echo wp_create_nonce( 'glozin-customer-reviews-detach' ); ?>" data-attachment="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( 'Yes', 'glozin-addons' ); ?></span>
						<span class="no"><?php echo esc_html( 'No', 'glozin-addons' ); ?></span>
					</div>
				</div>
				<?php
			} ?>
		</div>
		<div class="glozin-customer-reviews">
			<label for="glozin_customer_reviews_upload" class="glozin-customer-reviews-upload__message"><?php echo $label; ?></label>
			<input type="file" accept="<?php echo esc_attr( $accept ); ?>" multiple="multiple" name="glozin_customer_reviews_upload_<?php echo esc_attr( $comment->comment_ID ); ?>[]" id="glozin_customer_reviews_upload" />
			<input type="button" class="glozin-customer-reviews-upload__button" value="<?php esc_attr_e( 'Upload', 'glozin-addons' ); ?>" data-postid="<?php echo esc_attr( $comment->comment_post_ID ); ?>" data-commentid="<?php echo esc_attr( $comment->comment_ID ); ?>" data-nonce="<?php echo wp_create_nonce( 'glozin-customer-reviews-nonce' ); ?>"/>
		</div>
	<?php
	}

	public function glozin_customer_reviews_upload() {
		$return = array(
			'code'    => 100,
			'message' => array()
		);

		if( check_ajax_referer( 'glozin-customer-reviews-nonce', 'glozin_nonce', false ) ) {
			if( current_user_can( 'upload_files' ) ) {
				$args_id = array();
				$file_ids = get_comment_meta( $_POST['comment_id'], 'glozin_customer_reviews_upload_files' );

				if( isset( $_FILES ) && is_array( $_FILES ) ) {
					$uploadSuccess = array();
					$uploadError   = array();
					$comment       = get_comment( $_POST['comment_id'] );
					$commentAuthor = ! empty( $comment ) ? $comment->comment_author : '';
					$k             = intval( $_POST['count_files'] ) + 1;

					foreach( $_FILES as $file_id => $file ) {
						$attachmentId = media_handle_upload( $file_id, $_POST['post_id'] );
						if( ! is_wp_error( $attachmentId ) ) {
							$attachmentUrl = wp_get_attachment_url( $attachmentId );
							if( ! empty( $attachmentUrl ) ) {
								$uploadSuccess[] = array(
									'id'     => $attachmentId,
									'url'    => $attachmentUrl,
									'author' => sprintf( __( 'File #%1$d from ', 'glozin-addons' ), $k ) . $commentAuthor,
									'nonce'  => wp_create_nonce( 'glozin-customer-reviews-detach' ),
									'type'   => wp_attachment_is( 'image', $attachmentId ) ? 'image' : 'video',
								);

								array_push( $args_id, $attachmentId );

								$k++;
							} else {
								$uploadError[] = array(
									'code' => 501,
									'message' => $file['name'] . ': '. esc_html__( 'could not obtain URL of the attachment.', 'glozin-addons' )
								);
							}
						} else {
							$uploadError[] = array(
								'code'    => $attachmentId->get_error_code(),
								'message' => $attachmentId->get_error_message()
							);
						}
					}

					$countFiles   = count( $_FILES );
					$countSuccess = count( $uploadSuccess );
					if( $countSuccess === $countFiles ) {
						$return['code'] = 200;
					} elseif ( 0 < $countSuccess ) {
						$return['code'] = 201;
					} else {
						$return['code'] = 202;
					}

					$return['message'] = array( sprintf( __( '%1d of %2d files have been successfully uploaded.', 'glozin-addons' ), $countSuccess, $countFiles ) );
					$return['files']   = $uploadSuccess;
					foreach( $uploadError as $error ) {
						$return['message'][] = esc_html__( 'Error', 'glozin-addons' ) . ': ' . $error['message'];
					}
				}

				if( ! empty( $args_id ) ) {
					if( ! empty( $file_ids[0] ) ) {
						$args_id = array_merge( $file_ids[0], $args_id );
						update_comment_meta( $_POST['comment_id'], 'glozin_customer_reviews_upload_files', $args_id );
					} else {
						add_comment_meta( $_POST['comment_id'], 'glozin_customer_reviews_upload_files', $args_id );
					}
				}
			} else {
				$return['code'] = 501;
				$return['message'] = array( esc_html__( 'Error: no permission to upload files.', 'glozin-addons' ) );
			}
		} else {
			$return['code'] = 500;
			$return['message'] = array( esc_html__( 'Error: nonce validation failed. Please refresh the page and try again.', 'glozin-addons' ) );
		}

		wp_send_json( $return );
	}

	public function glozin_customer_reviews_detach() {
		$attachment_id = isset( $_POST['attachment_id'] ) ? $_POST['attachment_id'] : 0;
		$file_ids = get_comment_meta( $_POST['comment_id'], 'glozin_customer_reviews_upload_files' );

		$return = array(
			'code' => 0,
			'attachment' => $attachment_id
		);

		if( check_ajax_referer( 'glozin-customer-reviews-detach', 'nonce', false ) ) {
			if ( current_user_can( 'upload_files' ) ) {
				if( isset( $_POST['comment_id'] ) && 0 < $_POST['comment_id'] ) {
					if( isset( $_POST['attachment_id'] ) && 0 < $_POST['attachment_id'] ) {
						if( wp_delete_attachment( $_POST['attachment_id'], true ) ) {
							if( isset( $file_ids[0] ) && ( $key = array_search( $_POST['attachment_id'], $file_ids[0] ) ) !== false) {
								unset($file_ids[0][$key]);

								if( ! empty( $file_ids[0] ) ) {
									update_comment_meta( $_POST['comment_id'], 'glozin_customer_reviews_upload_files', $file_ids[0] );
								} else {
									delete_comment_meta( $_POST['comment_id'], 'glozin_customer_reviews_upload_files' );
								}
							}

							$return = array( 'code' => 1, 'attachment' => $_POST['attachment_id'] );
						}
					}
				}
			}
		}

		wp_send_json( $return );
	}

	public function upload_video() {
		if( get_option( 'glozin_customer_reviews_upload_video' ) == 'yes' ) {
			return true;
		}

		return false;
	}
}