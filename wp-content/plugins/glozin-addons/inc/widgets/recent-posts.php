<?php
/**
 * Recent posts widget
 *
 * @package Glozin
 */

namespace Glozin\Addons\Widgets;

/**
 * Class Recent_Posts_Widget
 */
class Recent_Posts_Widget extends \WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Class constructor
	 * Set up the widget
	 */
	public function __construct() {
		$this->defaults = array(
			'title'    => '',
			'limit' => 3,
		);

		parent::__construct(
			'glozin-recent-posts-widget',
			esc_html__( 'Glozin - Recent Posts', 'glozin-addons' ),
			array(
				'classname'                   => 'glozin-recent-posts-widget',
				'description'                 => esc_html__( 'Displays recent posts', 'glozin-addons' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Outputs the content for the current Archives widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Archives widget instance.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		$recent_posts = new \WP_Query( array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $instance['limit'],
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
			'ignore_sticky_posts'    => true,
		) );

		if ( ! $recent_posts->have_posts() ) {
			return;
		}

		echo $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>

		<ul class="list-unstyled">
			<?php while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
				<li class="d-flex align-items-center gap-15 mb-20">
					<?php $this->entry_thumbnail(); ?>
					<div class="post-summary flex-1">
						<div class="post-title fw-semibold mb-5 lh-normal"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute() ?>"><?php the_title(); ?></a></div>
						<div class="post-meta fs-13"><?php echo get_the_date(); ?></div>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>

		<?php
		wp_reset_postdata();

		echo $args['after_widget'];
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance New widget settings
	 * @param array $old_instance Old widget settings
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['title']    = strip_tags( $new_instance['title'] );
		$new_instance['limit'] = intval( $new_instance['limit'] );

		return $new_instance;
	}

	/**
	 * Outputs the settings form for the Archives widget.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'glozin-addons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php esc_html_e( 'Number of posts:', 'glozin-addons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo intval( $instance['limit'] ); ?>" />
		</p>

		<?php
	}

	public function entry_thumbnail() {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		$get_image         = wp_get_attachment_image( $post_thumbnail_id, 'thumbnail' );

		if ( empty( $get_image ) ) {
			return;
		}

		echo sprintf(
			'<a class="post-thumbnail gz-image-rounded" href="%s" aria-hidden="true" tabindex="-1">%s</a>',
			esc_url( get_permalink() ),
			$get_image
		);
	}
}