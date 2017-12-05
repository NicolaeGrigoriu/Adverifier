<?php
	/**
	 * @file
	 */
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	class Adverifier {
		private static $initiated = FALSE;

		public static function plugin_activation() {
			self::create_db_tables();
		}

		public static function plugin_deactivation() {
			self::delete_db_tables();
		}

		public static function init() {
			if (!self::$initiated ) {
				self::init_hooks();
			}
		}

		private static function init_hooks() {
			self::$initiated = TRUE;
		}

		private static function create_db_tables() {
			global $wpdb;

			self::create_db_table_criteria( $wpdb );
			self::create_db_table_criteria_terms( $wpdb );
			self::create_db_table_ads( $wpdb );
			self::create_db_table_criteria_ads( $wpdb );
		}

		private static function create_db_table_criteria($wpdb) {
			$sql = "CREATE TABLE `{$wpdb->base_prefix}criteria` (
				cid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				nume_RO VARCHAR(128) DEFAULT '' NOT NULL,
				nume_RU VARCHAR (128) DEFAULT '' NOT NULL,
				nume_EN VARCHAR (128) DEFAULT '' NOT NULL
			);";

			dbDelta($sql);

			$success = empty($wpdb->last_error);

			return $success;
		}

		private static function create_db_table_criteria_terms($wpdb) {
			$sql = "CREATE TABLE `{$wpdb->base_prefix}criteria_terms` (
				tid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				cid INT(9) NOT NULL,
				nume_RO VARCHAR(128) DEFAULT '' NOT NULL,
				nume_RU VARCHAR (128) DEFAULT '' NOT NULL,
				nume_EN VARCHAR (128) DEFAULT '' NOT NULL
			);";

			dbDelta($sql);

			$success = empty($wpdb->last_error);

			return $success;
		}

		private static function create_db_table_ads($wpdb) {
			$sql = "CREATE TABLE `{$wpdb->base_prefix}ads` (
				aid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
				language VARCHAR (8) DEFAULT 'ro' NOT NULL,
				content LONGTEXT NOT NULL
			);";

			dbDelta($sql);

			$success = empty($wpdb->last_error);

			return $success;
		}

		private static function create_db_table_criteria_ads($wpdb) {
			$sql = "CREATE TABLE `{$wpdb->base_prefix}criteria_ads` (
				aid INT(9) UNSIGNED,
				tid INT(9) UNSIGNED,
				UNIQUE KEY (aid, tid)
			);";

			dbDelta($sql);

			$success = empty($wpdb->last_error);

			return $success;
		}

		private static function delete_db_tables() {
			global $wpdb;
			$tables = array('criteria', 'criteria_terms', 'ads', 'criteria_ads');
			foreach ( $tables as $table ) {
				$table_name = $wpdb->prefix . $table;
				$sql = "DROP TABLE IF EXISTS $table_name;";
				$wpdb->query($sql);
			}
		}
	}
