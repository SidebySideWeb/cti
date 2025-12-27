<?php
/**
 * Customer processing order email
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6;">
	<?php
	if ( ! empty( $order->get_billing_first_name() ) ) {
		printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) );
	} else {
		echo esc_html__( 'Hi,', 'woocommerce' );
	}
	?>
</p>

<p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; font-weight: bold; color: #333;">
	<?php
	/* translators: %s: Order number */
	printf( esc_html__( 'Your order with code %s is in progress.', 'woocommerce' ), '<strong>' . esc_html( $order->get_order_number() ) . '</strong>' );
	?>
</p>

<h3 style="margin: 30px 0 15px; font-size: 16px; font-weight: bold; color: #333;"><?php esc_html_e( 'Customer Details', 'woocommerce' ); ?></h3>

<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom: 30px;">
	<tr>
		<td style="padding: 10px 0; color: #666;">
			<strong><?php esc_html_e( 'Email:', 'woocommerce' ); ?></strong> <?php echo esc_html( $order->get_billing_email() ); ?>
		</td>
	</tr>
	<?php if ( $order->get_billing_phone() ) : ?>
	<tr>
		<td style="padding: 10px 0; color: #666;">
			<strong><?php esc_html_e( 'Phone:', 'woocommerce' ); ?></strong> <?php echo esc_html( $order->get_billing_phone() ); ?>
		</td>
	</tr>
	<?php endif; ?>
</table>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
?>

<h3 style="margin: 30px 0 15px; font-size: 16px; font-weight: bold; color: #333;"><?php esc_html_e( 'Billing Address', 'woocommerce' ); ?></h3>
<address style="padding: 15px; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px; font-style: normal; line-height: 1.8;">
	<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
</address>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $order->has_shipping_address() ) : ?>
	<h3 style="margin: 30px 0 15px; font-size: 16px; font-weight: bold; color: #333;"><?php esc_html_e( 'Shipping Address', 'woocommerce' ); ?></h3>
	<address style="padding: 15px; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px; font-style: normal; line-height: 1.8;">
		<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
	</address>
<?php endif; ?>

<?php
if ( $additional_content ) {
	echo '<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo '</div>';
}

do_action( 'woocommerce_email_footer', $email );
?>
