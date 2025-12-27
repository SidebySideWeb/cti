<?php
/**
 * Order details table shown in emails - Custom Analytical Version
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

/**
 * Action hook to add custom content before order details in email.
 */
do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2 style="margin: 0 0 20px; font-size: 18px; font-weight: bold; color: #333;">
	<?php
	if ( $sent_to_admin ) {
		$before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/* translators: %s: Order ID. */
	echo wp_kses_post( $before . sprintf( __( 'Order #%s', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
	?>
</h2>

<div style="margin-bottom: 30px;">
	<table class="td" cellspacing="0" cellpadding="8" style="width: 100%; border: 1px solid #e0e0e0; border-collapse: collapse;" border="1">
		<thead>
			<tr style="background-color: #f5f5f5;">
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>; padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:center; padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:right; padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;"><?php esc_html_e( 'Unit Price', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:right; padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:right; padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;"><?php esc_html_e( 'VAT', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:right; padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $order->get_items() as $item_id => $item ) :
				$product = $item->get_product();
				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}
				
				$qty = $item->get_quantity();
				$line_subtotal = $item->get_subtotal();
				$line_total = $item->get_total();
				$line_tax = $item->get_subtotal_tax();
				$unit_price = $qty > 0 ? $line_subtotal / $qty : 0;
				$total_with_vat = $line_total + $line_tax;
			?>
			<tr>
				<td class="td" style="padding: 12px; border: 1px solid #e0e0e0; text-align:<?php echo esc_attr( $text_align ); ?>;">
					<?php
					echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
					if ( is_object( $product ) && $product->get_sku() ) {
						echo ' (#' . esc_html( $product->get_sku() ) . ')';
					}
					do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );
					wc_display_item_meta( $item, array( 'before' => '<br>', 'separator' => '<br>' ) );
					do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
					?>
				</td>
				<td class="td" style="padding: 12px; border: 1px solid #e0e0e0; text-align:center;">
					<?php echo esc_html( $qty ); ?>
				</td>
				<td class="td" style="padding: 12px; border: 1px solid #e0e0e0; text-align:right;">
					<?php echo wc_price( $unit_price ); ?>
				</td>
				<td class="td" style="padding: 12px; border: 1px solid #e0e0e0; text-align:right;">
					<?php echo wc_price( $line_subtotal ); ?>
				</td>
				<td class="td" style="padding: 12px; border: 1px solid #e0e0e0; text-align:right;">
					<?php echo wc_price( $line_tax ); ?>
				</td>
				<td class="td" style="padding: 12px; border: 1px solid #e0e0e0; text-align:right; font-weight: bold;">
					<?php echo wc_price( $total_with_vat ); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php
			$item_totals = $order->get_order_item_totals();
			if ( $item_totals ) :
				$i = 0;
				foreach ( $item_totals as $total ) :
					$i++;
					?>
					<tr>
						<th class="td" scope="row" colspan="5" style="text-align:<?php echo esc_attr( $text_align ); ?>; padding: 12px; border: 1px solid #e0e0e0; <?php echo ( $i === 1 ) ? 'border-top-width: 2px;' : ''; ?>">
							<?php echo wp_kses_post( $total['label'] ); ?>
						</th>
						<td class="td" style="text-align:right; padding: 12px; border: 1px solid #e0e0e0; font-weight: <?php echo ( $i === count( $item_totals ) ) ? 'bold; font-size: 16px;' : 'normal;'; ?> <?php echo ( $i === 1 ) ? 'border-top-width: 2px;' : ''; ?>">
							<?php echo wp_kses_post( $total['value'] ); ?>
						</td>
					</tr>
					<?php
				endforeach;
			endif;
			?>
		</tfoot>
	</table>
</div>

<?php
/**
 * Action hook to add custom content after order details in email.
 */
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
?>

