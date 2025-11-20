<?php

/**
 * Template part for displaying the currency
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div class="header-currency glozin-currency glozin-currency-language gz-color-dark">
	<?php echo \Glozin\WooCommerce\Currency::currency_switcher(); ?>
</div>