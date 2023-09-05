<?php
/*
Author: Luis del Cid
Author URI: https://luisdelcid.com/
Description: A collection of magic functions for your WordPress plugins and themes.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: Magic Functions
Plugin URI: https://magicfunctions.com/
Requires at least: 5.6
Requires PHP: 5.6
Text Domain: magic-functions
Version: 0.9.3.2
*/

defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.');
foreach(glob(plugin_dir_path(__FILE__) . 'src/autoload/*.php') as $magic_file){
    require_once($magic_file);
}
unset($magic_file);
__enqueue_functions();
__plugin_update_check('https://github.com/luisdelcid/magic-functions', __FILE__);
do_action('magic_loaded');
