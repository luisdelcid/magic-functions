<?php

/**
 * @return string
 */
function __add_action($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
    return __add_filter($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __add_action_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
    return __add_filter_once($hook_name, $callback, $priority, $accepted_args);
}

/**
 * @return string
 */
function __add_filter($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
    $idx = _wp_filter_build_unique_id($hook_name, $callback, $priority);
    add_filter($hook_name, $callback, $priority, $accepted_args);
    return $idx;
}

/**
 * @return string
 */
function __add_filter_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
    $idx = _wp_filter_build_unique_id($hook_name, $callback, $priority);
    $md5 = md5($idx);
    if($callback instanceof \Closure){
        $md5_closure = __md5_closure($callback);
        if(!is_wp_error($md5_closure)){
            $md5 = $md5_closure;
        }
    }
    if(__isset_array_cache('hooks', $md5)){
        return __set_array_cache('hooks', $md5); // $idx
    }
    add_filter($hook_name, $callback, $priority, $accepted_args);
    __set_array_cache('hooks', $md5, $idx);
    return $idx;
}
