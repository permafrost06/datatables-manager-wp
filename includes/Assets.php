<?php

namespace Datatables\Manager;

/**
 * Assets handler class
 */
class Assets
{
  public function __construct()
  {
    add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
    add_action('admin_enqueue_scripts', [$this, 'registerAdminAssets']);
  }

  /**
   * Get the javascript files for the plugin
   */
  public function getScripts(): array
  {
    return [
      'datatables-manager-datatables' => [
        'src' => DATATABLES_MANAGER_ASSETS . '/js/datatables.js',
        'version' => filemtime(DATATABLES_MANAGER_PATH . '/assets/js/datatables.js'),
        'deps' => ['jquery']
      ],
      'datatables-manager-custom-datatable' => [
        'src' => DATATABLES_MANAGER_ASSETS . '/js/custom-datatable.js',
        'version' => filemtime(DATATABLES_MANAGER_PATH . '/assets/js/custom-datatable.js'),
        'deps' => ['jquery', 'datatables']
      ]
    ];
  }

  /**
   * Get the admin javascript files for the plugin
   */
  public function getAdminScripts(): array
  {
    return [
      'datatables-manager-admin-vue-app' => [
        /* next-line-enables-hmr */
        'src' => 'http://localhost:8081/main.js',
        /* next-line-disables-hmr */
        // 'src' => DATATABLES_MANAGER_ASSETS . '/js/admin_app/main.js',
        'version' => filemtime(DATATABLES_MANAGER_PATH . '/assets/js/admin_app/main.js'),
        'deps' => ['jquery']
      ]
    ];
  }

  /**
   * Get the stylesheets for the plugin
   */
  public function getStyles(): array
  {
    return [
      'datatables-manager-style' => [
        'src' => DATATABLES_MANAGER_ASSETS . '/styles/datatables.css',
        'version' => filemtime(DATATABLES_MANAGER_PATH . '/assets/styles/datatables.css')
      ],
    ];
  }

  /**
   * Register the plugin assets
   */
  public function registerAssets(): void
  {
    $scripts = $this->getScripts();

    foreach ($scripts as $handle => $script) {
      $deps = isset($script['deps']) ? $script['deps'] : false;
      $footer = isset($script['footer']) ? $script['footer'] : true;

      wp_register_script($handle, $script['src'], $deps, $script['version'], $footer);
    }

    $styles = $this->getStyles();

    foreach ($styles as $handle => $style) {
      $deps = isset($style['deps']) ? $style['deps'] : false;

      wp_register_style($handle, $style['src'], $deps, $style['version']);
    }

    wp_localize_script(
      'datatables-manager-custom-datatable',
      'customDatatableAjax',
      [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dtm-frontend-shortcode')
      ]
    );
  }

  /**
   * Register the plugin admin assets
   */
  public function registerAdminAssets(): void
  {
    $scripts = $this->getAdminScripts();

    foreach ($scripts as $handle => $script) {
      $deps = isset($script['deps']) ? $script['deps'] : false;
      $footer = isset($script['footer']) ? $script['footer'] : true;

      wp_register_script($handle, $script['src'], $deps, $script['version'], $footer);
    }

    wp_localize_script(
      'datatables-manager-admin-vue-app',
      'datatablesMgrAdmin',
      [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('datatables_admin_app'),
      ]
    );
  }
}
