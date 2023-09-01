<?php

/**
 * @return void
 */
function __add_admin_notice($message = '', $class = 'warning', $is_dismissible = false){
	$html = __admin_notice_html($message, $class, $is_dismissible);
	$md5 = md5($html);
	__set_array_cache('admin_notices', $md5, $html);
	__add_action_once('admin_notices', '__maybe_add_admin_notices');
}

/**
 * @return string
 */
function __admin_notice_html($message = '', $class = 'warning', $is_dismissible = false){
	if(!in_array($class, ['error', 'info', 'success', 'warning'])){
		$class = 'warning';
	}
	if($is_dismissible){
		$class .= ' is-dismissible';
	}
	return '<div class="notice notice-' . $class . '"><p>' . $message . '</p></div>';
}

/**
 * @return void
 */
function __maybe_add_admin_notices(){
    $admin_notices = (array) __get_cache('admin_notices', []);
	if(!$admin_notices){
		return;
	}
	foreach($admin_notices as $md5 => $admin_notice){
		echo $admin_notice;
	}
}
