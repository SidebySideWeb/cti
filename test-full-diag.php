<?php
require_once( 'wp-load.php' );

global $wpdb;

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

?>
<!DOCTYPE html>
<html>
<head>
	<title>Customer Sync Full Diagnostic</title>
	<style>
		* { margin: 0; padding: 0; }
		body { font-family: 'Monaco', 'Menlo', monospace; font-size: 12px; background: #1e1e1e; color: #d4d4d4; }
		.container { max-width: 1400px; margin: 0 auto; padding: 20px; }
		h1 { color: #4ec9b0; margin: 30px 0 10px; padding-bottom: 10px; border-bottom: 2px solid #4ec9b0; }
		h2 { color: #569cd6; margin: 20px 0 10px; }
		.box { background: #252526; border: 1px solid #3e3e42; padding: 15px; margin: 10px 0; border-radius: 4px; }
		.error { border-left: 4px solid #f48771; }
		.success { border-left: 4px solid #6a9955; }
		.warning { border-left: 4px solid #dcdcaa; }
		.info { border-left: 4px solid #569cd6; }
		pre { background: #1e1e1e; padding: 10px; overflow-x: auto; color: #d4d4d4; white-space: pre-wrap; word-wrap: break-word; }
		.key { color: #9cdcfe; }
		.value { color: #ce9178; }
		.number { color: #b5cea8; }
		table { width: 100%; border-collapse: collapse; margin: 10px 0; }
		th, td { padding: 8px; text-align: left; border-bottom: 1px solid #3e3e42; }
		th { background: #2d2d30; font-weight: bold; color: #4ec9b0; }
		.status-ok { color: #6a9955; }
		.status-err { color: #f48771; }
		.status-warn { color: #dcdcaa; }
	</style>
</head>
<body>

<div class="container">

<h1>Customer Sync - Full Diagnostic Report</h1>

<?php

echo '<h2>Step 1: Database Status</h2>';
echo '<div class="box info">';

$customer_count = $wpdb->get_var( 
	"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} 
	 WHERE meta_key = 's1_customer_code'"
);

$lookup_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_customer_lookup" );
$total_users = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" );

echo '<pre>';
echo '<span class="key">Customers from ERP (s1_customer_code):</span> <span class="number">' . $customer_count . '</span>' . "\n";
echo '<span class="key">Customers in wp_wc_customer_lookup:</span> <span class="number">' . $lookup_count . '</span>' . "\n";
echo '<span class="key">Total WP Users:</span> <span class="number">' . $total_users . '</span>' . "\n";
echo '</pre>';

if ( $customer_count > 0 ) {
	echo '<div class="success" style="padding:10px;margin:10px 0;">';
	echo '✓ Customers found in database';
	echo '</div>';
} else {
	echo '<div class="warning" style="padding:10px;margin:10px 0;">';
	echo '⚠ No customers found - sync may not have run successfully';
	echo '</div>';
}
echo '</div>';

echo '<h2>Step 2: Plugin Settings</h2>';
echo '<div class="box info">';
echo '<pre>';

$browser_customers = get_option( 's1wc_softone_browser_customers' );
echo '<span class="key">Browser customers setting:</span> <span class="value">' . ( $browser_customers ?: 'NOT SET (default: CtiWSCustomers)' ) . '</span>' . "\n";

$sync_interval = get_option( 's1wc_softone_sync_customers_interval' );
echo '<span class="key">Sync interval:</span> <span class="value">' . ( $sync_interval ?: 'NOT SET' ) . '</span>' . "\n";

$api_endpoint = get_option( 's1wc_softone_api_endpoint' );
echo '<span class="key">API endpoint:</span> <span class="value">' . ( $api_endpoint ?: 'NOT SET' ) . '</span>' . "\n";

// Check cron schedule
$next_run = wp_next_scheduled( 's1wc_sync_customers' );
echo '<span class="key">Next sync scheduled:</span> <span class="value">' . ( $next_run ? date( 'Y-m-d H:i:s', $next_run ) : 'NOT SCHEDULED' ) . '</span>' . "\n";

echo '</pre>';
echo '</div>';

echo '<h2>Step 3: SoftOne API Connection Test</h2>';
echo '<div class="box info">';

try {
	$api = \S1WC\SoftOne_API::instance();
	echo '<div class="success" style="padding:10px;margin:10px 0;">✓ API instance created</div>';
	
	$list = get_option( 's1wc_softone_browser_customers', 'CtiWSCustomers' );
	echo '<pre><span class="key">Testing getBrowserRows for:</span> <span class="value">' . $list . '</span></pre>';
	
	echo '<pre>';
	echo 'Fetching 1 row to test API response...' . "\n\n";
	echo '</pre>';
	
	$res = $api->get_browser_rows( 'CUSTOMER', $list, '', 1, 0 );
	
	if ( is_wp_error( $res ) ) {
		echo '<div class="error" style="padding:10px;margin:10px 0;">';
		echo '✗ API Error: ' . implode( ', ', $res->get_error_messages() );
		echo '</div>';
	} else {
		echo '<div class="success" style="padding:10px;margin:10px 0;">✓ API Response received</div>';
		
		echo '<table>';
		echo '<tr><th>Key</th><th>Value</th></tr>';
		
		$success = $res['success'] ?? false;
		$status_class = $success ? 'status-ok' : 'status-err';
		echo '<tr><td>success</td><td><span class="' . $status_class . '">' . ( $success ? 'TRUE' : 'FALSE' ) . '</span></td></tr>';
		
		echo '<tr><td>totalcount</td><td><span class="number">' . ( $res['totalcount'] ?? 0 ) . '</span></td></tr>';
		echo '<tr><td>rows in response</td><td><span class="number">' . count( $res['rows'] ?? [] ) . '</span></td></tr>';
		echo '<tr><td>has _columns_meta</td><td><span class="status-ok">' . ( ! empty( $res['_columns_meta'] ) ? 'YES' : 'NO' ) . '</span></td></tr>';
		echo '</table>';
		
		if ( ! empty( $res['_columns_meta'] ) ) {
			echo '<h3 style="color:#569cd6;margin:10px 0;">Column Metadata:</h3>';
			echo '<pre>';
			foreach ( $res['_columns_meta'] as $idx => $col ) {
				$dataIndex = $col['dataIndex'] ?? 'UNKNOWN';
				echo sprintf( "  [%2d] %s\n", $idx, $dataIndex );
			}
			echo '</pre>';
		}
		
		if ( ! empty( $res['rows'] ) && count( $res['rows'] ) > 0 ) {
			echo '<h3 style="color:#569cd6;margin:10px 0;">First Row Sample:</h3>';
			$row = $res['rows'][0];
			echo '<pre>';
			echo 'Row has <span class="number">' . count( $row ) . '</span> fields' . "\n\n";
			for ( $i = 0; $i < min( 10, count( $row ) ); $i++ ) {
				$val = htmlspecialchars( substr( (string)$row[$i], 0, 80 ) );
				printf( "  [%2d] %s\n", $i, $val );
			}
			if ( count( $row ) > 10 ) {
				echo "  ... and " . ( count( $row ) - 10 ) . " more fields\n";
			}
			echo '</pre>';
		}
	}
	
} catch ( Exception $e ) {
	echo '<div class="error" style="padding:10px;margin:10px 0;">';
	echo '✗ Exception: ' . htmlspecialchars( $e->getMessage() );
	echo '</div>';
}

echo '</div>';

if ( $customer_count > 0 ) {
	echo '<h2>Step 4: Sample Synced Customers</h2>';
	echo '<div class="box success">';
	
	$users = $wpdb->get_results( 
		"SELECT ID, user_email, user_registered FROM {$wpdb->users} 
		 WHERE ID IN (
		 	SELECT user_id FROM {$wpdb->usermeta} 
		 	WHERE meta_key = 's1_customer_code'
		 )
		 ORDER BY user_registered DESC
		 LIMIT 5"
	);
	
	echo '<table>';
	echo '<tr><th>User ID</th><th>Email</th><th>ERP Code</th><th>Registered</th></tr>';
	
	foreach ( $users as $user ) {
		$code = $wpdb->get_var( $wpdb->prepare( 
			"SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 's1_customer_code'",
			$user->ID
		));
		$afm = $wpdb->get_var( $wpdb->prepare( 
			"SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 's1_customer_afm'",
			$user->ID
		));
		
		echo '<tr>';
		echo '<td>' . $user->ID . '</td>';
		echo '<td>' . htmlspecialchars( $user->user_email ) . '</td>';
		echo '<td>' . htmlspecialchars( $code ?: '-' ) . ' (AFM: ' . htmlspecialchars( $afm ?: '-' ) . ')</td>';
		echo '<td>' . $user->user_registered . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	
	echo '</div>';
}

echo '<h2>Step 5: Debug Logs</h2>';
echo '<div class="box info">';

$debug_log = WP_CONTENT_DIR . '/debug.log';
if ( file_exists( $debug_log ) ) {
	$file_size = filesize( $debug_log );
	$mod_time = filemtime( $debug_log );
	
	echo '<pre>';
	echo '<span class="key">Debug log:</span> <span class="value">EXISTS</span>' . "\n";
	echo '<span class="key">Size:</span> <span class="number">' . number_format( $file_size ) . '</span> bytes' . "\n";
	echo '<span class="key">Last modified:</span> ' . date( 'Y-m-d H:i:s', $mod_time ) . "\n";
	echo '</pre>';
	
	echo '<h3 style="color:#569cd6;margin:10px 0;">Last 50 Log Lines:</h3>';
	$logs = file( $debug_log );
	$recent = array_slice( $logs, -50 );
	echo '<pre style="max-height:600px;overflow-y:auto;">';
	echo htmlspecialchars( implode( '', $recent ) );
	echo '</pre>';
} else {
	echo '<div class="warning" style="padding:10px;margin:10px 0;">';
	echo '⚠ No debug.log file found at: ' . $debug_log;
	echo '</div>';
}

echo '</div>';

?>

</div>

</body>
</html>
