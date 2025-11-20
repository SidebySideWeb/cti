<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) :

	$columns = \Glozin\Helper::get_option( 'cross_sells_products_columns', [] );
	$desktop_columns = isset( $columns['desktop'] ) ? $columns['desktop'] : '2';
	$tablet_columns  = isset( $columns['tablet'] ) ? $columns['tablet'] : '2';
	$mobile_columns  = isset( $columns['mobile'] ) ? $columns['mobile'] : '1';

	$args_swiper = array(
		'slidesPerView' => array(
			'desktop' => $desktop_columns,
			'tablet' => $tablet_columns,
			'mobile' => $mobile_columns,
		),
		'spaceBetween' => array(
			'desktop' => 20,
			'tablet' => 20,
			'mobile' => 20,
		),
	);

?>

	<div class="cross-sells">
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may also like&hellip;', 'glozin' ) );

		if ( $heading ) :
			?>
			<h2 class="fs-18 mt-0 mb-15"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<div class="gz-cross-sells-content glozin-swiper glozin-product-carousel swiper navigation-class-dots navigation-class--tabletdots navigation-class--mobiledots" data-swiper="<?php echo esc_attr( json_encode( $args_swiper ) ); ?>" data-desktop="<?php echo esc_attr( $desktop_columns ); ?>" data-tablet="<?php echo esc_attr( $tablet_columns ); ?>" data-mobile="<?php echo esc_attr( $mobile_columns ); ?>">

			<?php woocommerce_product_loop_start(); ?>

				<?php foreach ( $cross_sells as $cross_sell ) : ?>

					<?php
						$post_object = get_post( $cross_sell->get_id() );

						setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

						wc_get_template_part( 'content', 'product-list' );
					?>

				<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>

			<div class="swiper-pagination swiper-pagination-bullets--small"></div>
		</div>
	</div>
	<?php
endif;

wp_reset_postdata();
