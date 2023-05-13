<?php

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
 * @return string
 */
function __validate_redirect_to($url = ''){
	$redirect_to = isset($_REQUEST['redirect_to']) ? wp_http_validate_url($_REQUEST['redirect_to']) : false;
	if(!$redirect_to){
		$redirect_to = wp_http_validate_url($url);
	}
	return (string) $redirect_to;
}
