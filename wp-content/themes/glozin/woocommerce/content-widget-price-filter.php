<?php
/**
 * The template for displaying product price filter widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-price-filter.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

?>
<?php do_action( 'woocommerce_widget_price_filter_start', $args ); ?>

<form method="get" action="<?php echo esc_url( $form_action ); ?>">
	<div class="price_slider_wrapper">
		<div class="price_slider_amount d-flex align-items-center gap-10 mb-17" data-step="<?php echo esc_attr( $step ); ?>">
			<label class="screen-reader-text" for="min_price"><?php esc_html_e( 'Min price', 'woocommerce' ); ?></label>
			<div class="price_slider_amount_input position-relative d-flex align-items-center">
				<span class="price_slider_amount_input_currency text-base fw-normal"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
				<input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( $current_min_price ); ?>" data-min="<?php echo esc_attr( $min_price ); ?>" placeholder="<?php echo esc_attr__( 'Min price', 'woocommerce' ); ?>" />
			</div>
			<span class="price_slider_amount_separator"><?php echo \Glozin\Icon::get_svg( 'price-filter-separator' ); ?></span>
			<label class="screen-reader-text" for="max_price"><?php esc_html_e( 'Max price', 'woocommerce' ); ?></label>
			<div class="price_slider_amount_input position-relative d-flex align-items-center">
				<span class="price_slider_amount_input_currency text-base fw-normal"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
				<input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( $current_max_price ); ?>" data-max="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php echo esc_attr__( 'Max price', 'woocommerce' ); ?>" />
			</div>
			<?php /* translators: Filter: verb "to filter" */ ?>
			<button type="submit" class="button hidden<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php echo esc_html__( 'Filter', 'woocommerce' ); ?></button>
			<?php echo wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ); ?>
		</div>
		<div class="price_slider" style="display:none;"></div>
		<div class="price_slider_amount price_slider_label" data-step="<?php echo esc_attr( $step ); ?>">
			<div class="price_label" style="display:none;">
				<?php echo esc_html__( 'Price:', 'woocommerce' ); ?> <span class="from"></span> &mdash; <span class="to"></span>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</form>

<?php do_action( 'woocommerce_widget_price_filter_end', $args ); ?>
