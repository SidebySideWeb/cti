<?php
/**
 * Hooks for page header
 *
 * @package Glozin
 */

namespace Glozin\Addons;


/**
 * Class Page_Header
 */
class Page_Header {

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

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	public function meta_boxes() {
		add_meta_box( 'glozin-page-header', esc_html__( 'Page Header Description', 'glozin-addons' ), array( $this, 'render_meta_box' ), 'page', 'normal', 'high' );
	}

	public function render_meta_box($post) {
		$description = get_post_meta($post->ID, '_page_header_description', true);
		?>
		<textarea name="page_header_description" style="width:100%;height:100px;"><?php echo esc_textarea($description); ?></textarea>
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
		if ( 'page' != $post->post_type ) {
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

		if ( isset( $_POST['page_header_description'] ) ) {
			update_post_meta( $post_id, '_page_header_description', $_POST['page_header_description'] );
		}

	}

}