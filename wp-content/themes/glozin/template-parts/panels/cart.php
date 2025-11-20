<?php
/**
 * Template part for displaying the cart panel
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
if ( function_exists('is_cart') && is_cart() ) {
	return;
}
$counter = ! empty(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$rtl_class = is_rtl() ? 'offscreen-panel--side-left' : 'offscreen-panel--side-right';
?>

<div id="cart-panel" class="offscreen-panel cart-panel woocommerce <?php echo esc_attr( $rtl_class ); ?>">
	<div class="panel__backdrop"></div>
	<div class="panel__container">
		<div class="panel__header h6">
			<?php echo esc_html__( 'Shopping Cart ', 'glozin' ); ?>
			<?php echo \Glozin\Icon::get_svg( 'close', 'ui', 'class=panel__button-close' ); ?>
		</div>

		<div class="panel__content">
			<?php do_action( 'glozin_before_mini_cart_content'); ?>

			<div class="widget_shopping_cart_content">
				<?php woocommerce_mini_cart(); ?>
			</div>

			<?php do_action( 'glozin_after_mini_cart_content'); ?>
		</div>
	</div>
</div>