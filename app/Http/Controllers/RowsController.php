<?php

namespace DtManager\App\Http\Controllers;

use DtManager\Framework\Request\Request;
use DtManager\App\Models\Row;
use DtManager\App\Services\Sanitizer;
use Exception;

class RowsController extends Controller
{
  public function store(Request $request, $table_id)
  {
    $data = $request->all();
    $data = Sanitizer::sanitizeRow($data);

    $row = new Row;
    $row->table_id = $table_id;
    $row->row = $data['row'];
    $row->save();

    return $row;
  }

  public function index($table_id)
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

  public function update(Request $request, $row_id)
  {
    $data = $request->all();
    $data = Sanitizer::sanitizeRow($data);

    $row = Row::find($row_id);
    $row->row = $data['row'];
    $row->save();

    return [
      'message' => "Row with id $row_id updated successfully"
    ];
  }

  public function destroy($row_id)
  {
    Row::destroy($row_id);

    return [
      'message' => "Row with id $row_id deleted successfully"
    ];
  }
}
