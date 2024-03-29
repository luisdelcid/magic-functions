<?php

/**
 * @return string
 */
function __add_plugin_action($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return __add_filter($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __add_plugin_action_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return __add_filter_once($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __add_plugin_filter($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return __add_filter($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __add_plugin_filter_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return __add_filter_once($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return mixed
 */
function __apply_plugin_filters($hook_name = '', $value = null, ...$arg){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return apply_filters($hook_name, $value, ...$arg);
}

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
function __did_plugin_action($hook_name = ''){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
	return did_action($hook_name);
}

/**
 * @return bool
 */
function __did_plugin_filter($hook_name = ''){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
	return did_filter($hook_name);
}

/**
 * @return void
 */
function __do_plugin_action($hook_name = '', ...$arg){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
	do_action($hook_name, ...$arg);
}

/**
 * @return bool
 */
function __doing_plugin_action($hook_name = ''){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return doing_filter($hook_name);
}

/**
 * @return bool
 */
function __doing_plugin_filter($hook_name = ''){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return doing_filter($hook_name);
}

/**
 * @return bool
 */
function __has_plugin_action($hook_name = '', $callback = false){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return has_filter($hook_name, $callback);
}

/**
 * @return bool
 */
function __has_plugin_filter($hook_name = '', $callback = false){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return has_filter($hook_name, $callback);
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
		$file = __caller_file();
	}
	$plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return '';
	}
	return plugin_basename($plugin_file);
}

/**
 * @return array|WP_Error
 */
function __plugin_data($file = '', $markup = true, $translate = true){
    if(!$file){
		$file = __caller_file();
	}
    $plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return __error(__('Plugin not found.'));
	}
    $md5 = md5($plugin_file);
    $key = 'plugin_data_' . $md5;
    if(__isset_cache($key)){
        return (array) __get_cache($key, []);
    }
    if(!function_exists('get_plugin_data')){
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $data = get_plugin_data($plugin_file, $markup, $translate);
    __set_cache($key, $data);
    return $data;
}

/**
 * @return string
 */
function __plugin_enqueue($filename = '', $deps = [], $in_footer_l10n_media = true){
	$file = __caller_file();
	$plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return '';
	}
	$mimes = [
		'css' => 'text/css',
		'js' => 'application/javascript',
	];
	$filename = wp_basename($filename);
	$filetype = wp_check_filetype($filename, $mimes);
	if(!$filetype['type']){
		return '';
	}
	$file = plugin_dir_path($file) . $filename; // Relative to the caller file.
	if(!file_exists($file)){
		$file = plugin_dir_path($plugin_file) . 'src/' . $filetype['ext'] . '/' . $filename;
		if(!file_exists($file)){
			return '';
		}
	}
	$handle = wp_basename($filename, '.' . $filetype['ext']);
	$handle = __plugin_slug($handle, $plugin_file);
	$is_script = false;
	if('application/javascript' === $filetype['type']){
		$deps[] = __slug('singleton');
		$in_footer_media = true;
		$is_script = true;
		$l10n = [];
		if(__is_associative_array($in_footer_l10n_media)){
			$l10n = $in_footer_l10n_media;
		} else {
            $in_footer_media = (bool) $in_footer_l10n_media;
        }
	} else { // text/css
		$in_footer_media = 'all';
		if(is_string($in_footer_l10n_media)){
			$in_footer_media = $in_footer_l10n_media;
		}
	}
	__local_enqueue($handle, $file, $deps, $in_footer_media);
    if(!$is_script){
        return $handle;
    }
    if(!$l10n){
        return $handle;
    }
    $object_name = __canonicalize($handle);
    wp_localize_script($handle, $object_name . '_l10n', $l10n);
	return $handle;
}

/**
 * @return string
 */
function __plugin_file($file = ''){
	global $wp_plugin_paths;
	if(!$file){
		$file = __caller_file();
	}
	if(!file_exists($file)){
		return '';
	}
    $md5 = md5($file);
    $key = 'plugin_file_' . $md5;
    if(__isset_cache($key)){
        return (string) __get_cache($key, '');
    }
	// $wp_plugin_paths contains normalized paths.
	$file = wp_normalize_path($file);
	arsort($wp_plugin_paths);
	foreach($wp_plugin_paths as $dir => $realdir){
		if(0 !== strpos($file, $realdir)){
			continue;
		}
		$file = $dir . substr($file, strlen($realdir));
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
		$part = $file; // The entire plugin consists of just a single PHP file, like Hello Dolly.
	} else {
		$parts = explode('/', $file, 2);
		$part = trailingslashit($parts[0]);
	}
	$active_plugins = (array) get_option('active_plugins', []);
	foreach($active_plugins as $active_plugin){
        if(0 !== strpos($active_plugin, $part)){
            continue;
        }
        $file = $dir . '/' . $active_plugin;
        __set_cache($key, $file);
        return $file; // File is a plugin.
	}
	$active_sitewide_plugins = (array) get_site_option('active_sitewide_plugins', []);
	$active_sitewide_plugins = array_keys($active_sitewide_plugins);
	foreach($active_sitewide_plugins as $active_sitewide_plugin){
        if(0 !== strpos($active_sitewide_plugin, $part)){
            continue;
        }
        $file = $dir . '/' . $active_sitewide_plugin;
        __set_cache($key, $file);
        return $file; // File is a must-use plugin.
	}
    __set_cache($key, '');
	return '';
}

/**
 * @return string
 */
function __plugin_folder($file = ''){
	if(!$file){
		$file = __caller_file();
	}
	$basename = __plugin_basename($file);
	if(false === strpos($basename, '/')){
		return ''; // Ignore. The entire plugin consists of just a single PHP file, like Hello Dolly.
	}
	$parts = explode('/', $basename, 2);
	return $parts[0];
}

/**
 * @return string
 */
function __plugin_meta($key = '', $file = ''){
    if(!$file){
		$file = __caller_file();
	}
    $data = __plugin_data($file);
    if(is_wp_error($data)){
        return $data->get_error_message();
    }
	if(array_key_exists($key, $data)){
		$arr = $data;
	} elseif(array_key_exists($key, $data['sections'])){
		$arr = $data['sections'];
	} else {
		return $key . ' ' . __('(not found)');
	}
	return $arr[$key];
}

/**
 * @return string
 */
function __plugin_prefix($str = '', $file = ''){
	if(!$file){
		$file = __caller_file();
	}
	$plugin_folder = __plugin_folder($file);
	if(!$plugin_folder){
		return '';
	}
	return __prefix($str, $plugin_folder);
}

/**
 * @return string
 */
function __plugin_slug($str = '', $file = ''){
	if(!$file){
		$file = __caller_file();
	}
	$plugin_folder = __plugin_folder($file);
	if(!$plugin_folder){
		return '';
	}
	return __slug($str, $plugin_folder);
}

/**
 * @return void
 */
function __plugin_update_check($url = '', $file = ''){
	$url = wp_http_validate_url($url);
	if(!$url){
		return;
	}
	if(!$file){
		$file = __caller_file();
	}
	$plugin_file = __plugin_file($file);
	if(!$plugin_file){
		return '';
	}
	$slug = __plugin_slug(false, $file);
	$metadata_url = add_query_arg([
		'action' => 'get_metadata',
		'slug' => $slug,
	], $url);
	__check_for_updates($metadata_url, $plugin_file, $slug);
}

/**
 * @return void
 */
function __plugin_update_license($license = '', $file = ''){
	if(!$license){
		return;
	}
	if(!$file){
		$file = __caller_file();
	}
	$slug = __plugin_slug(false, $file);
	__set_update_license($slug, $license);
}

/**
 * @return bool
 */
function __remove_plugin_action($hook_name = '', $callback = null, $priority = 10){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return remove_filter($hook_name, $callback, $priority);
}

/**
 * @return bool
 */
function __remove_plugin_filter($hook_name = '', $callback = null, $priority = 10){
	$file = __caller_file();
	$hook_name = __plugin_prefix($hook_name, $file);
    return remove_filter($hook_name, $callback, $priority);
}
