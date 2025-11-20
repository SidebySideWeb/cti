<?php
/**
 * Template part for displaying the topbar
 *
 * @package Glozin
 */

$topbar_class = '';
if ( \Glozin\Helper::get_option( 'mobile_topbar' ) ) {
	$topbar_class = 'topbar-mobile';
	$topbar_class .= ' topbar-mobile--keep-' . \Glozin\Helper::get_option( 'mobile_topbar_section' );
}
?>
<div id="topbar" class="topbar <?php echo esc_attr( $topbar_class ); ?>">
	<div class="topbar-container <?php echo esc_attr( apply_filters( 'glozin_topbar_container_classes', 'container-xxl' ) ) ?>">
		<?php if ( isset( $args['left_items'][0]['item'] ) && ! empty( $args['left_items'][0]['item'] ) ) : ?>
			<div class="topbar-items topbar-left-items d-flex gap-25 align-items-center justify-content-start text-left h-100 fs-13">
				<?php \Glozin\Header\Topbar::items( $args['left_items']); ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $args['right_items'][0]['item'] ) && ! empty( $args['right_items'][0]['item'] ) ) : ?>
			<div class="topbar-items topbar-right-items d-flex gap-25 align-items-center justify-content-end text-right h-100 fs-13">
				<?php \Glozin\Header\Topbar::items( $args['right_items'] ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
