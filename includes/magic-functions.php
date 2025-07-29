<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Hardcoded
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cache_sanitize_group')){
    /**
     * This function’s access is marked private.
     *
	 * @return string
	 */
	function __cache_sanitize_group($group = ''){
        $global_key = 'magic-cache'; // Hardcoded.
        if(!isset($GLOBALS[$global_key]) || !is_array($GLOBALS[$global_key])){
            $GLOBALS[$global_key] = [];
        }
        if(!$group || !is_string($group)){
            $group = 'default';
        }
		$group = __str_prefix($group);
        if(!isset($GLOBALS[$global_key][$group]) || !is_array($GLOBALS[$global_key][$group])){
            $GLOBALS[$global_key][$group] = [];
        }
        return $group;
	}
}

if(!function_exists('__dir')){
	/**
	 * @return string|WP_Error
	 */
	function __dir($subdir = ''){
        $target = 'magic-uploads'; // Hardcoded.
	    $subdir = untrailingslashit(ltrim($subdir, '/'));
	    if($subdir){
	        $target .= '/' . $subdir;
	    }
		return __upload_dir($target);
	}
}

if(!function_exists('__str_prefix')){
	/**
	 * @return string
	 */
	function __str_prefix($str = '', $prefix = ''){
        if($prefix){
            $prefix = str_replace('\\', '_', $prefix); // Fix namespaces.
    		$prefix = __canonicalize($prefix);
    		$prefix = rtrim($prefix, '_');
        }
		if(!$prefix){
			$prefix = 'magic_functions'; // Hardcoded.
		}
		$str = sanitize_text_field($str);
		if(!$str){
			return $prefix;
		}
		if(str_starts_with($str, $prefix)){
			return $str; // Text is already prefixed.
		}
		return $prefix . '_' . $str;
	}
}

if(!function_exists('__str_slug')){
	/**
	 * @return string
	 */
	function __str_slug($str = '', $slug = ''){
        if($slug){
            $slug = str_replace('_', '-', $slug); // Fix canonicalized.
    		$slug = str_replace('\\', '-', $slug); // Fix namespaces.
    		$slug = sanitize_title($slug);
    		$slug = rtrim($slug, '-');
        }
		if(!$slug){
			$slug = 'magic-functions'; // Hardcoded.
		}
		$str = sanitize_text_field($str);
		if(!$str){
			return $slug;
		}
		if(str_starts_with($str, $slug)){
			return $str; // Text is already slugged.
		}
		return $slug . '-' . $str;
	}
}

if(!function_exists('__enqueue_dependencies')){
	/**
	 * This function’s access is marked private.
	 *
	 * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
	 *
	 * @return void
	 */
	function __enqueue_dependencies(){
		if(!doing_action('admin_enqueue_scripts') && !doing_action('login_enqueue_scripts') && !doing_action('wp_enqueue_scripts')){
            // Too early or too late.
            return;
        }
        $handle = __handle();
        $ver = __get_plugin_meta('Version');
        $file = plugin_dir_path(__FILE__) . 'magic-functions.css'; // Hardcoded.
        if(file_exists($file)){
            $src = __path_to_url($file);
            if(is_wp_error($ver)){
                $ver = filemtime($file);
            }
            wp_enqueue_style($handle, $src, [], $ver);
        }
		$file = plugin_dir_path(__FILE__) . 'magic-functions.js'; // Hardcoded.
		if(file_exists($file)){
            wp_enqueue_script('stackframe', 'https://cdn.jsdelivr.net/npm/stackframe@1.3.4/stackframe.min.js', [], '1.3.4');
            wp_enqueue_script('error-stack-parser', 'https://cdn.jsdelivr.net/npm/error-stack-parser@2.1.4/error-stack-parser.min.js', ['stackframe'], '2.1.4');
            $src = __path_to_url($file);
            $deps = ['error-stack-parser', 'jquery', 'underscore', 'utils', 'wp-api', 'wp-hooks'];
            if(is_wp_error($ver)){
                $ver = filemtime($file);
            }
            wp_enqueue_script($handle, $src, $deps, $ver);
            $object_name = __str_prefix('l10n');
            $l10n = [
                'mu_plugins_url' => WPMU_PLUGIN_URL,
                'plugins_url' => WP_PLUGIN_URL,
                'site_url' => site_url(),
            ];
            wp_localize_script($handle, $object_name, $l10n);
        }
	}
}

if(!function_exists('__handle')){
	/**
     * This function’s access is marked private.
     *
	 * @return string
	 */
	function __handle(){
	    return 'magic-functions'; // Hardcoded.
	}
}

if(!function_exists('__include_theme_functions')){
	/**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'after_setup_theme' action hook.
	 *
	 * @return void
	 */
	function __include_theme_functions(){
		if(!doing_action('after_setup_theme')){
            // Too early or too late.
	        return;
	    }
		$filename = 'magic-functions.php'; // Hardcoded.
        foreach(wp_get_active_and_valid_themes() as $theme){
        	if(file_exists($theme . '/' . $filename)){
        		include_once $theme . '/' . $filename; // Load the functions for the active theme, for both parent and child theme if applicable.
        	}
        }
	}
}

