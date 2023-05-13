<?php

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
function __validate_redirect_to($url = ''){
	$redirect_to = isset($_REQUEST['redirect_to']) ? wp_http_validate_url($_REQUEST['redirect_to']) : false;
	if(!$redirect_to){
		$redirect_to = wp_http_validate_url($url);
	}
	return (string) $redirect_to;
}
