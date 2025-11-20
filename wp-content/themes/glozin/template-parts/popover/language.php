<?php
/**
 * Template part for displaying the language popover
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div id="language-popover" class="popover language-popover">
	<div class="popover__backdrop"></div>
	<div class="popover__container">
		<?php echo \Glozin\Icon::get_svg( 'close', 'ui', array('class' => 'gz-button gz-button-icon gz-button-light popover__button-close') ); ?>
		<div class="popover__content">
        <?php echo \Glozin\WooCommerce\Language::language_switcher(); ?>
		</div>
	</div>
</div>