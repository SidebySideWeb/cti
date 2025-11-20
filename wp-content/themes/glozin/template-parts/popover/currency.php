<?php
/**
 * Template part for displaying the currency popover
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div id="currency-popover" class="popover currency-popover">
	<div class="popover__backdrop"></div>
	<div class="popover__container">
		<?php echo \Glozin\Icon::get_svg( 'close', 'ui', array('class' => 'gz-button gz-button-icon gz-button-light popover__button-close') ); ?>
		<div class="popover__content">
        <?php echo \Glozin\WooCommerce\Currency::woocs_currency_switcher(); ?>
		</div>
	</div>
</div>