<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Glozin
 */

?>

<?php do_action( 'glozin_before_site_content_close' ); ?>
</div><!-- #content -->
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {?>
	<footer id="site-footer" class="site-footer border-top">
		<?php do_action('glozin_footer'); ?>
	</footer>
<?php } ?>
<?php do_action( 'glozin_after_close_site_footer' ); ?>

</div><!-- #page -->

<?php do_action( 'glozin_after_site' ) ?>

<?php wp_footer(); ?>

</body>
</html>
