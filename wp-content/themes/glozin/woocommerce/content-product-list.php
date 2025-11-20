<?php
/**
 * Display product list vie.
 *
 * @author        UIXThemes
 * @package       Glozin
 * @version       1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<li class="product gz-product-list-item d-flex align-items-center gap-15">
	<div class="product-thumbnail position-relative rounded-product-image-sm overflow-hidden w-100">
		<a class="product-thumbnails--fadein gz-hover-zoom gz-hover-effect gz-ratio gz-ratio--product-image gz-lazy-load" href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php echo ! empty( $product ) ?  $product->get_image( 'woocommerce_thumbnail' ) : ''; ?>
			<?php
				$image_ids = $product->get_gallery_image_ids();
				if ( ! empty( $image_ids ) ) {
					$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
					echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail product-thumbnails--fadein-image' ) );
				}
				\Glozin\WooCommerce\Helper::lazy_load_image_effect_html();
			?>
		</a>
	</div>
	<div class="product__summary d-flex flex-column w-100">
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<span class="product__title fw-semibold"><?php echo esc_html( $product->get_name() ); ?></span>
		</a>
		<?php if( ! empty( $args['show_rating'] ) ) : ?>
			<div class="product__rating"><?php Glozin\WooCommerce\Product_Card\Base::product_rating(); ?></div>
		<?php endif; ?>
		<span class="price gz-price"><?php echo ! empty( $product ) ? $product->get_price_html() : ''; ?></span>
		<?php if( ! empty( $args['show_add_to_cart_button'] ) ) : ?>
			<?php \Glozin\WooCommerce\Helper::add_to_cart_button($product); ?>
		<?php endif; ?>
	</div>
</li>