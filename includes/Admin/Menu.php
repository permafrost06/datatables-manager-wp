<?php

namespace Datatables\Manager\Admin;

/**
 * Menu handler class
 */
class Menu
{
  public function __construct()
  {
    add_action('admin_menu', [$this, 'adminMenu']);
  }

  /**
   * Creates a menu for the plugin in the admin panel
   */
  public function adminMenu()
  {
    add_menu_page(
      __('Datatables Manager Settings', 'datatables-manager'),
      'Datatables Manager',
      'manage_options',
      'datatables-manager',
      [$this, 'vueAppEntrypoint'],
      'dashicons-editor-table',
      25
    );

    /* debug-start */
    add_submenu_page(
      'datatables-manager',
      __('Datatables Manager Settings', 'datatables-manager'),
      __('Settings', 'datatables-manager'),
      'manage_options',
      'datatables-manager#/settings',
      [$this, 'vueAppEntrypoint']
    );
    /* debug-end */
  }

  /**
   * Creates an entrypoint for the Vue.js app
   */
  public function vueAppEntrypoint()
  {
?>
    <div id="datatables_admin_app"></div>
<?php

    wp_enqueue_script('datatables-manager-admin-vue-app');
  }
}
