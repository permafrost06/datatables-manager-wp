<?php

namespace DtManager\App\Http\Requests;

use DtManager\Framework\Foundation\RequestGuard;

class TableRequest extends RequestGuard
{
  /**
   * @return Array
   */
  public function rules()
  {
    return [
      // 'table_name' => 'required',
      // 'description' => 'required',
      // 'columns' => 'required'
    ];
  }

  /**
   * @return Array
   */
  public function messages()
  {
    return [];
  }

  // /**
  //  * @return Array
  //  */
  // public function sanitize()
  // {
  //   $data = $this->all();

  //   $data['table_name'] = sanitize_text_field($data['table_name']);
  //   $data['description'] = sanitize_text_field($data['description']);
  //   $data['columns'] = sanitize_text_field($data['columns']);

  //   return $data;
  // }
}
