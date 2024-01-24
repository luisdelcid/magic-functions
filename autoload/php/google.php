<?php

/**
 * This function MUST be called inside the 'wp_head' action hook.
 *
 * @return void
 */
function __hide_recaptcha_badge(){
	if(!doing_action('wp_head')){
        return;
    } ?>
    <style type="text/css">
        .grecaptcha-badge {
            visibility: hidden !important;
        }
    </style><?php
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
