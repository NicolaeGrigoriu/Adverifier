<?php
/**
 * @file
 */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Adverifier {
  private static $initiated = FALSE;

  public static function plugin_activation() {
    self::adverifier_create_statistics_table();
  }

  public static function plugin_deactivation() {
    self::delete_db_tables();
  }

  public static function init() {
    if ( ! self::$initiated ) {
      self::init_hooks();
    }
  }

  private static function init_hooks() {
    self::$initiated = TRUE;
  }

  private static function adverifier_create_statistics_table() {
    global $wpdb;
    $sql = "CREATE TABLE `{$wpdb->base_prefix}statistics` (
        cid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nume_RO VARCHAR(128) DEFAULT '' NOT NULL,
        nume_RU VARCHAR (128) DEFAULT '' NOT NULL,
        nume_EN VARCHAR (128) DEFAULT '' NOT NULL
      ) CHARACTER SET = utf8;";

    dbDelta( $sql );

    $success = empty( $wpdb->last_error );

    return $success;
  }

  private static function delete_db_tables() {
    global $wpdb;
    $tables = array('statistics');
    foreach ( $tables as $table ) {
      $table_name = $wpdb->prefix . $table;
      $sql = "DROP TABLE IF EXISTS $table_name;";
      $wpdb->query( $sql );
    }
  }
}
