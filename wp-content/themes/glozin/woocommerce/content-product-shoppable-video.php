<?php
/**
 * Display product shoppable video.
 *
 * @author        UIXThemes
 * @package       Glozin
 * @version       1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$classes = wc_get_product_class( '', $product  );

$classes[] = 'product-shoppable-video product-quickview';

if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
	$classes[] = 'has-wishlist';
}

if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
	$classes[] = 'has-compare';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="product-gallery-summary position-relative d-flex flex-column flex-md-row gap-30">
		<div class="custom-scrollbar">
			<div class="woocommerce-product-gallery woocommerce-product-gallery__video position-relative"></div>
		</div>
		<div class="summary entry-summary">
			<?php
			/**
			 * Hook: glozin_woocommerce_product_shoppable_video_summary
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 */
			do_action( 'glozin_woocommerce_product_shoppable_video_summary' );
			?>
		</div>

		<?php
		/**
		 * Hook: glozin_woocommerce_after_product_shoppable_video_summary
		 */
		do_action( 'glozin_woocommerce_after_product_shoppable_video_summary' );
		?>
	</div>
</div>