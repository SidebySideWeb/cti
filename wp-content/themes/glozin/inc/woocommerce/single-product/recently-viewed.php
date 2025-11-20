<?php
/**
 * Hooks of Products Recently Viewed.
 *
 * @package Glozin
 */

namespace Glozin\WooCommerce\Single_Product;

use \Glozin\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Products Recently Viewed template.
 */
class Recently_Viewed {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private $product_ids;

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
		$viewed_products   = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
		$this->product_ids = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		// Track Product View
		add_action( 'template_redirect', array( $this, 'track_product_view' ) );

		if( intval( Helper::get_option( 'recently_viewed_products') ) ) {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'recently_viewed_products' ), 30 );
		}
	}

	/**
	 * Track product views
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function track_product_view() {
		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );
		}

		if ( ! empty( $post->ID ) && ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ), time() + 60 * 60 * 24 * 30 );
	}

	/**
	 * Get product content AJAX
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_recently_viewed_products_heading() {
		$class = intval( Helper::get_option( 'recently_viewed_products_ajax' ) ) ? 'hidden' : '';
		echo '<h2 class="recently-viewed-products__title '.esc_attr( $class ).'">'. esc_html__( 'Recently Viewed', 'glozin' ) .'</h2>';
		if( ! empty( \Glozin\Helper::get_option( 'recently_viewed_products_description' ) ) ) :
			echo '<p class="recently-viewed-products__description '.esc_attr( $class ).'">'. \Glozin\Helper::get_option( 'recently_viewed_products_description' ) .'</p>';
		endif;
	}

	/**
	 * Recently Viewed Products
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function recently_viewed_products() {
		if( empty( self::get_product_recently_viewed_ids() ) ) {
			return;
		}
		?>
		<section class="recently-viewed-products <?php echo intval( Helper::get_option( 'recently_viewed_products_ajax' ) ) ? 'has-ajax' : ''; ?>">
			<?php
				self::get_recently_viewed_products_heading();
				if( ! intval( Helper::get_option( 'recently_viewed_products_ajax' ) ) ) {
					self::get_recently_viewed_products();
				}
			?>
		</section>
		<?php
	}

	/**
	 * Get recently viewed ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_product_recently_viewed_ids() {
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();

		return array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
	}

	/**
	 * Get products recently viewed
	 *
	 * @return void
	 *
	 */
	public static function get_recently_viewed_products( $settings = null ) {
		$products_ids = self::get_product_recently_viewed_ids();

		$columns = \Glozin\Helper::get_option( 'recently_viewed_products_columns', [] );
		$columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '4';
		$slides_per_view_auto = (array) \Glozin\Helper::get_option( 'mobile_single_product_slides_per_view_auto' );
		$columns_mobile = in_array( 'recently_viewed', $slides_per_view_auto ) ? '1' : '2';
		$args_swiper = array(
			'slidesPerView' => array(
				'desktop' => isset( $columns['desktop'] ) ? $columns['desktop'] : '4',
				'tablet' => isset( $columns['tablet'] ) ? $columns['tablet'] : '3',
				'mobile' => isset( $columns['mobile'] ) ? $columns['mobile'] : $columns_mobile,
			),
			'slidesPerGroup' => array(
				'desktop' => isset( $columns['desktop'] ) ? $columns['desktop'] : '4',
				'tablet' => isset( $columns['tablet'] ) ? $columns['tablet'] : '3',
				'mobile' => isset( $columns['mobile'] ) ? $columns['mobile'] : $columns_mobile,
			),
			'spaceBetween' => array(
				'desktop' => 30,
				'tablet' => 30,
				'mobile' => 15,
			),
		);

		if( in_array( 'recently_viewed', $slides_per_view_auto ) ) {
			$args_swiper['slidesPerViewAuto'] = array(
				'desktop' => false,
				'tablet' => false,
				'mobile' => true,
			);
		}

		if( ! empty( $settings ) ) {
			$limit = $settings['limit'];
			$column = $settings['columns'];
		} else {
			$limit = Helper::get_option( 'recently_viewed_products_numbers' );
			$column = $columns;
		}

		if ( empty( $products_ids ) ) {
			?>
				<div class="no-products">
					<p><?php echo esc_html__( 'No products in recent viewing history.', 'glozin' ) ?></p>
				</div>

			<?php
		} else {
			update_meta_cache( 'post', $products_ids );
			update_object_term_cache( $products_ids, 'product' );

			$original_post = $GLOBALS['post'];

			wc_setup_loop(
				array(
					'columns' => $column
				)
			);

		?>
			<div class="glozin-product-carousel glozin-swiper swiper navigation-class--tabletdots navigation-class--mobiledots gz-arrows-middle <?php echo in_array( 'recently_viewed', $slides_per_view_auto ) ? 'slides-per-view-auto--mobile' : ''; ?>" data-swiper="<?php echo esc_attr( json_encode( $args_swiper ) ); ?>" data-desktop="<?php echo esc_attr($args_swiper['slidesPerView']['desktop']); ?>" data-tablet="<?php echo esc_attr($args_swiper['slidesPerView']['tablet']); ?>" data-mobile="<?php echo esc_attr($args_swiper['slidesPerView']['mobile']); ?>" style="--gz-swiper-auto-width-mobile: 64%;--gz-swiper-auto-fluid-end-mobile: 15px;">
		<?php
				woocommerce_product_loop_start();

				$index = 1;

				foreach ( $products_ids as $product_id ) {
					if ( $index > intval( $limit ) ) {
						break;
					}

					$product_id = apply_filters('wpml_object_id', $product_id, 'product', true);
					if (empty($product_id)) {
						continue;
					}

					$index ++;

					$product = get_post( $product_id );
					if ( empty( $product ) ) {
						continue;
					}

					$GLOBALS['post'] = $product; // WPCS: override ok.
					setup_postdata( $GLOBALS['post'] );
					wc_get_template_part( 'content', 'product' );
				}

				$GLOBALS['post'] = $original_post; // WPCS: override ok.

				woocommerce_product_loop_end();

				\Glozin\Helper::get_swiper_navigation();
				\Glozin\Helper::get_swiper_pagination();
		?>
			</div>
		<?php
			wp_reset_postdata();
			wc_reset_loop();
		}
	}
}
