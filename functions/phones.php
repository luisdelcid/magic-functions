<?php

/**
 * @return bool|WP_User
 */
function __get_user_by_phone($phone = ''){
	$user_id = __phone_exists($phone);
	if(!$user_id){
		return false;
	}
	return get_userdata($user_id);
}

/**
 * @return bool|string
 */
function __is_e164($phone = ''){
	if(!preg_match('/^\+[1-9]\d{1,14}$/', $phone)){
		return false;
	}
	return $phone;
}

/**
 * @return bool|int
 */
function __phone_exists($phone = ''){
	$phone = __sanitize_phone($phone);
	if(!$phone){
		return false;
	}
	$phone = ltrim($phone, '+');
	return username_exists($phone);
}

/**
 * @return string
 */
function __sanitize_phone($phone = ''){
	$phone = preg_replace('%[()/.*#\s-]+%', '', $phone);
	$phone = '+' . ltrim($phone, '+');
	$e164 = __is_e164($phone);
	if(!$e164){
		return '';
	}
	return $e164;
}
