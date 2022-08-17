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
  public function adminMenu(): void
  {
    add_menu_page(
      __('Datatables Manager Settings', 'datatables-manager'),
      'Datatables Manager',
      'manage_options',
      'datatables-manager',
      [$this, 'vueAppEntrypoint'],
      'dashicons-id-alt',
      25
    );
    add_submenu_page(
      'datatables-manager',
      __('Datatables Manager Settings', 'datatables-manager'),
      __('Settings', 'datatables-manager'),
      'manage_options',
      'datatables-manager#/settings',
      [$this, 'vueAppEntrypoint']
    );
  }

  /**
   * Creates an entrypoint for the Vue.js app
   */
  public function vueAppEntrypoint(): void
  {
?>
    <div id="datatables_admin_app"></div>
<?php

    wp_enqueue_script('datatables-admin-vue-app');
  }
}
