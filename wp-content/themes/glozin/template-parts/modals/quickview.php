<?php
/**
 * Template part for displaying the quickview modal
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
?>

<div id="quick-view-modal" class="quick-view-modal modal single-product modal__quickview">
	<div class="modal__backdrop"></div>
	<div class="modal__container modal__container--quickview">
		<div class="modal__wrapper modal__wrapper--quickview">
			<a href="#" class="modal__button-close gz-button gz-button-icon position-absolute top-10 end-10 z-1">
				<?php echo \Glozin\Icon::get_svg( 'close', 'ui' ); ?>
			</a>
			<div class="modal__content woocommerce modal__content--quickview">
				<div class="modal__product product-quickview"></div>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="glozinSpinner"></span></span>
</div>