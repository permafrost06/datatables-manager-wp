<?php

namespace Datatables\Manager;

use Exception;
use Datatables\Manager\Http\Request;

/**
 * AJAX handler class for common AJAX calls
 */
class Ajax
{
  /**
   * @var string Prefix for AJAX actions for the plugin
   */
  protected $prefix = 'dtm';

  /**
   * @var TablesController
   */
  protected $tables_controller;

  /**
   * @var Request
   */
  protected $request;

  public function __construct($tables_controller)
  {
    $this->tables_controller = $tables_controller;
    $this->request = new Request();

    set_exception_handler([$this, 'exceptionHandler']);

    foreach ($this->getActions() as $action => $handler) {
      $nopriv = isset($handler['nopriv']) ? $handler['nopriv'] : false;

      if ($nopriv) {
        add_action("wp_ajax_nopriv_{$this->prefix}_{$action}", $handler['function']);
      }

      add_action("wp_ajax_{$this->prefix}_{$action}", $handler['function']);
    }
  }

  /**
   * Handles exceptions that occur during AJAX calls
   */
  public function exceptionHandler(Exception $error)
  {
    wp_send_json_error(['error' => $error->getMessage()], $error->getCode());
  }

  /**
   * Gets the actions and handlers for AJAX calls
   */
  public function getActions()
  {
    return [
      'get_all_tables' => ['function' => [$this, 'sendAllTables'], 'nopriv' => true],
      'add_table' => ['function' => [$this, 'addTable']],
      'get_table' => ['function' => [$this, 'sendTable']],
      'get_table_rows' => ['function' => [$this, 'sendTableRows']],
      'add_row' => ['function' => [$this, 'addRow']],
      'get_datatable_rows' => ['function' => [$this, 'sendDatatableRows']],
      'delete_table' => ['function' => [$this, 'deleteTable']],
      'update_table' => ['function' => [$this, 'updateTable']],
      'delete_row' => ['function' => [$this, 'deleteRow']],
      'update_row' => ['function' => [$this, 'updateRow']],
      /* debug-start */
      'delete_everything' => ['function' => [$this, 'deleteEverything']]
      /* debug-end */
    ];
  }

  /**
   * Checks the referer by validating nonce
   * 
   * @param string $referer
   */
  public function checkReferer($referer = 'datatables_admin_app')
  {
    if (!check_ajax_referer($referer, false, false)) {
      wp_send_json_error(['error' => 'Nonce check failed'], 401);
    }
  }

  /**
   * Checks multiple referers, continues if one nonce is validated
   * 
   * @param array $referers An array of referer strings
   */
  public function checkRefererMultiple($referers = ['dtm-frontend-shortcode', 'datatables_admin_app'])
  {
    $verified = false;

    foreach ($referers as $referer) {
      $verified = $verified || check_ajax_referer($referer, false, false);
    }

    if (!$verified) {
      wp_send_json_error(['error' => 'Nonce check failed'], 401);
    }
  }

  /**
   * Sends all tables with JSON response
   */
  public function sendAllTables()
  {
    $this->checkRefererMultiple();

    $tables = $this->tables_controller->getAllTables();

    wp_send_json_success($tables);
  }

  public function addTable()
  {
    $this->checkReferer();

    $table_name = $this->request->input("table_name");
    $description = $this->request->input("description", true, true);
    $columns = $this->request->input("columns");

    $this->tables_controller->addTable($table_name, $description, $columns);

    wp_send_json_success(['message' => "Successfully added table"]);
  }

  public function sendTable()
  {
    $this->checkReferer();

    $table_id = $this->request->input("table_id");

    $table = $this->tables_controller->getTable($table_id);

    wp_send_json_success($table);
  }

  public function sendTableRows()
  {
    $this->checkReferer();

    $table_id = $this->request->input("table_id");

    $rows = $this->tables_controller->getTableRows($table_id);

    wp_send_json_success($rows);
  }

  public function addRow()
  {
    $this->checkReferer();

    $table_id = $this->request->input("table_id");
    $row = $this->request->input("row");

    $this->tables_controller->addRow($table_id, $row);

    wp_send_json_success(['message' => 'Successfully added row']);
  }

  public function sendDatatableRows()
  {
    $this->checkReferer('dtm-frontend-shortcode');

    $table_id = $this->request->input('table_id');
    $draw = $this->request->tryInput('draw', 0);
    $start = $this->request->input('start');
    $length = $this->request->input('length');
    $search = $this->request->input('search', false);
    $order = $this->request->input('order', false);
    $columns = $this->request->input('columns', false);

    $rows = $this->tables_controller->getDataTableRows($table_id, $start, $length, $columns, $order, $search);

    $total_count = $this->tables_controller->getTableRowsCount($table_id);

    wp_send_json([
      'draw' => $draw,
      'recordsTotal' => $total_count,
      'recordsFiltered' => count($rows),
      'data' => $rows
    ]);
  }

  public function deleteTable()
  {
    $this->checkReferer();

    $table_id = $this->request->input('table_id');

    $this->tables_controller->deleteTable($table_id);

    wp_send_json_success(['message' => "Successfully deleted table '$table_id'"]);
  }

  public function updateTable()
  {
    $this->checkReferer();

    $table_id = $this->request->input('table_id');
    $table_name = $this->request->input('table_name');
    $table_desc = $this->request->input('table_desc', true, true);

    $this->tables_controller->updateTable($table_id, $table_name, $table_desc);

    wp_send_json_success(['mesasge' => "Successfully updated table '$table_id'"]);
  }

  public function deleteRow()
  {
    $this->checkReferer();

    $row_id = $this->request->input('row_id');

    $this->tables_controller->deleteRow($row_id);

    wp_send_json_success(['message' => "Successfully deleted row '$row_id'"]);
  }

  public function updateRow()
  {
    $this->checkReferer();

    $row_id = $this->request->input('row_id');
    $row = $this->request->input('row');

    $this->tables_controller->updateRow($row_id, $row);

    wp_send_json_success(['message' => "Successfully updated row '$row_id'"]);
  }

  /* debug-start */
  public function deleteEverything()
  {
    $this->tables_controller->deleteEverything();

    wp_send_json_success('everything deleted');
  }
  /* debug-end */
}
