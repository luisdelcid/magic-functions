<?php

/**
 * @return void
 */
function __include_theme_functions(){
	__set_cache('include_theme_functions', true);
	__one('after_setup_theme', '__maybe_include_theme_functions');
}

/**
 * @return void
 */
function __maybe_include_theme_functions(){
    $include_theme_functions = (bool) __get_cache('include_theme_functions', false);
	if(!$include_theme_functions){
		return;
	}
	$file = get_stylesheet_directory() . '/' . __slug('functions.php');
	if(!file_exists($file)){
		return;
	}
	require_once($file);
}

/**
 * @return string
 */
function __prefix($str = '', $prefix = '__'){
	if(!$prefix){
		return '';
	}
	$prefix = str_replace('\\', '_', $prefix); // fix namespaces
	$prefix = __canonicalize($prefix);
    if(false === $str){
        return $prefix;
    }
    if($prefix === $str){
        return $str;
    }
    $prefix .= '_';
    if(0 === strpos($str, $prefix)){
        return $str;
    }
    return $prefix . $str;
}

/**
 * @return string
 */
function __slug($str = '', $slug = '--'){
	if(!$slug){
		return '';
	}
	$slug = str_replace('_', '-', $slug); // fix canonicalized
    $slug = str_replace('\\', '-', $slug); // fix namespaces
	$slug = sanitize_title($slug);
	if(true === $str){
		return $slug . '-';
	}
    if(!$str){
        return $slug;
    }
    if($slug === $str){
        return $str;
    }
    $slug .= '-';
    if(0 === strpos($str, $slug)){
        return $str;
    }
    return $slug . $str;
}

/**
 * @return bool
 */
function __test(){
	__die('Hello, World!');
}
