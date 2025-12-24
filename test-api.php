<?php
require_once( 'wp-load.php' );

global $wpdb;

echo "=== Database Check ===\n\n";

$customer_count = $wpdb->get_var( 
	"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} 
	 WHERE meta_key = 's1_customer_code'"
);

echo "Customers with s1_customer_code meta: " . $customer_count . "\n";

$lookup_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_customer_lookup" );
echo "Customers in wp_wc_customer_lookup: " . $lookup_count . "\n\n";

echo "=== First 5 Customers ===\n";
$users = $wpdb->get_results( 
	"SELECT ID, user_email, user_registered FROM {$wpdb->users} 
	 WHERE ID IN (
	 	SELECT user_id FROM {$wpdb->usermeta} 
	 	WHERE meta_key = 's1_customer_code'
	 )
	 LIMIT 5"
);

foreach ( $users as $user ) {
	echo "ID: {$user->ID}, Email: {$user->user_email}, Registered: {$user->user_registered}\n";
	$code = $wpdb->get_var( $wpdb->prepare( 
		"SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 's1_customer_code'",
		$user->ID
	));
	echo "  Code: $code\n\n";
}

echo "=== Testing SoftOne API Connection ===\n";
$api = \S1WC\SoftOne_API::instance();

echo "\nTrying to fetch customer browser rows...\n";
$res = $api->get_browser_rows( 'CUSTOMER', 'CtiWSCustomers', '', 1, 0 );

if ( is_wp_error( $res ) ) {
	echo "ERROR: " . implode( ', ', $res->get_error_messages() ) . "\n";
} else {
	echo "Success: " . ( $res['success'] ? 'YES' : 'NO' ) . "\n";
	echo "Total count: " . ( $res['totalcount'] ?? 0 ) . "\n";
	echo "Rows in response: " . count( $res['rows'] ?? [] ) . "\n";
	echo "Has columns metadata: " . ( ! empty( $res['_columns_meta'] ) ? 'YES' : 'NO' ) . "\n";
	
	if ( ! empty( $res['_columns_meta'] ) ) {
		echo "\nColumn names: ";
		$cols = array_map( function( $c ) { return $c['dataIndex'] ?? 'UNKNOWN'; }, $res['_columns_meta'] );
		echo implode( ', ', $cols ) . "\n";
	}
	
	if ( ! empty( $res['rows'] ) ) {
		echo "\nFirst row sample: ";
		$first_row = $res['rows'][0];
		echo "(" . count( $first_row ) . " fields)\n";
		for ( $i = 0; $i < min( 5, count( $first_row ) ); $i++ ) {
			echo "  [$i] = " . substr( (string)$first_row[$i], 0, 50 ) . "\n";
		}
	}
}
?>
