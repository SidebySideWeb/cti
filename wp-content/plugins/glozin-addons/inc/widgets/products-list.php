<?php
/**
 * Recent posts widget
 *
 * @package Glozin
 */

namespace Glozin\Addons\Widgets;
use Glozin\Addons\Elementor\Base\Products_Widget_Base;

/**
 * Class Products_List
 */
class Products_List extends \WP_Widget {
	use \Glozin\Addons\WooCommerce\Products_Base;

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
			'title'    	=> '',
			'limit' 	=> 3,
			'type' 		=> 'recent_products',
		);

		parent::__construct(
			'glozin-products-list',
			esc_html__( 'Glozin - Products List', 'glozin-addons' ),
			array(
				'classname'                   => 'glozin-products-list-widget',
				'description'                 => esc_html__( 'Displays products list', 'glozin-addons' ),
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

		echo $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$attr = [
			'type' => $instance['type'],
			'limit' => $instance['limit'],
		];

		$query_posts = self::products_shortcode( $attr );
		$query_posts = ! empty($query_posts) ? $query_posts['ids'] : 0;

		if ( $query_posts ) {
			echo '<ul class="products glozin-products-list-widget__wrapper">';
			\Glozin\Addons\Helper::products_list_shortcode_template( $query_posts, [ 'show_rating' => true ] );
			echo '</ul>';
		}

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
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php esc_html_e( 'Number of products to show:', 'glozin-addons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo intval( $instance['limit'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php esc_html_e( 'Type:', 'glozin-addons' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<option value="recent_products"><?php esc_html_e( 'Recent Products', 'glozin-addons' ); ?></option>
				<option value="sale_products"><?php esc_html_e( 'Sale Products', 'glozin-addons' ); ?></option>
				<option value="top_rated_products"><?php esc_html_e( 'Top Rated Products', 'glozin-addons' ); ?></option>
				<option value="featured_products"><?php esc_html_e( 'Featured Products', 'glozin-addons' ); ?></option>
			</select>
		</p>

		<?php
	}
}