<?php
/**
 * Template part for displaying the product ask a question modal
 *
 * @package Glozin
 */

?>

<div id="product-ask-question-modal" class="product-ask-question-modal modal product-extra-link-modal glozin-contact-form">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<div class="modal__header">
				<h3 class="modal__title h5"><?php esc_html_e( 'Ask a question', 'glozin' ); ?></h3>
				<a href="#" class="modal__button-close">
					<?php echo \Glozin\Icon::get_svg( 'close', 'ui' ); ?>
				</a>
			</div>
			<div class="modal__content">
				<div class="ask-question-content"><?php echo do_shortcode( wp_kses_post( $args ) ); ?></div>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="glozinSpinner"></span></span>
</div>