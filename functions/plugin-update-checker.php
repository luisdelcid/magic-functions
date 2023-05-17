<?php

/**
 * @return Puc_v4p13_Plugin_UpdateChecker|Puc_v4p13_Theme_UpdateChecker|Puc_v4p13_Vcs_BaseChecker|WP_Error
 */
function __build_update_checker(...$args){
	$remote_lib = __use_plugin_update_checker();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return \Puc_v4_Factory::buildUpdateChecker(...$args);
}

/**
 * @return void
 */
function __check_for_updates(...$args){
	if(__did('plugins_loaded')){
		__build_update_checker(...$args);
	} else {
        $md5 = __md5($args);
        $check_for_updates = (array) __get_cache('check_for_updates', []);
        $check_for_updates[$md5] = $args;
        __set_cache('check_for_updates', $check_for_updates);
        __one('plugins_loaded', '__maybe_build_update_checkers');
	}
}

/**
 * @return void
 */
function __maybe_build_update_checkers(){
	$check_for_updates = (array) __get_cache('check_for_updates', []);
    if(!$check_for_updates){
		return;
	}
    foreach($check_for_updates as $check_for_update){
        __build_update_checker(...$check_for_update);
    }
}

/**
 * @return void
 */
function __set_update_license($slug = '', $license = ''){
	if(!$slug or !$license){
		return;
	}
    $update_licenses = (array) __get_cache('update_licenses', []);
	if(isset($update_licenses[$slug])){
		return;
	}
	$update_licenses[$slug] = $license;
    __set_cache('update_licenses', $update_licenses);
	// Closure. The slug must be passed to the use language construct.
	add_filter('puc_request_info_query_args-' . $slug, function($queryArgs) use($slug){
        $update_licenses = (array) __get_cache('update_licenses', []);
		if(!isset($update_licenses[$slug])){
			return $queryArgs;
		}
		$queryArgs['license'] = $update_licenses[$slug];
		return $queryArgs;
	});
}

/**
 * @return bool|WP_Error
 */
function __use_plugin_update_checker($preferred_version = '4.13'){
	$class = 'Puc_v4_Factory';
	if(class_exists($class)){
		return true;
	}
    $hook_name = __prefix('plugin_update_checker_preferred_version');
	$preferred_version = __filter($hook_name, $preferred_version);
	$dir = __remote_lib('https://github.com/YahnisElsts/plugin-update-checker/archive/refs/tags/v' . $preferred_version . '.zip', 'plugin-update-checker-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/plugin-update-checker.php';
	if(!file_exists($file)){
		return $this->error(__('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	return class_exists($class);
}
