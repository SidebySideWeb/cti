<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$classes = wc_get_product_class( '', $product  );

$classes[] = 'product-summary';

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
		 * Hook: glozin_woocommerce_before_product_summary_summary
		 *
		 * @hooked woocommerce_show_product_images - 10
		 */
		do_action( 'glozin_woocommerce_before_product_summary' );
		?>

		<div class="summary entry-summary">
			<?php
			/**
			 * Hook: glozin_woocommerce_product_summary_summary
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 */
			do_action( 'glozin_woocommerce_product_summary' );
			?>
		</div>

		<?php
		/**
		 * Hook: glozin_woocommerce_after_product_summary_summary
		 */
		do_action( 'glozin_woocommerce_after_product_summary' );
		?>
	</div>
</div>