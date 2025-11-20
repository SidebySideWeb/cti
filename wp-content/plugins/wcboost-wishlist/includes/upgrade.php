<?php
/**
 * Upgrade handler
 *
 * @version 1.1.6
 *
 * @package WCBoost\Wishlist
 */

namespace WCBoost\Wishlist;

defined( 'ABSPATH' ) || exit;

/**
 * Class \WCBoost\Wishlist\Upgrade
 */
class Upgrade {

	/**
	 * Update database schema to version 1.1.6
	 *
	 * Update the table wishlists to add the date_modified column,
	 * and add the session_id index
	 *
	 * @since 1.1.6
	 */
	public static function upgrade_116() {
		global $wpdb;

		// Add date_modified column.
		$column_name = 'date_modified';
		$table_name = $wpdb->wishlists;

		// phpcs:disable
		$row = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM `%s` LIKE %s",
				$table_name,
				$column_name
			)
		);

		if ( empty( $row ) ) {
			$result = $wpdb->query(
				$wpdb->prepare(
					"ALTER TABLE `%s` ADD COLUMN `%s` datetime NULL DEFAULT NULL AFTER `date_created`",
					$table_name,
					$column_name
				)
			);

			if ( false === $result && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf( 'WCBoost Wishlist: Failed to add column %s to table %s', $column_name, $table_name ) );
			}
		}

		// Add session_id index.
		$key_name = 'session_id';
		$row = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW INDEX FROM `%s` WHERE Key_name = %s",
				$table_name,
				$key_name
			)
		);

		if ( empty( $row ) ) {
			$result = $wpdb->query(
				$wpdb->prepare(
					"ALTER TABLE `%s` ADD INDEX `%s` (`%s`)",
					$table_name,
					$key_name,
					$key_name
				)
			);

			if ( false === $result && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf( 'WCBoost Wishlist: Failed to add index %s to table %s', $key_name, $table_name ) );
			}
		}
		// phpcs:enable
	}
}
