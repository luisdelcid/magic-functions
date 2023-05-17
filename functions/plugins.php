<?php

/**
 * @return bool
 */
function __are_plugins_active($plugins = []){
	if(!is_array($plugins)){
		return false;
	}
	foreach($plugins as $plugin){
		if(!__is_plugin_active($plugin)){
			return false;
		}
	}
	return true;
}

/**
 * @return bool
 */
function __is_plugin_active($plugin = ''){
	if(!function_exists('is_plugin_active')){
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	}
	return is_plugin_active($plugin);
}

/**
 * @return bool
 */
function __is_plugin_deactivating($file = ''){
	global $pagenow;
	if(!@is_file($file)){
		return false;
	}
	return (is_admin() and 'plugins.php' === $pagenow and isset($_GET['action'], $_GET['plugin']) and 'deactivate' === $_GET['action'] and plugin_basename($file) === $_GET['plugin']);
}

/**
 * @return string
 */
function __plugin_basename($file = ''){
	if(!$file){
		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $debug[0]['file'];
	}
	$plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return '';
	}
	return plugin_basename($plugin_file);
}

/**
 * @return bool
 */
function __plugin_did($hook_name = ''){
    $hook_name = __plugin_prefix($hook_name);
	return __did($hook_name);
}

/**
 * @return void
 */
function __plugin_do($hook_name = '', ...$arg){
	$hook_name = __plugin_prefix($hook_name);
	__do($hook_name, ...$arg);
}

/**
 * @return bool
 */
function __plugin_doing($hook_name = ''){
	$hook_name = __plugin_prefix($hook_name);
    return __doing($hook_name);
}

/**
 * @return string
 */
function __plugin_file($file = ''){
	global $wp_plugin_paths;
	if(!$file){
		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $debug[0]['file'];
	}
	if(!file_exists($file)){
		return '';
	}
    $md5 = md5($file);
    $key = 'plugin_file_' . $md5;
    if(__isset_cache($key)){
        return __get_cache($key, '');
    }
	// $wp_plugin_paths contains normalized paths.
	$file = wp_normalize_path($file);
	arsort($wp_plugin_paths);
	foreach($wp_plugin_paths as $dir => $realdir){
		if(strpos($file, $realdir) === 0){
			$file = $dir . substr($file, strlen($realdir));
		}
	}
	$plugin_dir = wp_normalize_path(WP_PLUGIN_DIR);
	$mu_plugin_dir = wp_normalize_path(WPMU_PLUGIN_DIR);
	if(!preg_match('#^' . preg_quote($plugin_dir, '#') . '/|^' . preg_quote($mu_plugin_dir, '#') . '/#', $file)){
        __set_cache($key, '');
		return ''; // File is not a plugin.
	}
	if(preg_match('#^' . preg_quote($plugin_dir, '#') . '/#', $file)){
		$dir = $plugin_dir; // File is a plugin.
	} else {
		$dir = $mu_plugin_dir; // File is a must-use plugin.
	}
	// Get relative path from plugins directory.
	$file = preg_replace('#^' . preg_quote($plugin_dir, '#') . '/|^' . preg_quote($mu_plugin_dir, '#') . '/#', '', $file);
	$file = trim($file, '/');
	if(strpos($file, '/') === false){
		$part = $file;
	} else {
		$parts = explode('/', $file, 2);
		$part = $parts[0] . '/'; // ?
	}
	$active_plugins = (array) get_option('active_plugins', []);
	foreach($active_plugins as $active_plugin){
		if(strpos($active_plugin, $part) === 0){
			$file = $dir . '/' . $active_plugin;
            __set_cache($key, $file);
			return $file;
		}
	}
	$active_sitewide_plugins = (array) get_site_option('active_sitewide_plugins', []);
	$active_sitewide_plugins = array_keys($active_sitewide_plugins);
	foreach($active_sitewide_plugins as $active_sitewide_plugin){
		if(strpos($active_sitewide_plugin, $part) === 0){
			$file = $dir . '/' . $active_sitewide_plugin;
            __set_cache($key, $file);
			return $file;
		}
	}
    __set_cache($key, '');
	return '';
}

/**
 * @return mixed
 */
function __plugin_filter($hook_name = '', $value = null, ...$arg){
	$hook_name = __plugin_prefix($hook_name);
    return __filter($hook_name, $value, ...$arg);
}

/**
 * @return bool
 */
function __plugin_has($hook_name = '', $callback = null){
	$hook_name = __plugin_prefix($hook_name);
    return __has($hook_name, $callback);
}

/**
 * @return bool
 */
function __plugin_off($hook_name = '', $callback = null, $priority = 10){
	$hook_name = __plugin_prefix($hook_name);
    return __off($hook_name, $callback, $priority);
}

/**
 * @return string
 */
function __plugin_on($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$hook_name = __plugin_prefix($hook_name);
	return __on($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __plugin_one($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$hook_name = __plugin_prefix($hook_name);
	return __one($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __plugin_prefix($str = '', $file = ''){
	if(!$file){
		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $debug[0]['file'];
	}
	$plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return '';
	}
	$basename = wp_basename($plugin_file, '.php');
	return __prefix($str, $basename);
}

/**
 * @return string
 */
function __plugin_slug($str = '', $file = ''){
	if(!$file){
		$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$file = $debug[0]['file'];
	}
	$plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return '';
	}
	$basename = wp_basename($plugin_file, '.php');
	return __slug($str, $basename);
}
