<?php
/**
 * Template part for displaying the custom HTML
 *
 * @package Glozin
 */

?>

<div class="header-custom-html">
	<?php echo do_shortcode( wp_kses_post( \Glozin\Helper::get_option('header_custom_html') ) ); ?>
</div>