if(!function_exists('__shortinit')){
	/**
     * This function’s access is marked private.
     *
	 * @return string
	 */
	function __shortinit(){
	    return plugin_dir_path(__FILE__) . 'shortinit'; // Hardcoded.
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Ace (Ajax.org Cloud9 Editor)
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__enqueue_ace')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return void
     */
	function __enqueue_ace($deps = []){
        if(!doing_action('admin_enqueue_scripts') && !doing_action('login_enqueue_scripts') && !doing_action('wp_enqueue_scripts')){
            // Too early or too late.
            return;
        }
        // Just in time.
        $dir = __use_ace();
        if(is_wp_error($dir)){
            return; // Silence is golden.
        }
        $base_path = __path_to_url($dir) . '/src-min';
        $ver = '1.43.1'; // Hardcoded.
        __enqueue('ace', $base_path . '/ace.js', $deps, $ver);
		__enqueue('ace-language-tools', $base_path . '/ext-language_tools.js', ['ace'], $ver);
        $data = "_.isUndefined(ace)||(ace.config.set('basePath','$base_path'),ace.require('ace/ext/language_tools'))";
        wp_add_inline_script('ace-language-tools', $data);
	}
}

if(!function_exists('__use_ace')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_ace(){
        $ver = '1.43.1'; // Hardcoded.
        $url = 'https://github.com/ajaxorg/ace-builds/archive/refs/tags/v' . $ver . '.zip';
        return __use($url, [
			'expected_dir' => 'ace-builds-' . $ver,
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Admin notices
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_admin_notice')){
	/**
	 * @return void
	 */
	function __add_admin_notice($message = '', $type = '', $is_dismissible = false){
		if(doing_action('admin_notices')){
            // Just in time.
	        __echo_admin_notice($message, $type, $is_dismissible);
			return;
	    }
		if(did_action('admin_notices')){
            // Too late.
			return;
		}
        // Too early.
		$admin_notice = [
			'is_dismissible' => $is_dismissible,
			'message' => $message,
            'type' => $type,
		];
        __add_action_once('admin_notices', '__maybe_add_admin_notices');
		__cache_add($admin_notice, 'admin_notices');
	}
}

if(!function_exists('__admin_notice_html')){
	/**
	 * @return string
	 */
	function __admin_notice_html($message = '', $type = '', $is_dismissible = false){
		if(!in_array($type, ['error', 'info', 'success', 'warning'])){
			$type = '';
		}
        if(function_exists('wp_get_admin_notice')){
            // @since 6.4.0
            $args = [
                'dismissible' => $is_dismissible,
                'type' => $type,
            ];
            return wp_get_admin_notice($message, $args);
        }
        // Backward compatibility.
        if(!$type){
            $type = 'warning';
        }
        if($is_dismissible){
			$type .= ' is-dismissible';
		}
		return '<div class="notice notice-' . $type . '"><p>' . $message . '</p></div>';
	}
}

if(!function_exists('__echo_admin_notice')){
	/**
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __echo_admin_notice($message = '', $type = '', $is_dismissible = false){
		if(!doing_action('admin_notices')){ // Too early or too late.
	        return;
	    }
		echo __admin_notice_html($message, $type, $is_dismissible);
	}
}

if(!function_exists('__maybe_add_admin_notices')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_admin_notices(){
		if(!doing_action('admin_notices')){
            // Too early or too late.
	        return;
	    }
        $admin_notices = __cache_get_group('admin_notices');
        if($admin_notices === null){
            return;
        }
		foreach($admin_notices as $admin_notice){
			__echo_admin_notice($admin_notice['message'], $admin_notice['type'], $admin_notice['is_dismissible']);
		}
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Arrays
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__array_keys_exist')){
	/**
	 * @return bool
	 */
	function __array_keys_exist($keys = [], $array = []){
		if(!is_array($keys) || !is_array($array)){
			return false;
		}
		foreach($keys as $key){
			if(!array_key_exists($key, $array)){
				return false;
			}
		}
		return true;
	}
}

if(!function_exists('__is_associative_array')){
	/**
	 * @return bool
	 */
	function __is_associative_array($array = []){
        return ($array && is_array($array)) ? !__is_numeric_array($array) : false;
	}
}

if(!function_exists('__is_numeric_array')){
	/**
	 * @return bool
	 */
	function __is_numeric_array($array = []){
		return ($array && is_array($array)) ? array_keys($array) === range(0, count($array) - 1) : false;
	}
}

if(!function_exists('__ksort_deep')){
	/**
	 * @return array
	 */
	function __ksort_deep($array = []){
		if(!__is_associative_array($array)){
			return $array;
		}
		ksort($array);
		foreach($array as $key => $value){
			$array[$key] = __ksort_deep($value);
		}
		return $array;
	}
}

if(!function_exists('__list_pluck')){
	/**
	 * @return array
	 */
	function __list_pluck($input_list = [], $index_key = '', $field = ''){
        if(!$index_key){
            return $input_list;
        }
        if($field){
            return wp_list_pluck($input_list, $field, $index_key);
        }
		$newlist = [];
		foreach($input_list as $value){
			if(is_object($value)){
				if(isset($value->$index_key)){
					$newlist[$value->$index_key] = $value;
				} else {
					$newlist[] = $value;
				}
			} else {
				if(isset($value[$index_key])){
					$newlist[$value[$index_key]] = $value;
				} else {
					$newlist[] = $value;
				}
			}
		}
		return $newlist;
	}
}

if(!function_exists('__object_properties_exist')){
	/**
	 * @return bool
	 */
	function __object_properties_exist($properties = [], $object_or_class = null){
		if(!is_array($properties) || !is_object($object_or_class) || !class_exists($object_or_class)){
			return false;
		}
		foreach($properties as $property){
			if(!property_exists($object_or_class, $property)){
				return false;
			}
		}
		return true;
	}
}

if(!function_exists('__object_to_array')){
	/**
	 * @return array|WP_Error
	 */
	function __object_to_array($data = null){
		if(!is_object($data)){
			return __error(__('Invalid data provided.'), $data);
		}
		return __json_decode(wp_json_encode($data), true);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Attachments
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__attachment_url_to_postid')){
	/**
	 * @return int
	 */
	function __attachment_url_to_postid($url = ''){
		$post_id = attachment_url_to_postid($url);
		if($post_id){
			return $post_id;
		}
        $post_id = __guid_to_postid($url);
		if($post_id){
			return $post_id;
		}
		preg_match('/^(.+)(\-\d+x\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // Resized.
		if($matches){
			$url = $matches[1];
			if(isset($matches[3])){
				$url .= $matches[3];
			}
			$post_id = __guid_to_postid($url);
			if($post_id){
				return $post_id;
			}
		}
		preg_match('/^(.+)(\-scaled)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // Scaled.
		if($matches){
			$url = $matches[1];
			if(isset($matches[3])){
				$url .= $matches[3];
			}
			$post_id = __guid_to_postid($url);
			if($post_id){
				return $post_id;
			}
		}
		preg_match('/^(.+)(\-e\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // Edited.
		if($matches){
			$url = $matches[1];
			if(isset($matches[3])){
				$url .= $matches[3];
			}
			$post_id = __guid_to_postid($url);
			if($post_id){
				return $post_id;
			}
		}
		return 0;
	}
}

if(!function_exists('__fa_file_type')){
	/**
	 * @return string
	 */
	function __fa_file_type($post = null){
		if('attachment' !== get_post_type($post)){
			return '';
		}
		if(wp_attachment_is('audio', $post)){
			return 'file-audio';
		}
		if(wp_attachment_is('image', $post)){
			return 'file-image';
		}
		if(wp_attachment_is('video', $post)){
			return 'file-video';
		}
		$type = get_post_mime_type($post);
		switch($type){
			case 'application/zip':
			case 'application/x-rar-compressed':
			case 'application/x-7z-compressed':
			case 'application/x-tar':
				return 'file-archive';
				break;
			case 'application/vnd.ms-excel':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				return 'file-excel';
				break;
			case 'application/pdf':
				return 'file-pdf';
				break;
			case 'application/vnd.ms-powerpoint':
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
				return 'file-powerpoint';
				break;
			case 'application/msword':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
				return 'file-word';
				break;
			default:
				return 'file';
		}
	}
}

if(!function_exists('__guid_to_postid')){
	/**
	 * @return int
	 */
	function __guid_to_postid($guid = '', $check_rewrite_rules = false){
		global $wpdb;
		$query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $guid);
		$post_id = $wpdb->get_var($query);
		if($post_id !== null){
			return (int) $post_id;
		}
		return $check_rewrite_rules ? url_to_postid($guid) : 0;
	}
}

if(!function_exists('__maybe_generate_attachment_metadata')){
	/**
	 * @return void
	 */
	function __maybe_generate_attachment_metadata($attachment_id = 0){
		$attachment = get_post($attachment_id);
		if($attachment === null){
			return;
		}
		if($attachment->post_type !== 'attachment'){
			return;
		}
		wp_raise_memory_limit('image');
		if(!function_exists('wp_generate_attachment_metadata')){
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		wp_maybe_generate_attachment_metadata($attachment);
	}
}

if(!function_exists('__sideload')){
	/**
	 * @return int|WP_Error
	 */
	function __sideload($file = '', $post_id = 0, $generate_attachment_metadata = true){
		if(!@is_file($file)){
			return __error(__('File doesn&#8217;t exist?'), $file);
		}
	    $filename = __test_type($file, wp_basename($file));
		if(is_wp_error($filename)){
			return $filename;
		}
	    $filetype_and_ext = wp_check_filetype($filename);
	    $attachment_id = wp_insert_attachment([
	        'guid' => __path_to_url($file),
	        'post_mime_type' => $filetype_and_ext['type'],
	        'post_status' => 'inherit',
	        'post_title' => preg_replace('/\.[^.]+$/', '', $filename), // Use the original filename. Remove the file extension (after the last `.`)
	    ], $file, $post_id, true);
	    if($generate_attachment_metadata){
	        __maybe_generate_attachment_metadata($attachment_id);
	    }
	    return $attachment_id;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Authentication
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__signon')){
	/**
	 * @return WP_User|WP_Error
	 */
	function __signon($username_or_email = '', $password = '', $remember = false){
		if(is_user_logged_in()){
            return __error(__first_p(__('You are logged in already. No need to register again!')));
		}
		$disable_captcha = !has_filter('wordfence_ls_require_captcha', '__return_false');
		if($disable_captcha){ // Don't filter twice.
			add_filter('wordfence_ls_require_captcha', '__return_false');
		}
	    $user = wp_signon([
	        'remember' => $remember,
	        'user_login' => $username_or_email,
	        'user_password' => $password,
	    ]);
		if($disable_captcha){
			remove_filter('wordfence_ls_require_captcha', '__return_false');
		}
	    if(is_wp_error($user)){
	        return $user;
	    }
	    return wp_set_current_user($user->ID);
	}
}

if(!function_exists('__signon_without_password')){
	/**
	 * @return WP_Error|WP_User
	 */
	function __signon_without_password($username_or_email = '', $remember = false){
		if(is_user_logged_in()){
            return __error(__first_p(__('You are logged in already. No need to register again!')));
		}
	    add_filter('authenticate', '__maybe_authenticate_without_password', 10, 3);
		$disable_captcha = !has_filter('wordfence_ls_require_captcha', '__return_false');
		if($disable_captcha){ // Don't filter twice.
			add_filter('wordfence_ls_require_captcha', '__return_false');
		}
	    $user = wp_signon([
	        'remember' => $remember,
	        'user_login' => $username_or_email,
	        'user_password' => '',
	    ]);
		if($disable_captcha){
			remove_filter('wordfence_ls_require_captcha', '__return_false');
		}
	    remove_filter('authenticate', '__maybe_authenticate_without_password');
	    if(is_wp_error($user)){
	        return $user;
	    }
	    return wp_set_current_user($user->ID);
	}
}

if(!function_exists('__maybe_authenticate_without_password')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'authenticate' filter hook.
	 *
	 * @return WP_User|WP_Error|false
	 */
	function __maybe_authenticate_without_password($user = null, $username_or_email = '', $password = ''){
		if(!doing_filter('authenticate')){ // Too early or too late.
	        return $user;
	    }
		if(!is_null($user)){
			return $user;
		}
		$user = false; // Returning a non-null value will effectively short-circuit the user authentication process.
		if(username_exists($username_or_email)){
			$user = get_user_by('login', $username_or_email);
		} elseif(is_email($username_or_email) && email_exists($username_or_email)){
			$user = get_user_by('email', $username_or_email);
		}
		return $user;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Beaver Builder
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__bb_add_system_font')){
	/**
	 * @return void
	 */
	function __bb_add_system_font($key = '', $weights = [], $fallback = ''){
        __cache_set($key, [
            'fallback' => $fallback,
            'weights' => __bb_sanitize_font_weights($weights),
        ], 'system_fonts');
        __add_filter_once('fl_builder_font_families_system', '__maybe_add_system_fonts');
        __add_filter_once('fl_theme_system_fonts', '__maybe_add_system_fonts');
	}
}

if(!function_exists('__bb_get_font_weights')){
	/**
	 * @return array
	 */
	function __bb_get_font_weights(){
        return [
            '100' => __('Thin', 'fl-automator'),
			'100italic' => __('Thin Italic', 'fl-automator'),
			'200' => __('Extra-Light', 'fl-automator'),
			'200italic' => __('Extra-Light Italic', 'fl-automator'),
			'300' => __('Light', 'fl-automator'),
			'300italic' => __('Light Italic', 'fl-automator'),
			'400' => __('Normal', 'fl-automator'),
			'500' => __('Medium', 'fl-automator'),
			'500italic' => __('Medium Italic', 'fl-automator'),
			'600' => __('Semi-Bold', 'fl-automator'),
			'600italic' => __('Semi-Bold Italic', 'fl-automator'),
			'700' => __('Bold', 'fl-automator'),
			'700italic' => __('Bold Italic', 'fl-automator'),
			'800' => __('Extra-Bold', 'fl-automator'),
			'800italic' => __('Extra-Bold Italic', 'fl-automator'),
			'900' => __('Ultra-Bold', 'fl-automator'),
			'900italic' => __('Ultra-Bold Italic', 'fl-automator'),
        ];
	}
}

if(!function_exists('__bb_is_b4_enabled')){
    /**
     * @return bool
     */
    function __bb_is_b4_enabled(){
		if(!__bb_is_theme_enabled()){
			return false;
		}
    	return get_theme_mod('fl-framework', 'none') === 'bootstrap-4';
    }
}

if(!function_exists('__bb_is_fa5_enabled')){
    /**
     * @return bool
     */
    function __bb_is_fa5_enabled(){
		if(!__bb_is_theme_enabled()){
			return false;
		}
    	return get_theme_mod('fl-awesome', 'none') === 'fa5';
    }
}

if(!function_exists('__bb_is_theme_enabled')){
    /**
     * @return bool
     */
    function __bb_is_theme_enabled(){
		return (__theme_is('Beaver Builder Theme') || __theme_is_child_of('bb-theme'));
    }
}

if(!function_exists('__bb_sanitize_font_weights')){
	/**
	 * @return array
	 */
	function __bb_sanitize_font_weights($weights = []){
        $defaults = array_keys(__bb_get_font_weights());
        foreach($weights as $index => $weight){
            $weight = (string) $weight;
            if(!in_array($weight, $defaults)){
                unset($weights[$index]);
            }
    	}
        return array_values($weights);
	}
}

if(!function_exists('__maybe_add_system_fonts')){
    /**
     * This function’s access is marked private.
     *
     * This function MUST be called inside the 'fl_builder_font_families_system' or 'fl_theme_system_fonts' filter hooks.
	 *
	 * @return void
	 */
	function __maybe_add_system_fonts($fonts){
        if(!doing_filter('fl_builder_font_families_system') && !doing_filter('fl_theme_system_fonts')){
            return; // Too early or too late.
        }
        $system_fonts = __cache_get_group('system_fonts');
        if($system_fonts === null){
            return;
        }
        foreach($system_fonts as $key => $value){
            $fonts[$key] = $value;
        }
        return $fonts;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Bootstrap
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__alert_html')){
	/**
	 * @return string
	 */
	function __alert_html($message = '', $class = '', $is_dismissible = false){
		if(!in_array($class, ['danger', 'dark', 'info', 'light', 'primary', 'secondary', 'success', 'warning'])){
			$class = 'warning';
		}
		if($is_dismissible){
			$class .= ' alert-dismissible fade show';
		}
		if($is_dismissible){
			$message .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		}
		return '<div class="alert alert-' . $class . '">' . $message . '</div>';
	}
}

if(!function_exists('__alert_danger')){
	/**
	 * @return string
	 */
	function __alert_danger($message = '', $is_dismissible = false){
		return __alert_html($message, 'danger', $is_dismissible);
	}
}

if(!function_exists('__alert_dark')){
	/**
	 * @return string
	 */
	function __alert_dark($message = '', $is_dismissible = false){
		return __alert_html($message, 'dark', $is_dismissible);
	}
}

if(!function_exists('__alert_info')){
	/**
	 * @return string
	 */
	function __alert_info($message = '', $is_dismissible = false){
		return __alert_html($message, 'info', $is_dismissible);
	}
}

if(!function_exists('__alert_light')){
	/**
	 * @return string
	 */
	function __alert_light($message = '', $is_dismissible = false){
		return __alert_html($message, 'light', $is_dismissible);
	}
}

if(!function_exists('__alert_primary')){
	/**
	 * @return string
	 */
	function __alert_primary($message = '', $is_dismissible = false){
		return __alert_html($message, 'primary', $is_dismissible);
	}
}

if(!function_exists('__alert_secondary')){
	/**
	 * @return string
	 */
	function __alert_secondary($message = '', $is_dismissible = false){
		return __alert_html($message, 'secondary', $is_dismissible);
	}
}

if(!function_exists('__alert_success')){
	/**
	 * @return string
	 */
	function __alert_success($message = '', $is_dismissible = false){
		return __alert_html($message, 'success', $is_dismissible);
	}
}

if(!function_exists('__alert_warning')){
	/**
	 * @return string
	 */
	function __alert_warning($message = '', $is_dismissible = false){
		return __alert_html($message, 'warning', $is_dismissible);
	}
}

if(!function_exists('__has_btn_class')){
	/**
	 * @return bool
	 */
	function __has_btn_class($class = ''){
	    $class = sanitize_text_field($class);
	    preg_match_all('/btn-[A-Za-z][-A-Za-z0-9_:.]*/', $class, $matches);
		$matches = array_filter($matches[0], function($match){
			return !in_array($match, ['btn-block', 'btn-lg', 'btn-sm']);
		});
		return $matches ? true : false;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Cache
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cache_add')){
    /**
	 * @return bool
	 */
	function __cache_add($data = null, $group = ''){
        return ($group && is_string($group)) ? __cache_set(__uuid($data), $data, $group) : false;
	}
}

if(!function_exists('__cache_delete')){
    /**
	 * @return bool
	 */
	function __cache_delete($key = '', $group = ''){
        $global_key = __str_prefix('cache');
        $group = __cache_sanitize_group($group);
        if(!isset($GLOBALS[$global_key][$group][$key])){
            return false;
        }
        unset($GLOBALS[$global_key][$group][$key]);
        return true;
	}
}

if(!function_exists('__cache_exists')){
    /**
	 * @return bool
	 */
	function __cache_exists($key = '', $group = ''){
        $global_key = __str_prefix('cache');
        $group = __cache_sanitize_group($group);
		return isset($GLOBALS[$global_key][$group][$key]);
	}
}

if(!function_exists('__cache_get')){
    /**
	 * @return mixed
	 */
	function __cache_get($key = '', $group = ''){
        $global_key = __str_prefix('cache');
        $group = __cache_sanitize_group($group);
        return isset($GLOBALS[$global_key][$group][$key]) ? $GLOBALS[$global_key][$group][$key] : null;
	}
}

if(!function_exists('__cache_get_group')){
    /**
	 * @return mixed
	 */
	function __cache_get_group($group = ''){
        $global_key = __str_prefix('cache');
        $group = __cache_sanitize_group($group);
        return $GLOBALS[$global_key][$group];
	}
}

if(!function_exists('__cache_set')){
    /**
	 * @return bool
	 */
	function __cache_set($key = '', $data = null, $group = ''){
        $global_key = __str_prefix('cache');
        $group = __cache_sanitize_group($group);
        $old_value = isset($GLOBALS[$global_key][$group][$key]) ? $GLOBALS[$global_key][$group][$key] : null;
        if($data === null){
            if($old_value !== null){
                unset($GLOBALS[$global_key][$group][$key]);
            }
        } else {
            $GLOBALS[$global_key][$group][$key] = $data;
        }
        return $data !== $old_value;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Closures
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_closure')){
	/**
	 * @return bool
	 */
	function __is_closure($thing = null){
        return $thing instanceof \Closure;
	}
}

if(!function_exists('__md5_closure')){
	/**
	 * @return string|WP_Error
	 */
	function __md5_closure($data = null, $spl_object_hash = false){
		if(!__is_closure($data)){
			return __error(__('Invalid object type.'));
		}
		$serialized_data = __serialize_closure($data);
		if(is_wp_error($serialized_data)){
			return $serialized_data;
		}
		return $spl_object_hash ? md5($serialized_data) : md5(str_replace(spl_object_hash($data), '__SPL_OBJECT_HASH__', $serialized_data));
	}
}

if(!function_exists('__serialize_closure')){
	/**
	 * @return string|WP_Error
	 */
	function __serialize_closure($data = null, $security = null){
		if(!__is_closure($data)){
			return __error('Invalid object type.', $data);
		}
        $dir = __require_closure();
		return is_wp_error($dir) ? $dir : \Opis\Closure\serialize($data, $security);
	}
}

if(!function_exists('__unserialize_closure')){
	/**
	 * @return mixed|WP_Error
	 */
	function __unserialize_closure($data = '', $security = null, $options = null){
		if(!is_string($data)){
			return __error('Invalid data provided.', $data);
		}
		$dir = __use_closure();
        return is_wp_error($dir) ? $dir : \Opis\Closure\unserialize($data, $security, $options);
	}
}

if(!function_exists('__use_closure')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_closure(){
        $ver = '4.3.1'; // Hardcoded.
        $url = 'https://github.com/opis/closure/archive/refs/tags/' . $ver . '.zip';
        return __use($url, [
            'autoload' => 'autoload.php',
			'expected_dir' => 'closure-' . $ver,
            'validation_class' => 'Opis\Closure\Serializer',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Cloudflare
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_cloudflare_country')){
	/**
	 * @return string
	 */
	function __get_cloudflare_country(){
		return isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : '';
	}
}

if(!function_exists('__get_cloudflare_ip')){
	/**
	 * @return string
	 */
	function __get_cloudflare_ip(){
		return isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '';
	}
}

if(!function_exists('__is_cloudflare_enabled')){
	/**
	 * @return string
	 */
	function __is_cloudflare_enabled(){
		return isset($_SERVER['CF-ray']);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Cloudinary
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_cloudinary_image_size')){
    /**
     * @return void
     */
    function __add_cloudinary_image_size($name = '', $options = []){
        $image_sizes = get_intermediate_image_sizes();
		$size = __canonicalize($name);
		if(in_array($size, $image_sizes)){
			return; // Does NOT overwrite.
		}
        $dir = __use_cloudinary();
        if(is_wp_error($dir)){
            return;
        }
        __add_filter_once('fl_builder_photo_sizes_select', '__maybe_cloudinary_fl_builder_photo_sizes_select');
        __add_filter_once('image_downsize', '__maybe_cloudinary_image_downsize', 10, 3);
        __add_filter_once('image_size_names_choose', '__maybe_cloudinary_image_size_names_choose');
        __cache_set($size, [
    		'name' => $name,
    		'options' => $options,
    	], 'cloudinary_image_sizes');
        add_image_size($size); // Fake size.
    }
}

if(!function_exists('__cloudinary_config')){
    /**
     * @return array|WP_Error
     */
    function __cloudinary_config($config = []){
        $cache_key = 'cloudinary_config';
        if(__cache_exists($cache_key)){
            return __cache_get($cache_key);
        }
        if(!$config){
            return __error(sprintf(__('Missing parameter(s): %s'), 'Access Keys'));
        }
        $dir = __use_cloudinary();
        if(is_wp_error($dir)){
            return $dir;
        }
        $config_keys = ['api_key', 'api_secret', 'cloud_name'];
        if(__array_keys_exist($config_keys, $config)){
            $config = \Cloudinary::config($config);
            __cache_set($cache_key, $config);
            return $config;
        }
        if(is_string($config) && preg_match('/^(?:CLOUDINARY_URL=)?(?:cloudinary:\/\/)(\d+):([^:@]+)@([^@]+)$/', $config, $matches)){
            $config = \Cloudinary::config([
                'api_key' => $matches[1],
                'api_secret' => $matches[2],
                'cloud_name' => $matches[3],
            ]);
            __cache_set($cache_key, $config);
            return $config;
        }
        return __error(sprintf(__('Invalid parameter(s): %s'), 'Access Keys'));
    }
}

if(!function_exists('__cloudinary_file')){
    /**
     * @return string|WP_Error
     */
    function __cloudinary_file($attachment_id = 0, $options = []){
        $meta_key = __cloudinary_meta_key('file', $options);
        if(is_wp_error($meta_key)){
            return $meta_key;
        }
        $meta_value = __cloudinary_meta_value($attachment_id, $meta_key);
        if($meta_value){
            if(is_wp_error($meta_value)){
                return $meta_value;
            }
            if(file_exists($meta_value)){
                return $meta_value; // Already downloaded.
            }
            delete_post_meta($attachment_id, $meta_key, $meta_value);
        }
        $url = __cloudinary_url($attachment_id, $options);
        if(is_wp_error($url)){
            return $url;
        }
        $download_dir = __dir('cloudinary');
        if(is_wp_error($download_dir)){
            return $download_dir;
        }
        $attachment_file = wp_get_original_image_path($attachment_id);
        $attachment_filename = wp_basename($attachment_file);
        $filename = wp_unique_filename($download_dir, $attachment_filename);
        $args = [
            'filename' => trailingslashit($download_dir) . $filename,
        ];
        $file = __remote_download($url, $args);
        if(is_wp_error($file)){
            return $file;
        }
        update_post_meta($attachment_id, $meta_key, $file);
        return $file;
    }
}

if(!function_exists('__cloudinary_file_info')){
    /**
     * @return array|WP_Error
     */
    function __cloudinary_file_info($attachment_id = 0, $options = []){
        $file = __cloudinary_file($attachment_id, $options);
        if(is_wp_error($file)){
            return $file;
        }
        list($width, $height) = wp_getimagesize($file);
        return [
            'file' => $file,
            'filename' => wp_basename($file),
            'height' => $height,
            'url' => __path_to_url($file),
            'width' => $width,
        ];
    }
}

if(!function_exists('__cloudinary_public_id')){
    /**
     * @return string|WP_Error
     */
    function __cloudinary_public_id($attachment_id = 0){
        $response = __cloudinary_maybe_upload($attachment_id);
        if(is_wp_error($response)){
            return $response;
        }
        return isset($response['public_id']) ? $response['public_id'] : '';
    }
}

if(!function_exists('__cloudinary_url')){
    /**
     * @return string|WP_Error
     */
    function __cloudinary_url($attachment_id = 0, $options = []){
        $meta_key = __cloudinary_meta_key('url', $options);
        if(is_wp_error($meta_key)){
            return $meta_key;
        }
        $meta_value = __cloudinary_meta_value($attachment_id, $meta_key);
        if($meta_value){
            if(is_wp_error($meta_value)){
                return $meta_value;
            }
            return $meta_value; // Already transformed.
        }
        $public_id = __cloudinary_public_id($attachment_id);
        if(is_wp_error($public_id)){
            return $public_id;
        }
        try {
            $response = cloudinary_url($public_id, $options);
        } catch(\Throwable $t){
            $response = __error($t->getMessage());
        } catch(\Exception $e){
            $response = __error($e->getMessage());
        }
        if(is_wp_error($response)){
            return $response;
        }
        if(__is_cloudinary_error($response)){
            return __error($response->getMessage());
        }
        update_post_meta($attachment_id, $meta_key, $response);
        return $response;
    }
}

if(!function_exists('__cloudinary_file_candidate')){
    /**
     * @return string|WP_Error
     */
    function __cloudinary_file_candidate($attachment_id = 0, $max_file_size = 0){
        if(!wp_attachment_is_image($attachment_id)){
            return __error(__first_p(__('This file is not an image. Please try another.')));
        }
        $image_file = get_attached_file($attachment_id);
        if(!file_exists($image_file)){
            return __error(__('The attached file cannot be found.'));
        }
        $type = wp_check_filetype($image_file);
        if(!$type['ext'] || !$type['type']){
            return __error(__first_p(__('The uploaded file is not a valid image. Please try again.')));
        }
        $image_meta = wp_get_attachment_metadata($attachment_id);
        $max_file_size_in_kb = ($max_file_size / KB_IN_BYTES);
        if(!$image_meta){
            $filesize = wp_filesize($image_file);
            if($filesize <= $max_file_size){
                return $image_file;
            }
            return __error(sprintf(__('This file is too big. Files must be less than %s KB in size.'), $max_file_size_in_kb));
        }
        if(isset($image_meta['original_image'])){
            $original_image = path_join(dirname($image_file), $image_meta['original_image']); // Alias for wp_get_original_image_path().
            if(!file_exists($original_image)){
                return __error(__('The attached file cannot be found.'));
            }
            $filesize = wp_filesize($original_image);
            if($filesize <= $max_file_size){
                return $original_image;
            }
        }
        if($image_meta['filesize'] <= $max_file_size){
            return $image_file;
        }
        $sizes = wp_list_pluck($image_meta['sizes'], 'filesize', 'file');
        arsort($sizes);
        $basedir = dirname($file);
        $path = '';
        foreach($sizes as $file => $filesize){
            if($filesize > $max_file_size){
                continue;
            }
            $path = path_join($basedir, $file);
            if(!file_exists($path)){
                continue;
            }
            break;
        }
        return $path ? $path : __error(sprintf(__('This file is too big. Files must be less than %s KB in size.'), $max_file_size_in_kb));
    }
}

if(!function_exists('__cloudinary_maybe_upload')){
    /**
     * @return array|WP_Error
     */
    function __cloudinary_maybe_upload($attachment_id = 0){
        $meta_key = __cloudinary_meta_key('response');
        if(is_wp_error($meta_key)){
            return $meta_key;
        }
        $meta_value = __cloudinary_meta_value($attachment_id, $meta_key);
        if($meta_value){
            if(is_wp_error($meta_value)){
                return $meta_value;
            }
            return (array) $meta_value; // Already uploaded.
        }
        $max_file_size = __is_cloudinary_paid_plan() ? 20 : 10; // Check for paid plans. See: https://support.cloudinary.com/hc/en-us/articles/202520592-Do-you-have-a-file-size-limit-
        $max_file_size = $max_file_size * MB_IN_BYTES;
        wp_raise_memory_limit('image');
        $file = __cloudinary_file_candidate($attachment_id, $max_file_size);
        if(is_wp_error($file)){
            return $file;
        }
        try {
            $response = \Cloudinary\Uploader::upload($file);
        } catch(\Throwable $t){
            $response = __error($t->getMessage());
        } catch(\Exception $e){
            $response = __error($e->getMessage());
        }
        if(is_wp_error($response)){
            return $response;
        }
        if(__is_cloudinary_error($response)){
            return __error($response->getMessage());
        }
        update_post_meta($attachment_id, $meta_key, $response);
        return $response;
    }
}

if(!function_exists('__is_cloudinary_error')){
    /**
     * @return bool
     */
    function __is_cloudinary_error($thing = null){
        return $thing instanceof \Cloudinary\Error;
    }
}

if(!function_exists('__is_cloudinary_paid_plan')){
	/**
	 * @return bool
	 */
	function __is_cloudinary_paid_plan(){
		return apply_filters('cloudinary_paid_plan', false);
	}
}

if(!function_exists('__cloudinary_meta_key')){
	/**
     * This function’s access is marked private.
     *
     * @return string|WP_Error
     */
    function __cloudinary_meta_key($context = '', $options = []){
        if(!$context){
            return __error(sprintf(__('The "%s" argument must be a non-empty string.'), 'context'));
        }
        $config = __cloudinary_config();
        if(is_wp_error($config)){
            return $config;
        }
        $meta_key = '_cloudinary_' . __md5($config) . '_' . $context;
        if(!$options){
            return $meta_key;
        }
        if(!is_array($options)){
            return __error(sprintf(__('The %s argument must be an array.'), 'options'));
        }
        $meta_key .= '_' . __md5($options);
        return $meta_key;
    }
}

if(!function_exists('__cloudinary_meta_value')){
	/**
     * This function’s access is marked private.
     *
     * @return array|string|WP_Error
     */
    function __cloudinary_meta_value($attachment_id = 0, $meta_key = ''){
        if(!$meta_key){
            return __error(sprintf(__('The "%s" argument must be a non-empty string.'), 'meta_key'));
        }
        if(!wp_attachment_is_image($attachment_id)){
            return __error(__first_p(__('This file is not an image. Please try another.')));
        }
        return get_post_meta($attachment_id, $meta_key, true);
    }
}

if(!function_exists('__cloudinary_width_height_ascending_sort')){
	/**
     * This function’s access is marked private.
     *
     * @return int
     */
    function __cloudinary_width_height_ascending_sort($a = 0, $b = 0){
        if($a['width'] === $b['width']){
			if($a['height'] === $b['height']){
				return 0;
			}
			if($a['height'] < $b['height']){
				return -1;
			}
			return 1;
		}
		if($a['width'] < $b['width']){
			return -1;
		}
		return 1;
    }
}

if(!function_exists('__maybe_cloudinary_fl_builder_photo_sizes_select')){
	/**
     * This function’s access is marked private.
     *
	 * @return array
	 */
	function __maybe_cloudinary_fl_builder_photo_sizes_select($sizes){
        $cache_key = 'cloudinary_image_sizes';
        if(!__cache_exists($cache_key)){
    		return $sizes;
    	}
		if(!isset($sizes['full'])){
			return $sizes;
		}
		$id = __attachment_url_to_postid($sizes['full']['url']);
		if(!$id){
			return $sizes;
		}
        $cloudinary_image_sizes = __cache_get($cache_key);
		foreach($cloudinary_image_sizes as $size => $args){
			if(isset($sizes[$size])){
				continue;
			}
            $file_info = __cloudinary_file_info($id, $args['options']);
            if(is_wp_error($file_info)){
                continue;
            }
			$sizes[$size] = [
				'filename' => $file_info['filename'],
				'height' => $file_info['height'],
				'url' => $file_info['url'],
				'width' => $file_info['width'],
			];
		}
		uasort($sizes, '__cloudinary_width_height_ascending_sort');
		return $sizes;
	}
}

if(!function_exists('__maybe_cloudinary_image_downsize')){
    /**
     * This function’s access is marked private.
     *
     * @return array|false
     */
    function __maybe_cloudinary_image_downsize($out, $id, $size){
    	if($out){
    		return $out; // A truthy value from the filter will effectively short-circuit down-sizing the image, returning that value instead.
    	}
        if(!wp_attachment_is_image($id)){
    		return $out;
    	}
        if(!is_scalar($size)){
            return $out; // Discard non-scalars.
        }
        $cache_key = 'cloudinary_image_sizes';
    	if(!__cache_exists($size, $cache_key)){
    		return $out;
    	}
        $args = __cache_get($size, $cache_key);
        $file_info = __cloudinary_file_info($id, $args['options']);
        if(is_wp_error($file_info)){
            return $out;
        }
        return [$file_info['url'], $file_info['width'], $file_info['height'], true];
    }
}

if(!function_exists('__maybe_cloudinary_image_size_names_choose')){
    /**
     * This function’s access is marked private.
     *
	 * @return array
	 */
	function __maybe_cloudinary_image_size_names_choose($sizes){
        $cache_key = 'cloudinary_image_sizes';
        if(!__cache_exists($cache_key)){
    		return $sizes;
    	}
        $cloudinary_image_sizes = __cache_get($cache_key);
		foreach($cloudinary_image_sizes as $size => $args){
			$sizes[$size] = $args['name'];
		}
		return $sizes;
	}
}

if(!function_exists('__use_cloudinary')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_cloudinary(){
        $ver = '1.20.2'; // Hardcoded.
        $url = 'https://github.com/cloudinary/cloudinary_php/archive/refs/tags/' . $ver . '.zip';
        return __use($url, [
            'autoload' => ['autoload.php', 'src/Helpers.php'], // Fallback to legacy autoloader.
			'expected_dir' => 'cloudinary_php-' . $ver,
            'validation_class' => 'Cloudinary',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Code
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__compress_css')){
	/**
	 * @return string
	 */
	function __compress_css($css = ''){
		return __remove_newlines(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css));
    }
}

if(!function_exists('__normalize_ie_filters')){
	/**
	 * @return string
	 */
	function __normalize_ie_filters($css = ''){
		return preg_replace_callback('(filter\s?:\s?(.*);)', '__normalize_ie_filters', $css); // Fix issue with IE filters.
    }
}

if(!function_exists('__parse_less')){
	/**
	 * @return string|WP_Error
	 */
	function __parse_less($css = '', $options = []){
        $dir = __use_lessphp();
    	if(is_wp_error($dir)){
    		return $dir;
    	}
    	\Less_Autoloader::register();
        $parser = new \Less_Parser($options);
        try {
            $parser->parse($css);
            $result = $parser->getCss();
        } catch(\Throwable $t){
            $error_msg = str_replace(' in file anonymous-file-0.less in anonymous-file-0.less', '.', $t->getMessage());
            $result = __error($error_msg);
        } catch(\Exception $e){
            $error_msg = str_replace(' in file anonymous-file-0.less in anonymous-file-0.less', '.', $e->getMessage());
            $result = __error($error_msg);
        }
        return $result;
    }
}

if(!function_exists('__minify_css')){
	/**
	 * @return string
	 */
	function __minify_css($css = ''){
        $css = __normalize_ie_filters($css);
        $less = __parse_less($css, [
            'compress' => true,
        ]);
        if(!is_wp_error($less)){
            $css = $less;
        }
        return __compress_css($css);
    }
}

if(!function_exists('__minify_js')){
	/**
	 * @return string
	 */
	function __minify_js($js = '', $options = []){
        $dir = __use_jshrink();
    	if(is_wp_error($dir)){
    		return $js;
    	}
        return \JShrink\Minifier::minify($js, $options);
    }
}

if(!function_exists('__normalize_ie_filters')){
    /**
     * This function’s access is marked private.
     *
	 * @return string
	 */
	function __normalize_ie_filters($matches = []){
        return empty($matches[1]) ? $matches[0] : 'filter: ~"' . $matches[1] . '";';
    }
}

if(!function_exists('__use_jshrink')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_jshrink(){
        $ver = '1.7.0'; // Hardcoded.
        $url = 'https://github.com/tedious/JShrink/archive/refs/tags/v' . $ver . '.zip';
        return __use($url, [
            'autoload' => 'src/JShrink/Minifier.php',
			'expected_dir' => 'JShrink-' . $ver,
            'validation_class' => 'JShrink\Minifier',
        ]);
	}
}

if(!function_exists('__use_lessphp')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_lessphp(){
        $ver = '5.4.0'; // Hardcoded.
        $url = 'https://github.com/wikimedia/less.php/archive/refs/tags/v' . $ver . '.zip';
        return __use($url, [
            'autoload' => 'lib/Less/Autoloader.php',
			'expected_dir' => 'less.php-' . $ver,
            'requires_php' => '8.1',
            'validation_class' => 'Less_Autoloader',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Content types
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__convert_exts_to_mimes')){
	/**
	 * @return array
	 */
	function __convert_exts_to_mimes($exts = []){
	    if(!$exts){
	        $exts = array_merge(wp_get_audio_extensions(), wp_get_video_extensions(), __image_extensions());
	    }
	    $mimes = wp_get_mime_types();
	    $ext_mimes = [];
	    foreach($exts as $ext){
	        foreach($mimes as $ext_preg => $mime_match){
	            if(preg_match('#' . $ext . '#i', $ext_preg)){
	                $ext_mimes[$ext] = $mime_match;
	                break;
	            }
	        }
	    }
	    return $ext_mimes;
	}
}

if(!function_exists('__get_content_type')){
	/**
	 * Alias for WP_REST_Request::get_content_type().
	 *
	 * Retrieves the Content-Type of the remote request or response.
	 *
	 * @return array
	 */
	function __get_content_type($value = []){
        if(__is_content_type($value)){
            return $value;
        }
        if(__is_remote_request($value) || __is_remote_response($value)){
            $values = (array) wp_remote_retrieve_header($value, 'Content-Type');
    		if(!$values){
    			return [];
    		}
            $value = $values[0];
        }
		if(!is_string($value)){
            return [];
        }
		$parameters = '';
		if(strpos($value, ';')){
			list($value, $parameters) = explode(';', $value, 2);
		}
		$value = strtolower($value);
		if(!str_contains($value, '/')){
			return [];
		}
		list($type, $subtype) = explode('/', $value, 2); // Parse type and subtype out.
		$data = compact('value', 'type', 'subtype', 'parameters');
		return array_map('trim', $data);
	}
}

if(!function_exists('__image_extensions')){
	/**
	 * @return array
	 */
	function __image_extensions(){
	    return ['jpg', 'jpeg', 'jpe', 'png', 'gif', 'bmp', 'tiff', 'tif', 'webp', 'ico', 'heic'];
	}
}

if(!function_exists('__is_content_type')){
	/**
	 * @return bool
	 */
	function __is_content_type($content_type = []){
		return __array_keys_exist(['parameters', 'subtype', 'type', 'value'], $content_type);
	}
}

if(!function_exists('__is_json_content_type')){
	/**
	 * Checks if the remote request or response has specified a JSON Content-Type.
	 *
	 * @return bool
	 */
	function __is_json_content_type($content_type = []){
		if(!$content_type){
			return wp_is_json_request(); // Checks whether current request is a JSON request, or is expecting a JSON response.
		}
		if(!__is_content_type($content_type)){
			$content_type = __get_content_type($content_type);
			if(!$content_type){
				return false;
			}
		}
		return wp_is_json_media_type($content_type['value']);
	}
}

if(!function_exists('__is_json_wp_die_handler')){
	/**
	 * @return bool|WP_error
	 */
	function __is_json_wp_die_handler($data = []){
		if(!__array_keys_exist(['additional_errors', 'code', 'data', 'message'], $data)){
            return false;
        }
        $error = new \WP_Error($data['code'], $data['message'], $data['data']);
        foreach($data['additional_errors'] as $additional_error){
            if(!__array_keys_exist(['code', 'data', 'message'], $additional_error)){
                continue;
            }
            $error->add($additional_error['code'], $additional_error['message'], $additional_error['data']);
        }
        return $error;
	}
}

if(!function_exists('__json_encode_for_js')){
    /**
     * @return string
     */
    function __json_encode_for_js($data = []){
        if(is_string($data)){
            $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
        } else {
            foreach((array) $data as $key => $value){
                if(!is_scalar($value)){
                    continue;
                }
                $data[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
            }
        }
        return wp_json_encode($data);
    }
}

if(!function_exists('__json_decode')){
	/**
	 * Alias for json_decode().
	 *
	 * Differs from json_decode in that it will return a WP_Error on failure.
	 *
	 * Retrieves the parameters from a JSON-formatted body.
	 *
	 * @return array|stdClass|WP_Error
	 */
	function __json_decode($json = '', $associative = null, $depth = 512, $flags = 0){
        $empty = ($associative || ($flags & JSON_OBJECT_AS_ARRAY)) ? [] : new \stdClass;
		if(!$json){
			return $empty;
		}
		$params = json_decode($json, $associative, $depth, $flags); // Parses the JSON parameters.
		if($params === null && JSON_ERROR_NONE !== json_last_error()){ // Check for a parsing error.
			return __error(__('Invalid JSON body passed.'), [
				'json_error_code' => json_last_error(),
				'json_error_message' => json_last_error_msg(),
				'status' => 400, // Bad request.
			]);
		}
		return $params;
	}
}

if(!function_exists('__mime_content_type')){
	/**
	 * @return string
	 */
	function __mime_content_type($filename = '', $mimes = null){
        $mime = wp_check_filetype($filename, $mimes);
        if($mime['type'] === false && function_exists('mime_content_type')){
            $mime['type'] = mime_content_type($filename);
        }
        return $mime['type'] === false ? '' : $mime['type'];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Cookies
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cookie_delete')){
    /**
     * @return void
     */
    function __cookie_delete($name = ''){
        if(!isset($_COOKIE[$name])){
            return;
        }
        setcookie($name, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
    }
}

if(!function_exists('__cookie_get')){
    /**
     * @return string
     */
    function __cookie_get($name = ''){
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
    }
}

if(!function_exists('__cookie_get_hash')){
    /**
     * @return array
     */
    function __cookie_get_hash($name = ''){
        wp_parse_str(__cookie_get($name), $values);
        return $values;
    }
}

if(!function_exists('__cookie_set')){
    /**
     * @return void
     */
    function __cookie_set($name = '', $value = '', $expires = 0){
        setcookie($name, $value, $expires, COOKIEPATH, COOKIE_DOMAIN, wp_is_home_url_using_https());
    }
}

if(!function_exists('__cookie_set_hash')){
    /**
     * @return void
     */
    function __cookie_set_hash($name = '', $values_obj = [], $expires = 0){
        if(!is_array($values_obj)){
            if(!is_object($values_obj)){
                return;
            }
            $values_obj = __object_to_array($values_obj);
            if(is_wp_error($values_obj)){
                return;
            }
        }
        __cookie_set($name, build_query($values_obj), $expires);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// DateTime
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__current_time')){
	/**
	 * Alias for current_time().
	 *
	 * Differs from current_time in that it will always return a string.
	 *
	 * If 'offset_or_tz' parameter is an empty string, the output is adjusted with the GMT offset in the WordPress option.
	 *
	 * @return string
	 */
	function __current_time($type = 'U', $offset_or_tz = ''){
		if($type === 'timestamp'){
			$type = 'U';
		} elseif($type === 'mysql'){
			$type = 'Y-m-d H:i:s';
		}
		$timezone = $offset_or_tz ? __timezone($offset_or_tz) : wp_timezone();
		$datetime = new \DateTime('now', $timezone);
		return $datetime->format($type);
	}
}

if(!function_exists('__date_convert')){
	/**
	 * @return string
	 */
	function __date_convert($string = '', $fromtz = '', $totz = '', $format = 'Y-m-d H:i:s'){
		$datetime = date_create($string, __timezone($fromtz));
		if($datetime === false){
			return gmdate($format, 0);
		}
		return $datetime->setTimezone(__timezone($totz))->format($format);
	}
}

if(!function_exists('__offset_or_tz')){
	/**
	 * @param string $offset_or_tz Optional. Default GMT offset or timezone string. Must be either a valid offset (-12 to 14) or a valid timezone string.
	 *
	 * @return array
	 */
	function __offset_or_tz($offset_or_tz = ''){
		if(preg_match('/^(-1[0-2]|-?[0-9]|1[0-4])$/', $offset_or_tz)){
			return [
				'gmt_offset' => $offset_or_tz,
				'timezone_string' => '',
			];
		}
		// Map UTC+- timezones to gmt_offsets and set timezone_string to empty.
		if(preg_match('/^UTC[+-]/', $offset_or_tz)){
			return [
				'gmt_offset' => (int) preg_replace('/UTC\+?/', '', $offset_or_tz),
				'timezone_string' => '',
			];
		}
		if(in_array($offset_or_tz, timezone_identifiers_list())){
			return [
				'gmt_offset' => 0,
				'timezone_string' => $offset_or_tz,
			];
		}
		return [
			'gmt_offset' => 0,
			'timezone_string' => 'UTC',
		];
	}
}

if(!function_exists('__timezone')){
	/**
	 * @return DateTimeZone
	 */
	function __timezone($offset_or_tz = ''){
		return new \DateTimeZone(__timezone_string($offset_or_tz));
	}
}

if(!function_exists('__timezone_string')){
	/**
	 * @return string
	 */
	function __timezone_string($offset_or_tz = ''){
		$offset_or_tz = __offset_or_tz($offset_or_tz);
		$timezone_string = $offset_or_tz['timezone_string'];
		if($timezone_string){
			return $timezone_string;
		}
		$offset = (float) $offset_or_tz['gmt_offset'];
		$hours = (int) $offset;
		$minutes = $offset - $hours;
		$sign = $offset < 0 ? '-' : '+';
		$abs_hour = abs($hours);
		$abs_mins = abs($minutes * 60);
		return sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Dependencies
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__enqueue')){
    /**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return string|WP_Error
     */
    function __enqueue($handle = '', $src = '', $deps = [], $ver = false, $args_media = null, $l10n = []){
        if(!doing_action('admin_enqueue_scripts') && !doing_action('login_enqueue_scripts') && !doing_action('wp_enqueue_scripts')){ // Too early or too late.
            return __error(trim(sprintf(__('Function %1$s was called <strong>incorrectly</strong>. %2$s %3$s'), __FUNCTION__, '', '')));
        }
        if(!$handle){
	        return __error(sprintf(__('Missing parameter(s): %s'), 'handle'));
        }
        if(!wp_http_validate_url($src)){
            if(!is_file($src)){
    	        return __error(sprintf(__('Invalid parameter(s): %s'), 'src'));
            }
            if(!$ver){
                $ver = filemtime($src);
            }
            $src = __path_to_url($src);
        }
        $filename = __basename($src);
        $mimes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
        ];
        $filetype = wp_check_filetype($filename, $mimes);
        if(!$filetype['ext']){
            return __error(__('Sorry, you are not allowed to upload this file type.'));
        }
        if(!is_array($deps)){ // Perhaps it was called directly?
            $deps = [];
        }
        if('css' === $filetype['ext']){
            if(is_null($args_media)){
                $args_media = 'all'; // The media for which this stylesheet has been defined.
            }
            wp_enqueue_style($handle, $src, $deps, $ver, $args_media);
            return $handle;
        }
        $dep = __handle();
        if(!in_array($dep, $deps)){
            $deps[] = $dep;
        }
        if(is_null($args_media)){
            $args_media = []; // An array of additional script loading strategies. Otherwise, it may be a boolean in which case it determines whether the script is printed in the footer.
        }
        wp_enqueue_script($handle, $src, $deps, $ver, $args_media);
        if(!$l10n){
            return $handle;
        }
        __localize($handle, $l10n);
        return $handle;
    }
}

if(!function_exists('__localize')){
    /**
     * @return bool
     */
	function __localize($handle = '', $l10n = []){
        return wp_localize_script($handle, __canonicalize($handle) . '_l10n', $l10n);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Error handling
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__debug_backtrace')){
    /**
     * @return array|WP_error
     */
    function __debug_backtrace($index = 0, $options = 2){ // options: 0: provide args and ignore object, 1: provide args and object, 2: ignore args and object, 3: ignore args and provide object.
        $index = __absint($index) + 1;
        $limit = $index + 1;
        $debug_backtrace = debug_backtrace($options, $limit);
        $debug_count = count($debug_backtrace);
        if($limit > $debug_count){
            return __error(sprintf(__('%1$s must be less than or equal to %2$d'), '"index"', $debug_count));
        }
        $atts = $debug_backtrace[$index];
        $pairs = [
            'args' => [],
            'class' => '',
            'file' => '',
            'function' => '',
            'line' => 0,
            'object' => null,
            'type' => '',
        ];
        return shortcode_atts($pairs, $atts);
    }
}

if(!function_exists('__debug_context')){
    /**
     * @return array|WP_error
     */
    function __debug_context($index = 0){
        $index = __absint($index) + 1;
        $debug_backtrace = __debug_backtrace($index);
        if(is_wp_error($debug_backtrace)){
            return $debug_backtrace;
        }
        $context = [
            'file' => $debug_backtrace['file'],
            'name' => '',
            'namespace_name' => '',
            'reflector' => '',
            'short_name' => '',
            'type' => '',
        ];
        if($debug_backtrace['class']){
            $context = __class_context($debug_backtrace['class']);
            if(is_wp_error($context)){
                return $context;
            }
            $context['type'] = 'class';
            return $context;
        }
        if($debug_backtrace['function']){
            if('{closure}' === $debug_backtrace['function']){
                $context['type'] = 'closure';
                return $context;
            }
            $context = __function_context($debug_backtrace['function']);
            if(is_wp_error($context)){
                return $context;
            }
            $context['type'] = 'function';
            return $context;
        }
        return $context;
    }
}

if(!function_exists('__caller_file')){
    /**
     * @return string|WP_error
     */
    function __caller_file($index = 0){
        $index = __absint($index) + 1;
        $context = __debug_context($index);
        if(is_wp_error($context)){
            return $context;
        }
        if('class' === $context['type'] && !$context['file']){
            return sprintf('The "%s" class is defined in the PHP core or in a PHP extension.', $context['name']);
        }
        if('function' === $context['type'] && !$context['file']){
            return sprintf('The "%s" function is defined in the PHP core or in a PHP extension.', $context['name']);
        }
        return $context['file'] ? $context['file'] : __error(__first_p(__('File does not exist! Please double check the name and try again.')));
    }
}

if(!function_exists('__class_context')){
    /**
     * @return array|WP_Error
     */
    function __class_context($class = ''){
        if(!$class){
            return __error(sprintf(__('The "%s" argument must be a non-empty string.'), 'class'));
        }
        if(!class_exists($class)){
            return __error(sprintf(__('Invalid parameter(s): %s'), 'class'));
        }
        $reflector = new \ReflectionClass($class);
        return __reflector_context($reflector);
    }
}

if(!function_exists('__error')){
	/**
	 * Alias for new WP_Error::__construct().
	 *
	 * @return WP_Error
	 */
	function __error($message = '', $data = ''){
		if(is_wp_error($message)){
			return $message;
		}
		if(!$message){
			$message = __('An error occurred.'); // Something went wrong.
		}
		return new \WP_Error(__str_prefix('error'), $message, $data);
	}
}

if(!function_exists('__exit_with_error')){
	/**
	 * @return void
	 */
	function __exit_with_error($message = '', $title = '', $args = []){
		if(is_wp_error($message)){
			$message = $message->get_error_message();
			if($title && !$args){
				$args = $title;
				$title = '';
			}
		}
		if(!$message){
			$message = __('Error');
		}
        if(is_int($args)){
            $args = [
                'response' => $args,
            ];
        }
        if(is_int($title)){
            if(!isset($args['response'])){
                $args['response'] = $title;
            }
            $title = get_status_header_desc($title);
        }
		if(!$title){
			$title = __('An error occurred.'); // Something went wrong.
		}
        $html = '<h1>' . $title . '</h1>';
        $html .= '<p>' . $message . '</p>';
        $referer = wp_get_referer();
        if($referer){
            $back = __('Go back');
        } else {
            $back = __go_to(get_bloginfo('title', 'display'));
            $referer = home_url('/');
        }
        $html_link = sprintf('<a href="%s">%s</a>', esc_url($referer), $back);
        $html .= '<p>' . $html_link . '</p>';
        wp_die($html, $title, $args);
	}
}

if(!function_exists('__function_context')){
    /**
     * @return array|WP_Error
     */
    function __function_context($function = ''){
        if(empty($function)){
            return __error(sprintf(__('The "%s" argument must be a non-empty string.'), 'function'));
        }
        if(!function_exists($function)){
            return __error(sprintf(__('Invalid parameter(s): %s'), 'function'));
        }
        $reflector = new \ReflectionFunction($function);
        return __reflector_context($reflector);
    }
}

if(!function_exists('__reflector_context')){
    /**
     * @return array|WP_Error
     */
    function __reflector_context($reflector = null){
        if(!$reflector instanceof \Reflector){
            return __error(__('Invalid object type.'));
        }
        return [
            'file' => $reflector->getFileName(),
            'name' => $reflector->getName(),
            'namespace_name' => $reflector->getNamespaceName(),
            'reflector' => $reflector,
            'short_name' => $reflector->getShortName(),
        ];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Files
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__basename')){
	/**
	 * @return string
	 */
	function __basename($path = '', $suffix = ''){
		return wp_basename(__remove_query($path), $suffix);
	}
}

if(!function_exists('__check_dir')){
	/**
	 * @return string|WP_Error
	 */
	function __check_dir($dir = ''){
		return ($dir && (!@is_dir($dir) || !wp_is_writable($dir))) ? __error(__('Destination directory for file streaming does not exist or is not writable.')) : $dir;
	}
}

if(!function_exists('__check_upload_size')){
	/**
	 * Alias for WP_REST_Attachments_Controller::check_upload_size().
	 *
	 * @return true|WP_Error
	 */
	function __check_upload_size($file_size = 0){
		if(!is_multisite()){
			return true;
		}
		if(get_site_option('upload_space_check_disabled')){
			return true;
		}
		$space_left = get_upload_space_available();
		if($space_left < $file_size){
			return __error(sprintf(__('Not enough space to upload. %s KB needed.'), number_format(($file_size - $space_left) / KB_IN_BYTES)));
		}
		if($file_size > (KB_IN_BYTES * get_site_option('fileupload_maxk', 1500))){
			return __error(sprintf(__('This file is too big. Files must be less than %s KB in size.'), get_site_option('fileupload_maxk', 1500)));
		}
		if(!function_exists('upload_is_user_over_quota')){
			require_once ABSPATH . 'wp-admin/includes/ms.php'; // Include multisite admin functions to get access to upload_is_user_over_quota().
		}
		if(upload_is_user_over_quota(false)){
			return __error(__('You have used your space quota. Please delete files before uploading.'));
		}
		return true;
	}
}

if(!function_exists('__delete_file')){
	/**
	 * @return string
	 */
	function __delete_file($file = ''){
        $fs = __get_filesystem();
        if(is_wp_error($fs)){
            return false;
        }
        if(!$fs->is_file($file)){
            return false;
        }
        return $fs->delete($file, false, 'f');
	}
}

if(!function_exists('__dirlist')){
	/**
	 * Alias for WP_Filesystem_Direct::dirlist().
	 *
	 * Differs from WP_Filesystem_Direct::dirlist in that it will return a WP_Error on failure.
	 *
	 * @return array|WP_error
	 */
	function __dirlist($path = '', $include_hidden = true, $recursive = false){
        $fs = __get_filesystem();
        if(is_wp_error($fs)){
            return $fs;
        }
        $ret = $fs->dirlist($path, $include_hidden, $recursive);
        if($ret === false){
            return __error(__('Error:') . ' ' . str_replace(__('Stylesheet'), wp_basename($path), __('Stylesheet is not readable.')));
        }
        return $ret;
    }
}

if(!function_exists('__dir_join')){
	/**
	 * Like path_join(), but for directories.
	 *
	 * @return string
	 */
	function __dir_join($base = '', $dir = ''){
        $dir = untrailingslashit(ltrim($dir, '/'));
        if(!$dir){
            return $base;
        }
		return path_join($base, $dir);
	}
}

if(!function_exists('__get_file_sample')){
	/**
	 * @return string
	 */
	function __get_file_sample($tmpfname = ''){
		if(!is_file($tmpfname)){
			return '';
		}
		$tmpf = fopen($tmpfname, 'rb'); // Retrieve a sample of the response body for debugging purposes.
		if(!$tmpf){
			return '';
		}
		$response_size = apply_filters('download_url_error_max_body_size', KB_IN_BYTES); // Filters the maximum error response body size. Default 1 KB.
		$sample = fread($tmpf, $response_size);
		fclose($tmpf);
		return $sample;
	}
}

if(!function_exists('__get_filesystem')){
	/**
	 * @return WP_Filesystem_Base|WP_Error
	 */
	function __get_filesystem(){
        global $wp_filesystem;
        if($wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && !$wp_filesystem->errors->has_errors()){
            return $wp_filesystem;
        }
        // Check filesystem credentials.
        ob_start();
        if(!function_exists('request_filesystem_credentials')){
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
        $url = self_admin_url();
        $credentials = request_filesystem_credentials($url);
        ob_end_clean();
        if($credentials === false || !WP_Filesystem($credentials)){
            // Pass through the error from WP_Filesystem if one was raised.
            if($wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->has_errors()){
                $error = __error(__('Filesystem error.'));
                $error->merge_from($wp_filesystem->errors);
                return $error;
            }
            return __error(__('Could not access filesystem.'));
        }
        return $wp_filesystem;
	}
}

if(!function_exists('__get_memory_size')){
	/**
	 * @return int
	 */
	function __get_memory_size(){
		if(!function_exists('exec')){
			$current_limit = ini_get('memory_limit');
			$current_limit_int = wp_convert_hr_to_bytes($current_limit);
			return $current_limit_int;
		}
		exec('free -b', $output);
		$output = sanitize_text_field($output[1]);
		$output = explode(' ', $output);
		return (int) $output[1];
	}
}

if(!function_exists('__is_extension_allowed')){
	/**
	 * @return bool
	 */
	function __is_extension_allowed($extension = ''){
        $is_extension_allowed = false;
		foreach(wp_get_mime_types() as $exts => $mime){
			if(preg_match('!^(' . $exts . ')$!i', $extension)){
				$is_extension_allowed = true;
                break;
			}
		}
		return $is_extension_allowed;
	}
}

if(!function_exists('__is_path_in_dir')){
	/**
	 * @return string
	 */
	function __is_path_in_dir($path = '', $dir = ''){
		return str_starts_with(wp_normalize_path($path), wp_normalize_path($dir));
	}
}

if(!function_exists('__is_path_in_mu_plugins_dir')){
	/**
	 * @return bool
	 */
	function __is_path_in_mu_plugins_dir($path = ''){
		return __is_path_in_dir($path, WPMU_PLUGIN_DIR);
	}
}

if(!function_exists('__is_path_in_plugins_dir')){
	/**
	 * @return bool
	 */
	function __is_path_in_plugins_dir($path = ''){
		return __is_path_in_dir($path, WP_PLUGIN_DIR);
	}
}

if(!function_exists('__is_path_in_themes_dir')){
	/**
	 * @return bool
	 */
	function __is_path_in_themes_dir($path = ''){
		return __is_path_in_dir($path, WP_CONTENT_DIR . '/themes');
	}
}

if(!function_exists('__is_path_in_uploads_dir')){
	/**
	 * @return bool
	 */
	function __is_path_in_uploads_dir($path = ''){
		return __is_path_in_dir($path, WP_CONTENT_DIR . '/uploads');
	}
}

if(!function_exists('__is_valid_filename')){
	/**
	 * @return bool
	 */
	function __is_valid_filename($filename = ''){
        $filetype = wp_check_filetype(wp_basename($filename));
        return $filetype['ext'] ? true : false;
    }
}

if(!function_exists('__list_files')){
    /**
	 * @return array|WP_Error
	 */
	function __list_files($path = '', $include_hidden = true){
        $ret = __dirlist($path, $include_hidden);
		if(is_wp_error($ret)){
			return $ret;
		}
        return array_filter($ret, function($ret){
            return $ret['type'] === 'f';
        });
    }
}

if(!function_exists('__mkdir_p')){
	/**
	 * Alias for wp_mkdir_p().
	 *
	 * Differs from wp_mkdir_p in that it will return an error if path wasn't created.
	 *
	 * @return string|WP_Error
	 */
	function __mkdir_p($target = ''){
        $group = 'mkdir_p';
		$key = __uuid($target);
        $cache = __cache_get($key, $group);
        if($cache !== null){
            return $cache;
        }
		if(!wp_mkdir_p($target)){
            $error = __error(__('Could not create directory.'));
            __cache_set($key, $error, $group);
			return $error;
		}
		if(!wp_is_writable($target)){
            $error = __error(__first_p(sprintf(__('The %s directory exists but is not writable. This directory is used for plugin and theme updates. Please make sure the server has write permissions to this directory.'), wp_basename($target))));
            __cache_set($key, $error, $group);
			return $error;
		}
        __cache_set($key, $target, $group);
		return $target;
	}
}

if(!function_exists('__move')){
	/**
	 * @return string
	 */
	function __move($source = '', $destination = '', $overwrite = false){
        $fs = __get_filesystem();
        if(is_wp_error($fs)){
            return '';
        }
        if($fs->is_file($source) && $fs->is_dir($destination)){
            $filename = wp_basename($source);
            if(!$overwrite){
                $filename = wp_unique_filename($destination, $filename);
            }
            $destination = path_join($destination, $filename);
        }
        if(!$fs->move($source, $destination, $overwrite)){
            return '';
        }
        return $destination;
	}
}

if(!function_exists('__path_to_url')){
	/**
	 * @return string
	 */
	function __path_to_url($path = ''){
		return str_replace(wp_normalize_path(ABSPATH), site_url('/'), wp_normalize_path($path));
	}
}

if(!function_exists('__read_file_chunk')){
	/**
	 * @return string
	 */
	function __read_file_chunk($handle = null, $chunk_size = 0, $chunk_lenght = 0){
		$giant_chunk = '';
		if(is_resource($handle) && $chunk_size){
			$byte_count = 0;
			if(!$chunk_lenght){
				$chunk_lenght = 8 * KB_IN_BYTES;
			}
			while(!feof($handle)){
				$chunk = fread($handle, $chunk_lenght);
				$byte_count += strlen($chunk);
				$giant_chunk .= $chunk;
				if($byte_count >= $chunk_size){
					return $giant_chunk;
				}
			}
		}
		return $giant_chunk;
	}
}

if(!function_exists('__remove_query')){
    /**
     * @return string
     */
    function __remove_query($path = ''){
        return preg_replace('/\?.*/', '', $path); // Fix file filename for query strings.
    }
}

if(!function_exists('__test_error')){
	/**
	 * @return true|WP_Error
	 */
	function __test_error($error = 0){ // A successful upload will pass this test.
		$upload_error_strings = [
			false,
			sprintf(__('The uploaded file exceeds the %1$s directive in %2$s.'), 'upload_max_filesize', 'php.ini'),
			sprintf(__('The uploaded file exceeds the %s directive that was specified in the HTML form.'), 'MAX_FILE_SIZE'),
			__('The uploaded file was only partially uploaded.'),
			__('No file was uploaded.'),
			'',
			__('Missing a temporary folder.'),
			__('Failed to write file to disk.'),
			__('File upload stopped by extension.'),
		]; // Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
		return $error > 0 ? __error(isset($upload_error_strings[$error]) ? $upload_error_strings[$error] : __first_p(__('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.'))) : true;
	}
}

if(!function_exists('__test_size')){
	/**
	 * @return true|WP_Error
	 */
	function __test_size($file_size = 0){ // A non-empty file will pass this test.
		return $file_size === 0 ? __error(is_multisite() ? __('File is empty. Please upload something more substantial.') : sprintf(__('File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your %1$s file or by %2$s being defined as smaller than %3$s in %1$s.'), 'php.ini', 'post_max_size', 'upload_max_filesize')) : true;
	}
}

if(!function_exists('__test_type')){
	/**
	 * @return string|WP_Error
	 */
	function __test_type($tmp_name = '', $name = '', $mimes = null){ // A correct MIME type will pass this test.
		$wp_filetype = wp_check_filetype_and_ext($tmp_name, $name, $mimes);
		$ext = empty($wp_filetype['ext']) ? '' : $wp_filetype['ext'];
		$type = empty($wp_filetype['type']) ? '' : $wp_filetype['type'];
		$proper_filename = empty($wp_filetype['proper_filename']) ? '' : $wp_filetype['proper_filename']; // Check to see if wp_check_filetype_and_ext() determined the filename was incorrect.
		if($proper_filename){
			$name = $proper_filename;
		}
		if((!$type or !$ext) && !current_user_can('unfiltered_upload')){
			return __error(__('Sorry, you are not allowed to upload this file type.'));
		}
		return $name;
	}
}

if(!function_exists('__test_uploaded_file')){
	/**
	 * @return true|WP_Error
	 */
	function __test_uploaded_file($tmp_name = ''){ // A properly uploaded file will pass this test.
		return is_uploaded_file($tmp_name) ? true : __error(__('Specified file failed upload test.'));
	}
}

if(!function_exists('__unique_path')){
	/**
	 * @return string
	 */
	function __unique_path($dir = '', $filename = ''){
        return path_join($dir, wp_unique_filename($dir, wp_basename($filename)));
	}
}

if(!function_exists('__upload_dir')){
	/**
	 * @return string|WP_Error
	 */
	function __upload_dir($subdir = ''){
		$upload_dir = wp_get_upload_dir();
		if($upload_dir['error']){
			return __error($upload_dir['error']);
		}
		$path = $upload_dir['basedir'];
	    $subdir = untrailingslashit(ltrim($subdir, '/'));
	    if($subdir){
	        $path .= '/' . $subdir;
	    }
		return __mkdir_p($path);
	}
}

if(!function_exists('__url_to_path')){
	/**
	 * @return string
	 */
	function __url_to_path($url = ''){
	    $site_url = site_url('/');
		return str_starts_with($url, $site_url) ? str_replace($site_url, wp_normalize_path(ABSPATH), $url) : '';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// FingerprintJS
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__use_fingerprintjs')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return void
     */
	function __use_fingerprintjs(){
        $ver = '4.6.2'; // Hardcoded.
        wp_add_inline_script(__handle(), "import('https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@" . $ver . "/+esm').then(f=>f.load()).then(p=>p.get()).then(r=>__do_action('fingerprintjs',r.visitorId)).catch(e=>__do_action('fingerprintjs_error',e))");
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// GitHub
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__github_api_token')){
    /**
     * @return void
     */
    function __github_api_token($token = ''){
        if(!$token){
            return;
        }
        __add_filter_once('http_request_args', '__maybe_add_github_api_token', 10, 2);
		__cache_set('github_api_token', $token);
    }
}

if(!function_exists('__github_api_token_exists')){
    /**
     * @return void
     */
    function __github_api_token_exists(){
		return __cache_exists('github_api_token');
    }
}

if(!function_exists('__maybe_add_github_api_token')){
	/**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'http_request_args' filter hook.
	 *
	 * @return array
	 */
	function __maybe_add_github_api_token($parsed_args, $url){
		if(!doing_filter('http_request_args')){ // Too early or too late.
	        return $parsed_args;
	    }
        $token = __cache_get('github_api_token');
        if($token === null){
            return $parsed_args;
        }
        if(!str_starts_with($url, 'https://api.github.com/')){
            return $parsed_args;
        }
        if(!isset($parsed_args['headers'])){
            $parsed_args['headers'] = [];
        }
        $parsed_args['headers']['Authorization'] = 'token ' . $token;
		return $parsed_args;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Google
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__hide_recaptcha_badge')){
	/**
	 * @return void
	 */
	function __hide_recaptcha_badge(){
		if(doing_action('wp_enqueue_scripts')){ // Just in time.
	        __hide_recaptcha_badge();
			return;
	    }
		if(did_action('wp_enqueue_scripts')){ // Too late.
			return;
		}
		__add_action_once('wp_enqueue_scripts', '__maybe_hide_recaptcha_badge');
	}
}

if(!function_exists('__is_google_workspace')){
	/**
	 * @return string|false
	 */
	function __is_google_workspace($domain = ''){
        if(!__is_domain($domain)){
            if(!is_email($domain)){
    			return false;
    		}
            list($local, $domain) = explode('@', $domain, 2);
        }
		if(strtolower($domain) === 'gmail.com'){
			return 'gmail.com';
		}
		if(!getmxrr($domain, $mxhosts)){
			return false;
		}
		if(!in_array('aspmx.l.google.com', $mxhosts)){
			return false;
		}
		return strtolower($domain);
	}
}

if(!function_exists('__get_recaptcha_branding')){
	/**
	 * @return string
	 */
	function __get_recaptcha_branding(){
		return 'This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a> apply.';
	}
}

if(!function_exists('__hide_recaptcha_badge')){
    /**
	 * This function MUST be called inside the 'wp_enqueue_scripts' action hook.
	 *
	 * @return void
	 */
	function __hide_recaptcha_badge(){
		if(!doing_action('wp_enqueue_scripts')){ // Too early or too late.
	        return;
	    }
        wp_add_inline_style(__handle(), '.grecaptcha-badge{visibility:hidden!important}');
	}
}

if(!function_exists('__maybe_hide_recaptcha_badge')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'wp_enqueue_scripts' action hook.
	 *
	 * @return void
	 */
	function __maybe_hide_recaptcha_badge(){
		if(!doing_action('wp_enqueue_scripts')){ // Too early or too late.
	        return;
	    }
		__hide_recaptcha_badge();
	}
}

if(!function_exists('__use_google_api_php_client')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_google_api_php_client(){
        if(is_php_version_compatible('8.3')){
            $ver = '2.18.3'; // Hardcoded.
            $url = 'https://github.com/googleapis/google-api-php-client/releases/download/v' . $ver . '/google-api-php-client-v' . $ver . '-PHP8.3.zip';
        } elseif(is_php_version_compatible('8.0')){
            $ver = '2.18.3'; // Hardcoded.
            $url = 'https://github.com/googleapis/google-api-php-client/releases/download/v' . $ver . '/google-api-php-client-v' . $ver . '-PHP8.0.zip';
        } elseif(is_php_version_compatible('7.4')){
            $ver = '2.16.0'; // Hardcoded.
            $url = 'https://github.com/googleapis/google-api-php-client/releases/download/v' . $ver . '/google-api-php-client--PHP7.4.zip';
        } elseif(is_php_version_compatible('7.0')){
            $ver = '2.14.0'; // Hardcoded.
            $url = 'https://github.com/googleapis/google-api-php-client/releases/download/v' . $ver . '/google-api-php-client--PHP7.0.zip';
        } else {
            $ver = '2.14.0'; // Hardcoded.
            $url = 'https://github.com/googleapis/google-api-php-client/releases/download/v' . $ver . '/google-api-php-client--PHP5.6.zip';
        }
        return __use($url, [
            'autoload' => 'vendor/autoload.php',
            'validation_class' => 'Google\Client',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Hooks
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_action')){
    /**
     * Alias for add_action().
     *
     * Differs from add_action in that it will always return a string.
     *
     * @return string
     */
    function __add_action($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        return __on($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__add_action_once')){
    /**
     * @return string
     */
    function __add_action_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        return __one($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__add_filter')){
    /**
     * Alias for add_filter().
     *
     * Differs from add_filter in that it will always return a string.
     *
     * @return string
     */
    function __add_filter($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        return __on($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__add_filter_once')){
    /**
     * @return string
     */
    function __add_filter_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        return __one($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__remove_action')){
    /**
     * Alias for remove_action().
     *
     * @return bool
     */
    function __remove_action($hook_name = '', $callback = null, $priority = 10){
        return __off($hook_name, $callback, $priority);
    }
}

if(!function_exists('__remove_filter')){
    /**
     * Alias for remove_filter().
     *
     * @return bool
     */
    function __remove_filter($hook_name = '', $callback = null, $priority = 10){
        return __off($hook_name, $callback, $priority);
    }
}

if(!function_exists('__callback_idx')){
    /**
     * This function’s access is marked private.
     *
     * @return string
     */
    function __callback_idx($callback = null){
        return _wp_filter_build_unique_id('', $callback, 0);
    }
}

if(!function_exists('__callback_md5')){
    /**
     * This function’s access is marked private.
     *
     * @return string
     */
    function __callback_md5($callback = null){
        $md5 = md5(__callback_idx($callback));
        if(!__is_closure($callback)){
            return $md5;
        }
        $md5_closure = __md5_closure($callback);
        return is_wp_error($md5_closure) ? $md5 : $md5_closure;
    }
}

if(!function_exists('__off')){
    /**
     * @return string
     */
    function __off($hook_name = '', $callback = null, $priority = 10){
        if(!is_null($callback)){
            $key = __uuid($hook_name . '-' . __callback_md5($callback));
            __cache_delete($key, 'hooks');
        }
        return remove_filter($hook_name, $callback, $priority);
    }
}

if(!function_exists('__on')){
    /**
     * @return string
     */
    function __on($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        if(is_null($callback)){
            return '';
        }
        add_filter($hook_name, $callback, $priority, $accepted_args);
        return __callback_idx($callback);
    }
}

if(!function_exists('__one')){
    /**
     * @return string
     */
    function __one($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        if($callback === null){
            return '';
        }
        $group = 'hooks';
        $key = __uuid($hook_name . '-' . __callback_md5($callback));
        $idx = __cache_get($key, $group);
        if($idx !== null){
            return $idx;
        }
        $idx = __on($hook_name, $callback, $priority, $accepted_args);
        __cache_set($key, $idx, $group);
        return $idx;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Image sizes
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_image_size')){
	/**
	 * @return void
	 */
	function __add_image_size($name = '', $width = 0, $height = 0, $crop = false){
		$image_sizes = get_intermediate_image_sizes();
		$size = __canonicalize($name);
		if(in_array($size, $image_sizes)){
			return; // Does NOT overwrite.
		}
		add_image_size($size, $width, $height, $crop);
        __add_filter_once('image_size_names_choose', '__maybe_add_image_size_names');
		__cache_set_multiple($size, $name, 'image_sizes');
	}
}

if(!function_exists('__maybe_add_image_size_names')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'image_size_names_choose' filter hook.
	 *
	 * @return array
	 */
	function __maybe_add_image_size_names($sizes){
		if(!doing_filter('image_size_names_choose')){ // Too early or too late.
	        return $sizes;
	    }
		$image_sizes = __cache_get_group('image_sizes');
		foreach($image_sizes as $size => $name){
			$sizes[$size] = $name;
		}
		return $sizes;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// jQuery ScrollSpy
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__enqueue_jquery_scrollspy')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return void
     */
	function __enqueue_jquery_scrollspy($deps = []){
        if(!doing_action('admin_enqueue_scripts') && !doing_action('login_enqueue_scripts') && !doing_action('wp_enqueue_scripts')){
            return; // Too early or too late.
        }
        $dir = __use_jquery_scrollspy();
        if(is_wp_error($dir)){
            return; // Silence is golden.
        }
        $base_path = __path_to_url($dir);
        $ver = '0.1.3'; // Hardcoded.
        __enqueue('jquery-scrollspy', $base_path . '/scrollspy.js', $deps, $ver);
	}
}

if(!function_exists('__use_jquery_scrollspy')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_jquery_scrollspy(){
        $ver = '0.1.3'; // Hardcoded.
        $url = 'https://github.com/thesmart/jquery-scrollspy/archive/refs/tags/' . $ver . '.zip';
        return __use($url, [
			'expected_dir' => 'jquery-scrollspy-' . $ver,
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Login pages
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__custom_interim_login_page')){
    /**
     * @return void
     */
    function __custom_interim_login_page($post_id = 0){
        $post_id = __sanitize_login_page_id($post_id);
        if(!$post_id){
            return;
        }
        __add_action_once('login_form_login', '__maybe_redirect_login_form_login');
        __cache_set('interim_login', $post_id, 'login_forms');
    }
}

if(!function_exists('__custom_login_page')){
    /**
     * @return void
     */
    function __custom_login_page($post_id = 0, $interim_login = false){
        $post_id = __sanitize_login_page_id($post_id);
        if(!$post_id){
            return;
        }
        __add_action_once('login_form_login', '__maybe_redirect_login_form_login');
        __cache_set('login', $post_id, 'login_forms');
        if(!$interim_login){
            return;
        }
        __custom_interim_login_page($post_id);
    }
}

if(!function_exists('__custom_lostpassword_page')){
    /**
     * @return void
     */
    function __custom_lostpassword_page($post_id = 0){
        $post_id = __sanitize_login_page_id($post_id);
        if(!$post_id){
            return;
        }
        __add_action_once('login_form_lostpassword', '__maybe_redirect_login_form_lostpassword');
        __add_action_once('login_form_retrievepassword', '__maybe_redirect_login_form_lostpassword');
        __cache_set('lostpassword', $post_id, 'login_forms');
    }
}

if(!function_exists('__custom_retrievepassword_page')){
    /**
	 * Alias for __custom_lostpassword_page().
	 *
     * @return void
     */
    function __custom_retrievepassword_page($post_id = 0){
        __custom_lostpassword_page($post_id);
    }
}

if(!function_exists('__custom_register_page')){
    /**
     * @return void
     */
    function __custom_register_page($post_id = 0){
        $post_id = __sanitize_login_page_id($post_id);
        if(!$post_id){
            return;
        }
        __add_action_once('login_form_register', '__maybe_redirect_login_form_register');
        __cache_set('register', $post_id, 'login_forms');
    }
}

if(!function_exists('__custom_resetpass_page')){
    /**
     * @return void
     */
    function __custom_resetpass_page($post_id = 0){
        $post_id = __sanitize_login_page_id($post_id);
        if(!$post_id){
            return;
        }
        __add_action_once('login_form_resetpass', '__maybe_redirect_login_form_resetpass');
        __add_action_once('login_form_rp', '__maybe_redirect_login_form_resetpass');
        __cache_set('resetpass', $post_id, 'login_forms');
    }
}

if(!function_exists('__custom_rp_page')){
    /**
	 * Alias for __custom_resetpass_page().
	 *
     * @return void
     */
    function __custom_rp_page($post_id = 0){
        __custom_resetpass_page($post_id);
    }
}

if(!function_exists('__maybe_redirect_login_form_login')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_form_login' action hook.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_login(){
        if(!doing_action('login_form_login')){ // Too early or too late.
	        return;
	    }
        $action = isset($_REQUEST['interim-login']) ? 'interim_login' : 'login';
        $post_id = __cache_get($action, 'login_forms');
        if($post_id === null){
            return;
        }
        $url = get_permalink($post_id);
        if($_GET){
            $args = urlencode_deep($_GET); // This re-URL-encodes things that were already in the query string.
            $url = add_query_arg($args, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_lostpassword')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_form_lostpassword' or 'login_form_retrievepassword' action hooks.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_lostpassword(){
        if(!doing_action('login_form_lostpassword') && !doing_action('login_form_retrievepassword')){ // Too early or too late.
	        return;
	    }
        $post_id = __cache_get('lostpassword', 'login_forms');
        if($post_id === null){
            return;
        }
        $url = get_permalink($post_id);
        if($_GET){
            $_GET = urlencode_deep($_GET); // This re-URL-encodes things that were already in the query string.
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_register')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_form_register' action hook.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_register(){
        if(!doing_action('login_form_register')){ // Too early or too late.
	        return;
	    }
        $post_id = __cache_get('register', 'login_forms');
        if($post_id === null){
            return;
        }
        $url = get_permalink($post_id);
        if($_GET){
            $_GET = urlencode_deep($_GET); // This re-URL-encodes things that were already in the query string.
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_resetpass')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_form_resetpass' or 'login_form_rp' action hooks.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_resetpass(){
        if(!doing_action('login_form_resetpass') && !doing_action('login_form_rp')){ // Too early or too late.
	        return;
	    }
        $post_id = __cache_get('resetpass', 'login_forms');
        if($post_id === null){
            return;
        }
        $url = get_permalink($post_id);
        if($_GET){
            $_GET = urlencode_deep($_GET); // This re-URL-encodes things that were already in the query string.
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__sanitize_login_page_id')){
    /**
     * This function’s access is marked private.
     *
     * @return int
     */
    function __sanitize_login_page_id($post_id = 0){
        $post = get_post($post_id);
        return ($post === null || $post->post_type !== 'page' || $post->post_status !== 'publish') ? 0 : $post->ID;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// MD5
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_md5')){
	/**
	 * @return bool
	 */
	function __is_md5($string = ''){
        return (is_string($string) && preg_match('/^[a-f0-9]{32}$/i', $string) === 1);
	}
}

if(!function_exists('__md5')){
	/**
	 * @return string
	 */
	function __md5($data = ''){
        if(is_scalar($data)){
			return md5($data);
		}
        if(__is_closure($data)){
			$md5_closure = __md5_closure($data);
			if(!is_wp_error($md5_closure)){
				return $md5_closure;
			}
            $data = $md5_closure;
        }
        if(is_array($data)){
			$data = __ksort_deep($data);
		}
		return md5(serialize($data));
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Miscellaneous
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__absint')){
	/**
	 * @return int
	 */
	function __absint($maybeint = 0){
		return is_numeric($maybeint) ? absint($maybeint) : 0; // Make sure the value is numeric to avoid casting objects, for example, to int 1.
	}
}

if(!function_exists('__breadcrumbs')){
	/**
	 * @return string
	 */
	function __breadcrumbs($breadcrumbs = [], $separator = '>'){
	    $elements = [];
	    foreach($breadcrumbs as $breadcrumb){
			$breadcrumb = wp_parse_args($breadcrumb, [
				'link' => '',
				'target' => '_self',
				'text' => '',
			]);
	        if(!$breadcrumb['text']){
	            continue;
	        }
	        $elements[] = $breadcrumb['link'] ? sprintf('<a href="%1$s" target="%2$s">%3$s</a>', esc_url($breadcrumb['link']), esc_attr($breadcrumb['target']), esc_html($breadcrumb['text'])) : sprintf('<span>%1$s</span>', esc_html($breadcrumb['text']));
	    }
		return implode(' ' . trim($separator) . ' ', $elements);
	}
}

if(!function_exists('__current_screen_in')){
	/**
	 * @return bool
	 */
	function __current_screen_in($ids = []){
		global $current_screen;
		if(!is_array($ids)){
			return false;
		}
		if(!isset($current_screen)){
			return false;
		}
		return in_array($current_screen->id, $ids);
	}
}

if(!function_exists('__current_screen_is')){
	/**
	 * @return bool
	 */
	function __current_screen_is($id = ''){
		global $current_screen;
		if(!is_string($id)){
			return false;
		}
		if(!isset($current_screen)){
			return false;
		}
		return $current_screen->id === $id;
	}
}

if(!function_exists('__custom_login_logo')){
    /**
     * @return void
     */
    function __custom_login_logo($attachment_id = 0, $half = true){
        if(!wp_attachment_is_image($attachment_id)){
            return;
        }
        $custom_logo = wp_get_attachment_image_src($attachment_id, 'medium');
        $height = $custom_logo[2];
        $width = $custom_logo[1];
        if($width > 300){ // Fix for SVG.
            $r = 300 / $width;
			$height *= $r;
            $width = 300;
        }
        if($half){
            $height = $height / 2;
            $width = $width / 2;
        }
		__add_action_once('login_enqueue_scripts', '__maybe_replace_login_logo');
        __cache_set('custom_login_logo', [$custom_logo[0], $width, $height]);
    }
}

if(!function_exists('__delete_options')){
	/**
	 * @return void
	 */
	function __delete_options($prefix = ''){
		global $wpdb;
        if(!$prefix){
            return;
        }
        $options = $wpdb->get_col("SELECT option_name FROM $wpdb->options WHERE option_name LIKE '{$prefix}%'");
        foreach($options as $option){
            delete_option($option);
        }
	}
}

if(!function_exists('__exec')){
    /**
     * @return array|WP_Error
     */
    function __exec($command = ''){
        $output = [];
		if(!function_exists('exec')){
			return __error(sprintf(__('Function %s used incorrectly in PHP.'), 'exec'));
		}
        try {
            $result = exec($command, $output);
        } catch(\Throwable $t){
            $result = __error($t->getMessage());
        } catch(\Exception $e){
            $result = __error($e->getMessage());
        }
        return is_wp_error($result) ? $result : $output;
    }
}

if(!function_exists('__format_atts')){
    /**
     * Returns a formatted string of HTML attributes.
     *
     * @param array $atts Associative array of attribute name and value pairs.
     * @return string Formatted HTML attributes.
     */
    function __format_atts($atts = [], $tag = ''){
        $atts_filtered = [];
        foreach($atts as $name => $value){
            $name = strtolower(trim($name));
            if(!preg_match('/^[a-z_:][a-z_:.0-9-]*$/', $name)){
                continue;
            }
            static $boolean_attributes = ['checked', 'disabled', 'inert', 'multiple', 'readonly', 'required', 'selected'];
            if(in_array($name, $boolean_attributes) && $value === ''){
                $value = false;
            }
            if(is_numeric($value)){
                $value = (string) $value;
            }
            if($value === null || $value === false){
                unset($atts_filtered[$name]);
            } elseif($value === true){
                $atts_filtered[$name] = $name; // boolean attribute
            } elseif(is_string($value)){
                $atts_filtered[$name] = trim($value);
            }
        }
        $output = '';
        foreach($atts_filtered as $name => $value){
            $output .= sprintf(' %1$s="%2$s"', $name, esc_attr($value));
        }
        $output = trim($output);
        $tag = trim($tag);
        if(!$tag){
            return $output;
        }
        if(!$output){
            return $tag;
        }
        return $tag . ' ' . $output;
    }
}

if(!function_exists('__format_function')){
	/**
	 * @return string
	 */
	function __format_function($function_name = '', $args = []){
		$str = '<span style="color: #24831d; font-family: monospace; font-weight: 400;">' . $function_name . '(';
		$function_args = [];
		foreach($args as $arg){
			$arg = shortcode_atts([
				'default' => 'null',
				'name' => '',
				'type' => '',
			], $arg);
			if($arg['default'] && $arg['name'] && $arg['type']){
				$function_args[] = '<span style="color: #cd2f23; font-family: monospace; font-style: italic; font-weight: 400;">' . $arg['type'] . '</span> <span style="color: #0f55c8; font-family: monospace; font-weight: 400;">$' . $arg['name'] . '</span> = <span style="color: #000; font-family: monospace; font-weight: 400;">' . $arg['default'] . '</span>';
			}
		}
		if($function_args){
			$str .= ' ' . implode(', ', $function_args) . ' ';
		}
		$str .= ')</span>';
		return $str;
	}
}

if(!function_exists('__get_country')){
	/**
	 * @return string
	 */
	function __get_country($default = ''){
        $cloudflare_country = __get_cloudflare_country();
        $wordfence_country = __wf_get_country();
		switch(true){
			case !empty($cloudflare_country):
				$country = $cloudflare_country;
				break;
            case !empty($wordfence_country):
				$country = $wordfence_country;
				break;
			default:
				$country = $default;
		}
		return preg_match('/^[a-zA-Z]{2}$/', $country) ? strtoupper($country) : ''; // ISO 3166-1 alpha-2.
	}
}

if(!function_exists('__get_ip')){
	/**
	 * @return string
	 */
	function __get_ip($default = ''){
        $cloudflare_ip = __get_cloudflare_ip();
        $wordfence_ip = __wf_get_ip();
		switch(true){
			case !empty($cloudflare_ip):
				$ip = $cloudflare_ip;
				break;
			case !empty($wordfence_ip):
				$ip = $wordfence_ip;
				break;
			case !empty($_SERVER['HTTP_X_FORWARDED_FOR']):
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				break;
			case !empty($_SERVER['HTTP_X_REAL_IP']):
				$ip = $_SERVER['HTTP_X_REAL_IP'];
				break;
			case !empty($_SERVER['REMOTE_ADDR']):
				$ip = $_SERVER['REMOTE_ADDR'];
				break;
			default:
				$ip = $default;
		}
		return \WP_Http::is_ip_address($ip) ? $ip : '';
	}
}

if(!function_exists('__get_redirect_to')){
	/**
	 * @return string
	 */
	function __get_redirect_to($fallback = ''){
		$redirect_to = isset($_REQUEST['redirect_to']) ? wp_http_validate_url($_REQUEST['redirect_to']) : false;
		if(!$redirect_to && $fallback){
			$redirect_to = wp_http_validate_url($fallback);
		}
		return $redirect_to ? $redirect_to : '';
	}
}

if(!function_exists('__go_to')){
	/**
	 * @return bool
	 */
	function __go_to($str = ''){
		return trim(str_replace('&larr;', '', sprintf(_x('&larr; Go to %s', 'site'), $str)));
	}
}

if(!function_exists('__has_shortcode')){
	/**
	 * @return array
	 */
	function __has_shortcode($content = '', $tag = ''){
	    if(strpos($content, '[') === false){
	        return [];
	    }
	    if(!shortcode_exists($tag)){
	        return [];
	    }
	    preg_match_all('/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER);
	    if(!$matches){
	        return [];
	    }
	    foreach($matches as $shortcode){
	        if($tag === $shortcode[2]){
	            return shortcode_parse_atts($shortcode[3]);
	        }
	        if(!$shortcode[5]){
	            continue;
	        }
	        $attr = __has_shortcode($shortcode[5], $tag);
	        if(!$attr){
	            continue;
	        }
	        return $attr;
	    }
	    return [];
	}
}

if(!function_exists('__host_url')){
	/**
     * @return string
     */
    function __host_url($url = ''){
        $host = wp_parse_url(sanitize_url($url), PHP_URL_HOST);
        if(is_null($host)){
            return '';
        }
        return substr($url, 0, (strpos($url, $host) + strlen($host)));
    }
}

if(!function_exists('__is_debug_enabled')){
	/**
	 * @return bool
	 */
	function __is_debug_enabled(){
        return (defined('WP_DEBUG') && WP_DEBUG);
    }
}

if(!function_exists('__is_doing_heartbeat')){
	/**
	 * @return bool
	 */
	function __is_doing_heartbeat(){
		return (wp_doing_ajax() && isset($_POST['action']) && $_POST['action'] === 'heartbeat');
	}
}

if(!function_exists('__is_domain')){
    /**
	 * Like is_email(), but for domains.
	 *
	 * @return string|false
	 */
    function __is_domain($domain = ''){
        if(strlen($domain) < 4){ // Test for the minimum length the domain can be.
            return false;
        }
        if(strpos($domain, '@') !== false){ // Test for an @ character.
            return false;
        }
        if(preg_match('/\.{2,}/', $domain)){ // Test for sequences of periods.
            return false;
        }
        if(trim($domain, " \t\n\r\0\x0B.") !== $domain){ // Test for leading and trailing periods and whitespace.
            return false;
        }
        $subs = explode('.', $domain); // Split the domain into subs.
        if(2 > count($subs)){ // Assume the domain will have at least two subs.
            return false;
        }
        foreach($subs as $sub){ // Loop through each sub.
            if(trim($sub, " \t\n\r\0\x0B-") !== $sub){ // Test for leading and trailing hyphens and whitespace.
                return false;
            }
            if(!preg_match('/^[a-z0-9-]+$/i', $sub)){ // Test for invalid characters.
                return false;
            }
        }
        return $domian; // Congratulations, your domain made it!
    }
}

if(!function_exists('__is_false')){
	/**
	 * @return bool
	 */
	function __is_false($data = ''){
		return in_array((string) $data, ['0', 'false', 'off'], true);
	}
}

if(!function_exists('__is_frontend')){
	/**
	 * @return bool
	 */
    function __is_frontend(){
        global $wp_query;
        if(is_admin()){
            return false; // The current request is for an administrative interface page.
        }
        if(wp_doing_ajax()){
            return false; // The current request is a WordPress Ajax request.
        }
        if(wp_is_serving_rest_request()){
            return false; // WordPress is currently serving a REST API request.
        }
        if(wp_is_json_request()){
            return false; // The current request is a JSON request, or is expecting a JSON response.
        }
        if(wp_is_jsonp_request()){
            return false; // The current request is a JSONP request, or is expecting a JSONP response.
        }
        if(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST){
            return false; // The current request is a WordPress XML-RPC request.
        }
        if(wp_is_xml_request() || (isset($wp_query) && (is_feed() || is_comment_feed() || is_trackback()))){
            return false; // The current request is an XML request, or is expecting an XML response.
        }
        return true;
    }
}

if(!function_exists('__is_mysql_date')){
	/**
	 * @return bool
	 */
	function __is_mysql_date($subject = ''){
		return preg_match('/^\d{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01]) ([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/', $subject);
	}
}

if(!function_exists('__is_name')){
	/**
     * A valid name starts with a letter or underscore, followed by any number of letters, numbers, or underscores.
     *
     * @link https://www.php.net/manual/en/functions.user-defined.php
     *
	 * @return bool
	 */
	function __is_name($name = ''){
		return preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name);
	}
}

if(!function_exists('__is_post_edit')){
    /**
     * @return int
     */
    function __is_post_edit($post_type = ''){
        global $hook_suffix;
        if(!is_admin()){
            return 0;
        }
        if('post.php' !== $hook_suffix){
            return 0;
        }
        if(!isset($_GET['action'], $_GET['post'])){
			return 0;
		}
        if('edit' !== $_GET['action']){
			return 0;
		}
        $post_id = (int) $_GET['post'];
        if(!$post_type){
            return $post_id;
        }
        if($post_type !== get_post_type($post_id)){
			return 0;
		}
        return $post_id;
    }
}

if(!function_exists('__is_post_list')){
    /**
     * @return bool
     */
    function __is_post_list($post_type = ''){
        global $hook_suffix;
        if(!is_admin()){
            return false;
        }
        if('edit.php' !== $hook_suffix){
            return false;
        }
        if(!$post_type){
            return true;
        }
        return $post_type === (isset($_GET['post_type']) ? $_GET['post_type'] : 'post');
    }
}

if(!function_exists('__is_post_new')){
    /**
     * @return bool
     */
    function __is_post_new($post_type = ''){
        global $hook_suffix;
        if(!is_admin()){
            return false;
        }
        if('post-new.php' !== $hook_suffix){
            return false;
        }
        if(!$post_type){
            return true;
        }
        return $post_type === (isset($_GET['post_type']) ? $_GET['post_type'] : 'post');
    }
}

if(!function_exists('__is_revision_or_auto_draft')){
	/**
	 * @return bool
	 */
	function __is_revision_or_auto_draft($post = null){
		return (wp_is_post_revision($post) || get_post_status($post) === 'auto-draft');
	}
}

if(!function_exists('__is_true')){
	/**
	 * @return bool
	 */
	function __is_true($data = ''){
		return in_array((string) $data, ['1', 'on', 'true'], true);
	}
}

if(!function_exists('__load_admin_textdomain')){
	/**
	 * @return bool
	 */
	function __load_admin_textdomain($domain = 'default'){
        if(is_admin()){
            return false;
        }
        $locale = determine_locale();
        $mofile = WP_LANG_DIR . '/admin-' . $locale . '.mo';
        return file_exists($mofile) ? load_textdomain($domain, $mofile, $locale) : false;
	}
}

if(!function_exists('__not_empty')){
    /**
	 * Useful for returning whether a variable is not empty to filters easily.
	 *
     * @return bool
     */
    function __not_empty($var = null){
        return !empty($var);
    }
}

if(!function_exists('__post_type_labels')){
	/**
	 * @return array
	 */
	function __post_type_labels($singular = '', $plural = '', $all = true){
		if(!$singular){
			return [];
		}
		if(!$plural){
			$plural = $singular;
		}
		$page = _x('Page', 'post type singular name');
        $pages = _x('Pages', 'post type general name');
        $labels = [
            'name' => _x('Pages', 'post type general name'),
			'singular_name' => _x('Page', 'post type singular name'),
			'add_new' => __('Add'),
			'add_new_item' => __('Add Page'),
			'edit_item' => __('Edit Page'),
			'new_item' => __('New Page'),
			'view_item' => __('View Page'),
			'view_items' => __('View Pages'),
			'search_items' => __('Search Pages'),
			'not_found' => __('No pages found.'),
			'not_found_in_trash' => __('No pages found in Trash.'),
			'parent_item_colon' => __('Parent Page:'),
			'all_items' => $all ? __('All Pages') : $pages,
			'archives' => __('Page Archives'),
			'attributes' => __('Page Attributes'),
			'insert_into_item' => __('Insert into page'),
			'uploaded_to_this_item' => __('Uploaded to this page'),
			'featured_image' => _x('Featured image', 'page'),
			'set_featured_image' => _x('Set featured image', 'page'),
			'remove_featured_image' => _x('Remove featured image', 'page'),
			'use_featured_image' => _x('Use as featured image', 'page'),
			'filter_items_list' => __('Filter pages list'),
			'filter_by_date' => __('Filter by date'),
			'items_list_navigation' => __('Pages list navigation'),
			'items_list' => __('Pages list'),
			'item_published' => __('Page published.'),
			'item_published_privately' => __('Page published privately.'),
			'item_reverted_to_draft' => __('Page reverted to draft.'),
			'item_trashed' => __('Page trashed.'),
			'item_scheduled' => __('Page scheduled.'),
			'item_updated' => __('Page updated.'),
			'item_link' => _x('Page Link', 'navigation link block title'),
			'item_link_description' => _x('A link to a page.', 'navigation link block description'),
        ];
        foreach($labels as $key => $value){
            $labels[$key] = str_replace([$page, $pages, lcfirst($page), lcfirst($pages)], [$singular, $plural, lcfirst($singular), lcfirst($plural)], $value);
        }
        return $labels;
	}
}

if(!function_exists('__table')){
    /**
     * @return string
     */
    function __table($data = [], $headers = [], $args = []){
        $data = array_values($data);
        if(!$data){
            return __('No posts found.');
        }
        $defaults = [
            'bordered' => false,
            'borderless' => false,
            'hover' => false,
            'responsive' => false,
            'sm' => false,
            'striped' => false,
        ];
        $args = wp_parse_args($args, $defaults);
        $classes = ['table'];
        $html = '';
        $responsive = '';
        if($args['bordered']){
            $classes[] = 'table-bordered';
        }
        if($args['borderless']){
            $classes[] = 'table-borderless';
        }
        if($args['hover']){
            $classes[] = 'table-hover';
        }
        if($args['responsive']){
            if(in_array($args['responsive'], ['sm', 'md', 'lg', 'xl'])){
                $responsive = 'table-responsive-' . $args['responsive'];
            } else {
                $responsive = 'table-responsive';
            }
        }
        if($args['sm']){
            $classes[] = 'table-sm';
        }
        if($args['striped']){
            $classes[] = 'table-striped';
        }
        $max_cols = 0;
        if($headers){
            $max_cols = count($headers);
        } else {
            $max_cols = count($data[0]);
        }
        if($responsive){
            $html .= '<div class="' . $responsive . '">';
        }
        $atts = [
            'class' => implode(' ', array_unique(array_map('trim', $classes))),
        ];
        $html .= '<' . __format_atts($atts, 'table') . '>';
        if($headers){
            $html .= '<thead>';
            $html .= '<tr>';
            foreach($headers as $header){
                $html .= '<th>' . $header . '</th>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
        }
        $html .= '<tbody>';
        foreach($data as $row){
            $row = array_values($row);
            $html .= '<tr>';
            foreach($row as $index => $column){
                if($index === $max_cols){
                    break;
                }
                $html .= '<td>' . $column . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        if($responsive){
            $html .= '</div>';
        }
        return $html;
    }
}

if(!function_exists('__test')){
	/**
	 * @return void
	 */
	function __test(){
        __exit_with_error(sprintf(__('%1$s is proudly powered by %2$s'), get_bloginfo('name'), '<a href="https://wordpress.org/">WordPress</a>'), __('Hello world!'), 200);
	}
}

if(!function_exists('__use')){
	/**
	 * @return string|WP_Error
	 */
	function __use($url = '', $args = []){
        if(!$url){
            return __error(__('No URL Provided.'));
    	}
        $group = 'packages';
        $key = __uuid(md5($url . '-' . __md5($args)));
        $expected_dir = __cache_get($key, $group);
        if($expected_dir !== null){
            return $expected_dir;
        }
        $defaults = [
            'autoload' => '',
            'expected_dir' => '',
            'requires_php' => '',
            'requires_wp' => '',
            'strict_validation' => false,
            'validation_class' => '',
            'validation_file' => '',
            'validation_function' => '',
        ];
        $args = wp_parse_args($args, $defaults);
        if($args['requires_php']){
            if(!is_php_version_compatible($args['requires_php'])){
                $error = __error(__('This update does not work with your version of PHP.'));
                __cache_set($key, $error, $group);
                return $error;
            }
        }
        if($args['requires_wp']){
            if(!is_wp_version_compatible($args['requires_wp'])){
                $error = __error(__('This update does not work with your version of WordPress.'));
                __cache_set($key, $error, $group);
                return $error;
            }
        }
        if($args['strict_validation']){
            if($args['validation_class']){
                $invalid = [];
                foreach((array) $args['validation_class'] as $class){
                    if(class_exists($class)){
                        $invalid[] = $class;
                    }
                }
                if($invalid){
                    $error = __error(sprintf(__('Invalid parameter(s): %s'), __implode_and($invalid)));
                    __cache_set($key, $error, $group);
                    return $error;
                }
            }
            if($args['validation_function']){
                $invalid = [];
                foreach((array) $args['validation_function'] as $function){
                    if(function_exists($function)){
                        $invalid[] = $function;
                    }
                }
                if($invalid){
                    $error = __error(sprintf(__('Invalid parameter(s): %s'), __implode_and($invalid)));
                    __cache_set($key, $error, $group);
                    return $error;
                }
            }
        }
        $dir = __remote_package($url);
        if(is_wp_error($dir)){
            return $dir;
        }
        $expected_dir = $dir;
        if($args['expected_dir']){
            $expected_dir = __dir_join($dir, $args['expected_dir']);
        }
        if($args['validation_class']){
            $valid = true;
            foreach((array) $args['validation_class'] as $class){
                if(class_exists($class)){
                    $valid = false;
                    break;
                }
            }
            if(!$valid){
                return $expected_dir;
            }
        }
        if($args['validation_function']){
            $valid = true;
            foreach((array) $args['validation_function'] as $function){
                if(function_exists($function)){
                    $valid = false;
                    break;
                }
            }
            if(!$valid){
                return $expected_dir;
            }
        }
        if($args['validation_file']){
            $missing = [];
            foreach((array) $args['validation_file'] as $path){
                $file = path_join($expected_dir, ltrim($path, '/'));
                if(!file_exists($file)){
                    $missing[] = $path;
                }
            }
            if($missing){
                $error = __error(sprintf(__('Missing parameter(s): %s'), __implode_and($missing)));
                __cache_set($key, $error, $group);
                return $error;
            }
        }
        if($args['autoload']){
            $missing = [];
            foreach((array) $args['autoload'] as $path){
                $file = path_join($expected_dir, ltrim($path, '/'));
                if(file_exists($file)){
                    require_once $file;
                } else {
                    $missing[] = $path;
                }
            }
            if($missing){
                $error = __error(sprintf(__('Missing parameter(s): %s'), __implode_and($missing)));
                __cache_set($key, $error, $group);
                return $error;
            }
        }
        if($args['validation_class']){
            $missing = [];
            foreach((array) $args['validation_class'] as $class){
                if(!class_exists($class)){
                    $missing[] = $class;
                }
            }
            if($missing){
                $error = __error(sprintf(__('Missing parameter(s): %s'), __implode_and($missing)));
                __cache_set($key, $error, $group);
                return $error;
            }
        }
        if($args['validation_function']){
            $missing = [];
            foreach((array) $args['validation_function'] as $function){
                if(!function_exists($function)){
                    $missing[] = $function;
                }
            }
            if($missing){
                $error = __error(sprintf(__('Missing parameter(s): %s'), __implode_and($missing)));
                __cache_set($key, $error, $group);
                return $error;
            }
        }
        __cache_set($key, $expected_dir, $group);
        return $expected_dir;
	}
}

if(!function_exists('__maybe_replace_login_logo')){
	/**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_enqueue_scripts' action hook.
	 *
     * @return string
     */
    function __maybe_replace_login_logo(){
        if(!doing_action('login_enqueue_scripts')){ // Too early or too late.
	        return false;
	    }
        $custom_login_logo = __cache_get('custom_login_logo');
        if($custom_login_logo === null){
            return;
        }
		wp_add_inline_style(__handle(), "#login h1 a,.login h1 a{background-image:url('$custom_login_logo[0]');background-size:$custom_login_logo[1]px $custom_login_logo[2]px;height:$custom_login_logo[2]px;width:$custom_login_logo[1]px;}");
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Nonces
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__create_nonce_guest')){
	/**
	 * @return string
	 */
	function __create_nonce_guest($action = -1){
        $i = wp_nonce_tick($action);
        $token = __get_session_token_guest();
        $uid = 0;
        return substr(wp_hash($i . '|' . $action . '|' . $uid . '|' . $token, 'nonce'), -12, 10);
	}
}

if(!function_exists('__get_session_token_guest')){
	/**
	 * @return string
	 */
	function __get_session_token_guest($action = -1){
        return '';
	}
}

if(!function_exists('__nonce_url')){
	/**
	 * @return string
	 */
	function __nonce_url($actionurl = '', $action = -1, $name = '_wpnonce'){
        //$actionurl = str_replace('&amp;', '&', $actionurl);
        return esc_html(add_query_arg($name, __create_nonce_guest($action), $actionurl));
	}
}

if(!function_exists('__nonce_url_guest')){
	/**
	 * @return string
	 */
	function __nonce_url_guest($actionurl = '', $action = -1, $name = '_wpnonce'){
        //$actionurl = str_replace('&amp;', '&', $actionurl);
        return esc_html(add_query_arg($name, __create_nonce_guest($action), $actionurl));
	}
}

if(!function_exists('__verify_nonce_guest')){
	/**
	 * @return bool
	 */
	function __verify_nonce_guest($nonce = '', $action = -1){
        $nonce = (string) $nonce;
    	if(!$nonce){
    		return false;
    	}
        $i = wp_nonce_tick($action);
        $token = __get_session_token_guest();
    	$uid = 0;
    	$expected = substr(wp_hash($i . '|' . $action . '|' . $uid . '|' . $token, 'nonce'), -12, 10);
    	if(hash_equals($expected, $nonce)){
    		return 1; // Nonce generated 0-12 hours ago
    	}
    	$expected = substr(wp_hash(($i - 1) . '|' . $action . '|' . $uid . '|' . $token, 'nonce'), -12, 10);
    	if(hash_equals($expected, $nonce)){
    		return 2; // Nonce generated 12-24 hours ago
    	}
    	return false; // Invalid nonce
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugin Update Checker
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__build_update_checker')){
    /**
     * @return string
     */
    function __build_update_checker(...$args){
        $group = 'update_checkers';
        $key = __md5($args);
        if(__cache_exists($key, $group)){
            return __cache_get($key, $group);
        }
		$dir = __use_plugin_update_checker();
		if(is_wp_error($dir)){
			return $dir;
		}
		$update_checker = \YahnisElsts\PluginUpdateChecker\v5p6\PucFactory::buildUpdateChecker(...$args);
		__cache_get($key, $update_checker, $group);
        return $update_checker;
    }
}

if(!function_exists('__set_plugin_update_license')){
	/**
	 * @return void
	 */
	function __set_plugin_update_license($slug = '', $license = ''){
		if(!$slug || !$license){
			return;
		}
		__add_filter_once('puc_request_info_query_args-' . $slug, '__maybe_set_update_license');
        __cache_set($slug, $license, 'puc_licenses');
	}
}

if(!function_exists('__maybe_set_update_license')){
	/**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'puc_request_info_query_args-SLUG' filter hook.
	 *
	 * @return array
	 */
	function __maybe_set_update_license($queryArgs){
		$current_filter = current_filter();
		if(!str_starts_with($current_filter, 'puc_request_info_query_args-')){ // Too early or too late.
	        return;
	    }
		$slug = str_replace('puc_request_info_query_args-', '', $current_filter);
		if(!__cache_exists($slug, 'puc_licenses')){
			return $queryArgs;
		}
		$queryArgs['license'] = __cache_get($slug, 'puc_licenses');
		return $queryArgs;
	}
}

if(!function_exists('__use_plugin_update_checker')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_plugin_update_checker(){
        $ver = '5.6'; // Hardcoded.
        $url = 'https://github.com/YahnisElsts/plugin-update-checker/archive/refs/tags/v' . $ver . '.zip';
        return __use($url, [
            'autoload' => 'plugin-update-checker.php',
			'expected_dir' => 'plugin-update-checker-' . $ver,
            'validation_class' => 'YahnisElsts\PluginUpdateChecker\v5p6\PucFactory',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugins
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__are_plugins_active')){
    /**
     * @return bool
     */
    function __are_plugins_active($plugins = []){
        if(!is_array($plugins)){
            return false;
        }
        foreach($plugins as $plugin){
            if(!__is_plugin_active($plugin)){
                return false;
            }
        }
        return true;
    }
}

if(!function_exists('__get_plugin_data')){
    /**
     * @return array|WP_Error
     */
    function __get_plugin_data($file = '', $markup = true, $translate = true){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        $plugin_file = __plugin_file($file);
        if(is_wp_error($plugin_file)){
            return $plugin_file;
        }
        $data = [
            'markup' => $markup,
            'plugin_file' => $plugin_file,
            'translate' => $translate,
        ];
        $md5 = __md5($data);
        $cache = __cache_get($md5, 'plugin_data');
        if($cache !== null){
            return $cache;
        }
        if(!function_exists('get_plugin_data')){
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $data = get_plugin_data($plugin_file, $markup, $translate);
        __cache_set($md5, $data, 'plugin_data');
        return $data;
    }
}

if(!function_exists('__get_plugin_meta')){
    /**
     * @return string|WP_Error
     */
    function __get_plugin_meta($key = '', $file = '', $markup = false, $translate = false){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        $data = __get_plugin_data($file, $markup, $translate);
        if(is_wp_error($data)){
            return $data;
        }
        if(isset($data[$key])){
            $arr = $data;
        } elseif(isset($data['sections'], $data['sections'][$key])){
            $arr = $data['sections'];
        } else {
            return __error('"' . $key . '" ' . __('(not found)'));
        }
        return $arr[$key];
    }
}

if(!function_exists('__is_plugin_active')){
    /**
     * @return bool
     */
    function __is_plugin_active($plugin = ''){
        $status = __cache_get($plugin, 'active_plugins');
        if($status !== null){
            return $status;
        }
        if(!function_exists('is_plugin_active')){
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $status = is_plugin_active($plugin);
        __cache_set($plugin, $status, 'active_plugins');
        return $status;
    }
}

if(!function_exists('__is_plugin_deactivating')){
    /**
     * @return bool
     */
    function __is_plugin_deactivating($file = ''){
        global $pagenow;
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        $plugin_file = __plugin_file($file);
        if(is_wp_error($plugin_file)){
            return false; // File is not a plugin.
        }
        return (is_admin() && $pagenow === 'plugins.php' && isset($_GET['action'], $_GET['plugin']) && $_GET['action'] === 'deactivate' && $_GET['plugin'] === plugin_basename($plugin_file));
    }
}

if(!function_exists('__plugin_file')){
    /**
     * @return string|WP_Error
     */
    function __plugin_file($file = ''){
        global $wp_plugin_paths;
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        if(!file_exists($file)){
            return __error(__('File does not exist! Please double check the name and try again.'));
        }
        $group = 'plugin_files';
        $key = md5($file);
        if(__cache_exists($key, $group)){
            return __cache_get($key, $group);
        }
        if(__is_path_in_plugins_dir($file)){
            $mu_plugin = false;
            $plugin = __plugin_basename($file, $mu_plugin);
        } elseif(__is_path_in_mu_plugins_dir($file)){
            $mu_plugin = true;
            $plugin = __plugin_basename($file, $mu_plugin);
        } else {
            $plugin = __error(__('Invalid plugin path.'));
        }
        if(is_wp_error($plugin)){
            __cache_set($key, $plugin, $group);
            return $plugin;
        }
        $plugin_file = __main_plugin_file($plugin, $mu_plugin);
        __cache_set($key, $plugin_file, $group);
        return $plugin_file;
    }
}

if(!function_exists('__plugin_id')){
    /**
     * @return string|WP_Error
     */
    function __plugin_id($file = ''){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        if(!file_exists($file)){
            return __error(__('File does not exist! Please double check the name and try again.'));
        }
        $group = 'plugin_ids';
        $key = md5($file);
        if(__cache_exists($key, $group)){
            return __cache_get($key, $group);
        }
        if(__is_path_in_plugins_dir($file)){
            $plugin = __plugin_basename($file);
        } elseif(__is_path_in_mu_plugins_dir($file)){
            $plugin = __plugin_basename($file, true);
        } else {
            $plugin = __error(__('Invalid plugin path.'));
        }
        if(is_wp_error($plugin)){
            __cache_set($key, $plugin, $group);
            return $plugin;
        }
        $parts = explode('/', $plugin);
        $plugin_id = wp_basename($parts[0], '.php');
        __cache_set($key, $plugin_id, $group);
        return $plugin_id;
    }
}

if(!function_exists('__plugin_prefix')){
    /**
     * @return string
     */
    function __plugin_prefix($str = '', $file = ''){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return __str_prefix($str);
            }
        }
        $plugin_id = __plugin_id($file);
        if(is_wp_error($plugin_id)){
            return __str_prefix($str);
        }
        return __str_prefix($str, $plugin_id);
    }
}

if(!function_exists('__plugin_slug')){
    /**
     * @return string
     */
    function __plugin_slug($str = '', $file = ''){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return __str_slug($str);
            }
        }
        $plugin_id = __plugin_id($file);
        if(is_wp_error($plugin_id)){
            return __str_slug($str);
        }
        return __str_slug($str, $plugin_id);
    }
}

if(!function_exists('__plugin_update_checker')){
    /**
     * @return Plugin\UpdateChecker|Theme\UpdateChecker|Vcs\BaseChecker|WP_Error
     */
    function __plugin_update_checker($file = ''){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        $plugin_file = __plugin_file($file);
        if(is_wp_error($plugin_file)){
            return $plugin_file;
        }
        $metadata_url = __get_plugin_meta('UpdateURI', $plugin_file);
        if(is_wp_error($metadata_url)){
            return $metadata_url;
        }
        if(!wp_http_validate_url($metadata_url)){
            return __error(__('A valid URL was not provided.'));
        }
        $slug = __plugin_slug($plugin_file);
        if(is_wp_error($slug)){
            return $slug;
        }
        $update_checker = __build_update_checker($metadata_url, $plugin_file, $slug);
        $constant = strtoupper(__str_prefix('license', $slug));
        if(defined($constant)){
            __set_plugin_update_license($slug, constant($constant));
        }
        return $update_checker;
    }
}

if(!function_exists('__main_plugin_file')){
     /**
     * This function’s access is marked private.
     *
     * @return string|WP_Error
     */
    function __main_plugin_file($plugin = '', $mu_plugin = false){
        $dir = wp_normalize_path($mu_plugin ? WPMU_PLUGIN_DIR : WP_PLUGIN_DIR);
        if(!$mu_plugin && __is_plugin_active($plugin)){
            return $dir . '/' . $plugin; // Plugin is the main plugin file.
        }
        $parts = explode('/', $plugin);
        if(count($parts) < 2){ // The entire plugin consists of just a single PHP file, like Hello Dolly.
            if($mu_plugin){
                return $dir . '/' . $plugin; // Plugin is a must-use plugin.
            }
            return __error(__('Plugin not found.')); // Plugin is inactive.
        }
        if($mu_plugin){
            return __error(__('Invalid plugin path.'));
        }
        $active_plugins = (array) get_option('active_plugins', []);
        $plugin_dir = trailingslashit($parts[0]); // The plugin directory name (with trailing slash).
        $plugin_file = '';
        foreach($active_plugins as $active_plugin){
            if(!str_starts_with($active_plugin, $plugin_dir)){
                continue;
            }
            $plugin_file = $dir . '/' . $active_plugin;
            break;
        }
        if($plugin_file){
            return $plugin_file; // Plugin is active.
        }
        $active_sitewide_plugins = (array) get_site_option('active_sitewide_plugins', []);
        $active_sitewide_plugins = array_keys($active_sitewide_plugins);
        foreach($active_sitewide_plugins as $active_sitewide_plugin){
            if(!str_starts_with($active_sitewide_plugin, $plugin_dir)){
                continue;
            }
            $plugin_file = $dir . '/' . $active_sitewide_plugin;
        }
        if($plugin_file){
            return $plugin_file; // Plugin is active for the entire network.
        }
        return __error(__('Plugin not found.')); // Plugin is inactive.
    }
}

if(!function_exists('__plugin_basename')){
     /**
     * This function’s access is marked private.
     *
     * @return string|WP_Error
     */
    function __plugin_basename($file = '', $mu_plugin = false){
        global $wp_plugin_paths;
    	// $wp_plugin_paths contains normalized paths.
    	$file = wp_normalize_path($file);
    	arsort($wp_plugin_paths);
    	foreach($wp_plugin_paths as $dir => $realdir){
    		if(str_starts_with($file, $realdir)){
    			$file = $dir . substr($file, strlen($realdir));
    		}
    	}
        $dir = wp_normalize_path($mu_plugin ? WPMU_PLUGIN_DIR : WP_PLUGIN_DIR);
        $pattern = '#^' . preg_quote($dir, '#') . '/#';
        if(!preg_match($pattern, $file)){
            __error(__('Plugin not found.'));
        }
    	// Get relative path from plugins directory.
        $file = preg_replace($pattern, '', $file);
    	$file = trim($file, '/');
    	return $file;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Queries
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_post')){
	/**
	 * @return WP_Post|array|null
	 */
	function __get_post($post = null, $output = OBJECT, $filter = 'raw'){
        if(!is_array($post)){
            return get_post($post, $output, $filter);
        }
        $post['fields'] = 'ids';
        $post['posts_per_page'] = 1;
        $post_ids = get_posts($post);
        if(!$post_ids){
            return null;
        }
        return get_post($post_ids[0], $output, $filter);
    }
}

if(!function_exists('__get_posts_query')){
	/**
	 * @return WP_Query
	 */
	function __get_posts_query($args = []){
		$defaults = [
			'category' => 0,
			'exclude' => [],
			'include' => [],
			'meta_key' => '',
			'meta_value' => '',
			'numberposts' => 5,
			'order' => 'DESC',
			'orderby' => 'date',
			'post_type' => 'post',
			'suppress_filters' => true,
		];
		$parsed_args = wp_parse_args($args, $defaults);
		if(empty($parsed_args['post_status'])){
			$parsed_args['post_status'] = $parsed_args['post_type'] === 'attachment' ? 'inherit' : 'publish';
		}
		if(!empty($parsed_args['numberposts']) && empty($parsed_args['posts_per_page'])){
			$parsed_args['posts_per_page'] = $parsed_args['numberposts'];
		}
		if(!empty($parsed_args['category'])){
			$parsed_args['cat'] = $parsed_args['category'];
		}
		if(!empty($parsed_args['include'])){
			$incposts = wp_parse_id_list($parsed_args['include']);
			$parsed_args['posts_per_page'] = count($incposts);  // Only the number of posts included.
			$parsed_args['post__in'] = $incposts;
		} elseif(!empty($parsed_args['exclude'])){
			$parsed_args['post__not_in'] = wp_parse_id_list($parsed_args['exclude']);
		}
		$parsed_args['ignore_sticky_posts'] = true;
		$parsed_args['no_found_rows'] = true;
		$query = new \WP_Query;
		$query->query($parsed_args);
		return $query;
	}
}

if(!function_exists('__get_the_id')){
	/**
	 * @return int
	 */
    function __get_the_id(){
        if(!did_action('parse_query')){
            return __get_the_id_early();
        }
        if(in_the_loop()){
            return get_the_ID();
        }
        return get_queried_object_id();
    }
}

if(!function_exists('__get_the_id_early')){
	/**
	 * @return int
	 */
    function __get_the_id_early(){
        if(!__is_frontend()){
            return 0;
        }
        if(!isset($_SERVER['HTTP_HOST'])){
            return 0;
        }
        // Build the URL in the address bar.
        $requested_url = (is_ssl() ? 'https://' : 'http://');
        $requested_url .= $_SERVER['HTTP_HOST'];
        if(isset($_SERVER['REQUEST_URI'])){
            $requested_url .= $_SERVER['REQUEST_URI'];
        }
        return url_to_postid($requested_url);
    }
}

if(!function_exists('__get_user')){
	/**
	 * Alias for wp_get_current_user(), get_user_by() or get_userdata().
	 *
	 * @return WP_User|false
	 */
	function __get_user($user = null){
	    if($user === null){
	        return is_user_logged_in() ? wp_get_current_user() : false;
		}
	    if($user instanceof \WP_User){
	        return $user->exists() ? $user : false;
	    }
	    if(is_numeric($user)){
	        return get_userdata($user);
	    }
	    if(!is_string($user)){
	        return false;
	    }
	    if(username_exists($user)){
	        return get_user_by('login', $user);
	    }
	    if(!is_email($user)){
	        return false;
	    }
	    return get_user_by('email', $email);
	}
}

if(!function_exists('__get_users_query')){
	/**
	 * @return WP_User_Query
	 */
	function __get_users_query($args = []){
	    return new \WP_User_Query(wp_parse_args($args, [
	        'count_total' => false,
	    ]));
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// HTTP
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_status_message')){
	/**
	 * @return string
	 */
	function __get_status_message($code = 0){
		if(!$code){
			return __first_p(__('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.'));
		}
		$message = get_status_header_desc($code);
		if($message){
			return $message;
		}
        return __is_success($code) ? sprintf(__('SUCCESS: %s'), $code) : sprintf(__('FAILED: %s'), $code);
	}
}

if(!function_exists('__is_parsed_remote_response')){
	/**
	 * @return bool
	 */
	function __is_parsed_remote_response($response = null){
        return __object_properties_exist(['body', 'code', 'cookies', 'download', 'filename', 'headers', 'json', 'json_params', 'message', 'response', 'status', 'tmpf', 'wp_error'], $response);
	}
}

if(!function_exists('__is_remote_request')){
	/**
	 * @return bool
	 */
	function __is_remote_request($args = []){
		if(!$args){
			return false;
		}
        if(!is_array($args)){
            $args = wp_parse_args($args);
        }
        $is_request = true;
		$request_args = ['body', 'blocking', 'compress', 'cookies', 'decompress', 'filename', 'headers', 'httpversion', 'limit_response_size', 'method', 'redirection', 'reject_unsafe_urls', 'sslcertificates', 'sslverify', 'stream', 'timeout', 'user-agent']; // https://developer.wordpress.org/reference/classes/wp_http/request/#parameters
		foreach(array_keys($args) as $arg){
			if(!in_array($arg, $request_args)){
				$is_request = false;
				break;
			}
		}
        return $is_request;
	}
}

if(!function_exists('__is_remote_response')){
	/**
	 * @return bool
	 */
	function __is_remote_response($response = []){
        return __array_keys_exist(['body', 'cookies', 'filename', 'headers', 'http_response', 'response'], $response); // https://developer.wordpress.org/reference/classes/wp_http/request/#return
	}
}

if(!function_exists('__is_success')){
	/**
     * Alias for is_success().
	 *
	 * @return bool
	 */
	function __is_success($sc = 0){
        if(!is_numeric($sc)){
            if(__is_parsed_remote_response($sc)){
                $sc = $sc->code;
            } elseif(__is_remote_response($sc)){
                $sc = (int) $sc['response']['code'];
            } else {
                return false;
            }
        }
        $sc = __absint($sc);
		return ($sc >= 200 && $sc < 300);
	}
}

if(!function_exists('__remote_delete')){
	/**
	 * @return stdClass
	 */
	function __remote_delete($url = '', $args = []){
		return __remote_request('delete', $url, $args);
	}
}

if(!function_exists('__remote_download')){
	/**
	 * @return string|WP_Error
	 */
	function __remote_download($url = '', $args = []){
        if(!$url){
            return __error(__('No URL Provided.'));
    	}
        $group = __str_prefix('downloads');
        $key = __uuid($url . '-' . __md5($args));
        $file = __cache_get($key, $group);
        if($file !== null){
            return $file;
        }
        $dir = __dir('downloads/' . $key);
        if(is_wp_error($dir)){
            __cache_set($key, $dir, $group);
            return $dir;
        }
        $ret = __list_files($dir, false);
		if(is_wp_error($ret)){
            __cache_set($key, $ret, $group);
            return $ret;
        }
        if($ret){
            $filename = array_key_first($ret);
            $file = path_join($dir, $filename);
            __cache_set($key, $file, $group);
            return $file;
        }
        $args = __sanitize_remote_request_args($args, $url);
        $new_filename = '';
        if(isset($args['filename'])){
            $new_filename = wp_basename($args['filename']);
            if(!__is_valid_filename($new_filename)){
                $new_filename = '';
            }
            unset($args['filename']);
        }
        $url_filename = __basename($url);
        if(!function_exists('wp_tempnam')){
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
        $tmpfname = wp_tempnam($url_filename, $dir);
    	if(!$tmpfname){
            $error = __error(__('Could not create temporary file.'));
            __cache_set($key, $error, $group);
    		return $error;
    	}
        $args['filename'] = $tmpfname;
        $args['stream'] = true;
        $response = __remote_request('get', $url, $args);
        if(!$response->status){
            __cache_set($key, $response->wp_error, $group);
            __delete_file($tmpfname);
            return $response->wp_error;
        }
        if(isset($response->headers['Content-Disposition'])){
            $content_disposition = $response->headers['Content-Disposition'];
            $content_disposition = strtolower($content_disposition);
            if(str_starts_with($content_disposition, 'attachment; filename=')){
    			$tmpfname_disposition = sanitize_file_name(substr($content_disposition, 21));
    		} else {
    			$tmpfname_disposition = '';
    		}
            // Potential file name must be valid string.
    		if($tmpfname_disposition && is_string($tmpfname_disposition) && validate_file($tmpfname_disposition) === 0){
    			$tmpfname_disposition = dirname($tmpfname) . '/' . $tmpfname_disposition;
                if(__move($tmpfname, $tmpfname_disposition)){
                    $tmpfname = $tmpfname_disposition;
                }
    		}
        }
        // Allow uploading images from URLs without extensions.
        if(isset($response->headers['content-type'])){
            $mime_type = $response->headers['content-type'];
            if($mime_type && pathinfo($tmpfname, PATHINFO_EXTENSION) === 'tmp'){
        		$valid_mime_types = array_flip(get_allowed_mime_types());
        		if(!empty($valid_mime_types[$mime_type])){
        			$extensions = explode('|', $valid_mime_types[$mime_type]);
        			$new_image_name = substr($tmpfname, 0, -4) . ".{$extensions[0]}";
        			if(validate_file($new_image_name) === 0){
                        if(__move($tmpfname, $new_image_name)){
                            $tmpfname = $new_image_name;
                        }
        			}
        		}
        	}
        }
        if(isset($response->headers['Content-MD5'])){
            $content_md5 = $response->headers['Content-MD5'];
            $md5_check = verify_file_md5($tmpfname, $content_md5);
    		if(is_wp_error($md5_check)){
                __cache_set($key, $md5_check, $group);
                __delete_file($tmpfname);
    			return $md5_check;
    		}
        }
        if($new_filename){
            $new_filename = __unique_path($dir, $new_filename);
            if(__move($tmpfname, $new_filename)){
                $tmpfname = $new_filename;
            }
            __cache_set($key, $tmpfname, $group);
            return $tmpfname;
        }
        if(__is_valid_filename($url_filename)){
            $new_filename = __unique_path($dir, $url_filename);
            if(__move($tmpfname, $new_filename)){
                $tmpfname = $new_filename;
            }
            __cache_set($key, $tmpfname, $group);
            return $tmpfname;
        }
        $filetype = wp_check_filetype($tmpfname);
        if($filetype['ext']){
            __cache_set($key, $tmpfname, $group);
            return $tmpfname;
        }
        $filetype = wp_check_filetype_and_ext($tmpfname, $url_filename);
        if($filetype['proper_filename']){
            $new_filename = __unique_path($dir, $filetype['proper_filename']);
            if(__move($tmpfname, $new_filename)){
                $tmpfname = $new_filename;
            }
            __cache_set($key, $tmpfname, $group);
            return $tmpfname;
        }
        $error = __error(__('Sorry, you are not allowed to upload this file type.'));
        __cache_set($key, $error, $group);
        __delete_file($tmpfname);
        return $error;
    }
}

if(!function_exists('__remote_get')){
	/**
	 * @return stdClass
	 */
	function __remote_get($url = '', $args = []){
		return __remote_request('get', $url, $args);
	}
}

if(!function_exists('__remote_head')){
	/**
	 * @return stdClass
	 */
	function __remote_head($url = '', $args = []){
		return __remote_request('head', $url, $args);
	}
}

if(!function_exists('__remote_options')){
	/**
	 * @return stdClass
	 */
	function __remote_options($url = '', $args = []){
		return __remote_request('options', $url, $args);
	}
}

if(!function_exists('__remote_package')){
	/**
	 * @return string|WP_Error
	 */
	function __remote_package($url = '', $args = []){
        if(!$url){
            return __error(__('No URL Provided.'));
    	}
        $group = 'packages';
		$key = __uuid($url . '-' . __md5($args));
        $cache = __cache_get($key, $group);
        if($cache !== null){
            return $cache;
        }
        $dir = __dir('packages/' . $key);
        if(is_wp_error($dir)){
            __cache_set($key, $dir, $group);
            return $dir;
        }
        $ret = __dirlist($dir, false);
		if(is_wp_error($ret)){
            __cache_set($key, $ret, $group);
            return $ret;
        }
        if($ret){
            __cache_set($key, $dir, $group);
            return $dir;
        }
        $file = __remote_download($url, $args);
        if(is_wp_error($file)){
            __cache_set($key, $file, $group);
            return $file;
        }
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if($extension !== 'zip'){
            $error = __error(__('Only .zip archives may be uploaded.'));
            __cache_set($key, $error, $group);
            return $error;
        }
        $result = unzip_file($file, $dir);
		if(is_wp_error($result)){
            __cache_set($key, $result, $group);
			return $result;
		}
        $ret = __dirlist($dir, false);
		if(is_wp_error($ret)){
            __cache_set($key, $ret, $group);
            return $ret;
        }
        if($ret){
            __cache_set($key, $dir, $group);
            return $dir;
        }
        $error = __error(__('Empty archive.'));
        __cache_set($key, $error, $group);
        return $error;
    }
}

if(!function_exists('__remote_patch')){
	/**
	 * @return stdClass
	 */
	function __remote_patch($url = '', $args = []){
		return __remote_request('patch', $url, $args);
	}
}

if(!function_exists('__remote_post')){
	/**
	 * @return stdClass
	 */
	function __remote_post($url = '', $args = []){
		return __remote_request('post', $url, $args);
	}
}

if(!function_exists('__remote_put')){
	/**
	 * @return stdClass
	 */
	function __remote_put($url = '', $args = []){
		return __remote_request('put', $url, $args);
	}
}

if(!function_exists('__remote_request')){
	/**
	 * @return stdClass
	 */
	function __remote_request($method = '', $url = '', $args = []){
		$args = __sanitize_remote_request_args($args, $url);
        if(!isset($args['method'])){
            $args['method'] = __sanitize_remote_request_method($method);
        }
		$response = wp_remote_request($url, $args);
		return __parse_remote_response($response);
	}
}

if(!function_exists('__remote_trace')){
	/**
	 * @return stdClass
	 */
	function __remote_trace($url = '', $args = []){
		return __remote_request('trace', $url, $args);
	}
}

if(!function_exists('__parse_remote_response')){
	/**
	 * @return stdClass
	 */
	function __parse_remote_response($raw_response = []){
        if(__is_parsed_remote_response($raw_response)){
            return $raw_response;
        }
        $response = new \stdClass;
        $response->body = '';
        $response->code = 0;
        $response->cookies = [];
        $response->download = false;
        $response->filename = '';
        $response->headers = [];
        $response->json = false;
        $response->json_params = [];
        $response->message = '';
        $response->response = [];
        $response->status = false;
        $response->tmpf = '';
        $response->wp_error = null;
        if(is_wp_error($raw_response)){
            $response->message = $raw_response->get_error_message();
            $response->wp_error = $raw_response;
        } elseif(__is_remote_response($raw_response)){
            $array = $raw_response['http_response']->to_array();
            $response->body = trim($array['body']);
            $response->code = (int) $array['response']['code'];
            $response->cookies = $array['cookies'];
            $response->filename = $array['filename'];
            $response->headers = $array['headers'];
            $response->json = __is_json_content_type($raw_response);
            $response->message = trim($array['response']['message']);
            $response->response = $raw_response;
            $response->status = __is_success($response->code);
            if($response->filename){
                $response->download = true;
            }
            if(!$response->message){
                $response->message = __get_status_message($response->code);
            }
            if(!$response->status){
                if($response->download){
                    $tmpf = fopen($response->filename, 'rb'); // Retrieve a sample of the response body for debugging purposes.
                    if($tmpf){
                        $response_size = apply_filters('download_url_error_max_body_size', KB_IN_BYTES); // Filters the maximum error response body size in `download_url()`.
                        $response->tmpf = fread($tmpf, $response_size);
                        fclose($tmpf);
                    }
                    unlink($response->filename);
                }
                $response->wp_error = __error($response->message, $raw_response);
            }
            if($response->json){
                $json_params = __json_decode($response->body, true);
                if(is_wp_error($json_params)){
                    $response->message = $json_params->get_error_message();
                    if($response->status){
                        $response->status = false;
                        $response->wp_error = $json_params;
                    } else {
                        $response->wp_error->merge_from($json_params);
                    }
                } else {
                    $response->json_params = $json_params;
                    $maybe_error = __is_json_wp_die_handler($json_params);
                    if(is_wp_error($maybe_error)){
                        $response->message = $maybe_error->get_error_message();
                        if($response->status){
                            $response->status = false;
                            $response->wp_error = $maybe_error;
                        } else {
                            $response->wp_error->merge_from($maybe_error);
                        }
                    }
                }
            }
        } else {
            $response->message = __('Invalid data provided.');
            $response->wp_error = __error($response->message, $raw_response);
        }
        return $response;
    }
}

if(!function_exists('__sanitize_remote_request_method')){
	/**
	 * @return string
	 */
	function __sanitize_remote_request_method($method = ''){
        $method = strtoupper($method);
        if(!in_array($method, ['DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE'])){
            $method = 'GET'; // Default.
        }
        return $method;
	}
}

if(!function_exists('__sanitize_remote_request_timeout')){
	/**
	 * @return int
	 */
	function __sanitize_remote_request_timeout($timeout = 0){
		$timeout = (int) $timeout;
		if($timeout < 0){
			$timeout = 0; // Timeout cannot be negative.
		}
        if(function_exists('ini_get')){ // Some hosts do not allow you to read configuration values.
            $max_execution_time = (int) ini_get('max_execution_time');
            if($max_execution_time > 0){ // The max_execution_time defaults to 0 when PHP runs from cli.
                $max_execution_time -= 2; // Reduce it a bit to prevent edge-case timeouts that may happen after the remote request has finished running.
                if($timeout === 0 || $timeout > $max_execution_time){
                    $timeout = $max_execution_time;
                }
            }
        }
		if(__cloudflare_enabled()){ // The Cloudflare’s proxy read timeout is 100 seconds. TODO: Check for Cloudflare Enterprise. See: https://developers.cloudflare.com/support/troubleshooting/http-status-codes/cloudflare-5xx-errors/error-524/
            $max_execution_time = 98; // Reduce it a bit to prevent edge-case timeouts that may happen after the remote request has finished running.
			if($timeout === 0 || $timeout > $max_execution_time){
				$timeout = $max_execution_time;
			}
		}
		return $timeout;
	}
}

if(!function_exists('__sanitize_remote_request_args')){
	/**
	 * @return array
	 */
	function __sanitize_remote_request_args($args = [], $url = ''){
        if(!is_array($args)){
            $args = wp_parse_args($args);
        }
        if(!$args){
            return [];
        }
		if(!__is_remote_request($args)){
			return [
				'body' => $args,
			];
		}
        if(isset($args['method'])){
            $args['method'] = __sanitize_remote_request_method($args['method']);
        }
        if(isset($args['timeout'])){
			$args['timeout'] = __sanitize_remote_request_timeout($args['timeout']);
		}
		if(!isset($args['cookies']) && wp_validate_redirect($url)){
            $args['cookies'] = $_COOKIE;
		}
		if(!isset($args['user-agent'])){
            $args['user-agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36'; // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/User-Agent#chrome_ua_string
		}
		if(isset($args['body']) && __is_json_content_type($args) && !is_scalar($args['body'])){
			$args['body'] = wp_json_encode($args['body']);
		}
		return $args;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Rewrite
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_external_rule')){
	/**
	 * @return void
	 */
	function __add_external_rule($regex = '', $query = '', $plugin_file = ''){
        if(!__is_apache()){
            return;
        }
		$rule = [
			'plugin_file' => $plugin_file,
			'query' => str_replace(site_url('/'), '', $query),
			'regex' => str_replace(site_url('/'), '', $regex),
		];
		if(doing_action('generate_rewrite_rules')){
            // Just in time.
			__maybe_add_external_rule($rule);
			__add_action_once('admin_notices', '__maybe_add_external_rules_notice');
			return;
		}
		if(did_action('generate_rewrite_rules')){
            // Too late.
			return;
		}
        // Too early.
        __add_action_once('generate_rewrite_rules', '__maybe_add_external_rules');
		__add_action_once('admin_notices', '__maybe_add_external_rules_notice');
        __cache_add($rule, 'external_rules');
	}
}

if(!function_exists('__external_rule_exists')){
	/**
	 * @return bool
	 */
	function __external_rule_exists($regex = '', $query = ''){
		$regex = str_replace('.+?', '.+', $regex); // Apache 1.3 does not support the reluctant (non-greedy) modifier.
		$rewrite_rules = __get_rewrite_rules();
		$rule = 'RewriteRule ^' . $regex . ' ' . __home_root() . $query . ' [QSA,L]';
		return in_array($rule, $rewrite_rules);
	}
}

if(!function_exists('__get_rewrite_rules')){
	/**
	 * @return array
	 */
	function __get_rewrite_rules(){
        $key = 'rewrite_rules';
        $rewrite_rules = __cache_get($key);
        if($rewrite_rules !== null){
            return $rewrite_rules;
        }
		$rewrite_rules = array_filter(extract_from_markers(get_home_path() . '.htaccess', 'WordPress'));
		__cache_set($key, $rewrite_rules);
		return $rewrite_rules;
	}
}

if(!function_exists('__home_root')){
	/**
	 * @return string
	 */
	function __home_root(){
		$home_root = parse_url(home_url());
		if(isset($home_root['path'])){
			$home_root = trailingslashit($home_root['path']);
		} else {
			$home_root = '/';
		}
		return $home_root;
	}
}

if(!function_exists('__is_apache')){
    /**
     * @return bool
     */
    function __is_apache(){
        global $is_apache;
        return $is_apache;
    }
}

if(!function_exists('__is_external_rule')){
	/**
	 * @return bool
	 */
	function __is_external_rule($rule = []){
	    return __array_keys_exist(['plugin_file', 'query', 'regex'], $rule);
	}
}

if(!function_exists('__maybe_add_external_rule')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'generate_rewrite_rules' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rule($rule = []){
		global $wp_rewrite;
		if(!doing_action('generate_rewrite_rules')){
            // Too early or too late.
	        return;
	    }
        // Just in time.
		if(!__is_external_rule($rule)){
			return;
		}
		if($rule['plugin_file'] && __is_plugin_deactivating($rule['plugin_file'])){
			return;
		}
		$wp_rewrite->add_external_rule($rule['regex'], $rule['query']);
	}
}

if(!function_exists('__maybe_add_external_rules')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'generate_rewrite_rules' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rules($wp_rewrite){
		if(!doing_action('generate_rewrite_rules')){
            // Too early or too late.
	        return;
	    }
        // Just in time.
        $external_rules = __cache_get_group('external_rules');
        if($external_rules === null){
            return;
        }
	    foreach($external_rules as $external_rule){
			__maybe_add_external_rule($external_rule);
	    }
	}
}

if(!function_exists('__maybe_add_external_rules_notice')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rules_notice(){
		if(!doing_action('admin_notices')){
            // Too early or too late.
	        return;
	    }
        // Just in time.
		if(!current_user_can('manage_options')){
			return;
		}
        $external_rules = __cache_get_group('external_rules');
        if($external_rules === null){
            return;
        }
        $add_admin_notice = false;
		foreach($external_rules as $external_rule){
			if(!__external_rule_exists($external_rule['regex'], $external_rule['query'])){
				$add_admin_notice = true;
				break;
			}
		}
		if(!$add_admin_notice){
	        return;
		}
        if(!apache_mod_loaded('mod_rewrite')){
            __add_admin_notice(sprintf(__('It looks like the Apache %s module is not installed.'), '<code>mod_rewrite</code>'));
            return;
        }
	    __add_admin_notice(sprintf(__('You should update your %s file now.'), '<code>.htaccess</code>') . ' ' . sprintf('<a href="%s">%s</a>', esc_url(admin_url('options-permalink.php')), __('Flush permalinks')) . '.');
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// PHP Simple HTML DOM Parser
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__file_get_html')){
	/**
	 * @return simple_html_dom|WP_Error
	 */
	function __file_get_html(...$args){
		$dir = __use_simple_html_dom();
		if(is_wp_error($dir)){
			return $dir;
		}
		return file_get_html(...$args);
	}
}

if(!function_exists('__str_get_html')){
	/**
	 * @return simple_html_dom|WP_Error
	 */
	function __str_get_html(...$args){
		$dir = __use_simple_html_dom();
		if(is_wp_error($dir)){
			return $dir;
		}
		return str_get_html(...$args);
	}
}

if(!function_exists('__use_simple_html_dom')){
    /**
     * This function’s access is marked private.
     *
	 * @return bool|WP_Error
	 */
	function __use_simple_html_dom(){
        $ver = '1.9.1'; // Hardcoded.
        $url = 'https://github.com/simplehtmldom/simplehtmldom/archive/refs/tags/' . $ver . '.zip';
        return __use($url, [
            'autoload' => 'simple_html_dom.php',
			'expected_dir' => 'simplehtmldom-' . $ver,
            'validation_class' => 'simple_html_dom',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Roles and capabilities
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_hiding_rule')){
    /**
     * @return void
     */
    function __add_hiding_rule($args = []){
        if(is_multisite()){
    		return; // The rewrite rules are not for WordPress MU networks.
    	}
    	$pairs = [
            'capability' => '',
            'exclude_other_media' => [],
            'exclude_public_media' => false,
            'file' => '',
    		'subdir' => '',
    	];
        $args = shortcode_atts($pairs, $args);
    	$md5 = __md5($args);
        __cache_set($md5, $args, 'hide_uploads_subdir');
    	$uploads_use_yearmonth_folders = false;
    	$subdir = ltrim(untrailingslashit($args['subdir']), '/');
    	if($subdir){
    		$subdir = '/(' . $subdir . ')';
    	} else {
    		if(get_option('uploads_use_yearmonth_folders')){
    			$subdir = '/(\d{4})/(\d{2})';
    			$uploads_use_yearmonth_folders = true;
    		} else {
    			$subdir = '';
    		}
    	}
    	$upload_dir = wp_get_upload_dir();
    	if($upload_dir['error']){
    		return;
    	}
        $atts = [];
        $path = __shortinit() . '/readfile.php';
        if(!file_exists($path)){
            return;
        }
    	$tmp = str_replace(wp_normalize_path(ABSPATH), '', wp_normalize_path($path));
    	$parts = explode('/', $tmp);
    	$levels = count($parts);
    	$query = __path_to_url($path);
    	$regex = $upload_dir['baseurl'] . $subdir. '/(.+)';
    	if($uploads_use_yearmonth_folders){
    		$atts['yyyy'] = '$1';
    		$atts['mm'] = '$2';
    		$atts['file'] = '$3';
    	} else {
    		$atts['subdir'] = '$1';
    		$atts['file'] = '$2';
    	}
    	$atts['levels'] = $levels;
        $atts['md5'] = $md5;
        $value = [
            'capability' => $args['capability'],
            'exclude_other_media' => $args['exclude_other_media'],
            'exclude_public_media' => $args['exclude_public_media'],
        ];
        $option = __str_prefix('hide_uploads_subdir_' . $md5);
        update_option($option, $value, 'no');
    	$query = add_query_arg($atts, $query);
    	__add_external_rule($regex, $query, $args['file']);
    }
}

if(!function_exists('__clone_role')){
	/**
	 * @return WP_Role|void
	 */
	function __clone_role($source = '', $destination = '', $display_name = ''){
		$role = get_role($source);
		if($role === null){
			return;
		}
		return add_role(__canonicalize($destination), $display_name, $role->capabilities);
	}
}

if(!function_exists('__hide_others_media')){
    /**
     * @return void
     */
    function __hide_others_media($capability = 'edit_others_posts'){
        __add_filter_once('ajax_query_attachments_args', '__maybe_hide_others_media');
        __cache_set('hide_others_media', [
            'capability' => $capability,
        ]);
    }
}

if(!function_exists('__hide_others_posts')){
    /**
     * @return void
     */
    function __hide_others_posts($capability = 'edit_others_posts'){
        __add_action_once('current_screen', '__maybe_hide_others_posts_count');
        __add_action_once('pre_get_posts', '__maybe_hide_others_posts_query_args');
        __cache_set('hide_others_posts', [
            'capability' => $capability,
        ]);
    }
}

if(!function_exists('__hide_the_dashboard')){
    /**
     * @return void
     */
    function __hide_the_dashboard($capability = 'edit_posts', $location = ''){
        __add_action_once('admin_init', '__maybe_hide_the_dashboard');
        __cache_set('hide_the_dashboard', [
            'capability' => $capability,
    		'location' => $location,
        ]);
    }
}

if(!function_exists('__hide_the_frontend')){
    /**
     * @return void
     */
    function __hide_the_frontend($capability = 'read', $exclude_special_pages = [], $exclude_other_pages = []){
        __add_action_once('template_redirect', '__maybe_hide_the_frontend');
        __cache_set('hide_the_frontend', [
            'capability' => $capability,
            'exclude_other_pages' => $exclude_other_pages,
    		'exclude_special_pages' => $exclude_special_pages,
        ]);
    }
}

if(!function_exists('__hide_the_rest_api')){
    /**
     * @return void
     */
    function __hide_the_rest_api($capability = 'read'){
        __add_filter_once('rest_authentication_errors', '__maybe_hide_the_rest_api');
        __cache_set('hide_the_rest_api', [
            'capability' => $capability,
        ]);
    }
}

if(!function_exists('__hide_the_toolbar')){
    /**
     * @return void
     */
    function __hide_the_toolbar($capability = 'edit_posts'){
        __add_filter_once('show_admin_bar', '__maybe_hide_the_toolbar');
        __cache_set('hide_the_toolbar', [
            'capability' => $capability,
        ]);
    }
}

if(!function_exists('__hide_uploads_subdir')){
    /**
     * @return void
     */
    function __hide_uploads_subdir($subdir = '', $capability = 'edit_others_posts', $exclude_public_media = false, $exclude_other_media = [], $file = ''){
        $args = [
            'capability' => $capability,
            'exclude_other_media' => $exclude_other_media,
            'exclude_public_media' => $exclude_public_media,
            'subdir' => $subdir,
        ];
        if(!$file){
            $file = __caller_file(1); // One level above.
    	}
        $args['file'] = $file;
        return __add_hiding_rule($args);
    }
}

if(!function_exists('__hide_wp')){
    /**
     * @return void
     */
    function __hide_wp(){
        __add_action_once('admin_init', '__maybe_hide_wp_from_admin');
        __add_action_once('wp_before_admin_bar_render', '__maybe_hide_wp_from_admin_bar');
        __cache_set('hide_wp', true);
        __local_login_header();
    }
}

if(!function_exists('__local_login_header')){
    /**
     * @return void
     */
    function __local_login_header(){
        __add_filter_once('login_headertext', '__maybe_local_login_header_text');
        __add_filter_once('login_headerurl', '__maybe_local_login_header_url');
        __cache_set('local_login_header', true);
    }
}

if(!function_exists('__maybe_hide_others_media')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'ajax_query_attachments_args' filter hook.
	 *
     * @return array
     */
    function __maybe_hide_others_media($query){
        if(!doing_filter('ajax_query_attachments_args')){ // Too early or too late.
	        return $query;
	    }
        $hide_others_media = __cache_get('hide_others_media');
        if($hide_others_media === null){
    		return $query;
    	}
        if(current_user_can($hide_others_media['capability'])){
            return $query;
        }
        $query['author'] = get_current_user_id();
        return $query;
    }
}

if(!function_exists('__maybe_hide_others_posts_count')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'current_screen' action hook.
	 *
     * @return void
     */
    function __maybe_hide_others_posts_count(){
        global $current_screen, $pagenow;
        if(!doing_action('current_screen')){ // Too early or too late.
	        return;
	    }
        $hide_others_posts = __cache_get('hide_others_posts');
        if($hide_others_posts === null){
    		return;
    	}
        if($pagenow !== 'edit.php'){
            return;
        }
        if(current_user_can($hide_others_posts['capability'])){
    		return;
    	}
        __add_filter_once('views_' . $current_screen->id, '__maybe_hide_others_posts_count_from_views');
    }
}

if(!function_exists('__maybe_hide_others_posts_count_from_views')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'views_SCREEN_ID' filter hook.
	 *
     * @return array
     */
    function __maybe_hide_others_posts_count_from_views($views){
        $current_filter = current_filter();
        if(!str_starts_with($current_filter, 'views_')){ // Too early or too late.
            return $views;
        }
        foreach($views as $index => $view){
            $views[$index] = preg_replace('/ <span class="count">\([0-9]+\)<\/span>/', '', $view);
        }
    	return $views;
    }
}

if(!function_exists('__maybe_hide_others_posts_query_args')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'pre_get_posts' action hook.
	 *
     * @return void
     */
    function __maybe_hide_others_posts_query_args($query){
        global $pagenow;
        if(!doing_action('pre_get_posts')){ // Too early or too late.
	        return $query;
	    }
        $hide_others_posts = __cache_get('hide_others_posts');
        if($hide_others_posts === null){
    		return $query;
    	}
        if($pagenow !== 'edit.php'){
            return $query;
        }
        if(current_user_can($hide_others_posts['capability'])){
    		return $query;
    	}
        $query->set('author', get_current_user_id());
        return $query;
    }
}

if(!function_exists('__maybe_hide_the_dashboard')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'admin_init' action hook.
	 *
     * @return void
     */
    function __maybe_hide_the_dashboard(){
        if(!doing_action('admin_init')){ // Too early or too late.
	        return;
	    }
        $hide_the_dashboard = __cache_get('hide_the_dashboard');
        if($hide_the_dashboard === null){
    		return;
    	}
        if(wp_doing_ajax()){ // TODO: Check for admin-post.php too.
            return;
        }
        if(current_user_can($hide_the_dashboard['capability'])){
            return;
        }
    	wp_safe_redirect(wp_validate_redirect($hide_the_dashboard['location'], home_url()));
    	exit;
    }
}

if(!function_exists('__maybe_hide_the_frontend')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'template_redirect' action hook.
	 *
     * @return void
     */
    function __maybe_hide_the_frontend(){
        if(!doing_action('template_redirect')){ // Too early or too late.
	        return;
	    }
        $hide_the_frontend = __cache_get('hide_the_frontend');
        if($hide_the_frontend === null){
    		return;
    	}
        $exclude_other_pages = in_array(get_the_ID(), (array) $hide_the_frontend['exclude_other_pages']);
    	$exclude_special_pages = ((is_front_page() && in_array('front_page', (array) $hide_the_frontend['exclude_special_pages'])) || (is_home() && in_array('home', (array) $hide_the_frontend['exclude_special_pages'])));
        if($exclude_other_pages || $exclude_special_pages){
            return;
        }
        if(!is_user_logged_in()){
            auth_redirect();
        }
        if(current_user_can($hide_the_frontend['capability'])){
            return;
        }
        __exit_with_error(__('Sorry, you are not allowed to access this page.'), __('You need a higher level of permission.'), 403);
    }
}

if(!function_exists('__maybe_hide_the_rest_api')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'rest_authentication_errors' filter hook.
	 *
     * @return WP_Error|null|true
     */
    function __maybe_hide_the_rest_api($errors = null){
        if(!doing_filter('rest_authentication_errors')){ // Too early or too late.
	        return $errors;
	    }
        $hide_the_rest_api = __cache_get('hide_the_rest_api');
        if($hide_the_rest_api === null){
    		return $errors;
    	}
        if(!is_null($errors)){
    		return $errors; // Avoid conflicts with other errors.
    	}
        if(current_user_can($hide_the_rest_api['capability'])){
            return null;
        }
        return __error(__('You need a higher level of permission.'), [
    		'status' => 401,
    	]);
    }
}

if(!function_exists('__maybe_hide_the_toolbar')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'show_admin_bar' filter hook.
	 *
     * @return bool
     */
    function __maybe_hide_the_toolbar($show = false){
        if(!doing_filter('show_admin_bar')){ // Too early or too late.
	        return $show;
	    }
        $hide_the_toolbar = __cache_get('hide_the_toolbar');
        if($hide_the_toolbar === null){
            return $show;
        }
        if(current_user_can($hide_the_toolbar['capability'])){
            return $show;
        }
        return false;
    }
}

if(!function_exists('__maybe_hide_wp_from_admin')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'admin_init' action hook.
	 *
     * @return void
     */
    function __maybe_hide_wp_from_admin(){
        if(!doing_action('admin_init')){ // Too early or too late.
	        return;
	    }
        $hide_wp = __cache_get('hide_wp');
        if($hide_wp === null){
            return;
        }
        remove_action('welcome_panel', 'wp_welcome_panel');
    }
}

if(!function_exists('__maybe_hide_wp_from_admin_bar')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'wp_before_admin_bar_render' action hook.
	 *
     * @return void
     */
    function __maybe_hide_wp_from_admin_bar(){
        global $wp_admin_bar;
        if(!doing_action('wp_before_admin_bar_render')){ // Too early or too late.
	        return;
	    }
        $hide_wp = __cache_get('hide_wp');
        if($hide_wp === null){
            return;
        }
    	$wp_admin_bar->remove_node('wp-logo');
    }
}

if(!function_exists('__maybe_local_login_header_text')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_headertext' filter hook.
	 *
     * @return string
     */
    function __maybe_local_login_header_text($login_header_text = ''){
        if(!doing_filter('login_headertext')){ // Too early or too late.
	        return $login_header_text;
	    }
        $local_login_header = __cache_get('local_login_header');
        if($local_login_header === null){
            return $login_header_text;
        }
    	return get_option('blogname');
    }
}

if(!function_exists('__maybe_local_login_header_url')){
    /**
     * This function’s access is marked private.
     *
	 * This function MUST be called inside the 'login_headerurl' filter hook.
	 *
     * @return string
     */
    function __maybe_local_login_header_url($login_header_url = ''){
        if(!doing_filter('login_headerurl')){ // Too early or too late.
	        return $login_header_url;
	    }
        $local_login_header = __cache_get('local_login_header');
        if($local_login_header === null){
            return $login_header_url;
        }
        return home_url();
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Slick
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__enqueue_slick')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return void
     */
	function __enqueue_slick($deps = []){
        if(!doing_action('admin_enqueue_scripts') && !doing_action('login_enqueue_scripts') && !doing_action('wp_enqueue_scripts')){
            return; // Too early or too late.
        }
        $dir = __use_slick();
        if(is_wp_error($dir)){
            return; // Silence is golden.
        }
        $base_path = __path_to_url($dir) . '/slick';
        $ver = '1.8.1'; // Hardcoded.
        __enqueue('slick', $base_path . '/slick.css', $deps, $ver);
        __enqueue('slick-theme', $base_path . '/slick-theme.css', ['slick'], $ver);
        __enqueue('slick', $base_path . '/slick.min.js', $deps, $ver);
	}
}

if(!function_exists('__use_slick')){
    /**
     * This function’s access is marked private.
     *
	 * @return string|WP_Error
	 */
	function __use_slick(){
        $ver = '1.8.1'; // Hardcoded.
        $url = 'https://github.com/kenwheeler/slick/archive/refs/tags/v' . $ver . '.zip';
        return __use($url, [
			'expected_dir' => 'slick-' . $ver,
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Strings
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__base64_urldecode')){
	/**
	 * @return string
	 */
	function __base64_urldecode($data = '', $strict = false){
		return base64_decode(strtr($data, '-_', '+/'), $strict);
	}
}

if(!function_exists('__base64_urlencode')){
	/**
	 * @return string
	 */
	function __base64_urlencode($data = ''){
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
}

if(!function_exists('__canonicalize')){
	/**
	 * @return string
	 */
	function __canonicalize($key = ''){
		return str_replace('-', '_', sanitize_title($key));
	}
}

if(!function_exists('__first_p')){
	/**
	 * @return string
	 */
	function __first_p($text = '', $dot = true){
		return __one_p($text, $dot, 'first');
	}
}

if(!function_exists('__implode')){
	/**
	 * @return string
	 */
	function __implode($array = [], $separator = ''){
        if(!$separator){
            $separator = wp_get_list_item_separator();
        }
		return implode($separator, $array);
	}
}

if(!function_exists('__implode_and')){
	/**
	 * @return string
	 */
	function __implode_and($array = [], $last = ''){
        if(!$last){
            $last = trim(sprintf(__('%1$s and %2$s'), '', ''));
        }
		return __implode_last($array, $last);
	}
}

if(!function_exists('__implode_last')){
	/**
	 * @return string
	 */
	function __implode_last($array = [], $last = '', $separator = ''){
		if(!$array || !is_array($array)){
			return '';
		}
        if(count($array) === 1){
			return $array[0];
		}
        if(!$last){
            return __implode($array, $separator);
        }
		$last_item = array_pop($array);
		return __implode($array, $separator) . ' ' . $last . ' ' . $last_item;
	}
}

if(!function_exists('__implode_or')){
	/**
	 * @return string
	 */
	function __implode_or($array = [], $last = ''){
        if(!$last){
            $last = trim(sprintf(__('%1$s or %2$s'), '', ''));
        }
		return __implode_last($array, $last);
	}
}

if(!function_exists('__last_p')){
	/**
	 * @return string
	 */
	function __last_p($text = '', $dot = true){
		return __one_p($text, $dot, 'last');
	}
}

if(!function_exists('__one_p')){
	/**
	 * @return string
	 */
	function __one_p($text = '', $dot = true, $p = 'first'){
        $text = sanitize_text_field($text);
        $matches = preg_split('/[\.\?!]+/', $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
        switch($p){
            case 'first':
                $match = array_shift($matches);
                break;
            case 'last':
                $match = array_pop($matches);
                break;
            default:
                $p = absint($p);
                if(count($matches) >= $p){
                    $p --;
                    $match = $matches[$p];
                } else {
                    $error_msg = __('Error');
                    if($dot){
                        $error_msg .= '.';
                    }
                    return $error_msg;
                }
        }
        $one = trim($match[0]);
        if(!$dot){
            return $one;
        }
        $dot_chr = substr($text, strlen($match[0]) + $match[1], 1);
        if(!$dot_chr){
            $dot_chr = '.';
        }
        $one .= $dot_chr;
        return $one;
	}
}

if(!function_exists('__prepare')){
	/**
	 * @return string
	 */
	function __prepare($str = '', ...$args){
		global $wpdb;
		if(!$args){
			return $str;
		}
		if(strpos($str, '%') === false){
			return $str;
		}
		return str_replace("'", '', $wpdb->remove_placeholder_escape($wpdb->prepare($str, ...$args)));
	}
}

if(!function_exists('__remove_newlines')){
	/**
	 * @return string
	 */
	function __remove_newlines($str = ''){
		return trim(preg_replace('/[\r\n\t ]+/', ' ', $str));
	}
}

if(!function_exists('__str_split')){
	/**
	 * @return string
	 */
	function __str_split($str = '', $line_length = 55){
		$str = sanitize_text_field($str);
		$lines = ceil(strlen($str) / $line_length);
		$words = explode(' ', $str);
		if(count($words) <= $lines){
			return $words;
		}
		$length = 0;
		$index = 0;
		$oputput = [];
		foreach($words as $word){
			$word_length = strlen($word);
			if((($length + $word_length) <= $line_length) || empty($oputput[$index])){
				$oputput[$index][] = $word;
				$length += ($word_length + 1);
			} else {
				if($index < ($lines - 1)){
					$index ++;
				}
				$length = $word_length;
				$oputput[$index][] = $word;
			}
		}
		foreach($oputput as $index => $words){
			$oputput[$index] = implode(' ', $words);
		}
		return $oputput;
	}
}

if(!function_exists('__str_split_lines')){
	/**
	 * @return string
	 */
	function __str_split_lines($str = '', $lines = 2){
		$str = sanitize_text_field($str);
		$words = explode(' ', $str);
		if(count($words) <= $lines){
			return $words;
		}
		$line_length = ceil(strlen($str) / $lines);
		$length = 0;
		$index = 0;
		$oputput = [];
		foreach($words as $word){
			$word_length = strlen($word);
			if((($length + $word_length) <= $line_length) || empty($oputput[$index])){
				$oputput[$index][] = $word;
				$length += ($word_length + 1);
			} else {
				if($index < ($lines - 1)){
					$index ++;
				}
				$length = $word_length;
				$oputput[$index][] = $word;
			}
		}
		foreach($oputput as $index => $words){
			$oputput[$index] = implode(' ', $words);
		}
		return $oputput;
	}
}

if(!function_exists('__trailingdotit')){
	/**
	 * @return string
	 */
	function __trailingdotit($text = ''){
        $text = sanitize_textarea_field($text);
		return str_ends_with($text, '.') ? $text : $text . '.';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Themes
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__theme_is')){
    /**
     * @return bool
     */
    function __theme_is($name = ''){
        $group = 'theme_is';
        $theme_is = __cache_get($name, $group);
        if($theme_is !== null){
            return $theme_is;
        }
    	$current_theme = wp_get_theme();
		$theme_is = $name === $current_theme->get('Name');
		__cache_set($name, $theme_is, $group);
    	return $theme_is;
    }
}

if(!function_exists('__theme_is_child_of')){
    /**
     * @return bool
     */
    function __theme_is_child_of($template = ''){
        $group = 'theme_is_child_of';
        $theme_is_child_of = __cache_get($template, $group);
        if($theme_is_child_of !== null){
            return $theme_is_child_of;
        }
		$current_theme = wp_get_theme();
		$theme_is_child_of = $template === $current_theme->get('Template');
        __cache_set($template, $theme_is_child_of, $group);
    	return $theme_is_child_of;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Uploads
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__handle_file')){
	/**
	 * This function works only if the file was uploaded via HTTP POST.
	 *
	 * @return array|WP_Error
	 */
	function __handle_file($file = [], $dir = '', $mimes = null){
		if(!$file){
			return __error(__('No data supplied.'));
		}
		if(!is_array($file)){
			if(!is_scalar($file)){
				return __error(__('Invalid data provided.'));
			}
			if(!isset($_FILES[$file])){
				return __error(__('File does not exist! Please double check the name and try again.'));
			}
			$file = $_FILES[$file];
		}
		$keys = ['error', 'name', 'size', 'tmp_name', 'type'];
		foreach($keys as $key){
			$file[$key] = isset($file[$key]) ? (array) $file[$key] : [];
		}
		$count = count($file['tmp_name']);
		$files = [];
		for($i = 0; $i < $count; $i ++){
			$files[$i] = [];
			foreach($keys as $key){
				if(isset($file[$key][$i])){
					$files[$i][$key] = $file[$key][$i];
				}
			}
		}
		$uploaded_files = [];
		foreach($files as $index => $file){
			$uploaded_files[$index] = __handle_upload($file, $dir, $mimes);
		}
		return $uploaded_files;
	}
}

if(!function_exists('__handle_files')){
	/**
	 * This function works only if the files were uploaded via HTTP POST.
	 *
	 * @return array|WP_Error
	 */
	function __handle_files($files = [], $dir = '', $mimes = null){
		if(!$files){
			if(!$_FILES){
				return __error(__('No data supplied.'));
			}
			$files = $_FILES;
		}
		$uploaded_files = [];
		foreach($files as $key => $file){
			$uploaded_files[$key] = __handle_file($file, $dir, $mimes);
		}
		return $uploaded_files;
	}
}

if(!function_exists('__handle_upload')){
	/**
	 * @return string|WP_Error
	 */
	function __handle_upload($file = [], $dir = '', $mimes = null){
	    $dir = __check_dir($dir);
	    if(is_wp_error($dir)){
	        return $dir;
	    }
		if(!$file){
			return __error(__('No data supplied.'));
		}
		$file = shortcode_atts([
			'error' => 0,
			'name' => '',
			'size' => 0,
			'tmp_name' => '',
			'type' => '',
		], $file);
		$uploaded_file = __test_uploaded_file($file['tmp_name']);
		if(is_wp_error($uploaded_file)){
			return $uploaded_file;
		}
		$error = __test_error($file['error']);
		if(is_wp_error($error)){
			return $error;
		}
		$size = __test_size($file['size']);
		if(is_wp_error($size)){
			return $size;
		}
		$filename = __test_type($file['tmp_name'], $file['name'], $mimes);
		if(is_wp_error($filename)){
			return $filename;
		}
		$size_check = __check_upload_size($file['size']);
		if(is_wp_error($size_check)){
			return $size_check;
		}
        if($dir){
            if(!__is_path_in_uploads_dir($dir)){
                return __error(sprintf(__('Unable to locate needed folder (%s).'), __('The uploads directory')));
            }
        } else {
            $upload_dir = wp_upload_dir();
			if($upload_dir['error']){
				return __error($upload_dir['error']);
			}
			$dir = $upload_dir['path'];
        }
		$filename = wp_unique_filename($dir, $filename);
		$new_file = path_join($dir, $filename);
		$move_new_file = @move_uploaded_file($file['tmp_name'], $new_file);
		if($move_new_file === false){
			return __error(sprintf(__('The uploaded file could not be moved to %s.'), str_replace(ABSPATH, '', $dir)));
		}
		$stat = stat(dirname($new_file));
		$perms = $stat['mode'] & 0000666;
		chmod($new_file, $perms); // Set correct file permissions.
		if(is_multisite()){
			clean_dirsize_cache($new_file);
		}
		return $new_file;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// UUID
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__is_uuid')){
	/**
	 * @return bool
	 */
	function __is_uuid($string = ''){
        return (is_string($string) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[1-5][a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i', $string) === 1);
	}
}

if(!function_exists('__md5_to_uuid')){
	/**
	 * @return string
	 */
	function __md5_to_uuid($md5 = ''){
        if(!__is_md5($md5)){
            return '';
        }
		$time_low = substr($md5, 0, 8);
	    $time_mid = substr($md5, 8, 4);
		$time_hi_and_version = sprintf('%04x', (hexdec(substr($md5, 12, 4)) & 0x0fff) | 0x3000); // Version 3 UUID.
		$clock_seq = sprintf('%04x', (hexdec(substr($md5, 16, 4)) & 0x3fff) | 0x8000); // Variant RFC 4122.
		$node = substr($md5, 20, 12);
		return sprintf('%s-%s-%s-%s-%s', $time_low, $time_mid, $time_hi_and_version, $clock_seq, $node);
	}
}

if(!function_exists('__uuid')){
	/**
	 * @return string
	 */
	function __uuid($data = ''){
		return __is_uuid($data) ? $data : __md5_to_uuid(__md5($data));
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Wordfence
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__wf_bulk_countries')){
    /**
     * @return array
     */
    function __wf_bulk_countries(){
        if(!defined('WORDFENCE_PATH')){
            return [];
        }
        $file = WORDFENCE_PATH . 'lib/wfBulkCountries.php';
        if(!file_exists($file)){
            return [];
        }
        include $file; /** @var array $wfBulkCountries */
        if(!isset($wfBulkCountries) || !is_array($wfBulkCountries)){
            return [];
        }
        asort($wfBulkCountries);
        return $wfBulkCountries;
    }
}

if(!function_exists('__wf_get_code_execution_protection_rules')){
    /**
     * @return array
     */
    function __wf_get_code_execution_protection_rules(){
        $upload_dir = wp_get_upload_dir();
		if($upload_dir['error']){
			return [];
		}
        $basedir = $upload_dir['basedir'];
        $htaccess = path_join($basedir . '.htaccess');
        if(!function_exists('extract_from_markers')){
            require_once ABSPATH . 'wp-admin/includes/misc.php';
        }
        $result = extract_from_markers($htaccess, 'Wordfence code execution protection');
        return array_filter($result);
    }
}

if(!function_exists('__wf_get_countries')){
    /**
     * @return array
     */
    function __wf_get_countries($preferred_countries = []){
        $wf_countries = __wf_bulk_countries();
        if(!$preferred_countries){
            return $wf_countries;
        }
        $countries = [];
        foreach($preferred_countries as $iso2){
            if(!isset($wf_countries[$iso2])){
                continue;
            }
            $countries[$iso2] = $wf_countries[$iso2];
            unset($wf_countries[$iso2]);
        }
        return array_merge($countries, $wf_countries);
    }
}

if(!function_exists('__wf_get_country')){
    /**
     * @return string
     */
    function __wf_get_country($ip = ''){
        if($ip && !\WP_Http::is_ip_address($ip)){
            return '';
        }
        $ip = $ip ? $ip : __get_ip();
        return is_callable(['wfUtils', 'IP2Country']) ? \wfUtils::IP2Country($ip) : '';
    }
}

if(!function_exists('__wf_get_ip')){
    /**
     * @return string
     */
    function __wf_get_ip(){
        return is_callable(['wfUtils', 'getIP']) ? \wfUtils::getIP() : '';
    }
}

if(!function_exists('__wf_is_code_execution_protection_enabled')){
    /**
     * @return bool
     */
    function __wf_is_code_execution_protection_enabled(){
        $disable_code_execution_uploads = is_callable(['wfConfig', 'get']) ? \wfConfig::get('disableCodeExecutionUploads') : false;
        if(!$disableCodeExecutionUploads){
            return false;
        }
        $rules = __wf_get_code_execution_protection_rules();
        return $rules ? true : false;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Zoom
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__zoom_app_credentials')){
    /**
     * @return array|WP_Error
     */
    function __zoom_app_credentials($app_credentials = []){
        $key = 'zoom_app_credentials';
        $app_credentials = __cache_get($key);
        if($app_credentials !== null){
            return $app_credentials;
        }
        $app_credentials = shortcode_atts([
            'account_id' => '',
            'client_id' => '',
            'client_secret' => '',
        ], $app_credentials);
        $missing = [];
        if(!$app_credentials['account_id']){
            $missing[] = 'Account ID';
        }
        if(!$app_credentials['client_id']){
            $missing[] = 'Client ID';
        }
        if(!$app_credentials['client_secret']){
            $missing[] = 'Client Secret';
        }
        if($missing){
            $error = __error(sprintf(__('Missing parameter(s): %s'), __implode_and($missing)));
            __cache_set($key, $error);
            return $error;
        }
        __cache_set($key, $app_credentials);
        return $app_credentials;
    }
}

if(!function_exists('__zoom_delete')){
    /**
     * @return object
     */
    function __zoom_delete($endpoint = '', $body = [], $timeout = 10){
        return __zoom_request('delete', $endpoint, $body, $timeout);
    }
}

if(!function_exists('__zoom_get')){
    /**
     * @return object
     */
    function __zoom_get($endpoint = '', $body = [], $timeout = 10){
        return __zoom_request('get', $endpoint, $body, $timeout);
    }
}

if(!function_exists('__zoom_patch')){
    /**
     * @return object
     */
    function __zoom_patch($endpoint = '', $body = [], $timeout = 10){
        return __zoom_request('patch', $endpoint, $body, $timeout);
    }
}

if(!function_exists('__zoom_post')){
    /**
     * @return object
     */
    function __zoom_post($endpoint = '', $body = [], $timeout = 10){
        return __zoom_request('post', $endpoint, $body, $timeout);
    }
}

if(!function_exists('__zoom_put')){
    /**
     * @return object
     */
    function __zoom_put($endpoint = '', $body = [], $timeout = 10){
        return __zoom_request('put', $endpoint, $body, $timeout);
    }
}

if(!function_exists('__zoom_request')){
    /**
     * @return object
     */
    function __zoom_request($method = '', $endpoint = '', $body = [], $timeout = 10){
        $oauth_token = __zoom_oauth_token();
        if(is_wp_error($oauth_token)){
            return $oauth_token;
        }
        $url = __zoom_api_url($endpoint);
        if(!is_array($body)){
            $body = wp_parse_args($body);
        }
        $args = [
            'body' => $body,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $oauth_token,
                'Content-Type' => 'application/json',
            ],
            'timeout' => $timeout,
        ];
        return __remote_request($method, $url, $args);
    }
}

if(!function_exists('__zoom_access_token')){
    /**
     * This function’s access is marked private.
     *
     * @return string|WP_Error
     */
    function __zoom_access_token(){
        $key = 'zoom_access_token';
        $access_token = __cache_get($key);
        if($access_token !== null){
            return $access_token;
        }
        $app_credentials = __zoom_app_credentials();
        if(is_wp_error($app_credentials)){
            return $app_credentials;
        }
        $authorization = base64_encode($app_credentials['client_id'] . ':' . $app_credentials['client_secret']);
        $url = 'https://zoom.us/oauth/token';
        $args = [
            'body' => [
                'account_id' => $app_credentials['account_id'],
                'grant_type' => 'account_credentials',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $authorization,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'timeout' => 10,
        ];
        $response = __remote_post($url, $args);
        if(!$response->status){
            return $response->wp_error;
        }
        if(!isset($response->json_params['access_token'])){
            $error = __error(sprintf(__('Missing parameter(s): %s'), 'access_token'));
            __cache_set($key, $error);
            return $error;
        }
        $access_token = $response->json_params['access_token'];
        if(!$access_token){
            $error = __error(sprintf(__('Invalid parameter(s): %s'), 'access_token'));
            __cache_set($key, $error);
            return $error;
        }
        __cache_set($key, $access_token);
        return $access_token;
    }
}

if(!function_exists('__zoom_api_url')){
    /**
     * This function’s access is marked private.
     *
     * @return string
     */
    function __zoom_api_url($endpoint = ''){
        $base = 'https://api.zoom.us/v2';
        if(str_starts_with($endpoint, $base)){
            $endpoint = str_replace($base, '', $endpoint);
        }
        return path_join($base, untrailingslashit(ltrim($endpoint, '/')));
    }
}

if(!function_exists('__zoom_oauth_token')){
    /**
     * This function’s access is marked private.
     *
     * @return string|WP_Error
     */
    function __zoom_oauth_token(){
        $transient = __str_prefix('zoom_oauth_token');
        $oauth_token = get_transient($transient);
        if($oauth_token){
            return $oauth_token;
        }
        $oauth_token = __zoom_access_token();
        if(is_wp_error($oauth_token)){
            return $oauth_token;
        }
        $expiration = 59 * MINUTE_IN_SECONDS; // The token’s time to live is 1 hour. Reduce it a bit to prevent edge-case timeouts that may happen before the page is fully loaded. See: https://developers.zoom.us/docs/internal-apps/s2s-oauth/
        set_transient($transient, $oauth_token, $expiration);
        return $oauth_token;
    }
}
