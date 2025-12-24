<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Settings {
	const OPTION = 's1wc_settings';

	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
		register_setting( 's1wc', self::OPTION, [ __CLASS__, 'sanitize' ] );
		add_filter( 'cron_schedules', [ __CLASS__, 'cron_schedules' ] );
		
		add_action( 'wp_ajax_s1wc_sync_products', [ __CLASS__, 'ajax_sync_products' ] );
		add_action( 'wp_ajax_s1wc_sync_customers', [ __CLASS__, 'ajax_sync_customers' ] );
		add_action( 'wp_ajax_s1wc_sync_orders', [ __CLASS__, 'ajax_sync_orders' ] );
	}

	public static function menu() {
		add_submenu_page(
			'woocommerce',
			'SoftOne Settings',
			'SoftOne Settings',
			'manage_woocommerce',
			's1wc-settings',
			[ __CLASS__, 'render' ]
		);
	}

	public static function sanitize( $input ) {
		$out = [];
		$out['endpoint']  = esc_url_raw( $input['endpoint'] ?? '' );
		$out['username']  = sanitize_text_field( $input['username'] ?? '' );
		$out['password']  = sanitize_text_field( $input['password'] ?? '' );
		$out['appid']     = sanitize_text_field( $input['appid'] ?? '1001' );
		$out['company']   = sanitize_text_field( $input['company'] ?? '1' );
		$out['branch']    = sanitize_text_field( $input['branch'] ?? '1' );
		$out['refid']     = sanitize_text_field( $input['refid'] ?? '900' );
		$out['userid']    = sanitize_text_field( $input['userid'] ?? '900' );
		$out['price_field'] = sanitize_text_field( $input['price_field'] ?? 'ITEM.PRICER' );
		$out['active_field'] = sanitize_text_field( $input['active_field'] ?? 'ITEM.ISACTIVE' );
		$out['browser_items'] = sanitize_text_field( $input['browser_items'] ?? 'CtiWSItems' );
		$out['browser_customers'] = sanitize_text_field( $input['browser_customers'] ?? 'CtiWSCustomers' );
		$out['lookup_category_object'] = sanitize_text_field( $input['lookup_category_object'] ?? '' );
		$out['lookup_group_object'] = sanitize_text_field( $input['lookup_group_object'] ?? '' );
		$out['lookup_brand_object'] = sanitize_text_field( $input['lookup_brand_object'] ?? '' );
		$out['enable_query_monitor'] = ! empty( $input['enable_query_monitor'] );
		$allowed = [ 'every_15_minutes', 'every_30_minutes', 'hourly', 'every_3_hours', 'every_4_hours', 'every_8_hours', 'twicedaily', 'daily' ];
		$out['sync_products_interval'] = in_array( $input['sync_products_interval'] ?? 'every_4_hours', $allowed, true ) ? $input['sync_products_interval'] : 'every_4_hours';
		$out['sync_customers_interval'] = in_array( $input['sync_customers_interval'] ?? 'every_8_hours', $allowed, true ) ? $input['sync_customers_interval'] : 'every_8_hours';
		$out['sync_orders_interval'] = in_array( $input['sync_orders_interval'] ?? 'every_3_hours', $allowed, true ) ? $input['sync_orders_interval'] : 'every_3_hours';

		if ( method_exists( __CLASS__, 'schedule_crons' ) ) {
			add_action( 'shutdown', [ __CLASS__, 'schedule_crons' ] );
		}
		return $out;
	}

	public static function get( $key = null, $default = '' ) {
		$opt = get_option( self::OPTION, [] );
		if ( $key === null ) return $opt;
		return $opt[$key] ?? $default;
	}

	public static function render() {
		$opt = self::get();
		?>
		<div class="wrap">
			<h1>SoftOne ERP Settings</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 's1wc' ); ?>
				<table class="form-table">
					<tr><th><label>Endpoint</label></th><td><input type="url" name="<?php echo esc_attr(self::OPTION); ?>[endpoint]" value="<?php echo esc_attr($opt['endpoint'] ?? 'https://cti.oncloud.gr/s1services'); ?>" class="regular-text" /></td></tr>
					<tr><th><label>Username</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[username]" value="<?php echo esc_attr($opt['username'] ?? ''); ?>" class="regular-text" /></td></tr>
					<tr><th><label>Password</label></th><td><input type="password" name="<?php echo esc_attr(self::OPTION); ?>[password]" value="<?php echo esc_attr($opt['password'] ?? ''); ?>" class="regular-text" /></td></tr>
					<tr><th><label>App ID</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[appid]" value="<?php echo esc_attr($opt['appid'] ?? '1001'); ?>" /></td></tr>
					<tr><th><label>Company</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[company]" value="<?php echo esc_attr($opt['company'] ?? '1'); ?>" /></td></tr>
					<tr><th><label>Branch</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[branch]" value="<?php echo esc_attr($opt['branch'] ?? '1'); ?>" /></td></tr>
					<tr><th><label>RefID</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[refid]" value="<?php echo esc_attr($opt['refid'] ?? '900'); ?>" /></td></tr>
					<tr><th><label>UserID</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[userid]" value="<?php echo esc_attr($opt['userid'] ?? '900'); ?>" /></td></tr>
					<tr><th><label>Item Browser Name</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[browser_items]" value="<?php echo esc_attr($opt['browser_items'] ?? 'CtiWSItems'); ?>" /></td></tr>
					<tr><th><label>Customer Browser Name</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[browser_customers]" value="<?php echo esc_attr($opt['browser_customers'] ?? 'CtiWSCustomers'); ?>" /></td></tr>
					<tr><th><label>Products Sync Interval</label></th><td>
						<select name="<?php echo esc_attr(self::OPTION); ?>[sync_products_interval]">
							<?php $sel = $opt['sync_products_interval'] ?? 'every_4_hours'; ?>
							<option value="every_15_minutes" <?php selected( $sel, 'every_15_minutes' ); ?>>Every 15 minutes</option>
							<option value="every_30_minutes" <?php selected( $sel, 'every_30_minutes' ); ?>>Every 30 minutes</option>
							<option value="hourly" <?php selected( $sel, 'hourly' ); ?>>Hourly</option>
							<option value="every_3_hours" <?php selected( $sel, 'every_3_hours' ); ?>>Every 3 hours</option>
							<option value="every_4_hours" <?php selected( $sel, 'every_4_hours' ); ?>>Every 4 hours</option>
							<option value="every_8_hours" <?php selected( $sel, 'every_8_hours' ); ?>>Every 8 hours</option>
							<option value="twicedaily" <?php selected( $sel, 'twicedaily' ); ?>>Twice Daily</option>
							<option value="daily" <?php selected( $sel, 'daily' ); ?>>Daily</option>
						</select>
					</td></tr>
					<tr><th><label>Customers Sync Interval</label></th><td>
						<select name="<?php echo esc_attr(self::OPTION); ?>[sync_customers_interval]">
							<?php $selc = $opt['sync_customers_interval'] ?? 'every_8_hours'; ?>
							<option value="every_15_minutes" <?php selected( $selc, 'every_15_minutes' ); ?>>Every 15 minutes</option>
							<option value="every_30_minutes" <?php selected( $selc, 'every_30_minutes' ); ?>>Every 30 minutes</option>
							<option value="hourly" <?php selected( $selc, 'hourly' ); ?>>Hourly</option>
							<option value="every_3_hours" <?php selected( $selc, 'every_3_hours' ); ?>>Every 3 hours</option>
							<option value="every_4_hours" <?php selected( $selc, 'every_4_hours' ); ?>>Every 4 hours</option>
							<option value="every_8_hours" <?php selected( $selc, 'every_8_hours' ); ?>>Every 8 hours</option>
							<option value="twicedaily" <?php selected( $selc, 'twicedaily' ); ?>>Twice Daily</option>
							<option value="daily" <?php selected( $selc, 'daily' ); ?>>Daily</option>
						</select>
					</td></tr>
					<tr><th><label>Orders Sync Interval</label></th><td>
						<select name="<?php echo esc_attr(self::OPTION); ?>[sync_orders_interval]">
							<?php $selo = $opt['sync_orders_interval'] ?? 'every_3_hours'; ?>
							<option value="every_15_minutes" <?php selected( $selo, 'every_15_minutes' ); ?>>Every 15 minutes</option>
							<option value="every_30_minutes" <?php selected( $selo, 'every_30_minutes' ); ?>>Every 30 minutes</option>
							<option value="hourly" <?php selected( $selo, 'hourly' ); ?>>Hourly</option>
							<option value="every_3_hours" <?php selected( $selo, 'every_3_hours' ); ?>>Every 3 hours</option>
							<option value="every_4_hours" <?php selected( $selo, 'every_4_hours' ); ?>>Every 4 hours</option>
							<option value="every_8_hours" <?php selected( $selo, 'every_8_hours' ); ?>>Every 8 hours</option>
							<option value="twicedaily" <?php selected( $selo, 'twicedaily' ); ?>>Twice Daily</option>
							<option value="daily" <?php selected( $selo, 'daily' ); ?>>Daily</option>
						</select>
						<p class="description">Note: Orders are also synced immediately when a customer completes checkout.</p>
					</td></tr>
					<tr><th><label>Price Field</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[price_field]" value="<?php echo esc_attr($opt['price_field'] ?? 'ITEM.PRICER'); ?>" /></td></tr>
					<tr><th><label>Active Field</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[active_field]" value="<?php echo esc_attr($opt['active_field'] ?? 'ITEM.ISACTIVE'); ?>" /></td></tr>
							<tr><th><label>Lookup Category Object</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[lookup_category_object]" value="<?php echo esc_attr($opt['lookup_category_object'] ?? ''); ?>" /><p class="description">Optional: SoftOne object/list name to resolve category IDs (e.g. &quot;MTRCATEGORIES&quot;).</p></td></tr>
							<tr><th><label>Lookup Group Object</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[lookup_group_object]" value="<?php echo esc_attr($opt['lookup_group_object'] ?? ''); ?>" /><p class="description">Optional: SoftOne object/list name to resolve group IDs.</p></td></tr>
							<tr><th><label>Lookup Brand Object</label></th><td><input type="text" name="<?php echo esc_attr(self::OPTION); ?>[lookup_brand_object]" value="<?php echo esc_attr($opt['lookup_brand_object'] ?? ''); ?>" /><p class="description">Optional: SoftOne object/list name to resolve brand IDs (e.g. &quot;MTRBRAND&quot;).</p></td></tr>
					<tr><th><label>Enable Query Monitoring</label></th><td>
						<label><input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[enable_query_monitor]" value="1" <?php checked( $opt['enable_query_monitor'] ?? false ); ?> /> Enable query performance monitoring (logs slow queries)</label>
						<p class="description">When enabled, logs queries taking longer than 100ms. Useful for debugging performance issues.</p>
					</td></tr>
				</table>
				<?php submit_button(); ?>
			</form>
			
			<hr style="margin: 30px 0;">
			
			<h2>Manual Sync</h2>
			<p class="description">Run manual syncs for products, customers, and orders. Incremental sync only processes items updated since the last sync, while full sync processes all items.</p>
			
			<table class="form-table">
				<tr>
					<th><label>Products Sync</label></th>
					<td>
						<button type="button" id="s1wc-sync-products-incremental" class="button button-secondary" data-type="products" data-mode="incremental">Run Incremental Sync</button>
						<button type="button" id="s1wc-sync-products-full" class="button button-primary" data-type="products" data-mode="full" style="margin-left: 10px;">Run Full Sync</button>
						<span id="s1wc-products-status" style="margin-left: 15px;"></span>
					</td>
				</tr>
				<tr>
					<th><label>Customers Sync</label></th>
					<td>
						<button type="button" id="s1wc-sync-customers-incremental" class="button button-secondary" data-type="customers" data-mode="incremental">Run Incremental Sync</button>
						<button type="button" id="s1wc-sync-customers-full" class="button button-primary" data-type="customers" data-mode="full" style="margin-left: 10px;">Run Full Sync</button>
						<span id="s1wc-customers-status" style="margin-left: 15px;"></span>
					</td>
				</tr>
				<tr>
					<th><label>Orders Sync</label></th>
					<td>
						<button type="button" id="s1wc-sync-orders-incremental" class="button button-secondary" data-type="orders" data-mode="incremental">Run Incremental Sync</button>
						<button type="button" id="s1wc-sync-orders-full" class="button button-primary" data-type="orders" data-mode="full" style="margin-left: 10px;">Run Full Sync</button>
						<span id="s1wc-orders-status" style="margin-left: 15px;"></span>
					</td>
				</tr>
			</table>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			function handleSync(type, mode) {
				var buttonId = '#s1wc-sync-' + type + '-' + mode;
				var statusId = '#s1wc-' + type + '-status';
				var $button = $(buttonId);
				var $status = $(statusId);
				
				// Disable all buttons for this type
				$('[data-type="' + type + '"]').prop('disabled', true);
				$status.html('<span class="spinner is-active" style="float:none;margin:0 5px;"></span> Running ' + mode + ' sync...');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 's1wc_sync_' + type,
						mode: mode,
						nonce: '<?php echo wp_create_nonce( 's1wc_manual_sync' ); ?>'
					},
					success: function(response) {
						if (response.success) {
							$status.html('<span style="color: green;">✓ ' + (response.data.message || 'Sync completed successfully') + '</span>');
						} else {
							$status.html('<span style="color: red;">✗ Error: ' + (response.data.message || 'Sync failed') + '</span>');
						}
					},
					error: function(xhr, status, error) {
						$status.html('<span style="color: red;">✗ Error: ' + error + '</span>');
					},
					complete: function() {
						// Re-enable all buttons for this type
						$('[data-type="' + type + '"]').prop('disabled', false);
					}
				});
			}
			
			$('[id^="s1wc-sync-"]').on('click', function() {
				var type = $(this).data('type');
				var mode = $(this).data('mode');
				handleSync(type, mode);
			});
		});
		</script>
		<?php
	}

	public static function cron_schedules( $schedules ) {
		$schedules['every_15_minutes'] = [ 'interval' => 15 * 60, 'display' => 'Every 15 Minutes' ];
		$schedules['every_30_minutes'] = [ 'interval' => 30 * 60, 'display' => 'Every 30 Minutes' ];
		$schedules['every_3_hours'] = [ 'interval' => 3 * 60 * 60, 'display' => 'Every 3 Hours' ];
		$schedules['every_4_hours'] = [ 'interval' => 4 * 60 * 60, 'display' => 'Every 4 Hours' ];
		$schedules['every_8_hours'] = [ 'interval' => 8 * 60 * 60, 'display' => 'Every 8 Hours' ];
		$schedules['twicedaily'] = [ 'interval' => 12 * 60 * 60, 'display' => 'Twice Daily' ];
		$schedules['daily'] = [ 'interval' => 24 * 60 * 60, 'display' => 'Daily' ];
		return $schedules;
	}

	public static function schedule_crons() {
		$opt = self::get();
		$prod_interval = $opt['sync_products_interval'] ?? 'every_4_hours';
		$cust_interval = $opt['sync_customers_interval'] ?? 'every_8_hours';
		$orders_interval = $opt['sync_orders_interval'] ?? 'every_3_hours';

		wp_clear_scheduled_hook( 's1wc_sync_products' );
		wp_schedule_event( time() + 60, $prod_interval, 's1wc_sync_products' );

		wp_clear_scheduled_hook( 's1wc_sync_customers' );
		wp_schedule_event( time() + 120, $cust_interval, 's1wc_sync_customers' );

		wp_clear_scheduled_hook( 's1wc_sync_orders' );
		wp_schedule_event( time() + 180, $orders_interval, 's1wc_sync_orders' );
	}

	public static function ajax_sync_products() {
		check_ajax_referer( 's1wc_manual_sync', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions' ] );
			return;
		}

		$mode = sanitize_text_field( $_POST['mode'] ?? 'incremental' );
		$force_full = ( $mode === 'full' );

		try {
			\S1WC\Product_Sync::sync_products( $force_full );
			wp_send_json_success( [ 'message' => 'Products sync completed. Check logs for details.' ] );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}

	public static function ajax_sync_customers() {
		check_ajax_referer( 's1wc_manual_sync', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions' ] );
			return;
		}

		$mode = sanitize_text_field( $_POST['mode'] ?? 'incremental' );
		$force_full = ( $mode === 'full' );

		try {
			\S1WC\Customer_Sync::sync_customers( $force_full );
			wp_send_json_success( [ 'message' => 'Customers sync completed. Check logs for details.' ] );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}

	public static function ajax_sync_orders() {
		check_ajax_referer( 's1wc_manual_sync', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions' ] );
			return;
		}

		$mode = sanitize_text_field( $_POST['mode'] ?? 'incremental' );
		$force_full = ( $mode === 'full' );

		try {
			\S1WC\Order_Sync::sync_orders( $force_full );
			wp_send_json_success( [ 'message' => 'Orders sync completed. Check logs for details.' ] );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}
}
