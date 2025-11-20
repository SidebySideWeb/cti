<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Glozin
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('gz-post-grid swiper-slide'); ?>>
	<?php if ( has_post_thumbnail() ) { ?>
		<div class="entry-header mb-30">
			<?php \Glozin\Blog\Post::thumbnail(); ?>
		</div>
	<?php } ?>
	<?php \Glozin\Blog\Post::title('h6', false, array( 'mt-0', 'mb-10', 'heading-letter-spacing' )); ?>
	<div class="entry-meta d-flex flex-wrap align-items-center lh-normal">
		<?php \Glozin\Blog\Post::author(); ?>
		<?php \Glozin\Blog\Post::date(false); ?>
	</div>
</article>