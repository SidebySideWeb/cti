<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Query Monitor for performance tracking
 */
class Query_Monitor {
	
	private static $queries = [];
	private static $start_time = null;
	private static $enabled = false;
	
	const OPTION_ENABLED = 's1wc_query_monitor_enabled';
	const SLOW_QUERY_THRESHOLD = 0.1; // 100ms
	
	public static function init() {
		// Only enable in debug mode or if explicitly enabled in settings
		self::$enabled = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || get_option( self::OPTION_ENABLED, false );
		
		if ( self::$enabled ) {
			self::$start_time = microtime( true );
			
			// Hook into WordPress query logging
			add_action( 'shutdown', [ __CLASS__, 'log_summary' ], 999 );
			
			// Monitor slow admin page loads
			if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				add_action( 'admin_init', [ __CLASS__, 'start_admin_timer' ], 1 );
				add_action( 'admin_footer', [ __CLASS__, 'log_admin_load_time' ], 999 );
			}
		}
	}
	
	public static function start_admin_timer() {
		self::$start_time = microtime( true );
	}
	
	public static function log_admin_load_time() {
		if ( ! self::$enabled ) return;
		$load_time = microtime( true ) - self::$start_time;
		if ( $load_time > 2.0 ) { // Log if admin page takes more than 2 seconds
			Logger::warning( sprintf( 
				'Slow admin page load: %.3fs on %s',
				$load_time,
				$_SERVER['REQUEST_URI'] ?? 'unknown'
			) );
		}
	}
	
	public static function log_query( $query ) {
		if ( ! self::$enabled || empty( $query ) ) {
			return $query;
		}
		
		$start = microtime( true );
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
		$caller = self::get_caller( $backtrace );
		
		return $query;
	}
	
	public static function track_query( $query, $duration, $caller = '' ) {
		if ( ! self::$enabled ) return;
		
		self::$queries[] = [
			'query' => substr( $query, 0, 200 ), // Truncate long queries
			'duration' => $duration,
			'caller' => $caller,
			'slow' => $duration > self::SLOW_QUERY_THRESHOLD,
		];
	}
	
	private static function get_caller( $backtrace ) {
		foreach ( $backtrace as $frame ) {
			if ( isset( $frame['file'] ) && strpos( $frame['file'], 'softone-woocommerce-sync' ) !== false ) {
				return basename( $frame['file'] ) . ':' . ( $frame['line'] ?? '?' );
			}
		}
		return 'unknown';
	}
	
	public static function log_summary() {
		if ( ! self::$enabled || empty( self::$queries ) ) {
			return;
		}
		
		$total_queries = count( self::$queries );
		$total_time = array_sum( array_column( self::$queries, 'duration' ) );
		$slow_queries = array_filter( self::$queries, function( $q ) {
			return $q['slow'];
		} );
		
		if ( $total_queries > 50 || ! empty( $slow_queries ) ) {
			Logger::warning( sprintf( 
				'Query Monitor: %d queries in %.3fs. %d slow queries (>%dms)',
				$total_queries,
				$total_time,
				count( $slow_queries ),
				self::SLOW_QUERY_THRESHOLD * 1000
			) );
			
			// Log slow queries
			foreach ( $slow_queries as $query ) {
				Logger::warning( sprintf( 
					'Slow query (%.3fs): %s [%s]',
					$query['duration'],
					$query['query'],
					$query['caller']
				) );
			}
		}
	}
	
	public static function get_stats() {
		return [
			'total' => count( self::$queries ),
			'time' => array_sum( array_column( self::$queries, 'duration' ) ),
			'slow' => count( array_filter( self::$queries, function( $q ) { return $q['slow']; } ) ),
		];
	}
}

