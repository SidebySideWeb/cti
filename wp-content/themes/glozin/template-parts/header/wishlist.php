<?php

/**
 * Template part for displaying the wishlist icon
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\Wishlist\Helper' ) ) {
	return;
}

$counter = isset($args['wishlist_count']) ? $args['wishlist_count'] : 0;
$classes = isset($args['wishlist_classes']) ? $args['wishlist_classes'] : '';
$text_classes = isset($args['wishlist_text_class']) ? $args['wishlist_text_class'] : '';
$counter_class = isset($args['wishlist_counter_class']) ? $args['wishlist_counter_class'] : '';
$original_size = isset($args['wishlist_icon_original_size']) ? $args['wishlist_icon_original_size'] : true;
?>
<a href="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>" class="header-wishlist<?php echo esc_attr( $classes); ?>" role="button">
	<div class="header-counter-content gz-button gz-button-text gz-button-icon position-relative">
		<?php echo Glozin\Icon::inline_svg(['icon' => 'heart', 'width' => 19, 'height' => 16, 'original_size' => $original_size]); ?>
		<span class="<?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $counter ); ?></span>
	</div>
	<span class="<?php echo esc_attr( $text_classes ); ?>"><?php esc_html_e( 'Wishlist', 'glozin' ) ?></span>
</a>