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
Version: 0.1.21
*/

defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.'); // Make sure we don't expose any info if called directly.
foreach(glob(plugin_dir_path(__FILE__) . 'src/autoload/*.php') as $__mf_file){
    require_once($__mf_file);
}
unset($__mf_file);
__build_update_checker('https://github.com/luisdelcid/magic-functions', __FILE__);
add_action('admin_enqueue_scripts', '__enqueue_functions', 0); // Highest priority.
add_action('after_setup_theme', '__require_theme_functions', 0); // Highest priority.
add_action('login_enqueue_scripts', '__enqueue_functions', 0); // Highest priority.
add_action('wp_enqueue_scripts', '__enqueue_functions', 0); // Highest priority.
do_action('magic_loaded');
