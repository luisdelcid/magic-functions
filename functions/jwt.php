<?php

/**
 * @return stdClass|WP_Error
 */
function __jwt_decode(...$args){
	$remote_lib = __use_jwt();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return \Firebase\JWT\JWT::decode(...$args);
}

/**
 * @return string|WP_Error
 */
function __jwt_encode(...$args){
	$remote_lib = __use_jwt();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return \Firebase\JWT\JWT::encode(...$args);
}

/**
 * @return bool|WP_Error
 */
function __use_jwt($preferred_version = '5.5.1'){
	$class = 'Firebase\JWT\JWT';
	if(class_exists($class)){
		return true;
	}
	$hook_name = __prefix('jwt_preferred_version');
	$preferred_version = __filter($hook_name, $preferred_version);
	$dir = __remote_lib('https://github.com/firebase/php-jwt/archive/refs/tags/v' . $preferred_version . '.zip', 'php-jwt-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$src = $dir . '/src';
	if(!file_exists($src)){
		return __error(__('File doesn&#8217;t exist?'), $src);
	}
	$files = [
		$src . '/BeforeValidException.php',
		$src . '/ExpiredException.php',
		$src . '/JWK.php',
		$src . '/JWT.php',
		$src . '/Key.php',
		$src . '/SignatureInvalidException.php',
	];
	foreach($files as $file){
		require_once($file);
	}
	return class_exists($class);
}
