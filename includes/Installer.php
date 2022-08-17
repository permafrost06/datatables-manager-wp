<?php

namespace Datatables\Manager;

/**
 * Installer class, initialized on plugin activation
 */
class Installer
{
  /**
   * Run the installer
   */
  public function run(): void
  {
    $this->addVersion();
    $this->createTables();
  }

  /**
   * Add the plugin version to option database
   */
  public function addVersion(): void
  {
    $installed = get_option('datatables_manager_installed');

    if (!$installed) {
      update_option('datatables_manager_installed', time());
    }

    update_option('datatables_manager_version', DATATABLES_MANAGER_VERSION);
  }

  /**
   * Create the database table(s) used by the plugin
   */
  public function createTables(): void
  {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $meta_table_name = "{$wpdb->prefix}datatables_tables";
    $data_table_name = "{$wpdb->prefix}datatables_tablerows";

    if (!function_exists('dbDelta')) {
      require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    }

    $create_table_query = "CREATE TABLE IF NOT EXISTS `{$meta_table_name}` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `table_name` varchar(32) NOT NULL,
              `columns` MEDIUMTEXT NOT NULL
            ) {$charset_collate};
    ";

    dbDelta($create_table_query);

    $create_table_query = "CREATE TABLE IF NOT EXISTS `{$data_table_name}` (
              `row_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `table_id` int NOT NULL,
              `row` MEDIUMTEXT NOT NULL,
              FOREIGN KEY (table_id) REFERENCES {$meta_table_name}(id)
            ) {$charset_collate};
    ";

    dbDelta($create_table_query);
  }
}
