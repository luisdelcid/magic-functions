<?php

/**
 * @return void
 */
function __hide_recaptcha_badge(){
	__set_cache('hide_recaptcha_badge', true);
	__add_action_once('wp_head', '___hide_recaptcha_badge');
}

/**
 * @return bool|string
 */
function __is_google_workspace($email = ''){
	if(!is_email($email)){
		return false;
	}
	list($local, $domain) = explode('@', $email, 2);
	if('gmail.com' === strtolower($domain)){
		return 'gmail.com';
	}
	if(!getmxrr($domain, $mxhosts)){
		return false;
	}
	if(!in_array('aspmx.l.google.com', $mxhosts)){
		return false;
	}
	return $domain;
}

/**
 * @return string
 */
function __recaptcha_branding(){
	return 'This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a> apply.';
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return void
 */
function ___hide_recaptcha_badge(){
	$hide_recaptcha_badge = (bool) __get_cache('hide_recaptcha_badge', false);
	if(!$hide_recaptcha_badge){
		return;
	} ?>
    <style type="text/css">
        .grecaptcha-badge {
            visibility: hidden !important;
        }
    </style><?php
}
