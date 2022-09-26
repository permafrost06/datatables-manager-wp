<?php

namespace DtManager\App\Services;

class Sanitizer
{
  protected static function sanitize($data, $keymap)
  {
    foreach ($data as $key => $value) {
      if (isset($keymap[$key])) {
        $data[$key] = call_user_func($keymap[$key], $value);
      }
    }

    return $data;
  }

  public static function sanitizeTable($data)
  {
    $keymap = [
      'table_id' => 'intval',
      'table_name' => 'sanitize_text_field',
      'description' => 'sanitize_textarea_field',
      'columns' => 'sanitize_text_field'
    ];

    return static::sanitize($data, $keymap);
  }

  public static function sanitizeRow($data)
  {
    $keymap = [
      'row' => 'sanitize_text_field',
    ];

    return static::sanitize($data, $keymap);
  }

  public static function sanitizeDatatable($data)
  {
    $keymap = [
      'table_id' => 'intval',
      'draw' => 'intval',
      'start' => 'intval',
      'length' => 'intval'
    ];

    return static::sanitize($data, $keymap);
  }
}
