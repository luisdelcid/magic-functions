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
