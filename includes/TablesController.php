<?php

namespace Datatables\Manager;

use Exception;

/**
 * Contacts controller class
 */
class TablesController
{
  protected $db;

  /**
   * @var string Name of the table meta database table
   */
  protected $meta_table;

  /**
   * @var string Name of the table data database table
   */
  protected $table_name;

  public function __construct()
  {
    global $wpdb;

    $this->db = $wpdb;
    $this->table_name = $wpdb->prefix . 'custom_datatable_rows';
  }

  /**
   * Checks the variables for validity.
   * Throws exception on invalid.
   */
  public function checkValidity(string $name, string $email, string $phone, string $address): void
  {
    // if (empty($name)) {
    //   throw new Exception('Name is empty', 400);
    // }
    // if (empty($email)) {
    //   throw new Exception('Email is empty', 400);
    // }
    // if (empty($phone)) {
    //   throw new Exception('Phone is empty', 400);
    // }
    // if (empty($address)) {
    //   throw new Exception('Address is empty', 400);
    // }
    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //   throw new Exception('Email is invalid', 400);
    // }
    // if (!preg_match('/^[-+ ()\d]+$/', $phone)) {
    //   throw new Exception('Phone number is invalid', 400);
    // }
    // if (strlen($phone) < 5 || strlen($phone) > 20) {
    //   throw new Exception('Phone number length must be between 5 and 20', 400);
    // }
  }

  /**
   * Gets all tables from the database
   */
  public function getAllTables(): array
  {
    $post_params = [
      'post_type' => 'custom_datatable',
      'post_status' => 'publish'
    ];

    $table_posts = get_posts($post_params);

    $tables = [];

    foreach ($table_posts as $table_post) {
      $columns = get_post_meta($table_post->ID, '_datatable_table_columns', true);

      $tables[] = [
        'id' => $table_post->ID,
        'table_name' => $table_post->post_title,
        'table_desc' => $table_post->post_content,
        'columns' => json_decode(stripslashes($columns))
      ];
    }

    // if (is_null($all_tables)) {
    //   throw new Exception("Could not get tables", 500);
    // }

    // foreach ($all_tables as &$table) {
    //   $table['columns'] = json_decode(stripslashes($table['columns']));
    // }

    return $tables;
  }

  public function addTable($table_name, $description, $columns): void
  {
    $table_attrs = [
      'post_title' => $table_name,
      'post_content' => $description,
      'post_type' => 'custom_datatable',
      'post_status' => 'publish'
    ];

    $table_id = wp_insert_post($table_attrs);

    update_post_meta($table_id, '_datatable_table_columns', wp_slash($columns));

    // check if inserted correctly
    // throw exception if needed
  }

  public function getTableColumns($table_id): array
  {
    $columns_json = get_post_meta($table_id, '_datatable_table_columns', true);

    return json_decode(stripslashes($columns_json));
  }

  public function getTable($table_id): array
  {
    $table_post = get_post($table_id);

    return [
      'id' => $table_id,
      'table_name' => $table_post->post_title,
      'table_desc' => $table_post->post_content,
      'columns' => $this->getTableColumns($table_id)
    ];
  }

  public function getTableRows($table_id)
  {
    $results = $this->db->get_results("SELECT row_id, row FROM {$this->table_name} WHERE `table_id` = '$table_id'", ARRAY_A);

    if (is_null($results)) {
      throw new Exception("Could not get results", 500);
    }

    foreach ($results as &$result) {
      $row_id = $result['row_id'];
      $result = json_decode(stripslashes($result['row']), true);
      $result['row_id'] = $row_id;
    }

    return $results;
  }

  public function getTableRowsCount($table_id)
  {
    $count = (int) $this->db->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE `table_id` = '$table_id'");

    if (is_null($count)) {
      throw new Exception("Could not get rows count", 500);
    }

    return $count;
  }

  public function addRow($table_id, $row): void
  {
    $response = $this->db->insert(
      $this->table_name,
      array('table_id' => $table_id, 'row' => $row)
    );

    if (!$response) {
      throw new Exception("Could not insert row", 500);
    }
  }

  public function getDataTableRows($table_id, $start, $length)
  {
    $results = $this->db->get_results(
      "SELECT row_id, row FROM {$this->table_name} WHERE `table_id` = '$table_id' LIMIT {$start}, {$length}",
      ARRAY_A
    );

    if (is_null($results)) {
      throw new Exception("Could not get results", 500);
    }

    foreach ($results as &$result) {
      $row_id = $result['row_id'];
      $result = json_decode(stripslashes($result['row']), true);
      $result['row_id'] = $row_id;
    }

    return $results;
  }

  /* debug-start */
  /**
   * Drops the plugin database table - debug only
   */
  public function deleteEverything(): void
  {
    $tables = $this->getAllTables();

    foreach ($tables as $table) {
      delete_post_meta($table['id'], '_datatable_table_columns');

      $response = wp_delete_post($table['id'], true);
      if (is_null($response) || !$response) {
        throw new Exception("Could not delete post", 500);
      }
    }
  }
  /* debug-end */
}
