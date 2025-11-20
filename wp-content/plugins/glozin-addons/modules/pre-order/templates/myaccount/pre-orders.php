<?php
/**
 * My Pre-Orders table
 *
 * @package Glozin\Addons\Modules\Pre_Order\Templates\MyAccount
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Vars used on this template.
 *
 * @var array $orders Customer's orders that include pre-order products.
 */

$columns = array(
	'thumbnail' => esc_html__( 'Thumbnail', 'glozin-addons' ),
	'title'     => esc_html__( 'Product', 'glozin-addons' ),
	'order'     => esc_html__( 'Order', 'glozin-addons' ),
	'price'     => esc_html__( 'Price', 'glozin-addons' ),
	'date'      => esc_html__( 'Availability date', 'glozin-addons' ),
);

?>
<?php if ( $orders ) : ?>
	<table class="shop_table shop_table_responsive my_account_orders my-account__pre-orders glozin-pre-orders__table">
		<thead>
		<tr>
			<?php foreach ( $columns as $column_id => $column ) : ?>
				<th id="glozin-column-<?php echo esc_attr( $column_id ); ?>" class="pre-orders-<?php echo esc_attr( $column_id ); ?>"><?php echo esc_html( $column ); ?></th>
			<?php endforeach; ?>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $orders as $order_id ) {
			$_order = wc_get_order( $order_id );
			$items  = $_order->get_items();
			foreach ( $items as $item_id => $item ) {
				$product = $item instanceof \WC_Order_Item_Product ? $item->get_product() : false;
				if ( ! $product ) {
					continue;
				}
				$item_is_pre_order = ! empty( $item['glozin_item_preorder'] ) ? $item['glozin_item_preorder'] : '';
				if ( apply_filters( 'glozin_my_pre_orders_show_row', 'yes' === $item_is_pre_order, $item ) ) {
					$is_visible        = $product->is_visible();
					$product_permalink = $is_visible ? $product->get_permalink() : '';
					?>
					<tr>
						<td data-title="<?php esc_attr_e( 'Image', 'glozin-addons' ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
						</td>
						<td data-title="<?php esc_attr_e( 'Product', 'glozin-addons' ); ?>">
							<a href="<?php echo esc_attr( $product_permalink ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
							<?php
							wc_display_item_meta( $item );
							wc_display_item_downloads( $item );
							?>
						</td>
						<td data-title="<?php esc_attr_e( 'Order', 'glozin-addons' ); ?>">
							<?php
							$url       = $_order->get_view_order_url();
							$link_text = _x( '#', 'hash before order number', 'glozin-addons' ) . $_order->get_order_number();
							?>
							<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $link_text ); ?></a>
						</td>
						<td data-title="<?php esc_attr_e( 'Price', 'glozin-addons' ); ?>">
							<?php echo '<span class="price">' . wp_kses_post( $_order->get_formatted_line_subtotal( $item ) ) . '</span>'; ?>
						</td>
						<td data-title="<?php esc_attr_e( 'Availability date', 'glozin-addons' ); ?>">
						<?php
							$date = get_post_meta( $product->get_id(), '_pre_order_date', true );
							echo ! empty( $date ) ? wp_date( get_option( 'date_format' ), $date ) : esc_html__( 'N/A', 'glozin-addons' );
						?>
						</td>
					</tr>
					<?php
				}
			}
		}
		?>
		</tbody>
	</table>
<?php else : ?>
	<div><?php esc_html_e( 'No pre-orders found.', 'glozin-addons' ); ?></div>
<?php endif; ?>