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

    do_action($slug . '_loading_app');

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

    // $currentUser = get_user_by('ID', get_current_user_id());

    // wp_localize_script($slug . '_admin_app_start', 'fluentFrameworkAdmin', [
    //   'slug'  => $slug = $app->config->get('app.slug'),
    //   'nonce' => wp_create_nonce($slug),
    //   'rest'  => $this->getRestInfo($app),
    //   'brand_logo' => $this->getMenuIcon(),
    //   'asset_url' => $assets,
    //   'me'          => [
    //     'id'        => $currentUser->ID,
    //     'full_name' => trim($currentUser->first_name . ' ' . $currentUser->last_name),
    //     'email'     => $currentUser->user_email
    //   ],
    // ]);
  }

  // protected function getRestInfo($app)
  // {
  //   $ns = $app->config->get('app.rest_namespace');
  //   $ver = $app->config->get('app.rest_version');

  //   return [
  //     'base_url'  => esc_url_raw(rest_url()),
  //     'url'       => rest_url($ns . '/' . $ver),
  //     'nonce'     => wp_create_nonce('wp_rest'),
  //     'namespace' => $ns,
  //     'version'   => $ver
  //   ];
  // }
}
