<?php

namespace DtManager\Database\Migrations;

class RowsTableMigrator
{
  static $tableName = 'dtmanager_rows';

  public static function migrate()
  {
    global $wpdb;

    $charsetCollate = $wpdb->get_charset_collate();

    $table = $wpdb->prefix . static::$tableName;

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
      $sql = "CREATE TABLE $table (
              `row_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `table_id` int NOT NULL,
              `row` MEDIUMTEXT NOT NULL
            ) $charsetCollate;";
      dbDelta($sql);
    }
  }
}
