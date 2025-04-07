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

if(!function_exists('_maybe_include_magic_functions')){
    /**
     * @return void
     */
    function _maybe_include_magic_functions(){
        $includes = plugin_dir_path(__FILE__) . 'includes';
        $js = $includes . '/magic-functions.js'; // Hardcoded.
        $php = $includes . '/magic-functions.php'; // Hardcoded.
        if(!file_exists($js) or !file_exists($php)){
            return;
        }
        include_once $php; // Load PHP classes and functions.
        __enqueue_magic_functions($js); // Enqueue JavaScript classes and functions.
        return;
    }
}

if(!function_exists('_maybe_load_magic_functions')){
    /**
     * @return void
     */
    function _maybe_load_magic_functions(){
        if(defined('MAGIC_FUNCTIONS')){ // Already loaded.
            return;
        }
        if(did_action('plugins_loaded')){ // Just in time.
            _maybe_include_magic_functions();
            return;
        }
        if(has_action('plugins_loaded', '_maybe_include_magic_functions')){ // Already enqueued.
            return;
        }
        add_action('plugins_loaded', '_maybe_include_magic_functions'); // Enqueue.
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Maybe load magic functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

_maybe_load_magic_functions();
