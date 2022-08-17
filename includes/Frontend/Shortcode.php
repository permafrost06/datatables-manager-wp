<?php

namespace Datatables\Manager\Frontend;

/**
 * Shortcode handler class
 */
class Shortcode
{
  public function __construct()
  {
    add_shortcode('custom-datatable', [$this, 'renderCustomDatatable']);
  }

  /**
   * Load the content of a file to a string and return it
   * 
   * @param string  $filename Name of the file in the "views" folder
   * @param mixed   $data     Any data the file may need
   */
  public function loadFile(string $filename, $data = ""): string
  {
    ob_start();
    include __DIR__ . '/views/' . $filename;
    return ob_get_clean();
  }

  /**
   * Shortcode handler function for shortcode 'contact-form'
   */
  public function renderCustomDatatable(): string
  {
    wp_enqueue_style('datatables-style');
    wp_enqueue_script('datatables');
    wp_enqueue_script('custom-datatable');

    return $this->loadFile('test-table.php');
  }
}
