<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class SoftOne_API {

	private $endpoint;
	private $username;
	private $password;
	private $appid;
	private $company;
	private $branch;
	private $refid;
	private $userid;

	const KEY_TOKEN = 's1wc_softone_token';
	const KEY_TOKEN_TS = 's1wc_softone_token_time';
	const DEBUG_OPTION = 's1wc_temp_debug';
	const KEY_TOKEN_ORDERS = 's1wc_softone_token_orders';
	const KEY_TOKEN_ORDERS_TS = 's1wc_softone_token_orders_time';
	const KEY_CIRCUIT_BREAKER = 's1wc_circuit_breaker';
	const CIRCUIT_BREAKER_THRESHOLD = 3; // Number of failures before circuit opens
	const CIRCUIT_BREAKER_TIMEOUT = 300; // 5 minutes before retry

	public function __construct() {
		$this->endpoint = rtrim( Settings::get('endpoint', ''), '/' );
		$this->username = Settings::get('username', '');
		$this->password = Settings::get('password', '');
		$this->appid    = Settings::get('appid', '1001');
		$this->company  = Settings::get('company', '1');
		$this->branch   = Settings::get('branch', '1');
		$this->refid    = Settings::get('refid', '900');
		$this->userid   = Settings::get('userid', '900');
	}

	public static function instance() {
		static $inst = null;
		if ( $inst === null ) {
			$inst = new self();
		}
		return $inst;
	}

	private function http( $body ) {
		// Check circuit breaker
		$circuit_breaker = get_option( self::KEY_CIRCUIT_BREAKER, [] );
		if ( ! empty( $circuit_breaker['open'] ) && ( time() - $circuit_breaker['opened_at'] ) < self::CIRCUIT_BREAKER_TIMEOUT ) {
			$remaining = self::CIRCUIT_BREAKER_TIMEOUT - ( time() - $circuit_breaker['opened_at'] );
			Logger::warning( sprintf( 'Circuit breaker is OPEN. API calls disabled for %d more seconds.', $remaining ) );
			return new \WP_Error( 'circuit_breaker_open', 'API circuit breaker is open due to repeated failures' );
		}
		
		// If debug enabled, log the outgoing payload
		if ( $this->is_debug_enabled() ) {
			// Log a compact JSON payload entry
			$this->debug_log( 'HTTP Request', ['payload' => wp_json_encode( $body )] );
			// For Browser info/data calls we want the full request for debugging (clientID/reqID)
			$svc = strtoupper( $body['service'] ?? '' );
			if ( in_array( $svc, [ 'GETBROWSERINFO', 'GETBROWSERDATA' ], true ) ) {
				// limit large payloads but include full request where reasonable
				$full_payload = wp_json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
				$this->debug_log( "HTTP Request FULL ({$svc})", [ 'full_payload' => $full_payload ] );
			}
		}
		
		// Check remaining execution time - don't start if less than 30 seconds left
		$max_execution_time = ini_get( 'max_execution_time' );
		if ( $max_execution_time > 0 ) {
			$elapsed = function_exists( 'timer_stop' ) ? timer_stop( false, 3 ) : 0;
			$remaining = $max_execution_time - $elapsed;
			if ( $remaining < 30 ) {
				Logger::error( sprintf( 'Not enough execution time remaining (%d seconds). Aborting API call.', $remaining ) );
				$this->record_circuit_breaker_failure();
				return new \WP_Error( 'timeout_risk', 'Not enough execution time remaining for API call' );
			}
		}
		
		// Reduce timeout - use shorter timeouts to prevent hanging
		$timeout = 30; // Reduced from 60 to 30 seconds
		if ( is_admin() && ( $GLOBALS['pagenow'] ?? '' ) === 'wp-login.php' ) {
			$timeout = 5; // Very short timeout on login page
		}
		
		$args = [
			'headers' => [
				'Content-Type' => 'application/json; charset=utf-8',
			],
			'timeout' => $timeout,
			'connect_timeout' => 10, // Connection timeout
			'body'    => wp_json_encode( $body ),
			'sslverify' => true,
		];

		$start_time = microtime( true );
		$response = wp_remote_post( $this->endpoint, $args );
		$request_duration = microtime( true ) - $start_time;
		
		if ( $request_duration > 25 ) {
			Logger::warning( sprintf( 'Slow API request: took %.2f seconds', $request_duration ) );
		}
		
		if ( is_wp_error( $response ) ) {
			$error_code = $response->get_error_code();
			$error_message = $response->get_error_message();
			Logger::error( sprintf( 'HTTP error [%s]: %s (took %.2fs)', $error_code, $error_message, $request_duration ) );
			
			// If timeout error, provide more context and record failure
			if ( strpos( $error_code, 'timeout' ) !== false || strpos( $error_message, 'timeout' ) !== false ) {
				Logger::error( 'API request timed out. The SoftOne endpoint may be slow or unreachable.' );
				$this->record_circuit_breaker_failure();
			}
			
			return $response;
		}
		
		// Reset circuit breaker on success
		$this->reset_circuit_breaker();

		$raw = wp_remote_retrieve_body( $response );
		if ( $this->is_debug_enabled() ) {
			$this->debug_log( 'Raw response (prefix)', [ 'raw_prefix' => mb_substr( $raw, 0, 2000 ) ] );
			// If this was a browser info/data call, persist the full raw response as well
			$svc = strtoupper( $body['service'] ?? '' );
			if ( in_array( $svc, [ 'GETBROWSERINFO', 'GETBROWSERDATA' ], true ) ) {
				// store up to 64KB to avoid infinite logs while keeping enough context
				$raw_snippet = mb_substr( $raw, 0, 65536 );
				$this->debug_log( "Raw response FULL ({$svc})", [ 'raw' => $raw_snippet ] );
			}
		}
		if ( ! $raw ) {
			return new \WP_Error( 'empty_body', 'Empty response from SoftOne' );
		}

		// Remove BOM (Byte Order Mark) & force UTF-8
		// Strips UTF-8 BOM (\xEF\xBB\xBF), UTF-16 BE (\xFE\xFF), UTF-16 LE (\xFF\xFE)
		$raw = preg_replace( '/^\xEF\xBB\xBF|\xFE\xFF|\xFF\xFE/', '', $raw );

		// Convert from UTF-16, ISO-8859-7 (Greek), CP1253 (Windows Greek) to UTF-8
		if ( function_exists( 'mb_check_encoding' ) && ! mb_check_encoding( $raw, 'UTF-8' ) ) {
			// Try several known Greek / SoftOne encodings safely
			$possible_encodings = [ 'UTF-16BE', 'UTF-16LE', 'ISO-8859-7', 'CP1253' ];
			$converted = false;
			foreach ( $possible_encodings as $enc ) {
				try {
					// Check if encoding is available on this system
					if ( ! in_array( $enc, mb_list_encodings(), true ) ) {
						continue;
					}
					$test = mb_convert_encoding( $raw, 'UTF-8', $enc );
					if ( $test && json_decode( $test ) ) {
						$raw = $test;
						$converted = true;
						Logger::log( "SoftOne response re-encoded from {$enc}" );
						break;
					}
				} catch ( \Throwable $e ) {
					Logger::log( "Encoding attempt {$enc} failed: {$e->getMessage()}" );
					continue;
				}
			}
			if ( ! $converted ) {
				// utf8_encode() is deprecated in PHP 8.2+ and removed in PHP 8.3+
				// Use mb_convert_encoding as fallback
				if ( function_exists( 'mb_convert_encoding' ) ) {
					$raw = mb_convert_encoding( $raw, 'UTF-8', 'ISO-8859-1' );
					Logger::log( 'SoftOne response mb_convert_encoding fallback used (ISO-8859-1 to UTF-8).' );
				} elseif ( function_exists( 'utf8_encode' ) && PHP_VERSION_ID < 80300 ) {
					// Only use utf8_encode if PHP < 8.3
					$raw = utf8_encode( $raw );
					Logger::log( 'SoftOne response utf8_encode fallback used (PHP < 8.3).' );
				} else {
					Logger::warning( 'Could not convert encoding - no suitable function available' );
				}
			}
		}

		// Handle HTML fallback (likely expired token or login page)
		if ( stripos( $raw, '<html' ) !== false ) {
			if ( $this->is_debug_enabled() ) {
				$this->debug_log( 'HTML response detected; forcing re-auth', ['raw_prefix' => mb_substr($raw,0,4000)] );
			}
			delete_option( self::KEY_TOKEN );
			delete_option( self::KEY_TOKEN_TS );
			Logger::error( 'SoftOne returned HTML (probably expired token). Forcing re-auth.' );
			return new \WP_Error( 'html_response', 'HTML instead of JSON' );
		}

		$data = json_decode( $raw, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			if ( $this->is_debug_enabled() ) {
				$this->debug_log( 'Invalid JSON', ['error' => json_last_error_msg(), 'raw_prefix' => mb_substr($raw,0,4000)] );
			}
			Logger::error( 'Invalid JSON: ' . json_last_error_msg(), ['raw' => mb_substr( $raw, 0, 1000 )] );
			return new \WP_Error( 'invalid_json', json_last_error_msg() );
		}

		return $data;
	}

	/**
	 * Record a circuit breaker failure
	 */
	private function record_circuit_breaker_failure() {
		$circuit_breaker = get_option( self::KEY_CIRCUIT_BREAKER, [ 'failures' => 0, 'open' => false, 'opened_at' => 0 ] );
		$circuit_breaker['failures'] = ( $circuit_breaker['failures'] ?? 0 ) + 1;
		
		if ( $circuit_breaker['failures'] >= self::CIRCUIT_BREAKER_THRESHOLD ) {
			$circuit_breaker['open'] = true;
			$circuit_breaker['opened_at'] = time();
			Logger::error( sprintf( 'Circuit breaker OPENED after %d failures. API calls disabled for %d seconds.', 
				$circuit_breaker['failures'], 
				self::CIRCUIT_BREAKER_TIMEOUT 
			) );
		}
		
		update_option( self::KEY_CIRCUIT_BREAKER, $circuit_breaker, false );
	}

	/**
	 * Reset circuit breaker on successful request
	 */
	private function reset_circuit_breaker() {
		$circuit_breaker = get_option( self::KEY_CIRCUIT_BREAKER, [] );
		if ( ! empty( $circuit_breaker['failures'] ) ) {
			$circuit_breaker['failures'] = 0;
			$circuit_breaker['open'] = false;
			$circuit_breaker['opened_at'] = 0;
			update_option( self::KEY_CIRCUIT_BREAKER, $circuit_breaker, false );
		}
	}

	private function token_valid() {
		$ts = (int) get_option( self::KEY_TOKEN_TS, 0 );
		// Token invalid after 55 minutes or on demand.
		return ( time() - $ts ) < 55 * 60 && get_option( self::KEY_TOKEN );
	}

	private function save_token( $token ) {
		update_option( self::KEY_TOKEN, $token, false );
		update_option( self::KEY_TOKEN_TS, time(), false );
	}

	public function client_id() {
		// Don't make API calls on login page or during admin page loads - return cached token or error
		$pagenow = $GLOBALS['pagenow'] ?? '';
		if ( is_admin() && in_array( $pagenow, [ 'wp-login.php', 'admin.php', 'admin-ajax.php' ], true ) ) {
			$cached = get_option( self::KEY_TOKEN );
			if ( $cached && $this->token_valid() ) {
				return $cached; // Return cached token if available and valid
			}
			// Don't block admin pages with API calls - return error instead
			return new \WP_Error( 'admin_page_blocked', 'API calls disabled during admin page load' );
		}
		
		// cached token
		if ( $this->token_valid() ) {
			return get_option( self::KEY_TOKEN );
		}
		
		$start_time = microtime( true );
		$max_auth_time = 60; // Maximum 60 seconds for authentication
		
		// Login
		$login = $this->http([
			'service'  => 'login',
			'username' => $this->username,
			'password' => $this->password,
			'appId'    => (string) $this->appid,
		]);
		
		$elapsed = microtime( true ) - $start_time;
		if ( $elapsed > $max_auth_time ) {
			Logger::error( sprintf( 'Login call exceeded %ds timeout (took %.2fs)', $max_auth_time, $elapsed ) );
			return new \WP_Error( 'login_timeout', 'Login took too long' );
		}
		
		if ( is_wp_error( $login ) ) {
			Logger::error( 'Login failed', [ 'error' => $login->get_error_message(), 'code' => $login->get_error_code() ] );
			return $login;
		}
		
		if ( empty( $login['success'] ) ) {
			Logger::error( 'Login failed - API returned unsuccessful response', $login );
			return new \WP_Error( 'login_failed', 'SoftOne login failed' );
		}
		
		$tmp = $login['clientID'] ?? '';
		if ( empty( $tmp ) ) {
			Logger::error( 'Login succeeded but no clientID returned', $login );
			return new \WP_Error( 'no_clientid', 'No clientID from login response' );
		}
		
		// Authenticate
		$auth = $this->http([
			'service' => 'authenticate',
			'clientID'=> $tmp,
			'COMPANY' => (string) $this->company,
			'BRANCH'  => (string) $this->branch,
			'MODULE'  => '0',
			'REFID'   => (string) $this->refid,
			'USERID'  => (string) $this->userid,
		]);
		
		$total_elapsed = microtime( true ) - $start_time;
		if ( $total_elapsed > $max_auth_time ) {
			Logger::error( sprintf( 'Auth call exceeded total %ds timeout (took %.2fs)', $max_auth_time, $total_elapsed ) );
			return new \WP_Error( 'auth_timeout', 'Auth took too long' );
		}
		
		if ( is_wp_error( $auth ) ) {
			Logger::error( 'Authenticate failed', [ 'error' => $auth->get_error_message(), 'code' => $auth->get_error_code() ] );
			return $auth;
		}
		
		if ( empty( $auth['success'] ) ) {
			Logger::error( 'Authenticate failed - API returned unsuccessful response', $auth );
			return new \WP_Error( 'auth_failed', 'SoftOne authenticate failed' );
		}
		
		$final = $auth['clientID'] ?? '';
		if ( ! $final ) {
			Logger::error( 'Authenticate succeeded but no final clientID returned', $auth );
			return new \WP_Error( 'no_token', 'SoftOne clientID missing' );
		}
		
		$this->save_token( $final );
		Logger::log( sprintf( 'Authentication successful (took %.2fs)', $total_elapsed ) );
		return $final;
	}

	public function call( $payload ) {
		$value = trim( (string) $value );
		if ( $value === '' ) return $value;
		// If not numeric, assume already a name
		if ( ! is_numeric( $value ) ) return $value;

		// Simple transient cache to avoid repeated lookups across sync runs
		$cache_key = 's1wc_ref_' . md5( strtolower( $type . ':' . $value ) );
		$cached = get_transient( $cache_key );
		if ( $cached ) return $cached;

		$settings_key = 'lookup_' . $type . '_object';
		$custom_object = Settings::get( $settings_key, '' );

		// Build an expanded candidate list with sensible fallbacks
		$candidates = [];
		if ( $custom_object ) $candidates[] = $custom_object;
		// common names and plural forms
		if ( $type === 'category' ) {
			$candidates = array_merge( $candidates, [ 'MTRCATEGORIES', 'MTRCATEGORY', 'CATEGORY', 'CtiWSMtrCategory' ] );
		} elseif ( $type === 'brand' ) {
			$candidates = array_merge( $candidates, [ 'MTRMARK', 'MTRBRAND', 'BRAND', 'MTRMARKS' ] );
		} elseif ( $type === 'group' ) {
			$candidates = array_merge( $candidates, [ 'MTRGROUP', 'GROUP', 'MTRGROUPS' ] );
		}
		$candidates = array_values( array_unique( $candidates ) );

		// candidate filter fields to try (short list to avoid too many calls)
		$filter_fields = [ 'ID', 'CODE', 'CODE1', 'NAME' ];

		$max_attempts = 8;
		$attempts = 0;
		foreach ( $candidates as $obj ) {
			foreach ( $filter_fields as $ff ) {
				if ( $attempts++ >= $max_attempts ) break 2;
				$filters = [ $ff => $value ];
				$res = $this->get_browser_rows( $obj, $obj, $filters, 1, 0 );
				if ( is_wp_error( $res ) ) continue;
				$rows = $res['rows'] ?? [];
				if ( empty( $rows ) ) continue;
				$first = $rows[0];
				// If columns metadata present, inspect it to find a human-friendly column
				$columns = $res['_columns_meta'] ?? [];
				if ( ! empty( $columns ) && is_array( $columns ) ) {
					foreach ( $columns as $idx => $colmeta ) {
						$dindex = strtoupper( trim( (string) ($colmeta['dataIndex'] ?? '') ) );
						if ( in_array( $dindex, [ 'NAME', 'DESCRIPTION', 'DESC', 'TITLE', 'DESCRIPTION1' ], true ) || stripos( $dindex, 'NAME' ) !== false || stripos( $dindex, 'DESC' ) !== false ) {
							if ( isset( $first[ $idx ] ) && trim( (string) $first[ $idx ] ) !== '' ) {
								$resolved = trim( (string) $first[ $idx ] );
								set_transient( $cache_key, $resolved, DAY_IN_SECONDS );
								Logger::log( "Resolved {$type} {$value} via {$obj}.{$ff} -> {$dindex}" );
								return $resolved;
							}
						}
					}
				}
				// Fallback: look for common keys or first non-empty string in returned row
				foreach ( [ 'NAME', 'DESCRIPTION', 'DESC', 'TITLE', 'CODE', 'CODE1' ] as $k ) {
					if ( isset( $first[ $k ] ) && trim( (string) $first[ $k ] ) !== '' ) {
						$resolved = trim( (string) $first[ $k ] );
						set_transient( $cache_key, $resolved, DAY_IN_SECONDS );
						Logger::log( "Resolved {$type} {$value} via {$obj}.{$ff} -> {$k}" );
						return $resolved;
					}
				}
				foreach ( $first as $fval ) {
					if ( is_string( $fval ) && trim( $fval ) !== '' ) {
						$resolved = trim( $fval );
						set_transient( $cache_key, $resolved, DAY_IN_SECONDS );
						Logger::log( "Resolved {$type} {$value} via {$obj}.{$ff} -> first_nonempty" );
						return $resolved;
					}
				}
			}
		}
		Logger::warning( "Could not resolve {$type} id {$value} via ERP (attempted objects: " . implode( ',', $candidates ) . ")" );
		return $value;
		$settings_key = 'lookup_' . $type . '_object';
		$custom_object = Settings::get( $settings_key, '' );
		$candidates = [];
		if ( $custom_object ) {
			$candidates[] = $custom_object;
		}
		// Common candidate object names used in some SoftOne setups
		if ( $type === 'category' ) {
			$candidates = array_merge( $candidates, [ 'MTRCATEGORY', 'CATEGORY', 'CtiWSMtrCategory', 'MTRCATEGORIES' ] );
		} elseif ( $type === 'brand' ) {
			$candidates = array_merge( $candidates, [ 'MTRMARK', 'MARK', 'MTRBRAND', 'BRAND' ] );
		} elseif ( $type === 'group' ) {
			$candidates = array_merge( $candidates, [ 'MTRGROUP', 'GROUP' ] );
		}
		$candidates = array_unique( $candidates );
		foreach ( $candidates as $obj ) {
			// Try several possible filter field names
			$filter_fields = [ 'ID', strtoupper($obj) . '.ID', 'CODE', strtoupper($obj) . '.CODE' ];
			foreach ( $filter_fields as $ff ) {
				$res = $this->get_browser_rows( $obj, $obj, [ $ff => $value ], 1, 0 );
				if ( is_wp_error( $res ) ) continue;
				$rows = $res['rows'] ?? [];
				if ( empty( $rows ) ) continue;
				$first = $rows[0];
				// Look for common display fields
				foreach ( [ 'NAME', 'DESCRIPTION', 'DESC', 'TITLE' ] as $d ) {
					if ( isset( $first[ $d ] ) && trim( (string) $first[ $d ] ) !== '' ) {
						Logger::log( "Resolved {$type} {$value} via {$obj}.{$ff} -> {$d}" );
						return trim( (string) $first[ $d ] );
					}
				}
				// otherwise return first non-empty field
				foreach ( $first as $fval ) {
					if ( is_string( $fval ) && trim( $fval ) !== '' ) {
						return trim( $fval );
					}
				}
			}
		}
		Logger::warning( "Could not resolve {$type} id {$value} via ERP" );
		return $value;
	}

	/**
	 * Force re-authentication by clearing cached token.
	 * Use before a sync run if you want a fresh clientID.
	 */
	public function force_reauth() {
		delete_option( self::KEY_TOKEN );
		delete_option( self::KEY_TOKEN_TS );
	}

	public function get_browser_rows( $object, $list, $filters = '', $limit = 1000, $start = 0 ) {
		// Get authenticated clientID
		$cid = $this->client_id();
		if ( is_wp_error( $cid ) ) return $cid;
		// Normalize filters - handle empty string, empty array, or null
		if ( $filters === '' || $filters === null || ( is_array( $filters ) && empty( $filters ) ) ) {
			// Only apply default date filter for ITEM objects (products), not for CUSTOMER or other objects
			if ( strtoupper( $object ) === 'ITEM' ) {
				// If no filters provided, default to items updated in the last day (if SoftOne supports this filter)
				$filters = [
					'ITEM.UPDDATE>' => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
				];
			} else {
				// For other objects (like CUSTOMER), use empty filters
				$filters = [];
			}
		}
		
		// Step 3: Call getBrowserInfo to get reqID and column metadata
		$info = $this->http([
			'service' => 'getBrowserInfo',
			'appId'   => (string) $this->appid,
			'clientID' => $cid,
			'object'  => $object,
			'list'    => $list,
			'filters' => $filters,
		]);
		if ( is_wp_error( $info ) || empty( $info['success'] ) ) {
			Logger::error( 'getBrowserInfo failed', is_wp_error($info) ? $info->get_error_messages() : $info );
			return $info;
		}
		$req = $info['reqID'] ?? '';
		if ( ! $req ) {
			Logger::error( 'Missing reqID in getBrowserInfo response', $info );
			return new \WP_Error( 'no_reqid', 'Missing reqID' );
		}
		
		// Step 4: Call getBrowserData with reqID
		$data = $this->http([
			'service' => 'getBrowserData',
			'appId'   => (string) $this->appid,
			'clientID' => $cid,
			'reqID'   => $req,
			'start'   => (int) $start,
			'limit'   => (int) $limit,
			'filters' => $filters,
		]);
		
		// If getBrowserData succeeded, attach column metadata from getBrowserInfo for client-side mapping
		if ( ! is_wp_error( $data ) && is_array( $data ) ) {
			$data['_columns_meta'] = $info['columns'] ?? [];
		}
		
		return $data;
	}

	public function set_data( $object, array $data, $key = '', $use_daily_token = false ) {
		$payload = [
			'service' => 'setData',
			'object'  => $object,
			'key'     => (string) $key,
			'DATA'    => $data,
			'APPID'   => (int) $this->appid,
			'SERVICE' => 'SetData',
			'OBJECT'  => $object,
			'KEY'     => (string) $key,
		];
		if ( $use_daily_token ) {
			$cid = $this->client_id_for_orders();
			if ( is_wp_error( $cid ) ) return $cid;
			$payload['clientID'] = $cid;
			if ( ! isset($payload['appId']) && ! isset($payload['APPID']) ) {
				$payload['appId'] = (string) $this->appid;
			}
			return $this->http( $payload );
		}
		return $this->call( $payload );
	}

	/**
	 * Check if temporary debug mode is enabled
	 */
	private function is_debug_enabled() {
		return (bool) get_option( self::DEBUG_OPTION, false );
	}

	/**
	 * Enable temporary debug logging (writes to softone-debug.log)
	 */
	public static function enable_temp_debug() {
		update_option( self::DEBUG_OPTION, true );
		Logger::log( 'Temporary debug enabled' );
	}

	/**
	 * Disable temporary debug logging
	 */
	public static function disable_temp_debug() {
		delete_option( self::DEBUG_OPTION );
		Logger::log( 'Temporary debug disabled' );
	}

	/**
	 * Write to debug log file (wp-content/uploads/softone-debug.log)
	 */
	private function debug_log( $message, $data = [] ) {
		$upload_dir = wp_upload_dir();
		$debug_file = $upload_dir['basedir'] . '/softone-debug.log';
		
		$timestamp = date( 'Y-m-d H:i:s' );
		$log_entry = "[$timestamp] $message\n";
		
		if ( ! empty( $data ) ) {
			$log_entry .= wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
		}
		$log_entry .= "\n";
		
		// Use error_log for safety (auto-creates if needed)
		error_log( $log_entry, 3, $debug_file );
	}
}

