<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Glozin
 */

$container_class = 'container-min';
$entry_class = 'justify-content-center';
if( \Glozin\Blog\Single::sidebar() ) {
	$container_class = '';
	$entry_class = '';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header <?php echo esc_attr( $container_class ); ?>">
		<?php \Glozin\Blog\Post::category('gz-blog-badge-outline', true); ?>
		<?php \Glozin\Blog\Post::title('h1', true); ?>
		<div class="entry-meta d-flex flex-wrap align-items-center lh-normal <?php echo esc_attr( $entry_class ); ?>">
			<?php \Glozin\Blog\Post::author(); ?>
			<?php \Glozin\Blog\Post::date(); ?>
			<?php \Glozin\Blog\Post::comment(); ?>
		</div>
	</header>
	<div class="entry-content entry-single-content <?php echo esc_attr( $container_class ); ?> mt-40 clearfix">
		<?php \Glozin\Blog\Post::content(); ?>
	</div>
	<footer class="entry-footer <?php echo esc_attr( $container_class ); ?> mt-40 mb-40 d-flex flex-wrap align-items-center justify-content-between gap-15">
		<?php \Glozin\Blog\Post::tags(); ?>
		<?php \Glozin\Blog\Post::share(); ?>
	</footer>
</article>
