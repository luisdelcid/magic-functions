<?php

/**
 * @return void
 */
function __maybe_tgmpa_register(){
	$tgmpa = (array) __get_cache('tgmpa', []);
	if(!$tgmpa){
		return;
	}
    foreach($tgmpa as $args){
        tgmpa($args['plugins'], $args['config']);
    }
}

/**
 * @return void
 */
function __tgmpa($plugins = [], $config = []){
	if(__did('tgmpa_register')){
		return;
	}
	$remote_lib = __use_tgm_plugin_activation();
	if(is_wp_error($remote_lib)){
		return;
	}
	$args = [
		'config' => $config,
		'plugins' => $plugins,
	];
	$md5 = __md5($args);
    $tgmpa = (array) __get_cache('tgmpa', []);
	$tgmpa[$md5] = $args;
    __set_cache('tgmpa', $tgmpa);
    __one('tgmpa_register', '__maybe_tgmpa_register');
}

/**
 * @return bool|WP_Error
 */
function __use_tgm_plugin_activation($preferred_version = '2.6.1'){
	$class = 'TGM_Plugin_Activation';
	if(class_exists($class)){
		return true;
	}
    $hook_name = __prefix('tgm_plugin_activation_preferred_version');
	$preferred_version = __filter($hook_name, $preferred_version);
	$dir = __remote_lib('https://github.com/TGMPA/TGM-Plugin-Activation/archive/refs/tags/' . $preferred_version . '.zip', 'TGM-Plugin-Activation-' . $preferred_version);
	if(is_wp_error($dir)){
		return $dir;
	}
	$file = $dir . '/class-tgm-plugin-activation.php';
	if(!file_exists($file)){
		return $this->error(__('File doesn&#8217;t exist?'), $file);
	}
	require_once($file);
	return class_exists($class);
}
