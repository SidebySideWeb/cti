<?php
ob_start();

require_once( 'wp-load.php' );

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

echo "=== Starting Customer Sync Test ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

do_action( 's1wc_sync_customers' );

echo "\n=== Customer Sync Complete ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

$debug_log = WP_CONTENT_DIR . '/debug.log';
if ( file_exists( $debug_log ) ) {
	echo "=== Debug Log (last 100 lines) ===\n";
	$logs = file( $debug_log );
	$recent = array_slice( $logs, -100 );
	echo implode( '', $recent );
} else {
	echo "No debug.log found at: " . $debug_log . "\n";
}

$output = ob_get_clean();
echo $output;
?>
