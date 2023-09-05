<?php

/**
 * @return void
 */
function __enqueue_functions(){
	__set_cache('enqueue_functions', true);
	__add_action_once('admin_enqueue_scripts', '__maybe_enqueue_functions', 0); // Highest priority.
    __add_action_once('after_setup_theme', '__maybe_include_theme_functions', 0); // Highest priority.
    __add_action_once('login_enqueue_scripts', '__maybe_enqueue_functions', 0); // Highest priority.
    __add_action_once('wp_enqueue_scripts', '__maybe_enqueue_functions', 0); // Highest priority.
}

/**
 * @return void
 */
function __maybe_enqueue_functions(){
    $enqueue_functions = (bool) __get_cache('enqueue_functions', false);
    if(!$enqueue_functions){
        return;
    }
    $handler = __slug(false);
    __local_enqueue($handler, plugin_dir_path(dirname(__FILE__)) . 'js/miscellaneous.js', ['jquery', 'wp-hooks']);
    wp_localize_script($handler, __prefix('l10n'), [
        'mu_plugins_url' => __dir_to_url(wp_normalize_path(WPMU_PLUGIN_DIR)),
        'plugins_url' => __dir_to_url(wp_normalize_path(WP_PLUGIN_DIR)),
        'site_url' => site_url(),
    ]);
    __local_enqueue(__slug('singleton'), plugin_dir_path(dirname(__FILE__)) . 'js/singleton.js', [$handler]);
}

/**
 * @return void
 */
function __maybe_include_theme_functions(){
    $enqueue_functions = (bool) __get_cache('enqueue_functions', false);
    if(!$enqueue_functions){
        return;
    }
    $file = get_stylesheet_directory() . '/' . __slug(false) . '.php';
    if(!file_exists($file)){
        return;
    }
    require_once($file);
}
