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
    return 'magic_functions'; // Hardcoded;
}

/**
 * @return string
 */
function __slug(){
    return 'magic-functions'; // Hardcoded;
}
