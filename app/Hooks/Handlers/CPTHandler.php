<?php

namespace DtManager\App\Hooks\Handlers;

use DtManager\App\App;

class CPTHandler
{
  protected $customPostTypes = [
    DTManager::class
  ];

  public function registerPostTypes()
  {
    foreach ($this->customPostTypes as $cpt) {
      App::make($cpt)->registerPostType();
    }
  }
}
