<?php

/**
 * @return void
 */
function __admin_search_post_meta(){
	__set_cache('admin_search_post_meta', true);
	__add_filter_once('posts_groupby', '__posts_groupby', 10, 2);
    __add_filter_once('posts_join', '__posts_join', 10, 2);
    __add_filter_once('posts_where', '__posts_where', 10, 2);
}

/**
 * @return void
 */
function __admin_search_user_meta(){
	__set_cache('admin_search_user_meta', true);
    __add_filter_once('users_pre_query', '__users_pre_query', 10, 2);
}

/**
 * @return string
 */
function __posts_groupby($groupby, $query){
	global $pagenow, $wpdb;
    $admin_search_post_meta = (bool) __get_cache('admin_search_post_meta', false);
	if(!$admin_search_post_meta){
		return $groupby;
	}
    if(!is_admin() or !is_search() or 'edit.php' !== $pagenow){
        return $groupby;
    }
    $g = $wpdb->posts . '.ID';
    if(!$groupby){
        $groupby = $g;
    } else {
        $groupby = trim($groupby) . ', ' . $g;
    }
	return $groupby;
}

/**
 * @return string
 */
function __posts_join($join, $query){
    global $pagenow, $wpdb;
    $admin_search_post_meta = (bool) __get_cache('admin_search_post_meta', false);
	if(!$admin_search_post_meta){
		return $join;
	}
    if(!is_admin() or !is_search() or 'edit.php' !== $pagenow){
        return $join;
    }
    $j = 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id';
    if(!$join){
        $join = $j;
    } else {
        $join = trim($join) . ' ' . $j;
    }
    return $join;
}

/**
 * @return string
 */
function __posts_where($where, $query){
    global $pagenow, $wpdb;
    $admin_search_post_meta = (bool) __get_cache('admin_search_post_meta', false);
	if(!$admin_search_post_meta){
		return $where;
	}
    if(!is_admin() or !is_search() or 'edit.php' !== $pagenow){
        return $where;
    }
    $s = get_query_var('s');
    $s = $wpdb->esc_like($s);
    $s = '%' . $s . '%';
    $str = '(' . $wpdb->posts . '.post_title LIKE %s)';
    $sql = $wpdb->prepare($str, $s);
    $search = $sql;
    $str = '(' . $wpdb->postmeta . '.meta_value LIKE %s)';
    $sql = $wpdb->prepare($str, $s);
    $replace = $search . ' OR ' . $sql;
    $where = str_replace($search, $replace, $where);
    return $where;
}

/**
 * @return array|null
 */
function __users_pre_query($results, $query){
	global $pagenow, $wpdb;
    $admin_search_user_meta = (bool) __get_cache('admin_search_user_meta', false);
	if(!$admin_search_user_meta){
		return $results;
	}
    $search = $query->get('search');
    if(!is_admin() or !$search or 'users.php' !== $pagenow or null !== $query->results){
        return $results;
    }
    $j = 'LEFT JOIN ' . $wpdb->usermeta . ' ON ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id';
    $query->query_from .= ' ' . $j;
    $s = $search;
    $s = str_replace('*', '%', $s);
    $str = 'user_login LIKE %s';
    $sql = $wpdb->prepare($str, $s);
    $search = $sql;
    $str = 'meta_value LIKE %s';
    $sql = $wpdb->prepare($str, $s);
    $replace = $search . ' OR ' . $sql;
    $query->query_where = str_replace($search, $replace, $query->query_where);
    $query->query_where .= ' GROUP BY ID';
	return $results;
}
