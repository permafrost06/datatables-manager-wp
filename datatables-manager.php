<?php

/**
 * Plugin Name: Datatables Manager
 * Plugin URI: https://github.com/permafrost06/datatables-manager-wp/
 * Description: A demonstration plugin that can be used to create and manage custom tables
 * Version: 1.0.0
 * Requires at least: 7.0
 * Requires PHP: 7.2
 * Author: permafrost06
 * Author URI: https://github.com/permafrost06/
 * License: GPL v2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: datatables-manager
 */

if (!defined('ABSPATH')) {
  exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class of Datatables Controller
 */

final class DatatablesManager
{
  /**
   * Plugin version
   * 
   * @var string
   */
  const version = '1.0';

  private function __construct()
  {
    $this->defineConstants();

    add_action('init', [$this, 'registerCustomPostType']);

    add_action('plugins_loaded', [$this, 'initPlugin']);

    register_activation_hook(__FILE__, [$this, 'activate']);
  }

  /**
   * Initializes a singleton instance of the plugin
   * or gets instance of plugin
   */
  public static function init()
  {
    static $instance = false;

    if (!$instance) {
      $instance = new self();
    }

    return $instance;
  }

  /**
   * Define the required plugin constants
   */
  public function defineConstants()
  {
    define('DATATABLES_MANAGER_VERSION', self::version);
    define('DATATABLES_MANAGER_FILE', __FILE__);
    define('DATATABLES_MANAGER_PATH', __DIR__);
    define('DATATABLES_MANAGER_URL', plugins_url('', DATATABLES_MANAGER_FILE));
    define('DATATABLES_MANAGER_ASSETS', DATATABLES_MANAGER_URL . '/assets');
  }

  public function registerCustomPostType()
  {
    $args = [
      'label'               => __('Custom Datatable', 'datatables-manager'),
      'public'              => false,
      'exclude_from_search' => true,
      'show_ui'             => true,
      'show_in_menu'        => false,
      'capability_type'     => 'post',
      'labels'              => [
        'name'               => __('Custom Datatables', 'datatables-manager'),
        'singular_name'      => __('Custom Datatable', 'datatables-manager'),
        'menu_name'          => __('Custom Datatables', 'datatables-manager'),
        'add_new'            => __('Add Table', 'datatables-manager'),
        'add_new_item'       => __('Add New Table', 'datatables-manager'),
        'edit'               => __('Edit', 'datatables-manager'),
        'edit_item'          => __('Edit Table', 'datatables-manager'),
        'new_item'           => __('New Table', 'datatables-manager'),
        'view'               => __('View Table', 'datatables-manager'),
        'view_item'          => __('View Table', 'datatables-manager'),
      ],
    ];
    register_post_type('datatables-manager_custom-datatable', $args);
  }

  /**
   * Initialize the plugin
   */
  public function initPlugin()
  {
    new Datatables\Manager\Assets();

    $tables_controller = new \Datatables\Manager\TablesController();

    if (defined('DOING_AJAX') && DOING_AJAX)
      new \Datatables\Manager\Ajax($tables_controller);
    elseif (is_admin())
      new Datatables\Manager\Admin();
    else
      new \Datatables\Manager\Frontend($tables_controller);
  }

  public function activate()
  {
    $installer = new Datatables\Manager\Installer();
    $installer->run();
  }
}

function datatables_manager()
{
  return DatatablesManager::init();
}

datatables_manager();
