<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Glozin
 */

$has_sidebar = apply_filters( 'glozin_get_sidebar', false );

if( ! $has_sidebar ) {
	return;
}

$sidebar = 'blog-sidebar';

if ( \Glozin\Helper::is_catalog() ) {
	$sidebar = 'catalog-filters-sidebar';
}

if ( ! is_active_sidebar( $sidebar ) ) {
	return;
}

$sidebar_class = apply_filters( 'glozin_primary_sidebar_classes', $sidebar );

?>

<aside id="<?php echo esc_attr( apply_filters( 'glozin_primary_sidebar_id', 'primary-sidebar' ) ); ?>" class="widget-area primary-sidebar position-sticky-lg top-30 <?php echo esc_attr( $sidebar_class ) ?>">
	<?php dynamic_sidebar( $sidebar ); ?>
</aside><!-- #primary -->
