<?php

/**
 * @return bool|WP_Error|WP_User
 */
function __maybe_authenticate_without_password($user, $username_or_email, $password){
	if(!is_null($user)){
		return $user;
	}
	if($password){
		return __error(__('The link you followed has expired.'));
	}
	$user = false; // Returning a non-null value will effectively short-circuit the user authentication process.
	if(username_exists($username_or_email)){
		$user = get_user_by('login', $username_or_email);
	} elseif(is_email($username_or_email) and email_exists($username_or_email)){
		$user = get_user_by('email', $username_or_email);
	}
	return $user;
}

/**
 * @return bool
 */
function __maybe_wordfence_ls_disable_captcha($required){
	$required = false;
	return $required; // Alias for __return_false. Try to prevent conflicts with other functions and plugins that rely on the same hook.
}

/**
 * @return WP_Error|WP_User
 */
function __signon($username_or_email = '', $password = '', $remember = false){
	if(is_user_logged_in()){
		return wp_get_current_user();
	}
    add_filter('wordfence_ls_require_captcha', '__maybe_wordfence_ls_disable_captcha');
    $user = wp_signon([
        'remember' => $remember,
        'user_login' => $username_or_email,
        'user_password' => $password,
    ]);
    remove_filter('wordfence_ls_require_captcha', '__maybe_wordfence_ls_disable_captcha');
    if(is_wp_error($user)){
        return $user;
    }
    return wp_set_current_user($user->ID);
}

/**
 * @return WP_Error|WP_User
 */
function __signon_without_password($username_or_email = '', $remember = false){
	if(is_user_logged_in()){
		return wp_get_current_user();
	}
    add_filter('authenticate', '__maybe_authenticate_without_password', 10, 3);
    add_filter('wordfence_ls_require_captcha', '__maybe_wordfence_ls_disable_captcha');
    $user = wp_signon([
        'remember' => $remember,
        'user_login' => $username_or_email,
        'user_password' => '',
    ]);
    remove_filter('wordfence_ls_require_captcha', '__maybe_wordfence_ls_disable_captcha');
    remove_filter('authenticate', '__maybe_authenticate_without_password');
    if(is_wp_error($user)){
        return $user;
    }
    return wp_set_current_user($user->ID);
}
