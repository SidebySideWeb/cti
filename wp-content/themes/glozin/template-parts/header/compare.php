<?php
/**
 * Template part for displaying the compare icon
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\ProductsCompare\Plugin' ) ) {
	return;
}

$counter = isset($args['compare_count']) ? $args['compare_count'] : 0;
$classes = isset($args['compare_classes']) ? $args['compare_classes'] : '';
$text_classes = isset($args['compare_text_class']) ? $args['compare_text_class'] : '';
$counter_class = isset($args['compare_counter_class']) ? $args['compare_counter_class'] : '';
?>

<a class="header-compare<?php echo esc_attr( $classes); ?>" role="button" href="<?php echo esc_url( wc_get_page_permalink( 'compare' ) ); ?>">
	<div class="header-counter-content gz-button gz-button-text gz-button-icon position-relative">
		<?php echo Glozin\Icon::inline_svg( 'icon=cross-arrow' ); ?>
		<span class="<?php echo esc_attr( $counter_class ); ?>"><?php echo esc_html( $counter ); ?></span>
	</div>
	<span class="<?php echo esc_attr( $text_classes ); ?>"><?php esc_html_e( 'Compare', 'glozin' ) ?></span>
</a>