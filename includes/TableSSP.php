<?php

namespace Datatables\Manager;

class TableSSP
{
  /**
   * Create the data output array for the DataTables rows
   *
   *  @param  array $columns Column information array
   *  @param  array $data    Data from the SQL get
   *  @return array          Formatted data in a row based format
   */
  static function data_output($columns, $data)
  {
    $out = array();

    for ($i = 0, $ien = count($data); $i < $ien; $i++) {
      $row = array();

      for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
        $column = $columns[$j];

        // Is there a formatter?
        if (isset($column['formatter'])) {
          if (empty($column['db'])) {
            $row[$column['dt']] = $column['formatter']($data[$i]);
          } else {
            $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
          }
        } else {
          if (!empty($column['db'])) {
            $row[$column['dt']] = $data[$i][$columns[$j]['db']];
          } else {
            $row[$column['dt']] = "";
          }
        }
      }

      $out[] = $row;
    }

    return $out;
  }

  /**
   * Paging
   *
   * Construct the LIMIT clause for server-side processing SQL query
   *
   *  @param  array $request Data sent to server by DataTables
   *  @param  array $columns Column information array
   *  @return string SQL limit clause
   */
  static function limit($request, $columns)
  {
    $limit = '';

    if (isset($request['start']) && $request['length'] != -1) {
      $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
    }

    return $limit;
  }


  /**
   * Ordering
   *
   * Construct the ORDER BY clause for server-side processing SQL query
   *
   *  @param  array $request Data sent to server by DataTables
   *  @param  array $columns Column information array
   *  @return string SQL order by clause
   */
  static function order($request, $columns)
  {
    $order = '';

    if (isset($request['order']) && count($request['order'])) {
      $orderBy = array();
      $dtColumns = self::pluck($columns, 'dt');

      for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
        // Convert the column index into the column data property
        $columnIdx = intval($request['order'][$i]['column']);
        $requestColumn = $request['columns'][$columnIdx];

        $columnIdx = array_search($requestColumn['data'], $dtColumns);
        $column = $columns[$columnIdx];

        if ($requestColumn['orderable'] == 'true') {
          $dir = $request['order'][$i]['dir'] === 'asc' ?
            'ASC' :
            'DESC';

          $orderBy[] = '`' . $column['db'] . '` ' . $dir;
        }
      }

      if (count($orderBy)) {
        $order = 'ORDER BY ' . implode(', ', $orderBy);
      }
    }

    return $order;
  }

  /**
   * Perform the SQL queries needed for an server-side processing requested,
   * utilising the helper functions of this class, limit(), order() and
   * filter() among others. The returned array is ready to be encoded as JSON
   * in response to an SSP request, or can be modified if needed before
   * sending back to the client.
   *
   *  @param  array $request Data sent to server by DataTables
   *  @param  array|PDO $conn PDO connection resource or connection parameters array
   *  @param  string $table SQL table to query
   *  @param  string $primaryKey Primary key of the table
   *  @param  array $columns Column information array
   *  @return array          Server-side processing response array
   */
  static function simple()
  {
    // Build the SQL query string from the request
    $limit = self::limit();
    $order = self::order();

    // Main query to actually get the data
    $data = "SELECT `" . implode("`, `", self::pluck($columns, 'db')) . "`
			 FROM `$table`
			 $order
			 $limit";

    // Data set length after filtering
    $resFilterLength = "SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`";

    $recordsFiltered = $resFilterLength[0][0];

    // Total data set length
    $resTotalLength = "SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`";

    $recordsTotal = $resTotalLength[0][0];

    /*
		 * Output
		 */
    return array(
      "draw"            => isset($request['draw']) ?
        intval($request['draw']) :
        0,
      "recordsTotal"    => intval($recordsTotal),
      "recordsFiltered" => intval($recordsFiltered),
      "data"            => self::data_output($columns, $data)
    );
  }

  /**
   * Create a PDO binding key which can be used for escaping variables safely
   * when executing a query with sql_exec()
   *
   * @param  array &$a    Array of bindings
   * @param  *      $val  Value to bind
   * @param  int    $type PDO field type
   * @return string       Bound key to be used in the SQL where this parameter
   *   would be used.
   */
  static function bind(&$a, $val, $type)
  {
    $key = ':binding_' . count($a);

    $a[] = array(
      'key' => $key,
      'val' => $val,
      'type' => $type
    );

    return $key;
  }


  /**
   * Pull a particular property from each assoc. array in a numeric array, 
   * returning and array of the property values from each item.
   *
   *  @param  array  $a    Array to get data from
   *  @param  string $prop Property to read
   *  @return array        Array of property values
   */
  static function pluck($a, $prop)
  {
    $out = array();

    for ($i = 0, $len = count($a); $i < $len; $i++) {
      if (empty($a[$i][$prop])) {
        continue;
      }
      //removing the $out array index confuses the filter method in doing proper binding,
      //adding it ensures that the array data are mapped correctly
      $out[$i] = $a[$i][$prop];
    }

    return $out;
  }
}
