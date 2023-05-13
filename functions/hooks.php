<?php

/**
 * @return string
 */
function __current(){
    return current_filter(); // Alias for current_action and current_filter.
}

/**
 * @return bool
 */
function __did($hook_name = ''){
    return did_action($hook_name) or did_filter($hook_name); // Alias for did_action and did_filter.
}

/**
 * @return void
 */
function __do($hook_name = '', ...$arg){
    do_action($hook_name, ...$arg); // Alias for do_action.
}

/**
 * @return bool
 */
function __doing($hook_name = ''){
    return doing_filter($hook_name); // Alias for doing_action and doing_filter.
}

/**
 * @return mixed
 */
function __filter($hook_name = '', $value = null, ...$arg){
    return apply_filters($hook_name, $value, ...$arg); // Alias for apply_filters.
}

/**
 * @return bool
 */
function __has($hook_name = '', $callback = null){
    return has_filter($hook_name, $callback); // Alias for has_action and has_filter.
}

/**
 * @return bool
 */
function __off($hook_name = '', $callback = null, $priority = 10){
    return remove_filter($hook_name, $callback, $priority); // Alias for remove_action and remove_filter.
}

/**
 * @return string
 */
function __on($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
    $idx = _wp_filter_build_unique_id($hook_name, $callback, $priority);
    add_filter($hook_name, $callback, $priority, $accepted_args); // Alias for add_action and add_filter.
    return $idx;
}

/**
 * @return string
 */
function __one($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
	$hooks = (array) __get_cache('hooks', []);
    if(!array_key_exists($hook_name, $hooks)){
        $hooks[$hook_name] = [];
    }
    $idx = _wp_filter_build_unique_id($hook_name, $callback, $priority);
    $md5 = md5($idx);
    if($callback instanceof \Closure){
        $md5_closure = __md5_closure($callback);
        if(!is_wp_error($md5_closure)){
            $md5 = $md5_closure;
        }
    }
    if(array_key_exists($md5, $hooks[$hook_name])){
        return $hooks[$hook_name][$md5]; // $idx
    }
    add_filter($hook_name, $callback, $priority, $accepted_args); // Alias for add_action and add_filter.
    $hooks[$hook_name][$md5] = $idx;
    __set_cache('hooks', $hooks);
    return $idx;
}
