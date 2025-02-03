<?php

namespace WML\Classes;

/**
 * Database Tables
 *
 * This class is to manage database table.
 *
 */

class DbTable {

	private static $wml_db_version = '1.1';
	/**
	 * This function fire on plugins_loaded action in bootstrap file.
	 *
	 * @access public
	 * @since 0.3
	 * @return void
	 */
	public static function wml_plugin_activated() {

		if ( get_option( 'wml_db_version' ) !== self::$wml_db_version ) {
			self::create_db_table();
		}
	}

	/**
	 * Create Database Table
	 *
	 * @return void
	 * @since 0.3
	 * @access public
	 */

	public static function create_db_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wml_entries';

		$wpdb_collate = $wpdb->collate;

		$sql = "CREATE TABLE {$table_name} (
            `id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
            `to_email` VARCHAR(100) NOT NULL,
            `subject` VARCHAR(250) NOT NULL,
            `message` TEXT NOT NULL,
            `headers` TEXT NOT NULL,
            `attachments` VARCHAR(50) NOT NULL,
            `sent_date` VARCHAR(50) NOT NULL,
            `captured_gmt` VARCHAR(50) NOT NULL,
			`attachments_file` TEXT
            ) collate {$wpdb_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql, true );

		$db_version_update = true;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}wml_entries'" ) === null ) {
			$db_version_update = false;
		}

		if ( $db_version_update ) {
			update_option( 'wml_db_version', self::$wml_db_version );
		} else {
			update_option( 'wml_db_version', '0' );
		}
	}
}
