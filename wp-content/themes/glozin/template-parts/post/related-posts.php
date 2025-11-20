<?php
/**
 * The template part for displaying related posts
 *
 * @package Glozin
 */

$related_posts = new WP_Query( apply_filters( 'glozin_related_posts_args', array(
	'post_type'           => 'post',
	'posts_per_page'      => intval(\Glozin\Helper::get_option('posts_related_number')),
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'order'               => 'rand',
	'post__not_in'        => array( $post->ID ),
	'tax_query'           => array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => \Glozin\Blog\Post::get_related_terms( 'category', $post->ID ),
			'operator' => 'IN',
		),
		array(
			'taxonomy' => 'post_tag',
			'field'    => 'term_id',
			'terms'    => \Glozin\Blog\Post::get_related_terms( 'post_tag', $post->ID ),
			'operator' => 'IN',
		),
	),
	'no_found_rows'          => true,
	'update_post_term_cache' => false,
	'update_post_meta_cache' => false,
	'cache_results'          => false,
	'ignore_sticky_posts'    => true,
) ) );

$posts_spacing = intval(\Glozin\Helper::get_option('posts_related_spacing'));

if ( ! $related_posts->have_posts() ) {
	return;
}

$swiper_options = array(
	'slidesPerView' => array(
		'desktop' => 3,
		'tablet' => 3,
		'mobile' => 1,
	),
	'spaceBetween' => array(
		'desktop' => $posts_spacing,
		'tablet' => $posts_spacing,
		'mobile' => 15,
	),
);

$class = \Glozin\Blog\Single::sidebar() ? '' : 'container-min';
?>
    <div class="glozin-posts-related <?php echo esc_attr( $class ); ?> pt-55 border-top">
        <h3 class="glozin-posts-related__heading h2 mt-0 mb-33 text-center heading-letter-spacing"><?php esc_html_e( 'Related Posts', 'glozin' ); ?></h3>
        <div class="glozin-posts-related__content swiper navigation-class-both navigation-class--tabletdots navigation-class--mobiledots" data-swiper=<?php echo esc_attr( json_encode( $swiper_options ) ); ?> data-desktop="<?php echo esc_attr( $swiper_options['slidesPerView']['desktop'] ); ?>" data-tablet="<?php echo esc_attr( $swiper_options['slidesPerView']['tablet'] ); ?>" data-mobile="<?php echo esc_attr( $swiper_options['slidesPerView']['mobile'] ); ?>">
			<div class="glozin-posts-related__inner swiper-wrapper">
				<?php
					while ( $related_posts->have_posts() ) : $related_posts->the_post();

						get_template_part( 'template-parts/content/content', 'related' );

					endwhile;
				?>
			</div>
			<?php \Glozin\Helper::get_swiper_navigation(); ?>
			<?php \Glozin\Helper::get_swiper_pagination(); ?>
        </div>
    </div>
<?php
wp_reset_postdata();