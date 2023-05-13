<?php

/**
 * @return Opis\Closure\SerializableClosure|WP_Error
 */
function __serializable_closure(...$args){
	$remote_lib = __use_serializable_closure();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return new \Opis\Closure\SerializableClosure(...$args);
}

/**
 * @return bool|WP_Error
 */
function __use_serializable_closure($preferred_version = '3.6.3'){
	$class = 'Opis\Closure\SerializableClosure';
	if(class_exists($class)){
		return true;
	}
	$hook_name = __prefix('serializable_closure_preferred_version');
    $preferred_version = __filter($hook_name, $preferred_version);
	$dir = __remote_lib('https://github.com/opis/closure/archive/refs/tags/' . $preferred_version . '.zip', 'closure-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/autoload.php';
	if(!file_exists($file)){
		return __error(__('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	return class_exists($class);
}
