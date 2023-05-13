<?php

/**
 * @return void
 */
function __die($message = '', $title = ''){
	if(is_wp_error($message)){
		$message = $message->get_error_message();
	}
	if(!$message){
		$message = __('Error');
	}
	if(!$title){
		$title = __('Something went wrong.');
	}
	$hook_name = __prefix('pre_exit_with_error');
	__do($hook_name, $message, $title);
	$html = '<h1>' . $title . '</h1>';
	$html .= '<p>';
	$html .= rtrim($message, '.') . '.';
	$referer = wp_get_referer();
	if($referer){
		$back = __('Go back');
		$html_link = sprintf('<a href="%s">%s</a>', esc_url($referer), $back);
	} else {
		$back = sprintf(_x('&larr; Go to %s', 'site'), get_bloginfo('title', 'display'));
		$back = str_replace('&larr;', '', $back);
		$back = trim($back);
		$html_link = sprintf('<a href="%s">%s</a>', esc_url(home_url('/')), $back);
	}
	$html .= ' ' . $html_link . '.';
	$html .= '</p>';
	wp_die($html);
}

/**
 * @return WP_Error
 */
function __error($message = '', $data = ''){
	if(is_wp_error($message)){
		$data = $message->get_error_data();
		$message = $message->get_error_message();
	}
	if(empty($message)){
		$message = __('Something went wrong.');
	}
	$code = __prefix('error');
	return new \WP_Error($code, $message, $data);
}

/**
 * @return void
 */
function __exit(...$args){
	__die(...$args); // Alias for __die for backward compatibility.
}

/**
 * @return void
 */
function __exit_with_error(...$args){
	__die(...$args); // Alias for __die for backward compatibility.
}

/**
 * @return bool|WP_Error
 */
function __is_error($error = []){
	if(is_wp_error($error)){
		return $error;
	}
	if(!__array_keys_exist(['code', 'data', 'message'], $error)){
		return false;
	}
	if(4 === count($error)){
		if(!array_key_exists('additional_errors', $error)){
			return false;
		}
	} else {
		if(3 !== count($error)){
			return false;
		}
	}
	if(!$error['code'] or !$error['message']){
		return false;
	}
	return new \WP_Error($error['code'], $error['message'], $error['data']);
}
