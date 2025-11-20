<?php
/**
 * Template part for displaying post navigation
 *
 * @package Glozin
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ( ! $next_post && ! $prev_post ) {
	return;
}

$class = $prev_post && ! $next_post ? 'justify-content-start' : 'justify-content-between';
$class = ! $prev_post && $next_post ? 'justify-content-end' : $class;
$class .= \Glozin\Blog\Single::sidebar() ? '' : ' container-min';
?>

<nav class="navigation post-navigation mt-50 py-30 border-top d-flex gap-15 <?php echo esc_attr( $class ); ?>" role="navigation">
	<?php if ( $prev_post ) : ?>
		<a class="nav-previous d-flex flex-column flex-1" href="<?php echo esc_url( get_permalink( $prev_post ) ) ?>">
			<span class="gz-button gz-button-text justify-content-start align-items-center gap-10 text-base lh-1">
				<?php echo \Glozin\Icon::inline_svg('icon=icon-back'); ?>
				<span class="nav-label text-uppercase fs-13 lh-1"><?php esc_html_e( 'Previous Post', 'glozin' );?></span>
			</span>
			<span class="nav-link d-flex mb-0 mt-15 text-left">
				<span class="nav-title h6 my-0"><?php  echo esc_html( $prev_post->post_title ); ?></span>
			</span>
		</a>
	<?php endif; ?>
	<?php if ( $next_post ) : ?>
		<a class="nav-next d-flex flex-column align-items-end flex-1" href="<?php echo esc_url( get_permalink( $next_post ) )  ?>">
			<span class="gz-button gz-button-text justify-content-end align-items-center gap-10 text-base lh-1">
				<span class="nav-label text-uppercase fs-13 lh-1"><?php esc_html_e( 'Next Post', 'glozin' );?></span>
				<?php echo \Glozin\Icon::inline_svg('icon=icon-next'); ?>
			</span>
			<span class="nav-link d-flex text-right mb-0 mt-15">
				<span class="nav-title h6 my-0"><?php echo esc_html( $next_post->post_title ); ?></span>
			</span>
		</a>
	<?php endif; ?>
</nav>