<?php
/*
Author: Luis del Cid
Author URI: https://luisdelcid.com/
Description: A collection of magic functions for WordPress, plugins and themes.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: Magic Functions
Plugin URI: https://magicfunctions.com/
Requires at least: 5.6
Requires PHP: 5.6
Text Domain: magic-functions
Version: 0.9.1
*/

defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.');
require_once(plugin_dir_path(__FILE__) . 'src/php/loader.php');
__Loader::load(__FILE__);
if(did_action('magic_functions_loaded')){
    __enqueue_functions();
    __plugin_update_check('https://github.com/luisdelcid/magic-functions', __FILE__);
}
