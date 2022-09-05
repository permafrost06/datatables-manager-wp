<?php

namespace DtManager\App\Models;

use DtManager\Framework\Database\Orm\Model as BaseModel;

class Model extends BaseModel
{
    protected $guarded = ['id', 'ID'];
}