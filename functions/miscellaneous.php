<?php

/**
 * @return string
 */
function __caller_file(){
	$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
	if(2 > count($debug)){
		return '';
	}
	return $debug[1]['file'];
}

/**
 * @return null|void|WP_Role
 */
function __clone_role($source = '', $destination = '', $display_name = ''){
	$role = get_role($source);
	if(is_null($role)){
		return null;
	}
	$destination = __canonicalize($destination);
	return add_role($destination, $display_name, $role->capabilities);
}

/**
 * @return bool
 */
function __current_screen_in($ids = []){
	global $current_screen;
	if(!is_array($ids)){
		return false;
	}
	if(!isset($current_screen)){
		return false;
	}
	return in_array($current_screen->id, $ids);
}

/**
 * @return bool
 */
function __current_screen_is($id = ''){
	global $current_screen;
	if(!is_string($id)){
		return false;
	}
	if(!isset($current_screen)){
		return false;
	}
	return ($current_screen->id === $id);
}

/**
 * @return bool|WP_Error
 */
function __custom_login_logo($attachment_id = 0, $half = true){
	if(!wp_attachment_is_image($attachment_id)){
		return __error(__('File is not an image.'));
	}
	$custom_logo = wp_get_attachment_image_src($attachment_id, 'medium');
	$height = $custom_logo[2];
	$width = $custom_logo[1];
	if($width > 300){ // Fix for SVG.
		$r = 300 / $width;
		$width = 300;
		$height *= $r;
	}
	if($half){
		$height = $height / 2;
		$width = $width / 2;
	}
	$custom_login_logo = [$custom_logo[0], $width, $height];
    __set_cache('custom_login_logo', $custom_login_logo);
    __one('login_enqueue_scripts', '__maybe_replace_login_logo');
	return true;
}

/**
 * @return string
 */
function __format_function($function_name = '', $args = []){
	$str = '<div style="color: #24831d; font-family: monospace; font-weight: 400;">' . $function_name . '(';
	$function_args = [];
	foreach($args as $arg){
		$arg = shortcode_atts([
			'default' => 'null',
			'name' => '',
			'type' => '',
		], $arg);
		if($arg['default'] and $arg['name'] and $arg['type']){
			$function_args[] = '<span style="color: #cd2f23; font-family: monospace; font-style: italic; font-weight: 400;">' . $arg['type'] . '</span> <span style="color: #0f55c8; font-family: monospace; font-weight: 400;">$' . $arg['name'] . '</span> = <span style="color: #000; font-family: monospace; font-weight: 400;">' . $arg['default'] . '</span>';
		}
	}
	if($function_args){
		$str .= ' ' . implode(', ', $function_args) . ' ';
	}
	$str .= ')</div>';
	return $str;
}

/**
 * @return void
 */
function __include_theme_functions(){
	__set_cache('include_theme_functions', true);
	__one('after_setup_theme', '__maybe_include_theme_functions');
}

/**
 * @return bool
 */
function __is_doing_heartbeat(){
	return (wp_doing_ajax() and isset($_POST['action']) and 'heartbeat' === $_POST['action']);
}

/**
 * @return bool
 */
function __is_false($data = ''){
	return in_array((string) $data, ['0', 'false', 'off'], true);
}

/**
 * @return bool
 */
function __is_true($data = ''){
	return in_array((string) $data, ['1', 'on', 'true'], true);
}

/**
 * @return void
 */
function __local_login_header(){
    __set_cache('local_login_header', true);
    __one('login_headertext', '__maybe_local_login_headertext');
    __one('login_headerurl', '__maybe_local_login_headerurl');
}

/**
 * @return void
 */
function __maybe_include_theme_functions(){
    $include_theme_functions = (bool) __get_cache('include_theme_functions', false);
	if(!$include_theme_functions){
		return;
	}
	$file = get_stylesheet_directory() . '/' . __prefix('functions.php');
	if(!file_exists($file)){
		return;
	}
	require_once($file);
}

/**
 * @return string
 */
function __maybe_local_login_headertext($login_header_text){
	$local_login_header = (bool) __get_cache('local_login_header', false);
	if(!$local_login_header){
        return $login_header_text;
	}
	return get_option('blogname');
}

/**
 * @return string
 */
function __maybe_local_login_headerurl($login_header_url){
    $local_login_header = (bool) __get_cache('local_login_header', false);
	if(!$local_login_header){
        return $login_header_url;
	}
    return home_url();
}

/**
 * @return string
 */
function __maybe_replace_login_logo(){
    $custom_login_logo = (array) __get_cache('custom_login_logo', []);
    if(!$custom_login_logo){
        return;
    } ?>
	<style type="text/css">
		#login h1 a,
		.login h1 a {
			background-image: url(<?php echo $custom_login_logo[0]; ?>);
			background-size: <?php echo $custom_login_logo[1]; ?>px <?php echo $custom_login_logo[2]; ?>px;
			height: <?php echo $custom_login_logo[2]; ?>px;
			width: <?php echo $custom_login_logo[1]; ?>px;
		}
	</style><?php
}

/**
 * @return void
 */
function __test(){
	__die('Hello, World!');
}

/**
 * @return string
 */
function __validate_redirect_to($url = ''){
	$redirect_to = isset($_REQUEST['redirect_to']) ? wp_http_validate_url($_REQUEST['redirect_to']) : false;
	if(!$redirect_to){
		$redirect_to = wp_http_validate_url($url);
	}
	return (string) $redirect_to;
}
