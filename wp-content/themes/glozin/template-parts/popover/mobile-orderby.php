<?php
/**
 * Template part for displaying the filter sidebar panel
 *
 * @package Glozin
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

?>

<div id="mobile-orderby-popover" class="popover mobile-orderby-popover catalog-toolbar__orderby-form">
	<div class="popover__backdrop"></div>
	<div class="popover__container">
		<?php echo \Glozin\Icon::get_svg( 'close', 'ui', array('class' => 'gz-button gz-button-icon gz-button-light popover__button-close') ); ?>
		<div class="popover__content">
			<ul class="catalog-toolbar__orderby-list list-unstyled">
				<?php foreach ( $args as $id => $name ) : ?>
					<li><a class="catalog-toolbar__orderby-item text-base py-4 d-block" href="#" data-id="<?php echo esc_attr( $id ); ?>" data-title="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>