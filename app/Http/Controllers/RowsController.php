<?php

namespace DtManager\App\Http\Controllers;

use DtManager\App\Http\Requests\TableRequest;
use DtManager\App\Models\Row;
use Exception;

class RowsController extends Controller
{
  public function addRow(TableRequest $request, $table_id)
  {
    $row = new Row;
    $row->table_id = $table_id;
    $row->row = $request->get('row');
    $row->save();

    return $row;
  }

  public function getTableRows($table_id)
  {
    $results = Row::where('table_id', $table_id)->get();

    $rows = [];

    foreach ($results as $result) {
      $row = json_decode(stripslashes($result->row), true);
      $row['row_id'] = $result->row_id;
      $rows[] = $row;
    }

    return $rows;
  }

  public function getTableRowsCount($table_id)
  {
    $count = Row::where('table_id', $table_id)->count();

    if (is_null($count)) {
      throw new Exception("Could not get rows count", 500);
    }

    return $count;
  }

  public function updateRow(TableRequest $request, $row_id)
  {
    $row = Row::find($row_id);
    $row->row = $request->get('row');
    $row->save();

    return [
      'message' => "Row with id $row_id updated successfully"
    ];
  }

  public function deleteRow($row_id)
  {
    Row::destroy($row_id);

    return [
      'message' => "Row with id $row_id deleted successfully"
    ];
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
}