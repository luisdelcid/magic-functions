<?php
/*
Author: Luis del Cid
Author URI: https://luisdelcid.com/
Description: Magic functions for WordPress, plugins and themes.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: Magic Functions
Plugin URI: https://magicfunctions.com/
Requires at least: 5.6
Requires PHP: 5.6
Text Domain: magic-functions
Version: 0.1.21
*/

// Make sure we don't expose any info if called directly.
defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.');
foreach(glob(plugin_dir_path(__FILE__) . 'src/autoload/*.php') as $__file){
    require_once($__file);
}
unset($__file);
__build_update_checker('https://github.com/luisdelcid/magic-functions', __FILE__);
__enqueue_functions();
__include_theme_functions();
do_action('magic_loaded');
