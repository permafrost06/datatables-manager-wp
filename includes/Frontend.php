<?php

namespace Datatables\Manager;

/**
 * Frontend handler class
 */
class Frontend
{
  public function __construct($tables_controller)
  {
    new Frontend\Shortcode($tables_controller);
  }
}
