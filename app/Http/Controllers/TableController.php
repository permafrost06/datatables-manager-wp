<?php

namespace DtManager\App\Http\Controllers;

use DtManager\App\Http\Requests\TableRequest;
use Exception;

class TableController extends Controller
{
  protected $custom_post_type = 'DtManager_table';

  protected $columns_meta_key = '_DtManager_table_columns';

  public function getAllTables()
  {
    $post_params = [
      'post_type' => $this->custom_post_type,
      'post_status' => 'publish'
    ];

    $table_posts = get_posts($post_params);

    $tables = [];

    foreach ($table_posts as $table_post) {
      $columns = $this->getTableColumns($table_post->ID);

      // if ($columns == false) throw new Exception("Columns not set for table $table_post->ID", 500);
      // if ($columns == "") throw new Exception("Columns not set for table $table_post->ID", 500);

      $tables[] = [
        'id' => $table_post->ID,
        'table_name' => $table_post->post_title,
        'table_desc' => $table_post->post_content,
        'columns' => $columns
      ];
    }

    return $tables;
  }

  public function getTable($table_id)
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

  public function addTable(TableRequest $request)
  {
    $table_name = $request->get('table_name');
    $description = $request->get('description');
    $columns = $request->get('columns');

    $table_attrs = [
      'post_title' => $table_name,
      'post_content' => $description,
      'post_type' => $this->custom_post_type,
      'post_status' => 'publish'
    ];

    $table_id = wp_insert_post($table_attrs);

    $response = add_post_meta($table_id, $this->columns_meta_key, wp_slash($columns));

    if ($response == false) {
      throw new Exception("Could not insert table", 500);
    }

    return [
      'message' => 'Table added successfully'
    ];
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

    return [
      'message' => "Table $table_id updated successfully"
    ];
  }

  public function deleteTable($table_id)
  {
    $this->getTable($table_id);

    // $num_rows = $this->getTableRowsCount($table_id);

    // if ($num_rows) {
    //   $response = $this->db->delete($this->table_name, array('table_id' => $table_id));

    //   if ($response == false) {
    //     throw new Exception("Could not delete table rows", 500);
    //   }
    // }

    delete_post_meta($table_id, $this->columns_meta_key);

    $response = wp_delete_post($table_id, true);

    if (is_null($response) || !$response) {
      throw new Exception("Could not delete post", 500);
    }

    return [
      'message' => "Table $table_id deleted successfully"
    ];
  }

  public function getTableColumns($table_id)
  {
    $columns_json = get_post_meta($table_id, $this->columns_meta_key, true);

    return json_decode(stripslashes($columns_json));
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

  public function addRow($table_id, $row)
  {
    $response = $this->db->insert(
      $this->table_name,
      array('table_id' => $table_id, 'row' => $row)
    );

    if (!$response) {
      throw new Exception("Could not insert row", 500);
    }
  }

  // public function getDataTableRows($table_id, $start, $length, $columns, $order, $search)
  // {
  //   $results = $this->db->get_results(
  //     "SELECT `row_id`, `row` FROM {$this->table_name} WHERE `table_id` = '$table_id' LIMIT {$start}, {$length}",
  //     ARRAY_A
  //   );

  //   if (is_null($results)) {
  //     throw new Exception("Could not get results", 500);
  //   }

  //   foreach ($results as &$result) {
  //     $row_id = $result['row_id'];
  //     $result = json_decode(stripslashes($result['row']), true);
  //     $result['row_id'] = $row_id;
  //   }

  //   DTProcessing::filter($results, $search);

  //   DTProcessing::order($results, $columns, $order);

  //   return $results;
  // }

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
}