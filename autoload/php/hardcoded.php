<?php

/**
 * @return void
 */
function __include_theme_functions(){
    $file = get_stylesheet_directory() . '/magic-functions.php';
    if(!file_exists($file)){
        return;
    }
    require_once($file);
}

/**
 * @return string
 */
function __prefix(){
    return 'mf'; // Hardcoded;
}

/**
 * @return string
 */
function __slug(){
    return 'mf'; // Hardcoded;
}
