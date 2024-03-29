<?php

/**
 * @return string|WP_Error
 */
function __use_xlsxwriter($preferred_version = '0.39'){
	$key = 'xlsxwriter-dir-' . $preferred_version;
    if(__isset_cache($key)){
        return (string) __get_cache($key, '');
    }
	$class = 'XLSXWriter';
	if(class_exists($class)){
        return ''; // Already handled outside of this function.
    }
	$dir = __remote_lib('https://github.com/mk-j/PHP_XLSXWriter/archive/refs/tags/' . $preferred_version . '.zip', 'PHP_XLSXWriter-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}

	$file = $dir . '/xlsxwriter.class.php';
	if(!file_exists($file)){
		return __error(translate('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	__set_cache($key, $dir);
	return $dir;
}

/**
 * @return XLSXWriter|WP_Error
 */
function __xlsx_writer(){
	$lib = __use_xlsxwriter();
	if(is_wp_error($lib)){
		return $lib;
	}
	return new \XLSXWriter;
}
