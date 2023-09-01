<?php

/**
 * @return simple_html_dom|WP_Error
 */
function __file_get_html(...$args){
	$remote_lib = __use_simple_html_dom();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return file_get_html(...$args);
}

/**
 * @return simple_html_dom|WP_Error
 */
function __str_get_html(...$args){
	$remote_lib = __use_simple_html_dom();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return str_get_html(...$args);
}

/**
 * @return bool|WP_Error
 */
function __use_simple_html_dom($preferred_version = '1.9.1'){
	$class = 'simple_html_dom';
	if(class_exists($class)){
		return true;
	}
	$dir = __remote_lib('https://github.com/simplehtmldom/simplehtmldom/archive/refs/tags/' . $preferred_version . '.zip', 'simplehtmldom-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/simple_html_dom.php';
	if(!file_exists($file)){
		return __error(__('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	return class_exists($class);
}
