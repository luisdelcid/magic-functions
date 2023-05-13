<?php

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
