<?php

/**
 * @return void
 */
function __enqueue($handle = '', $src = '', $deps = [], $ver = false, $in_footer_media = true){
	$mimes = [
		'css' => 'text/css',
		'js' => 'application/javascript',
	];
	$filetype = wp_check_filetype(__basename($src), $mimes);
	switch($filetype['type']){
		case 'application/javascript':
			$in_footer = (bool) $in_footer_media;
			wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
			break;
		case 'text/css':
			$media = (is_string($in_footer_media) ? $in_footer_media : 'all');
			wp_enqueue_style($handle, $src, $deps, $ver, $media);
			break;
	}
}

/**
 * @return void
 */
function __enqueue_fa6($preferred_version = '6.5.1'){
	__set_cache('enqueue_fa6', $preferred_version);
	__add_action_once('wp_enqueue_scripts', '___enqueue_fa6');
}

/**
 * @return void
 */
function __enqueue_functions(){
	__set_cache('enqueue_functions', true);
	__add_action_once('admin_enqueue_scripts', '___enqueue_functions', 0); // Highest priority.
	__add_action_once('login_enqueue_scripts', '___enqueue_functions', 0); // Highest priority.
	__add_action_once('wp_enqueue_scripts', '___enqueue_functions', 0); // Highest priority.
}

/**
 * @return void
 */
function __enqueue_inputmask($preferred_version = '5.0.8'){
	__set_cache('enqueue_inputmask', $preferred_version);
	__add_action_once('wp_enqueue_scripts', '___enqueue_inputmask');
}

/**
 * @return void
 */
function __enqueue_stylesheet(){
	__set_cache('enqueue_stylesheet', true);
	__add_action_once('wp_enqueue_scripts', '___enqueue_stylesheet');
}

/**
 * @return void
 */
function __include_functions(){
	__set_cache('include_functions', true);
	__add_action_once('after_setup_theme', '___include_functions', 0); // Highest priority.
}

/**
 * @return void
 */
function __local_enqueue($handle = '', $file = '', $deps = [], $in_footer_media = true){
	if(!file_exists($file)){
		return;
	}
	$mimes = [
		'css' => 'text/css',
		'js' => 'application/javascript',
	];
	$filename = wp_basename($file);
	$filetype = wp_check_filetype($filename, $mimes);
	if(!$filetype['type']){
		return '';
	}
	if(!$handle){
		$handle = wp_basename($filename, '.' . $filetype['ext']);
	}
	$src = __dir_to_url($file);
	$ver = filemtime($file);
	__enqueue($handle, $src, $deps, $ver, $in_footer_media);
}

/**
 * @return string
 */
function __localize($data = []){
	if(is_string($data)){
		$data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
	} else {
		foreach((array) $data as $key => $value){
			if(!is_scalar($value)){
				continue;
			}
			$data[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
		}
	}
	return wp_json_encode($data);
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return void
 */
function ___enqueue_fa6(){
	$enqueue_fa6 = (string) __get_cache('enqueue_fa6', '');
	if(!$enqueue_fa6){
		return;
	}
	__enqueue('font-awesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . $enqueue_fa6 . '/css/all.min.css', [], $enqueue_fa6);
}

/**
 * @return void
 */
function ___enqueue_functions(){
	$enqueue_functions = (bool) __get_cache('enqueue_functions', false);
	if(!$enqueue_functions){
		return;
	}
	$handles = [];
	foreach(glob(plugin_dir_path(dirname(__FILE__)) . 'js/*.js') as $file){
		$name = wp_basename($file, '.js');
		$handle = __str_slug($name);
		$handles[] = $handle;
		__local_enqueue($handle, $file);
	}
	$handle = __str_slug();
    $deps = array_merge($handles, ['jquery', 'wp-hooks']);
    $file = plugin_dir_path(dirname(dirname(__FILE__))) . 'php/js/functions.js';
    __local_enqueue($handle, $file, $deps);
    wp_localize_script($handle, __str_prefix('l10n'), [
        'mu_plugins_url' => __dir_to_url(wp_normalize_path(WPMU_PLUGIN_DIR)),
        'plugins_url' => __dir_to_url(wp_normalize_path(WP_PLUGIN_DIR)),
        'site_url' => site_url(),
    ]);
}

/**
 * @return void
 */
function ___enqueue_inputmask(){
	$enqueue_inputmask = (string) __get_cache('enqueue_inputmask', '');
	if(!$enqueue_inputmask){
		return;
	}
	__enqueue('jquery-inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/' . $enqueue_inputmask . '/jquery.inputmask.min.js', ['jquery'], $enqueue_inputmask);
}

/**
 * @return void
 */
function ___enqueue_stylesheet(){
	$enqueue_stylesheet = (bool) __get_cache('enqueue_stylesheet', false);
	if(!$enqueue_stylesheet){
		return;
	}
	$file = get_stylesheet_directory() . '/style.css';
	$ver = filemtime($file);
	__enqueue(get_stylesheet(), get_stylesheet_uri(), [], $ver);
}

/**
 * @return void
 */
function ___include_functions(){
	$include_functions = (bool) __get_cache('include_functions', false);
	if(!$include_functions){
		return;
	}
	___include_theme_functions();
}
