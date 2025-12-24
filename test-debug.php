<?php
define( 'WP_ENVIRONMENT_TYPE', 'local' );
require_once( 'wp-load.php' );

if ( ! session_id() ) {
	session_start();
}

error_reporting( E_ALL );
set_error_handler( function( $errno, $errstr, $errfile, $errline ) {
	echo "<pre style='background:#f0f0f0;padding:10px;'>";
	echo "ERROR [$errno]: $errstr\n";
	echo "File: $errfile\n";
	echo "Line: $errline\n";
	echo "</pre>";
	return true;
});

?>
<!DOCTYPE html>
<html>
<head>
	<title>Customer Sync Debug</title>
	<style>
		body { font-family: monospace; margin: 20px; background: #f5f5f5; }
		.section { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #0073aa; }
		.error { border-left-color: #dc3545; }
		.success { border-left-color: #28a745; }
		h2 { color: #0073aa; margin-top: 0; }
		pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
	</style>
</head>
<body>

<h1>SoftOne Customer Sync Debug</h1>

<?php

global $wpdb;

echo '<div class="section success"><h2>1. Database Status</h2><pre>';
$customer_count = $wpdb->get_var( 
	"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} 
	 WHERE meta_key = 's1_customer_code'"
);
echo "Customers synced from ERP: " . $customer_count . "\n";

$lookup_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_customer_lookup" );
echo "Customers in WC lookup table: " . $lookup_count . "\n";

$total_users = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" );
echo "Total WordPress users: " . $total_users . "\n";

echo '</pre></div>';

echo '<div class="section"><h2>2. Plugin Settings</h2><pre>';
$browser_customers = get_option( 's1wc_softone_browser_customers' );
echo "Browser customers object: " . ( $browser_customers ? $browser_customers : 'NOT SET (using default: CtiWSCustomers)' ) . "\n";

$api = \S1WC\SoftOne_API::instance();
echo "API instance: " . ( $api ? 'OK' : 'FAILED' ) . "\n";
echo '</pre></div>';

echo '<div class="section"><h2>3. API Connection Test</h2><pre>';
$list = get_option( 's1wc_softone_browser_customers', 'CtiWSCustomers' );
$res = $api->get_browser_rows( 'CUSTOMER', $list, '', 1, 0 );

if ( is_wp_error( $res ) ) {
	echo '<span style="color:red;">ERROR:</span> ' . implode( ', ', $res->get_error_messages() ) . "\n";
} else {
	echo '<span style="color:green;">SUCCESS</span>\n';
	echo "Response keys: " . implode( ', ', array_keys( $res ) ) . "\n\n";
	echo "success: " . ( $res['success'] ? 'true' : 'false' ) . "\n";
	echo "totalcount: " . ( $res['totalcount'] ?? 'NOT SET' ) . "\n";
	echo "rows count: " . count( $res['rows'] ?? [] ) . "\n";
	echo "has _columns_meta: " . ( ! empty( $res['_columns_meta'] ) ? 'YES' : 'NO' ) . "\n";
	
	if ( ! empty( $res['_columns_meta'] ) ) {
		echo "\nColumn names (" . count( $res['_columns_meta'] ) . "):\n";
		foreach ( $res['_columns_meta'] as $idx => $col ) {
			echo "  [$idx] " . ( $col['dataIndex'] ?? 'UNKNOWN' ) . "\n";
		}
	}
	
	if ( ! empty( $res['rows'] ) ) {
		echo "\nFirst row field count: " . count( $res['rows'][0] ) . "\n";
		echo "First row sample:\n";
		$row = $res['rows'][0];
		for ( $i = 0; $i < min( 5, count( $row ) ); $i++ ) {
			$val = substr( (string)$row[$i], 0, 60 );
			echo "  [$i] " . $val . "\n";
		}
	}
}
echo '</pre></div>';

echo '<div class="section"><h2>4. Running Customer Sync...</h2><pre>';
ob_start();
do_action( 's1wc_sync_customers' );
$sync_output = ob_get_clean();
echo htmlspecialchars( $sync_output ?: '(no output captured)' );
echo '</pre></div>';

echo '<div class="section success"><h2>5. Post-Sync Database Check</h2><pre>';
$customer_count_after = $wpdb->get_var( 
	"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} 
	 WHERE meta_key = 's1_customer_code'"
);
echo "Customers after sync: " . $customer_count_after . "\n";
echo "New customers created: " . max( 0, $customer_count_after - $customer_count ) . "\n";
echo '</pre></div>';

?>

</body>
</html>
