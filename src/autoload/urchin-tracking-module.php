<?php

/**
 * @return array
 */
function __current_utm(){
    $keys = __get_utm_keys();
    $utm = [];
	foreach($keys as $key){
        if(isset($_GET[$key])){
			$utm[$key] = $_GET[$key];
            continue;
		}
        $prefixed_key = __prefix($key . '_' . COOKIEHASH);
        if(isset($_COOKIE[$prefixed_key])){
			$utm[$key] = $_COOKIE[$prefixed_key];
            continue;
		}
        $utm[$key] = '';
    }
    return $utm;
}

/**
 * @return void
 */
function __enable_utm(){
    __add_action_once('after_setup_theme', '__maybe_set_utm');
    __add_action_once('wp_enqueue_scripts', '__maybe_enqueue_utm');
    __set_cache('is_utm_enabled', true);
}

/**
 * @return array
 */
function __get_utm(){
	$utm = [
		'utm_campaign' => 'Name',
		'utm_content' => 'Content',
		'utm_id' => 'ID',
		'utm_medium' => 'Medium',
		'utm_source' => 'Source',
		'utm_term' => 'Term',
	];
	return $utm;
}

/**
 * @return array
 */
function __get_utm_keys(){
	$utm = __get_utm();
	return array_keys($utm);
}

/**
 * @return bool
 */
function __is_utm_enabled(){
    return (bool) __get_cache('is_utm_enabled', false);
}

/**
 * @return void
 */
function __maybe_enqueue_utm(){
    if(!__is_utm_enabled()){
        return;
    }
    $current_utm = __current_utm();
    $query_string = build_query($current_utm);
    $current_utm['utm_md5'] = md5($query_string);
    $current_utm['utm_query'] = $query_string;
	ksort($current_utm);
    $object_name = __prefix('utm');
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', $object_name, $current_utm);
}

/**
 * @return void
 */
function __maybe_set_utm(){
    if(!__is_utm_enabled()){
        return;
    }
	$at_least_one = false;
	$keys = __get_utm_keys();
	foreach($keys as $key){
		if(isset($_GET[$key])){
			$at_least_one = true;
			break;
		}
	}
	if(!$at_least_one){
		return;
	}
	__unset_utm_parameters();
	$cookie_lifetime = time() + WEEK_IN_SECONDS;
	$secure = ('https' === parse_url(home_url(), PHP_URL_SCHEME));
	foreach($keys as $key){
		if(!isset($_GET[$key])){
			continue;
		}
        $prefixed_key = __prefix($key . '_' . COOKIEHASH);
		$value = wp_unslash($_GET[$key]);
		$value = esc_attr($value);
		setcookie($prefixed_key, $value, $cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure);
	}
}

/**
 * @return void
 */
function __unset_utm_parameters(){
	$past = time() - YEAR_IN_SECONDS;
    $keys = __get_utm_keys();
	foreach($keys as $key){
        $prefixed_key = __prefix($key . '_' . COOKIEHASH);
		if(!isset($_COOKIE[$prefixed_key])){
			continue;
		}
		setcookie($prefixed_key, ' ', $past, COOKIEPATH, COOKIE_DOMAIN);
	}
}
