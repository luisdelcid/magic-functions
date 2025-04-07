<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Magic Functions
// https://github.com/luisdelcid/magic-functions
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!defined('ABSPATH')){
    die('Invalid request.');
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('_load_magic_functions')){
    /**
     * @return void
     */
    function _load_magic_functions(){
        if(!did_action('plugins_loaded')){ // Too early.
            if(has_action('plugins_loaded', '_maybe_load_magic_functions')){ // Already added.
                return;
            }
            add_action('plugins_loaded', '_maybe_load_magic_functions'); // Add.
        }
        _maybe_load_magic_functions(); // Just in time.
    }
}

if(!function_exists('_maybe_load_magic_functions')){
    /**
     * @return void
     */
    function _maybe_load_magic_functions(){
        if(!did_action('plugins_loaded')){ // Too early.
            return;
        }
        if(defined('MAGIC_FUNCTIONS')){ // Already loaded.
            return;
        }
        $file = plugin_dir_path(__FILE__) . 'includes/magic-functions.php'; // Hardcoded.
        if(!file_exists($file)){
            return;
        }
        require_once $file; // Load PHP classes and functions.
        __enqueue_magic_functions(); // Load JavaScript classes and functions.
        __include_theme_functions(); // Load theme functions.
        do_action('magic_functions_loaded'); // Hardcoded.
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Load magic functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!did_action('magic_functions_preloaded')){ // Hardcoded.
    _load_magic_functions();
    do_action('magic_functions_preloaded'); // Hardcoded.
}
