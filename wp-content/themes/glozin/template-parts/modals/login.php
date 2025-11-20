<?php
/**
 * Template part for displaying the my login modal
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if( \Glozin\Helper::get_option('header_signin_icon_behaviour') == 'page' ) {
	return;
}
?>

<div id="login-modal" class="login-modal modal woocommerce woocommerce-account">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<a href="#" class="modal__button-close gz-button gz-button-icon gz-button-text position-absolute z-3 top-10 end-10">
				<?php echo \Glozin\Icon::get_svg( 'close', 'ui' ); ?>
			</a>
			<div class="modal__content">
				<?php wc_get_template( 'myaccount/form-login.php', array('action' => 'popup') ); ?>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="glozinSpinner"></span></span>
</div>