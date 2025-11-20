<?php
namespace Glozin\Addons\Elementor\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Categories {
	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action('wc_ajax_glozin_load_more_categories', [ $this, 'load_more_categories' ]);
	}

	public function load_more_categories() {
		$paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$number = isset($_POST['number']) ? intval($_POST['number']) : 8;
		$button_type = isset($_POST['button_type']) ? $_POST['button_type'] : '';

		$button_class = ! empty( $button_type ) ? ' gz-button-'  . $button_type : '';
		$button_class .= in_array( $button_type, ['', 'light', 'outline-dark' , 'outline'] ) ? ' py-17 px-20' : '';
		$button_class .= in_array( $button_type, ['', 'light', 'outline-dark'] ) ? ' gz-button-hover-effect' : '';

		$total_terms = count(get_terms('product_cat'));
		$total_pages = ceil( $total_terms / intval( $number ) );

		$args = [
			'taxonomy' => 'product_cat',
			'number'   => $number,
			'offset'   => ($paged - 1) * $number,
			'orderby'  => 'id',
		];

		$terms = get_terms($args);

		echo '<div class="glozin-categories-grid__items">';

		foreach ($terms as $term) {
			$thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
			$image = $thumbnail_id ? wp_get_attachment_image($thumbnail_id, 'full') : '<img src="' . wc_placeholder_img_src() . '" alt="' . esc_attr($term->name) . '"/>';

			echo '<div class="glozin-categories-grid__item">
					<a href="' . esc_url(get_term_link($term)) . '" class="gz-ratio gz-hover-zoom gz-hover-effect overflow-hidden gz-image-rounded position-relative">
						' . $image . '
						<span class="glozin-categories-grid__button position-absolute bottom-15 bottom-30-md start-50 translate-middle-x z-3">
							<span class="glozin-button gz-button'. esc_attr($button_class) .'">
								<span class="glozin-categories-grid__text">'. esc_html($term->name) .'</span>
							</span>
						</span>
					</a>
				</div>';
		}

		echo '</div>';

		if ( $paged < $total_pages ) {
			echo '<nav class="woocommerce-pagination">';

			echo sprintf( '<a href="#" class="woocommerce-pagination-button" data-page="%s"></a>',
				esc_attr( $paged + 1 )
			);

			echo '</nav>';
		}
	}
}