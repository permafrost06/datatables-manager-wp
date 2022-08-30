<?php

namespace Datatables\Manager\Frontend;

use Datatables\Manager\TablesController;

/**
 * Shortcode handler class
 */
class Shortcode
{
  protected $tables_controller;

  public function __construct(TablesController $tables_controller)
  {
    $this->tables_controller = $tables_controller;

    add_shortcode('custom-datatable', [$this, 'renderCustomDatatable']);
  }

  /**
   * Load the content of a file to a string and return it
   * 
   * @param string  $filename Name of the file in the "views" folder
   * @param mixed   $data     Any data the file may need
   */
  public function loadFile($filename, $data = "")
  {
    ob_start();
    include __DIR__ . '/views/' . $filename;
    return ob_get_clean();
  }

  /**
   * Shortcode handler function for shortcode 'custom-datatable'
   */
  public function renderCustomDatatable($atts)
  {
    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    wp_enqueue_style('datatables-manager-style');
    wp_enqueue_script('datatables-manager-datatables');
    wp_enqueue_script('datatables-manager-custom-datatable');

    if (array_key_exists('id', $atts)) {
      $id = $atts['id'];
    } else {
      $id = 1;
    }

    $columns = $this->tables_controller->getTableColumns($id);

    $data = [
      'table_id' => $id,
      'columns' => $columns,
    ];

    return $this->loadFile('table-view.php', $data);
  }
}
