<?php

/**
 * @return mixed
 */
function __get_cache($key = '', $default = null){
	$group = __prefix(false);
	$value = wp_cache_get($key, $group, false, $found);
	if($found){
		return $value;
	}
    return $default;
}

/**
 * @return bool
 */
function __isset_cache($key = ''){
	$group = __prefix(false);
	$value = wp_cache_get($key, $group, false, $found);
    return $found;
}

/**
 * @return bool
 */
function __set_cache($key = '', $data = null){
	$group = __prefix(false);
	return wp_cache_set($key, $data, $group);
}

/**
 * @return bool
 */
function __unset_cache($key = ''){
	$group = __prefix(false);
	return wp_cache_delete($key, $group);
}
