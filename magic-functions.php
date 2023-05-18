<?php
/*
Author: Luis del Cid
Author URI: https://luisdelcid.com/
Description: A collection of magic functions for your WordPress plugins and theme's functions.php.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: Magic Functions
Plugin URI: https://magicfunctions.com/
Requires at least: 5.6
Requires PHP: 5.6
Text Domain: magic-functions
Version: 0.5.13
*/

defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.');
foreach(glob(plugin_dir_path(__FILE__) . 'functions/*.php') as $file){
    require_once($file);
}
__enqueue_functions();
__include_theme_functions();
//__plugin_update_check('https://magicfunctions.com/', __FILE__);
