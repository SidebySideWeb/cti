<?php
/**
 * The Template for displaying all product archive
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>
	<?php do_action( 'glozin_woocommerce_product_archive_before_content' ); ?>

	<?php do_action( 'glozin_woocommerce_product_archive_content' ); ?>

	<?php do_action( 'glozin_woocommerce_product_archive_after_content' ); ?>
<?php
get_footer();