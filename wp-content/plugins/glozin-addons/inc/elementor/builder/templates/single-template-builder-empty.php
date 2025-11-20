<?php
/**
 * The Template for displaying all single products empty
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>
	<?php do_action( 'glozin_woocommerce_product_content', get_the_ID() ); ?>
<?php
get_footer();