<?php
/**
 * Hooks for single post
 *
 * @package Glozin
 */

namespace Glozin\Addons;


/**
 * Class Single_Post
 */
class Single_Post {

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
		add_meta_box( 'glozin-single-post', esc_html__( 'Sidebar', 'glozin-addons' ), array( $this, 'render_meta_box' ), 'post', 'side', 'high' );
	}

	public function render_meta_box($post) {
		$sidebar = get_post_meta($post->ID, '_post_sidebar', true);
		$options = [
			'' 					=> esc_html__('Default', 'glozin-addons'),
			'no-sidebar' 		=> esc_html__('No Sidebar', 'glozin-addons'),
			'content-sidebar' 	=> esc_html__('Content Sidebar', 'glozin-addons'),
			'sidebar-content' 	=> esc_html__('Sidebar Content', 'glozin-addons'),
		];
		?>
		<select name="post_sidebar">
			<?php foreach ($options as $value => $label): ?>
				<option value="<?php echo esc_attr($value); ?>" <?php selected($sidebar, $value); ?>>
					<?php echo esc_html($label); ?>
				</option>
			<?php endforeach; ?>
		</select>
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
		if ( 'post' != $post->post_type ) {
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

		if ( !empty( $_POST['post_sidebar'] ) ) {
			update_post_meta( $post_id, '_post_sidebar', $_POST['post_sidebar'] );
		}

	}

}