<?php
/*
 * Plugin Name: Magic Functions
 * Plugin URI: https://magicfunctions.com
 * Description: A collection of magic functions for WordPress, plugins and themes.
 * Version: 0.8.14.1
 * Requires at least: 5.6
 * Requires PHP: 5.6
 * Author: Luis del Cid
 * Author URI: https://luisdelcid.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: magic-functions
 * Network: true
 * Update URI: https://github.com/luisdelcid/magic-functions
 */

/*
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

// Make sure we don't expose any info if called directly.
if(!defined('ABSPATH')){
    echo 'Hi there! I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Wait for the `plugins_loaded` action hook.
add_action('plugins_loaded', function(){

    // Load PHP classes and functions.
    $autoload_dir = plugin_dir_path(__FILE__) . 'autoload';
    require_once($autoload_dir . '/singleton.php');
    require_once($autoload_dir . '/functions.php');

    // Check for updates.
    __build_update_checker('https://github.com/luisdelcid/magic-functions', __FILE__, 'magic-functions');

    // Load JavaScript classes and functions.
    __enqueue_functions();

    // Include theme functions.
    __include_theme_functions();

    // Fires when magic is fully loaded.
    do_action('magic_loaded');

}, 0); // Highest priority.
