<?php
/**
 * @file
 */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Adverifier {
  private static $initiated = FALSE;

  public static function plugin_activation() {
    self::create_db_tables();
    self::create_user_post();
  }

  public static function plugin_deactivation() {
    self::delete_db_tables();
    self::delete_user_post();
  }

  public static function init() {
    if ( ! self::$initiated ) {
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

  private static function create_user_post() {
    $post = get_posts( array( 'name' => 'adverify' ) );
    if (empty($post)) {
      $new_post = array(
        'post_title' => 'Verify ad for disriminatory words',
        'post_name' => 'adverify',
        'post_content' => 'Some custom content',
        'post_status' => 'publish',
        'post_author' => 1
      );
      $post_id = wp_insert_post($new_post);
      if ($post_id) {
        update_post_meta($post_id, '_wp_page_template', 'template-ad.php');
      }
    }
  }

  private static function create_db_table_criteria( $wpdb ) {
    $sql = "CREATE TABLE `{$wpdb->base_prefix}criteria` (
        cid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nume_RO VARCHAR(128) DEFAULT '' NOT NULL,
        nume_RU VARCHAR (128) DEFAULT '' NOT NULL,
        nume_EN VARCHAR (128) DEFAULT '' NOT NULL
      ) CHARACTER SET = utf8;";

    dbDelta( $sql );

    $success = empty( $wpdb->last_error );

    return $success;
  }

  private static function create_db_table_criteria_terms( $wpdb ) {
    $sql = "CREATE TABLE `{$wpdb->base_prefix}criteria_terms` (
        tid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cid INT(9) NOT NULL,
        nume_RO VARCHAR(128) DEFAULT '' NOT NULL,
        nume_RU VARCHAR (128) DEFAULT '' NOT NULL,
        nume_EN VARCHAR (128) DEFAULT '' NOT NULL
      ) CHARACTER SET = utf8;";

    dbDelta( $sql );

    $success = empty( $wpdb->last_error );

    return $success;
  }

  private static function create_db_table_ads( $wpdb ) {
    $sql = "CREATE TABLE `{$wpdb->base_prefix}ads` (
        aid INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        language VARCHAR (8) DEFAULT 'ro' NOT NULL,
        content LONGTEXT NOT NULL
      )CHARACTER SET = utf8;";

    dbDelta( $sql );

    $success = empty( $wpdb->last_error );

    return $success;
  }

  private static function create_db_table_criteria_ads( $wpdb ) {
    $sql = "CREATE TABLE `{$wpdb->base_prefix}criteria_ads` (
        aid INT(9) UNSIGNED,
        tid INT(9) UNSIGNED,
        UNIQUE KEY (aid, tid)
      )CHARACTER SET = utf8;";

    dbDelta( $sql );

    $success = empty( $wpdb->last_error );

    return $success;
  }

  private static function delete_db_tables() {
    global $wpdb;
    $tables = [ 'criteria', 'criteria_terms', 'ads', 'criteria_ads' ];
    foreach ( $tables as $table ) {
      $table_name = $wpdb->prefix . $table;
      $sql = "DROP TABLE IF EXISTS $table_name;";
      $wpdb->query( $sql );
    }
  }

  private static function delete_user_post() {
    $posts = get_posts( array( 'post_name' => 'adverify' ) );
    foreach ($posts as $post) {
      if (!empty($post)) {
        wp_delete_post($post->ID);
      }
    }
  }
}
