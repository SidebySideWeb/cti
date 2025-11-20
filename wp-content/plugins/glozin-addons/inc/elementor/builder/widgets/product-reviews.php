<?php
namespace Glozin\Addons\Elementor\Builder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Reviews extends Widget_Base {
	use \Glozin\Addons\Elementor\Builder\Traits\Product_Id_Trait;

	public function get_name() {
		return 'glozin-product-reviews';
	}

	public function get_title() {
		return esc_html__( '[Glozin] Product Reviews', 'glozin-addons' );
	}

	public function get_icon() {
		return 'eicon-review';
	}

	public function get_categories() {
		return ['glozin-addons-product'];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'data', 'tabs', 'reviews', 'product' ];
	}

	public function get_script_depends() {
		return [
			'glozin-product-elementor-widgets'
		];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		global $product;

		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			global $wpdb;

    		// Query to find one product ID with an approved review
			$product_id = $wpdb->get_var("
				SELECT c.comment_post_ID
				FROM {$wpdb->comments} AS c
				INNER JOIN {$wpdb->posts} AS p ON p.ID = c.comment_post_ID
				WHERE c.comment_approved = '1'
				AND c.comment_type = 'review'
				AND p.post_type = 'product'
				AND p.post_status = 'publish'
				LIMIT 1
			");

			if ( ! empty( $product_id ) ) {
				$product = wc_get_product($product_id);
			}
		}

		setup_postdata( $product->get_id() );

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$original_post = $GLOBALS['post'];
			$GLOBALS['post'] = get_post( $product->get_id() );
			setup_postdata( $GLOBALS['post'] );
		}

		add_action( 'woocommerce_review_before', array( $this, 'review_before_open' ), 1 );
		add_action( 'woocommerce_review_before_comment_text', array( $this, 'review_before_close' ), 1 );


		?>
		<div id="tab-reviews" class="woocommerce-tabs woocommerce-tabs--reviews">
			<?php comments_template(); ?>
		</div>
		<?php

		remove_action( 'woocommerce_review_before', array( $this, 'review_before_open' ), 1 );
		remove_action( 'woocommerce_review_before_comment_text', array( $this, 'review_before_close' ), 1 );

		if ( \Glozin\Addons\Elementor\Builder\Helper::is_elementor_editor_mode() ) {
			$GLOBALS['post'] = $original_post;
			wp_reset_postdata();
			?>
			<script>
				jQuery( '.wc-tabs-wrapper, .woocommerce-tabs, #rating' ).trigger( 'init' );
			</script>
			<?php
		}
	}

	public function review_before_open() {
		echo '<div class="glozin-review-avatar-name d-flex flex-wrap align-items-center gap-10 mb-20">';
	}

	public function review_before_close() {
		echo '</div>';
	}
}
