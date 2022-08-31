<?php

namespace Datatables\Manager;

class DTProcessing
{
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
}
