<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :
	$columns = \Glozin\Helper::get_option( 'related_products_columns', [] );
	$slides_per_view_auto = (array) \Glozin\Helper::get_option( 'mobile_single_product_slides_per_view_auto' );
	$columns_mobile = in_array( 'related', $slides_per_view_auto ) ? '1' : '2';

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

	if( in_array( 'related', $slides_per_view_auto ) ) {
		$args_swiper['slidesPerViewAuto'] = array(
			'desktop' => false,
			'tablet' => false,
			'mobile' => true,
		);
	}

	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
?>

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if( ! empty( \Glozin\Helper::get_option( 'related_products_description' ) ) ) : ?>
			<p class="related-products__description"><?php echo \Glozin\Helper::get_option( 'related_products_description' ); ?></p>
		<?php endif; ?>

		<div class="glozin-product-carousel swiper glozin-swiper navigation-class--tabletdots navigation-class--mobiledots gz-arrows-middle <?php echo in_array( 'related', $slides_per_view_auto ) ? 'slides-per-view-auto--mobile' : ''; ?>" data-swiper="<?php echo esc_attr( json_encode( $args_swiper ) ); ?>" data-desktop="<?php echo esc_attr($args_swiper['slidesPerView']['desktop']); ?>" data-tablet="<?php echo esc_attr($args_swiper['slidesPerView']['tablet']); ?>" data-mobile="<?php echo esc_attr($args_swiper['slidesPerView']['mobile']); ?>" style="--gz-swiper-auto-width-mobile: 64%;--gz-swiper-auto-fluid-end-mobile: 15px;">
			<?php woocommerce_product_loop_start(); ?>

				<?php foreach ( $related_products as $related_product ) : ?>

						<?php
						$post_object = get_post( $related_product->get_id() );

						setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

						wc_get_template_part( 'content', 'product' );
						?>

				<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>
			<?php \Glozin\Helper::get_swiper_navigation(); ?>
			<?php \Glozin\Helper::get_swiper_pagination(); ?>
		</div>
	</section>
	<?php
endif;

wp_reset_postdata();
