<?php

namespace DtManager\App\Hooks\Handlers;

class DTManager
{
  public function registerPostType()
  {
    $args = [
      'label'               => __('DtManager_table', 'DtManager'),
      'public'              => false,
      'exclude_from_search' => true,
      'show_ui'             => true,
      'show_in_menu'        => false,
      'capability_type'     => 'post',
      'labels'              => [
        'name'               => __('DtManager_tables', 'DtManager'),
        'singular_name'      => __('DtManager_table', 'DtManager'),
        'menu_name'          => __('DtManager_table', 'DtManager'),
        'add_new'            => __('Add Table', 'DtManager'),
        'add_new_item'       => __('Add New Table', 'DtManager'),
        'edit'               => __('Edit', 'DtManager'),
        'edit_item'          => __('Edit Table', 'DtManager'),
        'new_item'           => __('New Table', 'DtManager'),
        'view'               => __('View Table', 'DtManager'),
        'view_item'          => __('View Table', 'DtManager'),
      ],
    ];
    register_post_type('DtManager_table', $args);
  }
}
