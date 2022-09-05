<?php defined('ABSPATH') or die;

/*
Plugin Name: DtManager
Description: DtManager WordPress Plugin
Version: 1.0.0
Author: permafrost06
Author URI: 
Plugin URI: 
License: GPLv2 or later
Text Domain: DtManager
Domain Path: /language
*/

require __DIR__.'/vendor/autoload.php';

call_user_func(function($bootstrap) {
    $bootstrap(__FILE__);
}, require(__DIR__.'/boot/app.php'));
