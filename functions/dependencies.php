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
			$in_footer = $in_footer_media;
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
function __enqueue_fa6($preferred_version = '6.4.0'){
	$hook_name = __prefix('fa6_preferred_version');
	$preferred_version = __filter($hook_name, $preferred_version);
	__set_cache('enqueue_fa6', $preferred_version);
	__one('wp_enqueue_scripts', '__maybe_enqueue_scripts');
}

/**
 * @return void
 */
function __enqueue_functions(){
	__set_cache('enqueue_functions', true);
	__one('admin_enqueue_scripts', '__maybe_enqueue_functions');
	__one('login_enqueue_scripts', '__maybe_enqueue_functions');
	__one('wp_enqueue_scripts', '__maybe_enqueue_functions');
}

/**
 * @return void
 */
function __enqueue_inputmask($preferred_version = '5.0.8'){
	$hook_name = __prefix('inputmask_preferred_version');
	$preferred_version = __filter($hook_name, $preferred_version);
	__set_cache('enqueue_inputmask', $preferred_version);
	__one('wp_enqueue_scripts', '__maybe_enqueue_scripts');
}

/**
 * @return void
 */
function __enqueue_stylesheet(){
	__set_cache('enqueue_stylesheet', true);
	__one('wp_enqueue_scripts', '__maybe_enqueue_scripts');
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

/**
 * @return void
 */
function __maybe_enqueue_functions(){
	$enqueue_functions = (bool) __get_cache('enqueue_functions', false);
	if(!$enqueue_functions){
		return;
	}
	$handle = __prefix('functions');
	$file = plugin_dir_path(__FILE__) . 'functions.js';
	__local_enqueue($handle, $file, ['jquery', 'wp-hooks']);
	$object_name = __prefix('object');
	$mu_plugin_dir = wp_normalize_path(WPMU_PLUGIN_DIR);
	$plugin_dir = wp_normalize_path(WP_PLUGIN_DIR);
	$site_url = site_url();
	$l10n = [
		'mu_plugins_url' => __dir_to_url($mu_plugin_dir),
		'plugins_url' => __dir_to_url($plugin_dir),
		'site_url' => $site_url,
	];
    wp_localize_script($handle, $object_name, $l10n);
}

/**
 * @return void
 */
function __maybe_enqueue_scripts(){
	$enqueue_stylesheet = (bool) __get_cache('enqueue_stylesheet', false);
	if($enqueue_stylesheet){
		$file = get_stylesheet_directory() . '/style.css';
		$ver = filemtime($file);
		__enqueue(get_stylesheet(), get_stylesheet_uri(), [], $ver);
	}
	$enqueue_fa6 = (string) __get_cache('enqueue_fa6', '');
	if($enqueue_fa6){
		__enqueue('font-awesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . $enqueue_fa6 . '/css/all.min.css', [], $enqueue_fa6);
	}
	$enqueue_inputmask = (string) __get_cache('enqueue_inputmask', '');
	if($enqueue_inputmask){
		__enqueue('jquery-inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/' . $enqueue_inputmask . '/jquery.inputmask.min.js', ['jquery'], $enqueue_inputmask);
	}

}
