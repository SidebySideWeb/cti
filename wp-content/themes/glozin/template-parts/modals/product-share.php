<?php
/**
 * Template part for displaying the product share modal
 *
 * @package Glozin
 */

?>

<div id="product-share-modal" class="product-share-modal modal product-extra-link-modal">
	<div class="modal__backdrop"></div>
	<div class="modal__container">
		<div class="modal__wrapper">
			<div class="modal__header">
				<h3 class="modal__title h5"><?php esc_html_e( 'Copy link', 'glozin' ); ?></h3>
				<a href="#" class="modal__button-close">
					<?php echo \Glozin\Icon::get_svg( 'close', 'ui' ); ?>
				</a>
			</div>
			<div class="modal__content">
				<div class="product-share__copylink">
					<form class="gz-responsive d-flex align-items-center gap-10 mb-20">
						<input class="product-share__copylink--link glozin-copylink__link flex-1" type="text" value="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" readonly="readonly" />
						<button class="product-share__copylink--button glozin-copylink__button gz-button gz-button-icon" data-icon="<?php echo esc_attr( \Glozin\Icon::get_svg( 'copy' ) ); ?>" data-icon_copied="<?php echo esc_attr( \Glozin\Icon::inline_svg( ['icon' => 'check', 'class' => 'has-vertical-align'] ) ); ?>"><?php echo \Glozin\Icon::get_svg( 'copy' ); ?></button>
					</form>
				</div>
				<div class="product-share__share">
					<div class="product-share__copylink-heading h6 mb-15 mt-0"><?php echo esc_html__( 'Share', 'glozin' ); ?></div>
					<?php echo ! empty( $args ) ? $args : '' ; ?>
				</div>
			</div>
		</div>
	</div>
	<span class="modal__loader"><span class="glozinSpinner"></span></span>
</div>