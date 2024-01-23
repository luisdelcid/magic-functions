<?php

/**
 * @return YahnisElsts\PluginUpdateChecker\v5p1\Plugin\UpdateChecker|YahnisElsts\PluginUpdateChecker\v5p1\Theme\UpdateChecker|YahnisElsts\PluginUpdateChecker\v5p1\Vcs\BaseChecker|WP_Error
 */
function __build_update_checker(...$args){
	$remote_lib = __use_plugin_update_checker();
	if(is_wp_error($remote_lib)){
		return $remote_lib;
	}
	return \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(...$args);
}

/**
 * @return void
 */
function __check_for_updates(...$args){
	if(did_action('plugins_loaded')){
		__build_update_checker(...$args);
	} else {
        $md5 = __md5($args);
		__set_array_cache('check_for_updates', $md5, $args);
        __add_action_once('plugins_loaded', '__maybe_build_update_checkers');
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
	if(__isset_array_cache('update_licenses', $slug)){
		return;
	}
	__set_array_cache('update_licenses', $slug, $license);
	// Closure. The slug must be passed to the use language construct.
	add_filter('puc_request_info_query_args-' . $slug, function($queryArgs) use($slug){
		if(!__isset_array_cache('update_licenses', $slug)){
			return $queryArgs;
		}
		$queryArgs['license'] = (string) __get_array_cache('update_licenses', $slug, '');
		return $queryArgs;
	});
}

/**
 * @return bool|WP_Error
 */
function __use_plugin_update_checker($preferred_version = '5.2'){
	$class = 'YahnisElsts\PluginUpdateChecker\v5\PucFactory';
	if(class_exists($class)){
		return true;
	}
	$dir = __remote_lib('https://github.com/YahnisElsts/plugin-update-checker/archive/refs/tags/v' . $preferred_version . '.zip', 'plugin-update-checker-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/plugin-update-checker.php';
	if(!file_exists($file)){
		return __error(__('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	return class_exists($class);
}
