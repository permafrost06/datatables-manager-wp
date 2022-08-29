<?php

namespace Datatables\Manager;

use Exception;

/**
 * Tables controller class
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
      $columns = $this->getTableColumns($table_post->ID);

      if ($columns == false) throw new Exception("Invalid table id $table_post->ID provided", 500);
      if ($columns == "") throw new Exception("Columns not set for table $table_post->ID", 500);

      $tables[] = [
        'id' => $table_post->ID,
        'table_name' => $table_post->post_title,
        'table_desc' => $table_post->post_content,
        'columns' => $columns
      ];
    }

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

    $response = add_post_meta($table_id, '_datatable_table_columns', wp_slash($columns));

    if ($response == false) {
      throw new Exception("Could not insert table", 500);
    }
  }

  public function getTableColumns($table_id): array
  {
    $columns_json = get_post_meta($table_id, '_datatable_table_columns', true);

    return json_decode(stripslashes($columns_json));
  }

  public function getTable($table_id): array
  {
    $table_post = get_post($table_id);

    if (is_null($table_post)) throw new Exception("Table with id $table_id does not exist", 404);

    return [
      'id' => $table_id,
      'table_name' => $table_post->post_title,
      'table_desc' => $table_post->post_content,
      'columns' => $this->getTableColumns($table_id)
    ];
  }

  public function getTableRows($table_id)
  {
    $this->getTable($table_id);

    $results = $this->db->get_results("SELECT `row_id`, `row` FROM {$this->table_name} WHERE `table_id` = '$table_id'", ARRAY_A);

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
      "SELECT `row_id`, `row` FROM {$this->table_name} WHERE `table_id` = '$table_id' LIMIT {$start}, {$length}",
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

  public function deleteTable($table_id)
  {
    $this->getTable($table_id);

    $num_rows = $this->getTableRowsCount($table_id);

    if ($num_rows) {
      $response = $this->db->delete($this->table_name, array('table_id' => $table_id));

      if ($response == false) {
        throw new Exception("Could not delete table rows", 500);
      }
    }

    delete_post_meta($table_id, '_database_table_columns');

    $response = wp_delete_post($table_id, true);

    if (is_null($response) || !$response) {
      throw new Exception("Could not delete post", 500);
    }
  }

  public function updateTable($table_id, $table_name, $table_desc)
  {
    $this->getTable($table_id);

    $table_attrs = [
      'ID' => $table_id,
      'post_title' => $table_name,
      'post_content' => $table_desc
    ];

    $response = wp_update_post($table_attrs);

    if ($response == 0) throw new Exception("Could not update table $table_id", 500);
  }

  public function deleteRow($row_id)
  {
    $response = $this->db->delete($this->table_name, ['row_id' => $row_id]);

    if (!$response) {
      throw new Exception("Could not delete row with id $row_id", 404);
    }
  }

  public function updateRow($row_id, $row)
  {
    $response = $this->db->update($this->table_name, ['row' => $row], ['row_id' => $row_id]);

    if (!$response) throw new Exception("Could not update row with id '$row_id'", 500);
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
