<?php
/*
 * Plugin Name: Magic Functions
 * Plugin URI: https://magicfunctions.com
 * Description: Magic functions for WordPress plugins and themes.
 * Version: 5.7.28
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author: Luis del Cid
 * Author URI: https://luisdelcid.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: magic-functions
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
	die('Invalid request.');
}

// Load PHP functions.
require_once plugin_dir_path(__FILE__) . 'includes/magic-functions.php';

// Load JavaScript functions.
add_action('admin_enqueue_scripts', '__enqueue_dependencies', 0); // Highest priority.
add_action('login_enqueue_scripts', '__enqueue_dependencies', 0); // Highest priority.
add_action('wp_enqueue_scripts', '__enqueue_dependencies', 0); // Highest priority.

// Wait for the `plugins_loaded` action hook.
add_action('plugins_loaded', function(){

    // Check for plugin updates.
    __plugin_update_checker(__FILE__);

    // Fires after the plugin is fully loaded and instantiated.
    do_action('magic_loaded');

}, 0); // Highest priority.

// Wait for the `after_setup_theme` action hook.
add_action('after_setup_theme', function(){

    // Load theme functions.
    __include_theme_functions();

    // Fires after the theme is fully loaded and instantiated.
    do_action('after_magic_loaded');

}, 0); // Highest priority.
