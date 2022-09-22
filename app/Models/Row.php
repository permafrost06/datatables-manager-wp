<?php

namespace DtManager\App\Models;

use DtManager\App\Models\Model;

class Row extends Model
{
  protected $table = 'dtmanager_rows';

  protected $primaryKey = 'row_id';

  public $timestamps = false;
}
