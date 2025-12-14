<?php
namespace S1WC;

if ( ! defined( 'ABSPATH' ) ) exit;

class Logger {
	public static function log( $message, $context = [] ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			$logger = wc_get_logger();
			$logger->info( is_string($message) ? $message : wp_json_encode($message), ['source' => 'softone-sync'] );
		} else {
			error_log( '[SoftOne Sync] ' . ( is_string($message) ? $message : wp_json_encode($message) ) );
		}
	}
	public static function error( $message, $context = [] ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			$logger = wc_get_logger();
			$logger->error( is_string($message) ? $message : wp_json_encode($message), ['source' => 'softone-sync'] );
		} else {
			error_log( '[SoftOne Sync ERROR] ' . ( is_string($message) ? $message : wp_json_encode($message) ) );
		}
	}
	public static function warning( $message, $context = [] ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			$logger = wc_get_logger();
			$logger->warning( is_string($message) ? $message : wp_json_encode($message), ['source' => 'softone-sync'] );
		} else {
			error_log( '[SoftOne Sync WARNING] ' . ( is_string($message) ? $message : wp_json_encode($message) ) );
		}
	}
}
