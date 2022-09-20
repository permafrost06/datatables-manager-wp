<?php

namespace DtManager\App\Hooks\Handlers;

use DtManager\App\App;
use DtManager\App\Http\Controllers\TableController;

class ShortcodeHandler
{
  public function render($atts)
  {
    $this->enqueueAssets();

    if (array_key_exists('id', $atts)) {
      $id = $atts['id'];
    } else {
      $id = 1;
    }

    $columns = (new TableController())->getTableColumns($id);

    ob_start();
    App::make('view')->render('shortcode', [
      'table_id' => $id,
      'columns' => $columns,
    ]);
    return ob_get_clean();
  }

  public function enqueueAssets()
  {
    $app = App::getInstance();

    $assets = $app['url.assets'];

    $slug = $app->config->get('app.slug');

    wp_enqueue_script('datatables-manager-datatables');
    wp_enqueue_script('datatables-manager-custom-datatable');

    wp_enqueue_style(
      $slug . '_datatable_style',
      $assets . '/css/datatables.css'
    );

    wp_enqueue_script(
      $slug . '_datatables',
      $assets . '/js/datatables.js',
      array('jquery'),
      '1.0',
      true
    );

    wp_enqueue_script(
      $slug . '_custom_datatable',
      $assets . '/js/dtmanager_custom_datatable.js',
      array($slug . '_datatables'),
      '1.0',
      true
    );

    $ns = $app->config->get('app.rest_namespace');
    $ver = $app->config->get('app.rest_version');

    wp_localize_script($slug . '_custom_datatable', 'dtmanagerDatatable', [
      'url' => rest_url($ns . '/' . $ver . '/datatable'),
      'nonce' => wp_create_nonce('dtmanager-shortcode')
    ]);
  }
}
