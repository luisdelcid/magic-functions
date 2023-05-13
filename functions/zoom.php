<?php

/**
 * @return string
 */
function __zoom_api_url($endpoint = ''){
	$base = 'https://api.zoom.us/v2';
	$endpoint = str_replace($base, '', $endpoint);
	$endpoint = ltrim($endpoint, '/');
	$endpoint = untrailingslashit($endpoint);
	$endpoint = trailingslashit($base) . $endpoint;
	return $endpoint;
}

/**
 * @return string|WP_Error
 */
function __zoom_auth($api_key = '', $api_secret = ''){
	$zoom_jwt = (string) __get_cache('zoom_jwt', '');
	if($zoom_jwt){
		return $zoom_jwt;
	}
	if(!$api_key or !$api_secret){
		$message = sprintf(__('Missing parameter(s): %s'), __implode_and(['Site Key', 'Secret Key'])) . '.';
		return __error($message);
	}
	$payload = [
		'exp' => time() + DAY_IN_SECONDS,
		'iss' => $api_key,
	];
	$zoom_jwt = __jwt_encode($payload, $api_secret);
	__set_cache('zoom_jwt', $zoom_jwt);
	return $zoom_jwt;
}

/**
 * @return array|WP_Error
 */
function __zoom_delete($endpoint = '', $args = [], $timeout = 30){
	return __zoom_request('DELETE', $endpoint, $args, $timeout);
}

/**
 * @return array|WP_Error
 */
function __zoom_get($endpoint = '', $args = [], $timeout = 30){
	return __zoom_request('GET', $endpoint, $args, $timeout);
}

/**
 * @return array|WP_Error
 */
function __zoom_patch($endpoint = '', $args = [], $timeout = 30){
	return __zoom_request('PATCH', $endpoint, $args, $timeout);
}

/**
 * @return array|WP_Error
 */
function __zoom_post($endpoint = '', $args = [], $timeout = 30){
	return __zoom_request('POST', $endpoint, $args, $timeout);
}

/**
 * @return array|WP_Error
 */
function __zoom_put($endpoint = '', $args = [], $timeout = 30){
	return __zoom_request('PUT', $endpoint, $args, $timeout);
}

/**
 * @return array|WP_Error
 */
function __zoom_request($method = '', $endpoint = '', $args = [], $timeout = 30){
	$jwt = __zoom_auth();
	if(is_wp_error($jwt)){
		return $jwt;
	}
	$url = __zoom_api_url($endpoint);
	if(!is_array($args)){
		$args = wp_parse_args($args);
	}
	$args = [
		'body' => $args,
		'headers' => [
			'Accept' => 'application/json',
			'Authorization' => 'Bearer ' . $jwt,
			'Content-Type' => 'application/json',
		],
		'timeout' => __sanitize_timeout($timeout),
	];
	return __remote_request($method, $url, $args);
}
