<?php

namespace DtManager\App\Http\Controllers;

use DtManager\App\Http\Requests\TableRequest;
use DtManager\App\Models\Row;
use Exception;

class RowsController extends Controller
{
  public function store(TableRequest $request, $table_id)
  {
    $row = new Row;
    $row->table_id = $table_id;
    $row->row = $request->get('row');
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

  public function update(TableRequest $request, $row_id)
  {
    $row = Row::find($row_id);
    $row->row = $request->get('row');
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
