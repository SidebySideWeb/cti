<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Order_Admin {

	private static $hpos_enabled = null;

	private static function is_hpos_enabled() {
		if ( self::$hpos_enabled === null ) {
			self::$hpos_enabled = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && 
			                      \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}
		return self::$hpos_enabled;
	}

	public static function init() {
		add_action( 'current_screen', [ __CLASS__, 'maybe_init' ] );
	}

	public static function maybe_init() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$is_order_screen = in_array( $screen->id, [ 'edit-shop_order', 'shop_order', 'woocommerce_page_wc-orders' ], true );
		if ( ! $is_order_screen ) {
			return;
		}

		$hpos_enabled = self::is_hpos_enabled();
		
		if ( $hpos_enabled ) {
			add_filter( 'manage_woocommerce_page_wc-orders_columns', [ __CLASS__, 'add_order_column' ], 20 );
			add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ __CLASS__, 'render_order_column_hpos' ], 10, 2 );
			add_filter( 'manage_woocommerce_page_wc-orders_sortable_columns', [ __CLASS__, 'make_column_sortable' ] );
		} else {
			add_filter( 'manage_edit-shop_order_columns', [ __CLASS__, 'add_order_column' ], 20 );
			add_action( 'manage_shop_order_posts_custom_column', [ __CLASS__, 'render_order_column' ], 10, 2 );
			add_filter( 'manage_edit-shop_order_sortable_columns', [ __CLASS__, 'make_column_sortable' ] );
			add_action( 'pre_get_posts', [ __CLASS__, 'handle_sorting' ] );
		}
		
		add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_box' ] );
		add_action( 'add_meta_boxes_shop_order', [ __CLASS__, 'add_meta_box' ] );
	}

	public static function add_order_column( $columns ) {
		$new_columns = [];
		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( $key === 'order_number' ) {
				$new_columns['s1wc_erp_sync'] = __( 'ERP Sync', 'softone-woocommerce-sync' );
			}
		}
		return $new_columns;
	}

	public static function render_order_column( $column, $post_id ) {
		if ( $column !== 's1wc_erp_sync' ) {
			return;
		}
		self::render_sync_column_content( $post_id );
	}

	public static function render_order_column_hpos( $column, $order ) {
		if ( $column !== 's1wc_erp_sync' ) {
			return;
		}
		$order_id = is_a( $order, 'WC_Order' ) ? $order->get_id() : $order;
		self::render_sync_column_content( $order_id );
	}

	private static function render_sync_column_content( $order_id ) {
		$status = get_post_meta( $order_id, '_s1wc_erp_sync_status', true );
		$erp_id = get_post_meta( $order_id, '_s1wc_erp_sync_erp_id', true );
		$sync_time = get_post_meta( $order_id, '_s1wc_erp_sync_time', true );
		$error = get_post_meta( $order_id, '_s1wc_erp_sync_error', true );

		if ( empty( $status ) ) {
			echo '<span class="s1wc-status s1wc-status-pending" style="color: #999;">—</span>';
			return;
		}

		switch ( $status ) {
			case 'success':
				$status_label = __( 'Synced', 'softone-woocommerce-sync' );
				$status_class = 's1wc-status-success';
				$status_color = '#46b450';
				break;
			case 'failed':
				$status_label = __( 'Failed', 'softone-woocommerce-sync' );
				$status_class = 's1wc-status-failed';
				$status_color = '#dc3232';
				break;
			case 'skipped':
				$status_label = __( 'Skipped', 'softone-woocommerce-sync' );
				$status_class = 's1wc-status-skipped';
				$status_color = '#f0b849';
				break;
			default:
				$status_label = ucfirst( $status );
				$status_class = 's1wc-status-' . sanitize_html_class( $status );
				$status_color = '#999';
		}

		echo '<div class="s1wc-sync-info">';
		echo '<span class="' . esc_attr( $status_class ) . '" style="color: ' . esc_attr( $status_color ) . '; font-weight: 600;">';
		echo esc_html( $status_label );
		echo '</span>';

		if ( ! empty( $erp_id ) ) {
			echo '<br><small style="color: #666;">';
			echo esc_html__( 'ERP ID:', 'softone-woocommerce-sync' ) . ' <strong>' . esc_html( $erp_id ) . '</strong>';
			echo '</small>';
		}

		if ( ! empty( $sync_time ) ) {
			echo '<br><small style="color: #999;">';
			echo esc_html( human_time_diff( $sync_time, current_time( 'timestamp' ) ) ) . ' ' . esc_html__( 'ago', 'softone-woocommerce-sync' );
			echo '</small>';
		}

		if ( ! empty( $error ) && $status === 'failed' ) {
			echo '<br><small style="color: ' . esc_attr( $status_color ) . ';" title="' . esc_attr( $error ) . '">';
			echo esc_html( wp_trim_words( $error, 10 ) );
			echo '</small>';
		}

		echo '</div>';
	}

	public static function make_column_sortable( $columns ) {
		$columns['s1wc_erp_sync'] = 's1wc_erp_sync_status';
		return $columns;
	}

	public static function handle_sorting( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( $orderby !== 's1wc_erp_sync_status' ) {
			return;
		}

		$query->set( 'meta_key', '_s1wc_erp_sync_status' );
		$query->set( 'orderby', 'meta_value' );
	}

	public static function add_meta_box() {
		$hpos_enabled = self::is_hpos_enabled();
		
		if ( $hpos_enabled ) {
			add_meta_box(
				's1wc_erp_sync_info',
				__( 'SoftOne ERP Sync', 'softone-woocommerce-sync' ),
				[ __CLASS__, 'render_meta_box' ],
				'woocommerce_page_wc-orders',
				'side',
				'default'
			);
		} else {
			add_meta_box(
				's1wc_erp_sync_info',
				__( 'SoftOne ERP Sync', 'softone-woocommerce-sync' ),
				[ __CLASS__, 'render_meta_box' ],
				'shop_order',
				'side',
				'default'
			);
		}
	}

	public static function render_meta_box( $post_or_order ) {
		if ( is_a( $post_or_order, 'WC_Order' ) ) {
			$order_id = $post_or_order->get_id();
		} else {
			$order_id = $post_or_order->ID;
		}
		$status = get_post_meta( $order_id, '_s1wc_erp_sync_status', true );
		$erp_id = get_post_meta( $order_id, '_s1wc_erp_sync_erp_id', true );
		$sync_time = get_post_meta( $order_id, '_s1wc_erp_sync_time', true );
		$error = get_post_meta( $order_id, '_s1wc_erp_sync_error', true );
		$error_code = get_post_meta( $order_id, '_s1wc_erp_sync_error_code', true );

		?>
		<div class="s1wc-order-sync-info">
			<?php if ( empty( $status ) ) : ?>
				<p>
					<strong><?php esc_html_e( 'Status:', 'softone-woocommerce-sync' ); ?></strong>
					<span style="color: #999;"><?php esc_html_e( 'Not synced', 'softone-woocommerce-sync' ); ?></span>
				</p>
			<?php else : ?>
				<p>
					<strong><?php esc_html_e( 'Status:', 'softone-woocommerce-sync' ); ?></strong>
					<?php
					switch ( $status ) {
						case 'success':
							echo '<span style="color: #46b450; font-weight: 600;">✓ ' . esc_html__( 'Synced', 'softone-woocommerce-sync' ) . '</span>';
							break;
						case 'failed':
							echo '<span style="color: #dc3232; font-weight: 600;">✗ ' . esc_html__( 'Failed', 'softone-woocommerce-sync' ) . '</span>';
							break;
						case 'skipped':
							echo '<span style="color: #f0b849; font-weight: 600;">⊘ ' . esc_html__( 'Skipped', 'softone-woocommerce-sync' ) . '</span>';
							break;
						default:
							echo '<span>' . esc_html( ucfirst( $status ) ) . '</span>';
					}
					?>
				</p>

				<?php if ( ! empty( $erp_id ) ) : ?>
					<p>
						<strong><?php esc_html_e( 'ERP Order ID:', 'softone-woocommerce-sync' ); ?></strong><br>
						<code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; font-size: 13px;"><?php echo esc_html( $erp_id ); ?></code>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $sync_time ) ) : ?>
					<p>
						<strong><?php esc_html_e( 'Last Sync:', 'softone-woocommerce-sync' ); ?></strong><br>
						<?php
						echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $sync_time ) );
						echo ' <small>(' . esc_html( human_time_diff( $sync_time, current_time( 'timestamp' ) ) ) . ' ' . esc_html__( 'ago', 'softone-woocommerce-sync' ) . ')</small>';
						?>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $error ) ) : ?>
					<p>
						<strong><?php esc_html_e( 'Error:', 'softone-woocommerce-sync' ); ?></strong><br>
						<span style="color: #dc3232;"><?php echo esc_html( $error ); ?></span>
						<?php if ( ! empty( $error_code ) ) : ?>
							<br><small style="color: #999;"><?php esc_html_e( 'Code:', 'softone-woocommerce-sync' ); ?> <?php echo esc_html( $error_code ); ?></small>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			if ( empty( $status ) || $status === 'failed' ) :
				$order = wc_get_order( $order_id );
				if ( $order ) :
					?>
					<p>
						<button type="button" class="button button-secondary s1wc-manual-sync-order" data-order-id="<?php echo esc_attr( $order_id ); ?>">
							<?php esc_html_e( 'Sync to ERP', 'softone-woocommerce-sync' ); ?>
						</button>
						<span class="s1wc-sync-spinner" style="display: none; margin-left: 10px;">⏳</span>
					</p>
					<?php
				endif;
			endif;
			?>
		</div>

		<style>
			.s1wc-order-sync-info p {
				margin: 10px 0;
			}
			.s1wc-order-sync-info code {
				display: inline-block;
				margin-top: 4px;
			}
		</style>

		<script>
		jQuery(document).ready(function($) {
			$('.s1wc-manual-sync-order').on('click', function(e) {
				e.preventDefault();
				var $button = $(this);
				var $spinner = $button.siblings('.s1wc-sync-spinner');
				var orderId = $button.data('order-id');
				
				$button.prop('disabled', true);
				$spinner.show();
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 's1wc_manual_sync_single_order',
						order_id: orderId,
						nonce: '<?php echo wp_create_nonce( 's1wc_manual_sync_single_order' ); ?>'
					},
					success: function(response) {
						if (response.success) {
							location.reload();
						} else {
							alert('Sync failed: ' + (response.data || 'Unknown error'));
							$button.prop('disabled', false);
							$spinner.hide();
						}
					},
					error: function() {
						alert('Sync request failed');
						$button.prop('disabled', false);
						$spinner.hide();
					}
				});
			});
		});
		</script>
		<?php
	}
}

