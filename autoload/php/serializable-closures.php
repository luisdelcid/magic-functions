<?php

/**
 * @return Opis\Closure\SerializableClosure|WP_Error
 */
function __serializable_closure($closure = null){
	$lib = __use_serializable_closure();
	if(is_wp_error($lib)){
		return $lib;
	}
	return new \Opis\Closure\SerializableClosure($closure);
}

/**
 * @return string|WP_Error
 */
function __use_serializable_closure($preferred_version = '3.6.3'){
	$key = 'serializable-closure-dir-' . $preferred_version;
    if(__isset_cache($key)){
        return (string) __get_cache($key, '');
    }
	$class = 'Opis\Closure\SerializableClosure';
	if(class_exists($class)){
        return ''; // Already handled outside of this function.
    }
	$dir = __remote_lib('https://github.com/opis/closure/archive/refs/tags/' . $preferred_version . '.zip', 'closure-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/autoload.php';
	if(!file_exists($file)){
		return __error(translate('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	__set_cache($key, $dir);
	return $dir;
}
