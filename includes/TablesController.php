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
  protected $data_table;

  public function __construct()
  {
    global $wpdb;

    $this->db = $wpdb;
    $this->meta_table = $wpdb->prefix . 'datatables_tables';
    $this->data_table = $wpdb->prefix . 'datatables_tablerows';
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
    $all_tables = $this->db->get_results("SELECT * FROM {$this->meta_table}", ARRAY_A);

    if (is_null($all_tables)) {
      throw new Exception("Could not get tables", 500);
    }

    foreach ($all_tables as &$table) {
      error_log(print_r($table, 1));
      $table['columns'] = json_decode(stripslashes($table['columns']));
    }

    return $all_tables;
  }

  public function addTable($table_name, $columns): void
  {
    $response = $this->db->insert(
      $this->meta_table,
      array('table_name' => $table_name, 'columns' => $columns)
    );

    if (!$response) {
      throw new Exception("Could not insert table", 500);
    }
  }

  public function getTableName($table_id): string
  {
    $table_name = $this->db->get_var("SELECT table_name FROM {$this->meta_table} WHERE `id` = '$table_id'");

    return $table_name;
  }

  public function getTableColumns($table_id): array
  {
    $columns_json = $this->db->get_var("SELECT columns FROM {$this->meta_table} WHERE `id` = '$table_id'");

    return json_decode(stripslashes($columns_json));
  }

  public function getTable($table_id): array
  {
    return [
      'id' => $table_id,
      'table_name' => $this->getTableName($table_id),
      'columns' => $this->getTableColumns($table_id)
    ];
  }

  public function getTableRows($table_id)
  {
    $results = $this->db->get_results("SELECT row_id, row FROM {$this->data_table} WHERE `table_id` = '$table_id'", ARRAY_A);

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
    $count = (int) $this->db->get_var("SELECT COUNT(*) FROM {$this->data_table} WHERE `table_id` = '$table_id'");

    if (is_null($count)) {
      throw new Exception("Could not get rows count", 500);
    }

    return $count;
  }

  public function addRow($table_id, $row): void
  {
    $response = $this->db->insert(
      $this->data_table,
      array('table_id' => $table_id, 'row' => $row)
    );

    if (!$response) {
      throw new Exception("Could not insert row", 500);
    }
  }

  public function getDataTableRows($table_id, $start, $length)
  {
    $results = $this->db->get_results(
      "SELECT row_id, row FROM {$this->data_table} WHERE `table_id` = '$table_id' LIMIT {$start}, {$length}",
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
  public function dropTable(): void
  {
    $response =  $this->db->query("DROP TABLE IF EXISTS {$this->meta_table}");

    if (false == $response) {
      throw new Exception("Could not drop table", 500);
    }
  }
  /* debug-end */
}
