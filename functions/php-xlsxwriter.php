<?php

/**
 * @return bool|WP_Error
 */
function __use_xlsxwriter($preferred_version = '0.38'){
	$class = 'XLSXWriter';
	if(class_exists($class)){
		return true;
	}
    $hook_name = __prefix('xlsxwriter_preferred_version');
	$preferred_version = __filter($hook_name, $preferred_version);
	$dir = $this->remote_lib('https://github.com/mk-j/PHP_XLSXWriter/archive/refs/tags/' . $preferred_version . '.zip', 'PHP_XLSXWriter-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/xlsxwriter.class.php';
	if(!file_exists($file)){
		return $this->error(__('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	return class_exists($class);
}

/**
 * @return XLSXWriter|WP_Error
 */
function __xlsx_writer(...$args){
	$remote_lib = __use_xlsxwriter();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return new \XLSXWriter(...$args);
}
