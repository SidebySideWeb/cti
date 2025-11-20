<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Glozin
 */

get_header();
?>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) { ?>
	<div class="site-content-container d-flex justify-content-between flex-column flex-lg-row gap-40">
		<div id="primary" class="content-area position-sticky-lg top-30">
			<main id="main" class="site-main gz-row row-gap-40">
				<?php if ( have_posts() ) :

					do_action( 'glozin_before_search_loop' );

					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						$content_template = apply_filters( 'glozin_content_template_part', get_post_type() );

						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'template-parts/content/content', $content_template );

					endwhile;

					do_action( 'glozin_after_search_loop' );

				else :

					get_template_part( 'template-parts/content/content', 'none' );

				endif;
				?>
			</main>
		</div>

	<?php get_sidebar(); ?>
	</div>
<?php } ?>
<?php
get_footer();
