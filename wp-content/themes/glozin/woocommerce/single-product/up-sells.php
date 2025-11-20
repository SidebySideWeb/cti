<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $upsells ) :
	$columns = \Glozin\Helper::get_option( 'upsells_products_columns', [] );
	$slides_per_view_auto = (array) \Glozin\Helper::get_option( 'mobile_single_product_slides_per_view_auto' );
	$columns_mobile = in_array( 'upsells', $slides_per_view_auto ) ? '1' : '2';

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

	if( in_array( 'upsells', $slides_per_view_auto ) ) {
		$args_swiper['slidesPerViewAuto'] = array(
			'desktop' => false,
			'tablet' => false,
			'mobile' => true,
		);
	}
?>

	<section class="up-sells upsells products">
		<?php
		$heading = apply_filters( 'woocommerce_product_upsells_products_heading', __( 'You may also like&hellip;', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if( ! empty( \Glozin\Helper::get_option( 'upsells_products_description' ) ) ) : ?>
			<p class="up-sells__description"><?php echo \Glozin\Helper::get_option( 'upsells_products_description' ); ?></p>
		<?php endif; ?>

		<div class="glozin-product-carousel swiper glozin-swiper navigation-class--tabletdots navigation-class--mobiledots gz-arrows-middle <?php echo in_array( 'upsells', $slides_per_view_auto ) ? 'slides-per-view-auto--mobile' : ''; ?>" data-swiper="<?php echo esc_attr( json_encode( $args_swiper ) ); ?>" data-desktop="<?php echo esc_attr($args_swiper['slidesPerView']['desktop']); ?>" data-tablet="<?php echo esc_attr($args_swiper['slidesPerView']['tablet']); ?>" data-mobile="<?php echo esc_attr($args_swiper['slidesPerView']['mobile']); ?>" style="--gz-swiper-auto-width-mobile: 64%;--gz-swiper-auto-fluid-end-mobile: 15px;">
			<?php woocommerce_product_loop_start(); ?>

				<?php foreach ( $upsells as $upsell ) : ?>

					<?php
					$post_object = get_post( $upsell->get_id() );

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
