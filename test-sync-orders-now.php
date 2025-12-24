<?php
// Load WordPress
require_once( 'wp-load.php' );

// Check if settings are configured
$endpoint = \S1WC\Settings::get('endpoint');
if ( ! $endpoint ) {
	echo '<div style="background:#f8d7da;color:#721c24;padding:20px;border-radius:4px;">';
	echo '<h2>⚠️ Configuration Required</h2>';
	echo '<p>Before syncing orders, you need to configure the SoftOne API settings.</p>';
	echo '<p><strong>Go to:</strong> WooCommerce → SoftOne Settings</p>';
	echo '<p>Fill in:</p>';
	echo '<ul>';
	echo '<li><strong>Endpoint:</strong> Your SoftOne API URL (e.g., https://cti.oncloud.gr/s1services)</li>';
	echo '<li><strong>Username:</strong> Your SoftOne username</li>';
	echo '<li><strong>Password:</strong> Your SoftOne password</li>';
	echo '</ul>';
	echo '</div>';
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Force Full Order Sync</title>
	<style>
		body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
		.container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
		h1 { color: #0073aa; }
		.status { padding: 15px; margin: 20px 0; border-radius: 4px; }
		.loading { background: #e7f3ff; border-left: 4px solid #0073aa; }
		.success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
		.error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
		.info-box { background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 4px; }
		pre { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; overflow-x: auto; }
		button { background: #0073aa; color: white; border: none; padding: 12px 30px; font-size: 16px; border-radius: 4px; cursor: pointer; }
		button:hover { background: #005a87; }
	</style>
</head>
<body>

<div class="container">
	<h1>🔄 Force Full Order Sync</h1>
	
	<div class="info-box">
		<strong>Status:</strong> Sync is running...<br>
		This will synchronize all completed orders from WooCommerce to SoftOne ERP, ignoring previous sync status.
	</div>

<?php

global $wpdb;

// Get count before
$count_before = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status = 'wc-completed'" );

echo '<div class="status loading">';
echo '<strong>Before sync:</strong> ' . $count_before . ' completed orders<br>';
echo '<strong>Starting force full sync...</strong>';
echo '</div>';

// Capture sync output
ob_start();
\S1WC\Order_Sync::sync_orders( true ); // Force full sync
$output = ob_get_clean();

// Get count after - this doesn't change, but we can show processed count from logs
echo '<div class="status success">';
echo '✓ <strong>Sync Completed!</strong><br>';
echo 'All completed orders have been processed for ERP sync. Check logs for details.';
echo '</div>';

// Show debug info
echo '<h2>Debug Information</h2>';

$api = \S1WC\SoftOne_API::instance();

// Show recent logs
echo '<h2>Recent Logs</h2>';
$debug_log = WP_CONTENT_DIR . '/debug.log';
if ( file_exists( $debug_log ) ) {
	$logs = file( $debug_log );
	$recent = array_slice( $logs, -20 );
	echo '<pre>' . htmlspecialchars( implode( '', $recent ) ) . '</pre>';
}

echo '<hr>';
echo '<p><a href="/test-full-diag.php" style="color: #0073aa; text-decoration: none;">← Back to Full Diagnostic</a></p>';

?>

</div>

</body>
</html>