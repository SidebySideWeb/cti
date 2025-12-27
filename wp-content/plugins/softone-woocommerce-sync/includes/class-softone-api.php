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
		if ( $this->is_debug_enabled() ) {
			$this->debug_log( 'HTTP Request', ['payload' => wp_json_encode($body)] );
		}
		$args = [
			'headers' => [
				'Content-Type' => 'application/json; charset=utf-8',
			],
			'timeout' => 60,
			'body'    => wp_json_encode( $body ),
		];

		$response = wp_remote_post( $this->endpoint, $args );
		if ( is_wp_error( $response ) ) {
			Logger::error( 'HTTP error: ' . $response->get_error_message() );
			return $response;
		}

		$raw = wp_remote_retrieve_body( $response );
		if ( $this->is_debug_enabled() ) {
			$this->debug_log( 'Raw response (prefix)', ['raw_prefix' => mb_substr( $raw, 0, 2000 )] );
		}
		if ( ! $raw ) {
			return new \WP_Error( 'empty_body', 'Empty response from SoftOne' );
		}

		$raw = preg_replace( '/^\xEF\xBB\xBF|\xFE\xFF|\xFF\xFE/', '', $raw );

		if ( function_exists( 'mb_check_encoding' ) && ! mb_check_encoding( $raw, 'UTF-8' ) ) {
			$possible_encodings = [ 'UTF-16BE', 'UTF-16LE', 'ISO-8859-7', 'CP1253' ];
			$converted = false;
			foreach ( $possible_encodings as $enc ) {
				try {
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
				if ( function_exists( 'utf8_encode' ) ) {
					$raw = utf8_encode( $raw );
				}
				Logger::log( 'SoftOne response utf8_encode fallback used.' );
			}
		}

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
		if ( $this->token_valid() ) {
			return get_option( self::KEY_TOKEN );
		}
		$start_time = time();
		$login = $this->http([
			'service'  => 'login',
			'username' => $this->username,
			'password' => $this->password,
			'appId'    => (string) $this->appid,
		]);
		if ( time() - $start_time > 120 ) {
			Logger::error( 'Login call exceeded 120s timeout' );
			return new \WP_Error( 'login_timeout', 'Login took too long' );
		}
		if ( is_wp_error( $login ) || empty( $login['success'] ) ) {
			return new \WP_Error( 'login_failed', 'SoftOne login failed' );
		}
		$tmp = $login['clientID'] ?? '';
		$auth = $this->http([
			'service' => 'authenticate',
			'clientID'=> $tmp,
			'COMPANY' => (string) $this->company,
			'BRANCH'  => (string) $this->branch,
			'MODULE'  => '0',
			'REFID'   => (string) $this->refid,
			'USERID'  => (string) $this->userid,
		]);
		if ( time() - $start_time > 240 ) {
			Logger::error( 'Auth call exceeded total 240s timeout' );
			return new \WP_Error( 'auth_timeout', 'Auth took too long' );
		}
		if ( is_wp_error( $auth ) || empty( $auth['success'] ) ) {
			return new \WP_Error( 'auth_failed', 'SoftOne authenticate failed' );
		}
		$final = $auth['clientID'] ?? '';
		if ( ! $final ) {
			return new \WP_Error( 'no_token', 'SoftOne clientID missing' );
		}
		$this->save_token( $final );
		return $final;
	}

	public function call( $payload ) {
		$cid = $this->client_id();
		if ( is_wp_error( $cid ) ) return $cid;
		$payload['clientID'] = $cid;
		if ( ! isset($payload['appId']) && ! isset($payload['APPID']) ) {
			$payload['appId'] = (string) $this->appid;
		}
		return $this->http( $payload );
	}

	public function client_id_for_orders() {
		$ts = (int) get_option( self::KEY_TOKEN_ORDERS_TS, 0 );
		if ( ( time() - $ts ) < 24 * 60 * 60 && get_option( self::KEY_TOKEN_ORDERS ) ) {
			return get_option( self::KEY_TOKEN_ORDERS );
		}
		$cid = $this->client_id();
		if ( is_wp_error( $cid ) ) return $cid;
		update_option( self::KEY_TOKEN_ORDERS, $cid, false );
		update_option( self::KEY_TOKEN_ORDERS_TS, time(), false );
		return $cid;
	}

	public function enable_temp_debug( $seconds = 3600 ) {
		update_option( self::DEBUG_OPTION, time() + intval( $seconds ), false );
		return true;
	}

	public function disable_temp_debug() {
		delete_option( self::DEBUG_OPTION );
		return true;
	}

	private function is_debug_enabled() {
		$ts = (int) get_option( self::DEBUG_OPTION, 0 );
		return ( $ts && $ts > time() );
	}

	private function debug_log( $message, $context = [] ) {
		if ( ! $this->is_debug_enabled() ) return;
		$up = wp_upload_dir();
		$file = rtrim( $up['basedir'], "\/." ) . DIRECTORY_SEPARATOR . 'softone-debug.log';
		$entry = sprintf( "%s %s", date('c'), is_string($message) ? $message : wp_json_encode($message) );
		if ( ! empty( $context ) ) {
			$entry .= ' ' . wp_json_encode( $context );
		}
		$entry .= PHP_EOL;
		@file_put_contents( $file, $entry, FILE_APPEND | LOCK_EX );
	}

	public function resolve_reference( $type, $value ) {
		$value = trim( (string) $value );
		if ( $value === '' ) return $value;
		if ( ! is_numeric( $value ) ) return $value;
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
			$filter_fields = [ 'ID', strtoupper($obj) . '.ID', 'CODE', strtoupper($obj) . '.CODE' ];
			foreach ( $filter_fields as $ff ) {
				$res = $this->get_browser_rows( $obj, $obj, [ $ff => $value ], 1, 0 );
				if ( is_wp_error( $res ) ) continue;
				$rows = $res['rows'] ?? [];
				if ( empty( $rows ) ) continue;
				$first = $rows[0];
				foreach ( [ 'NAME', 'DESCRIPTION', 'DESC', 'TITLE' ] as $d ) {
					if ( isset( $first[ $d ] ) && trim( (string) $first[ $d ] ) !== '' ) {
						Logger::log( "Resolved {$type} {$value} via {$obj}.{$ff} -> {$d}" );
						return trim( (string) $first[ $d ] );
					}
				}
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

	public function force_reauth() {
		delete_option( self::KEY_TOKEN );
		delete_option( self::KEY_TOKEN_TS );
	}

	public function get_browser_rows( $object, $list, $filters = '', $limit = 1000, $start = 0 ) {
		$cid = $this->client_id();
		if ( is_wp_error( $cid ) ) return $cid;
		if ( $filters === '' || $filters === null ) {
			$filters = [
				'ITEM.UPDDATE>' => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			];
		}
		
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
		
		$data = $this->http([
			'service' => 'getBrowserData',
			'appId'   => (string) $this->appid,
			'clientID' => $cid,
			'reqID'   => $req,
			'start'   => (int) $start,
			'limit'   => (int) $limit,
			'filters' => $filters,
		]);
		
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

	public function get_sales_status( $findoc ) {
		$base_endpoint = rtrim( $this->endpoint, '/' );
		$status_endpoint = $base_endpoint . '/JS/b2bwebservices/getsalesstatus';
		
		$args = [
			'headers' => [
				'Content-Type' => 'application/json; charset=utf-8',
			],
			'timeout' => 30,
			'body'    => wp_json_encode( [ 'findoc' => (string) $findoc ] ),
		];

		if ( $this->is_debug_enabled() ) {
			$this->debug_log( 'Sales Status Request', ['endpoint' => $status_endpoint, 'findoc' => $findoc] );
		}

		$response = wp_remote_post( $status_endpoint, $args );
		if ( is_wp_error( $response ) ) {
			Logger::error( 'HTTP error getting sales status: ' . $response->get_error_message() );
			return $response;
		}

		$raw = wp_remote_retrieve_body( $response );
		if ( ! $raw ) {
			return new \WP_Error( 'empty_body', 'Empty response from SoftOne status API' );
		}

		if ( $this->is_debug_enabled() ) {
			$this->debug_log( 'Sales Status Response', ['raw_prefix' => mb_substr( $raw, 0, 500 )] );
		}

		$raw = preg_replace( '/^\xEF\xBB\xBF|\xFE\xFF|\xFF\xFE/', '', $raw );

		if ( function_exists( 'mb_check_encoding' ) && ! mb_check_encoding( $raw, 'UTF-8' ) ) {
			$possible_encodings = [ 'UTF-16BE', 'UTF-16LE', 'ISO-8859-7', 'CP1253' ];
			$converted = false;
			foreach ( $possible_encodings as $enc ) {
				try {
					if ( ! in_array( $enc, mb_list_encodings(), true ) ) {
						continue;
					}
					$test = mb_convert_encoding( $raw, 'UTF-8', $enc );
					if ( $test && json_decode( $test ) ) {
						$raw = $test;
						$converted = true;
						break;
					}
				} catch ( \Throwable $e ) {
					continue;
				}
			}
			if ( ! $converted && function_exists( 'utf8_encode' ) ) {
				$raw = utf8_encode( $raw );
			}
		}

		$data = json_decode( $raw, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			Logger::error( 'Invalid JSON from status API: ' . json_last_error_msg(), ['raw' => mb_substr( $raw, 0, 1000 )] );
			return new \WP_Error( 'invalid_json', json_last_error_msg() );
		}

		return $data;
	}
}
