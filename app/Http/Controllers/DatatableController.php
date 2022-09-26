<?php

namespace DtManager\App\Http\Controllers;

use DtManager\App\Http\Requests\TableRequest;
use DtManager\App\Models\Row;

class DatatableController extends Controller
{
  public function index(TableRequest $request)
  {
    $table_id = $request->get('table_id');

    $draw = $request->get('draw');

    $start = $request->get('start');
    $length = $request->get('length');

    $search = $request->get('search');
    $order = $request->get('order');
    $columns = $request->get('columns');

    $rows = $this->getDatatableRows($table_id);

    static::filter($rows, $search);

    $filteredLength = count($rows);

    static::order($rows, $columns, $order);

    static::limit($rows, $start, $length);

    $total_count = Row::where('table_id', $table_id)->count();

    return [
      'draw' => $draw,
      'recordsTotal' => $total_count,
      'recordsFiltered' => $filteredLength,
      'data' => $rows
    ];
  }

  public function getDatatableRows($table_id)
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

  public static function filter(&$results, $search)
  {
    if (isset($search['value']) && $search['value'] != '') {
      $results = array_values(array_filter($results, function ($result) use ($search) {
        foreach ($result as $key => $value) {
          if ($key != 'row_id') {
            if (strpos($value, $search['value']) !== false) {
              return true;
            }
          }
        }

        return false;
      }));
    }
  }

  public static function order(&$results, $columns, $order)
  {
    $columns_data = array_map(function ($column) {
      return $column['data'];
    }, $columns);

    $order_by = $columns_data[$order[0]['column']];
    $order_dir = $order[0]['dir'];

    usort($results, function ($a, $b) use ($order_by, $order_dir) {
      $value_a = $a[$order_by];
      $value_b = $b[$order_by];

      if ($order_dir == 'asc') {
        return strcmp($value_a, $value_b);
      } else {
        return strcmp($value_b, $value_a);
      }
    });
  }

  public static function limit(&$results, $offset, $limit)
  {
    $results = array_slice($results, $offset, $limit);
  }
}
