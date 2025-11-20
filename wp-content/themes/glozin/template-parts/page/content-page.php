<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Glozin
 */

 if( ! \Glozin\Helper::is_built_with_elementor() ) { ?>
<article id="post-<?php the_ID(); ?>" class="<?php echo join( ' ', get_post_class( '', get_the_ID() ) ); ?> clearfix" >
<?php }; ?>
	<?php the_content(); ?>
	<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'glozin' ),
			'after'  => '</div>',
		) );
	?>
<?php if( ! \Glozin\Helper::is_built_with_elementor() ) { ?>
</article>
<?php }; ?>