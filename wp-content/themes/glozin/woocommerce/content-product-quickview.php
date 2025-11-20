<?php
/**
 * Display product quickview.
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

$classes[] = 'product-quickview';

if( get_option( 'glozin_buy_now' ) == 'yes' ) {
	$classes[] = 'has-buy-now';
}

if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
	$classes[] = 'has-wishlist';
}

if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
	$classes[] = 'has-compare';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="product-gallery-summary position-relative d-flex flex-column flex-md-row gap-30">
		<?php
		/**
		 * Hook: glozin_woocommerce_before_product_quickview_summary
		 *
		 * @hooked woocommerce_show_product_images - 10
		 */
		do_action( 'glozin_woocommerce_before_product_quickview_summary' );
		?>

		<div class="summary entry-summary">
			<?php
			/**
			 * Hook: glozin_woocommerce_product_quickview_summary
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 */
			do_action( 'glozin_woocommerce_product_quickview_summary' );
			?>
		</div>

		<?php
		/**
		 * Hook: glozin_woocommerce_after_product_quickview_summary
		 */
		do_action( 'glozin_woocommerce_after_product_quickview_summary' );
		?>
	</div>
</div>