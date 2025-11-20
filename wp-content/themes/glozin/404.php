<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Glozin
 */

get_header();
?>
<?php if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) { ?>
	<div id="primary" class="content-area">
		<div class="error-404 not-found text-center">
			<div class="error-404__image m-w-450 ms-auto me-auto mt-30 mb-40">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/images/404.jpg' ); ?>" alt="<?php echo esc_attr__( '404 Image', 'glozin' ) ?>">
			</div>
			<div class="error-404__wrapper">
				<div class="error-404__content mb-40">
					<h1 class="error-404__title h2 mt-0 mb-20"><?php esc_html_e( 'Oops...That link is broken.', 'glozin' ); ?></h1>
					<div class="text-dark"><?php esc_html_e( 'Sorry for the inconvenience. Go to our homepage or check out our latest collections.', 'glozin' ); ?></div>
				</div>
				<a href="<?php echo esc_url( get_home_url() ); ?>"
				class="error-404__button glozin-button gz-button gz-button-hover-effect fw-semibold px-30 min-w-200"><?php echo esc_html__( 'Back to Homepage', 'glozin' ); ?></a>
			</div>
		</div>

	</div><!-- #primary -->
<?php } ?>
<?php
get_footer();
