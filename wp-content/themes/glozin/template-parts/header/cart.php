<?php

/**
 * Template part for displaying the cart icon
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

$cart_display = isset($args['cart_display']) ? $args['cart_display'] : 'icon';
$counter = ! empty(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$counter_class = $counter == 0 ? 'empty-counter' : '';
$classes = isset($args['cart_classes']) ? $args['cart_classes'] : '';
$classes .= $cart_display == 'icon-text' ? ' gap-5' : '';
$text_classes = isset($args['cart_text_class']) ? $args['cart_text_class'] : 'screen-reader-text';
$total_price = ! empty(WC()->cart) && $cart_display == 'icon-text' ? WC()->cart->get_cart_subtotal() : '';
$original_size = isset($args['cart_icon_original_size']) ? $args['cart_icon_original_size'] : true;

?>

<a href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" class="header-cart d-flex align-items-center<?php echo esc_attr( $classes); ?>" data-toggle="off-canvas" data-target="cart-panel">
	<div class="header-counter-content gz-button gz-button-text gz-button-icon position-relative">
		<?php echo \Glozin\Helper::get_cart_icons($original_size); ?>
		<span class="header-counter header-cart__counter <?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $counter ) ?></span>
	</div>
	<div class="header-cart-wrapper d-flex flex-column gap-3 text-left lh-1">
		<span class="<?php echo esc_attr( $text_classes ) ?>"><?php esc_html_e( 'Cart', 'glozin' ) ?></span>
		<?php echo ! empty( $total_price ) ? '<span id="header-cart-subtotal" class="total-price fs-14 fw-semibold">'. $total_price .'</span>' : ''; ?>
	</div>
</a>
