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
  public function exceptionHandler(Exception $error): void
  {
    wp_send_json_error(['error' => $error->getMessage()], $error->getCode());
  }

  /**
   * Gets the actions and handlers for AJAX calls
   */
  public function getActions(): array
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
      'delete_everything' => ['function' => [$this, 'deleteEverything']]
    ];
  }

  /**
   * Checks the referer by validating nonce
   * 
   * @param string $referer
   */
  public function checkReferer(string $referer = 'datatables_admin_app'): void
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
  public function checkRefererMultiple(array $referers = ['dtm-frontend-shortcode', 'datatables_admin_app']): void
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
  public function sendAllTables(): void
  {
    $this->checkRefererMultiple();

    $tables = $this->tables_controller->getAllTables();

    wp_send_json_success($tables);
  }

  public function addTable(): void
  {
    $this->checkReferer();

    $table_name = $this->request->input("table_name");
    $description = $this->request->input("description", true, true);
    $columns = $this->request->input("columns");

    $this->tables_controller->addTable($table_name, $description, $columns);

    wp_send_json_success();
  }

  public function sendTable(): void
  {
    $this->checkReferer();

    $table_id = $this->request->input("table_id");

    $table = $this->tables_controller->getTable($table_id);

    wp_send_json_success($table);
  }

  public function sendTableRows(): void
  {
    $this->checkReferer();

    $table_id = $this->request->input("table_id");

    $rows = $this->tables_controller->getTableRows($table_id);

    wp_send_json_success($rows);
  }

  public function addRow(): void
  {
    $this->checkReferer();

    $table_id = $this->request->input("table_id");
    $row = $this->request->input("row");

    $this->tables_controller->addRow($table_id, $row);

    wp_send_json_success();
  }

  public function sendDatatableRows(): void
  {
    $this->checkReferer('dtm-frontend-shortcode');

    $table_id = $this->request->input('table_id');
    $draw = $this->request->tryInput('draw', 0);
    $start = $this->request->input('start');
    $length = $this->request->input('length');

    $rows = $this->tables_controller->getDataTableRows($table_id, $start, $length);

    $count = $this->tables_controller->getTableRowsCount($table_id);

    wp_send_json([
      'draw' => $draw,
      'recordsTotal' => $count,
      'recordsFiltered' => $count,
      'data' => $rows
    ]);
  }

  public function deleteTable(): void
  {
    $this->checkReferer();

    $table_id = $this->request->input('table_id');

    $this->tables_controller->deleteTable($table_id);

    wp_send_json_success();
  }

  public function updateTable(): void
  {
    $this->checkReferer();

    $table_id = $this->request->input('table_id');
    $table_name = $this->request->input('table_name');
    $table_desc = $this->request->input('table_desc', true, true);

    $this->tables_controller->updateTable($table_id, $table_name, $table_desc);

    wp_send_json_success();
  }

  public function deleteRow(): void
  {
    $this->checkReferer();

    $row_id = $this->request->input('row_id');

    $this->tables_controller->deleteRow($row_id);

    wp_send_json_success();
  }

  /* debug-start */
  public function deleteEverything()
  {
    $this->tables_controller->deleteEverything();

    wp_send_json_success('everything deleted');
  }
  /* debug-end */
}
