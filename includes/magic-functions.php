<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Hardcoded
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__ext')){
	/**
	 * @return string|WP_Error
	 */
	function __ext($repo = '', $ver = ''){
        $args = [
            'autoload' => 'ext.php',
            'owner' => 'luisdelcid',
            'repo' => $repo,
            'subdir' => 'ext',
			'tag' => $ver,
            'validation' => __canonicalize($repo),
        ];
		return __use($args);
	}
}

if(!function_exists('__get_cache')){
	/**
	 * @return mixed
	 */
	function __get_cache($key = '', $default = null){
        global $magic_cache; // Hardcoded.
        if(!is_array($magic_cache)){
            $magic_cache = []; // Reset.
        }
        if(isset($magic_cache[$key])){
            return $magic_cache[$key];
        }
	    return $default;
	}
}

if(!function_exists('__instance')){
	/**
	 * @return object|WP_Error
	 */
	function __instance($class = '', $singleton = false){
	    if(!class_exists($class)){
            $error_msg = sprintf(translate('Missing parameter(s): %s'), '"' . $class . '"') . '.';
	        return __error($error_msg);
	    }
	    if(!is_subclass_of($class, '__Base')){ // Hardcoded.
            $error_msg = sprintf(translate('Invalid parameter(s): %s'), '"' . $class . '"') . '.';
	        return __error($error_msg);
	    }
		if($singleton){
            $method = 'get_instance';
		} else {
            $method = 'new_instance';
        }
	    return call_user_func_array([$class, $method]);
	}
}

if(!function_exists('__isset_cache')){
	/**
	 * @return bool
	 */
	function __isset_cache($key = ''){
        global $magic_cache; // Hardcoded.
        if(!is_array($magic_cache)){
            $magic_cache = []; // Reset.
        }
	    return isset($magic_cache[$key]);
	}
}

if(!function_exists('__prefix')){
	/**
	 * @return string
	 */
	function __prefix(){
	    return 'magic_functions'; // Hardcoded.
	}
}

if(!function_exists('__set_cache')){
	/**
	 * @return bool
	 */
	function __set_cache($key = '', $data = null){
        global $magic_cache; // Hardcoded.
        if(!is_array($magic_cache)){
            $magic_cache = []; // Reset.
        }
        $magic_cache[$key] = $data;
        return true;
	}
}

if(!function_exists('__slug')){
	/**
	 * @return string
	 */
	function __slug(){
        return 'magic-functions'; // Hardcoded.
	}
}

if(!function_exists('__unset_cache')){
	/**
	 * @return bool
	 */
	function __unset_cache($key = ''){
        global $magic_cache; // Hardcoded.
        if(!is_array($magic_cache)){
            $magic_cache = []; // Reset.
        }
        if(isset($magic_cache[$key])){
            unset($magic_cache[$key]);
            return true;
        }
        return false;
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
		if(doing_action('admin_notices')){ // Just in time.
	        __echo_admin_notice($message, $type, $is_dismissible);
			return;
	    }
		if(did_action('admin_notices')){ // Too late.
			return;
		}
		$admin_notice = [
			'is_dismissible' => $is_dismissible,
			'message' => $message,
            'type' => $type,
		];
		$md5 = __md5($admin_notice);
		if(__isset_array_cache('admin_notices', $md5)){
            return; // Prevent admin notice being added twice.
        }
		__set_array_cache('admin_notices', $md5, $admin_notice);
		__add_action_once('admin_notices', '__maybe_add_admin_notices');
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
            $args = [
                'type' => $type,
                'dismissible' => $is_dismissible,
            ];
            $html = wp_get_admin_notice($message, $args);
			return $html;
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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

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
		$html = __admin_notice_html($message, $type, $is_dismissible);
		echo $html;
	}
}

if(!function_exists('__maybe_add_admin_notices')){
	/**
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_admin_notices(){
		if(!doing_action('admin_notices')){ // Too early or too late.
	        return;
	    }
	    $admin_notices = (array) __get_cache('admin_notices', []);
		if(empty($admin_notices)){
			return;
		}
		foreach($admin_notices as $md5 => $admin_notice){
			__echo_admin_notice($admin_notice['message'], $admin_notice['type'], $admin_notice['is_dismissible']);
		}
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Arrays
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__array_keys_exists')){
	/**
	 * @return bool
	 */
	function __array_keys_exists($keys = [], $array = []){
		if(!is_array($keys) or !is_array($array)){
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
		if(!is_array($array)){
			return false;
		}
		if(!$array){
			return false;
		}
        return !__is_numeric_array($array);
	}
}

if(!function_exists('__is_numeric_array')){
	/**
	 * @return bool
	 */
	function __is_numeric_array($array = []){
		if(!is_array($array)){
			return false;
		}
		if(!$array){
			return false;
		}
		return array_keys($array) === range(0, count($array) - 1);
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

if(!function_exists('__list')){
	/**
	 * @return array
	 */
	function __list($list = [], $index_key = ''){
        if(!$index_key){
            return $list;
        }
		$newlist = [];
		foreach($list as $value){
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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Authentication
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__signon')){
	/**
	 * @return WP_Error|WP_User
	 */
	function __signon($username_or_email = '', $password = '', $remember = false){
		if(is_user_logged_in()){
			return wp_get_current_user();
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
			return wp_get_current_user();
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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_authenticate_without_password')){
	/**
	 * This function MUST be called inside the 'authenticate' filter hook.
	 *
	 * @return bool|WP_Error|WP_User
	 */
	function __maybe_authenticate_without_password($user = null, $username_or_email = '', $password = ''){
		if(!doing_filter('authenticate')){
	        return $user;
	    }
		if(!is_null($user)){
			return $user;
		}
		if(!empty($password)){
			$message = translate('The link you followed has expired.');
			return __error($message);
		}
		$user = false; // Returning a non-null value will effectively short-circuit the user authentication process.
		if(username_exists($username_or_email)){
			$user = get_user_by('login', $username_or_email);
		} elseif(is_email($username_or_email) and email_exists($username_or_email)){
			$user = get_user_by('email', $username_or_email);
		}
		return $user;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Bootstrap 4
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
	    $class = __remove_whitespaces($class);
	    preg_match_all('/btn-[A-Za-z][-A-Za-z0-9_:.]*/', $class, $matches);
		$matches = array_filter($matches[0], function($match){
			return !in_array($match, ['btn-block', 'btn-lg', 'btn-sm']);
		});
		return (bool) $matches;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Cache
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__array_cache_delete')){
	/**
     * Alias for __unset_array_cache().
     *
	 * @return bool
	 */
	function __array_cache_delete($array_key = '', $key = ''){
		return __unset_array_cache($array_key, $key);
	}
}

if(!function_exists('__array_cache_exists')){
	/**
     * Alias for __isset_array_cache().
     *
	 * @return bool
	 */
	function __array_cache_exists($array_key = '', $key = ''){
		return __isset_array_cache($array_key, $key);
	}
}

if(!function_exists('__array_cache_get')){
    /**
     * Alias for __get_array_cache().
     *
	 * @return mixed
	 */
	function __array_cache_get($array_key = '', $key = '', $default = null){
		return __get_array_cache($array_key, $key, $default);
	}
}

if(!function_exists('__array_cache_set')){
    /**
     * Alias for __set_array_cache().
     *
	 * @return bool
	 */
	function __array_cache_set($array_key = '', $key = '', $data = null){
		return __set_array_cache($array_key, $key, $data);
	}
}

if(!function_exists('__cache_delete')){
    /**
     * Alias for __unset_cache().
     *
	 * @return bool
	 */
	function __cache_delete($key = ''){
		return __unset_cache($key);
	}
}

if(!function_exists('__cache_exists')){
    /**
     * Alias for __isset_cache().
     *
	 * @return bool
	 */
	function __cache_exists($key = ''){
		return __isset_cache($key);
	}
}

if(!function_exists('__cache_get')){
    /**
     * Alias for __get_cache().
     *
	 * @return mixed
	 */
	function __cache_get($key = '', $default = null){
		return __get_cache($key, $default);
	}
}

if(!function_exists('__cache_set')){
    /**
     * Alias for __set_cache().
     *
	 * @return bool
	 */
	function __cache_set($key = '', $data = null){
		return __set_cache($key, $data);
	}
}

if(!function_exists('__get_array_cache')){
    /**
	 * @return mixed
	 */
	function __get_array_cache($array_key = '', $key = '', $default = null){
		$array = (array) __get_cache($array_key, []);
		return isset($array[$key]) ? $array[$key] : $default;
	}
}

if(!function_exists('__isset_array_cache')){
	/**
	 * @return bool
	 */
	function __isset_array_cache($array_key = '', $key = ''){
		$array = (array) __get_cache($array_key, []);
		return isset($array[$key]);
	}
}

if(!function_exists('__set_array_cache')){
	/**
	 * @return bool
	 */
	function __set_array_cache($array_key = '', $key = '', $data = null){
		$array = (array) __get_cache($array_key, []);
		$array[$key] = $data;
		return __set_cache($array_key, $array);
	}
}

if(!function_exists('__unset_array_cache')){
	/**
	 * @return bool
	 */
	function __unset_array_cache($array_key = '', $key = ''){
		$array = (array) __get_cache($array_key, []);
		if(isset($array[$key])){
			unset($array[$key]);
		}
		return __set_cache($array_key, $array);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Closures
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__serialize_closure')){
	/**
	 * @return string|WP_Error
	 */
	function __serialize_closure($closure = null){
		//$lib = __use_serializable_closure();
        $lib = __use_closure();
		if(is_wp_error($lib)){
			return $lib;
		}
		return \Opis\Closure\serialize($closure);
	}
}

if(!function_exists('__unserialize_closure')){
	/**
	 * @return mixed|WP_Error
	 */
	function __unserialize_closure($closure = null){
		//$lib = __use_serializable_closure();
		$lib = __use_closure();
		if(is_wp_error($lib)){
			return $lib;
		}
		return \Opis\Closure\unserialize($closure);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__use_closure')){
	/**
	 * @return string|WP_Error
	 */
	function __use_closure($ver = '4.3.1'){
        return __use([
            'autoload' => 'autoload.php',
            'owner' => 'opis',
            'repo' => 'closure',
            'tag' => $ver,
            //'release' => $ver,
            'validation' => 'Opis\Closure\Serializer',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Code
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__compile_less')){
	/**
	 * @return string|WP_Error
	 */
	function __compile_less($css = ''){
        $lib = __use_lessphp();
    	if(is_wp_error($lib)){
    		return $lib;
    	}
    	\Less_Autoloader::register();
        $parser = new \Less_Parser([
            'compress' => true,
        ]);
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

if(!function_exists('__compress_css')){
	/**
	 * @return string
	 */
	function __compress_css($css = ''){
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = __remove_whitespaces($css);
		return $css;
    }
}

if(!function_exists('__minify_css')){
	/**
	 * @return string
	 */
	function __minify_css($css = ''){
        $css = __normalize_ie_filters($css);
        $less = __compile_less($css);
        if(!is_wp_error($less)){
            $css = $less;
        }
        /*if(__in_debug()){
			return $css;
		}*/
        $css = __compress_css($css);
        return $css;
    }
}

if(!function_exists('__minify_js')){
	/**
	 * @return string|WP_Error
	 */
	function __minify_js($js = ''){
        if(__in_debug()){
			return $js;
		}
        $lib = __use_jshrink();
    	if(is_wp_error($lib)){
    		return $js; // Silence is golden.
    	}
    	$result = \JShrink\Minifier::minify($js, [
            'flaggedComments' => false,
        ]);
        return $result;
    }
}

if(!function_exists('__normalize_ie_filters')){
	/**
	 * @return string
	 */
	function __normalize_ie_filters($css = ''){
		return preg_replace_callback('(filter\s?:\s?(.*);)', '__preg_replace_less', $css); // Fix issue with IE filters.
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__preg_replace_less')){
	/**
	 * @return string
	 */
	function __preg_replace_less($matches = []){
        if(!empty($matches[1])){
			return 'filter: ~"' . $matches[1] . '";';
		}
		return $matches[0];
    }
}

if(!function_exists('__use_jshrink')){
	/**
	 * @return string|WP_Error
	 */
	function __use_jshrink($ver = '1.7.0'){
        return __use([
            'autoload' => 'src/JShrink/Minifier.php',
            'owner' => 'tedious',
            'repo' => 'JShrink',
            'tag' => 'v' . $ver,
            'release' => $ver,
            'validation' => 'JShrink\Minifier',
        ]);
	}
}

if(!function_exists('__use_lessphp')){
	/**
	 * @return string|WP_Error
	 */
	function __use_lessphp($ver = '5.3.1'){
        return __use([
            'autoload' => 'lib/Less/Autoloader.php',
            'owner' => 'wikimedia',
            'repo' => 'less.php',
            'tag' => 'v' . $ver,
            'release' => $ver,
            'validation' => 'Less_Autoloader',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Contact Form 7
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_abort')){
	/**
	 * This function MUST be called inside the 'wpcf7_before_send_mail' action hook.
	 *
	 * @return void
	 */
	function __cf7_abort(&$abort, $message = '', $submission = null){
		if(!doing_action('wpcf7_before_send_mail')){
	        return; // Too early or too late.
	    }
		if($abort){
			return; // Already aborted.
		}
	    $submission = __cf7_submission($submission);
	    if(is_null($submission)){
	        return;
	    }
		if(!$submission->is('init')){
			return; // Avoid conflicts with other statuses.
		}
	    $abort = true; // Avoid mail_sent and mail_failed action hooks.
		$message = __cf7_message($message, 'aborted');
	    $submission->set_response($message);
	    $submission->set_status('aborted');
	}
}

if(!function_exists('__cf7_additional_setting')){
	/**
	 * Alias for WPCF7_ContactForm::pref.
	 *
	 * Differs from WPCF7_ContactForm::pref in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_additional_setting($name = '', $contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return '';
		}
		return (string) $contact_form->pref($name);
	}
}

if(!function_exists('__cf7_additional_settings')){
	/**
	 * Alias for WPCF7_ContactForm::additional_setting(@param $max = false).
	 *
	 * Differs from WPCF7_ContactForm::additional_setting in that it will always return an array.
	 *
	 * @return array
	 */
	function __cf7_additional_settings($name = '', $contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return [];
		}
		return $contact_form->additional_setting($name, false);
	}
}

if(!function_exists('__cf7_contact_form')){
	/**
	 * Alias for wpcf7_contact_form, wpcf7_get_contact_form_by_hash and wpcf7_get_contact_form_by_title.
	 *
	 * Returns the current contact form if the specified setting has a falsey value and restores the current contact form.
	 *
	 * @return null|WPCF7_ContactForm
	 */
	function __cf7_contact_form($contact_form = null){
		$current_contact_form = wpcf7_get_current_contact_form();
		if(empty($contact_form)){ // 0, false, null and other PHP falsey values.
			return $current_contact_form;
		}
		if(__is_cf7($contact_form)){
			return $contact_form;
		}
		$post_id = __cf7_hash_exists($contact_form); // Hash-based contact form identification.
		if($post_id){
			$contact_form = wpcf7_contact_form($post_id); // Avoid wpcf7_get_contact_form_by_hash for backcompat.
		} elseif(is_numeric($contact_form)){
			$contact_form = __cf7_get_contact_form_by('id', $contact_form);
		} elseif(is_string($contact_form)){
	        $contact_form = __cf7_get_contact_form_by('title', $contact_form);
	    } elseif($contact_form instanceof \WP_Post){
			$contact_form = wpcf7_contact_form($contact_form);
		} else {
			return null;
		}
		if(__is_cf7($current_contact_form)){
			wpcf7_contact_form($current_contact_form); // Restores the current contact form.
		}
		return $contact_form;
	}
}

if(!function_exists('__cf7_container_post_id')){
	/**
	 * @return int
	 */
	function __cf7_container_post_id(){
		$submission = __cf7_submission();
        if(__cf7_is_submission($submission)){
            return $submission->get_meta('container_post_id');
        }
        if(!__is_front()){
            return 0;
        }
        if(!in_the_loop()){
            return 0;
        }
        return get_the_ID();
	}
}

if(!function_exists('__cf7_error')){
	/**
	 * @return string
	 */
	function __cf7_error($message = ''){
		if(empty($message)){
			//$message = translate('Contact form not found.', 'contact-form-7');
            //translate('An error occurred.');
            $message = translate('An error occurred while processing your request. Please try again later.');
            $message = __first_p($message);
		}
		$error = translate('Error:', 'contact-form-7');
		return sprintf('<p class="wpcf7-contact-form-not-found"><strong>%1$s</strong> %2$s</p>', esc_html($error), esc_html($message));
	}
}

if(!function_exists('__cf7_fake_mail')){
	/**
	 * Skips or sends emails based on user input values and contact form email templates avoiding mail_sent and mail_failed action hooks.
	 *
	 * This function MUST be called inside the 'wpcf7_before_send_mail' action hook.
	 *
	 * @return bool
	 */
	function __cf7_fake_mail($contact_form = null, $submission = null){
		if(!doing_action('wpcf7_before_send_mail')){
	        return; // Too early or too late.
	    }
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return false;
		}
		$submission = __cf7_submission($submission);
		if(is_null($submission)){
			return false;
		}
		if(!$submission->is('init')){
			return false; // Avoid conflicts with other statuses.
		}
		if(__cf7_skip_mail($contact_form) or __cf7_mail($contact_form)){
			$status = 'mail_sent';
			$message = __cf7_message('', $status);
			$submission->set_response($message);
			$submission->set_status($status);
			return true;
		}
		$status = 'mail_failed';
		$message = __cf7_message('', $status);
		$submission->set_response($message);
		$submission->set_status($status);
		return false;
	}
}

if(!function_exists('__cf7_form_tag_class')){
	/**
	 * Alias for WPCF7_FormTag::get_class_option.
	 *
	 * Differs from WPCF7_FormTag::get_class_option in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_form_tag_class($tag = null, $default_classes = ''){
	    if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
		return (string) $tag->get_class_option($default_classes);
	}
}

if(!function_exists('__cf7_form_tag_content')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_content($tag = null, $remove_whitespaces = false){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return ($remove_whitespaces ? __remove_whitespaces($tag->content) : trim($tag->content));
	}
}

if(!function_exists('__cf7_form_tag_content_label')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_content_label($tag = null){
		$content = __cf7_form_tag_content($tag, true);
		if(empty($content)){
			return '';
		}
		if(!in_array($tag->basetype, ['checkbox', 'date', 'email', 'file', 'number', 'password', 'radio', 'range', 'select', 'tel', 'text', 'textarea', 'url'])){
	        return '';
	    }
		if('textarea' === $tag->basetype and $tag->has_option('has_content')){
			return '';
		}
		return $content;
	}
}

if(!function_exists('__cf7_form_tag_content_placeholder_equals')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_content_placeholder_equals($tag = null){
		$content = __cf7_form_tag_content($tag, true);
		$placeholder = __cf7_form_tag_placeholder($tag);
		return ($content === $placeholder);
	}
}

if(!function_exists('__cf7_form_tag_fa')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_fa($tag = null){
		$class = __cf7_form_tag_fa_class($tag);
		if(!$class){
			return '';
		}
		return '<i class="' . $class . '"></i>';
	}
}

if(!function_exists('__cf7_form_tag_fa_class')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_fa_class($tag = null){
	    $classes = __cf7_form_tag_fa_classes($tag);
	    if(!$classes){
			return '';
		}
		return implode(' ', $classes);
	}
}

if(!function_exists('__cf7_form_tag_fa_classes')){
	/**
	 * @return array
	 */
	function __cf7_form_tag_fa_classes($tag = null){
		if(!__cf7_is_form_tag($tag)){
	        return [];
	    }
		if(!$tag->has_option('fa')){
			return [];
		}
		$classes = [];
		switch(true){
		    case $tag->has_option('fab'):
		        $classes[] = 'fab';
		        break;
		    case $tag->has_option('fad'):
		        $classes[] = 'fad';
		        break;
		    case $tag->has_option('fal'):
		        $classes[] = 'fal';
		        break;
		    case $tag->has_option('far'):
		        $classes[] = 'far';
		        break;
		    case $tag->has_option('fas'):
		        $classes[] = 'fas';
		        break;
		    default:
		        return '';
		}
		$fa = $tag->get_option('fa', 'class', true);
		if(0 !== strpos($fa, 'fa-')){
			$fa = 'fa-' . $fa;
		}
		$classes[] = $fa;
		if($tag->has_option('fw')){
		    $classes[] = 'fa-fw';
		}
	    $rotate = $tag->get_option('rotate', 'int', true);
	    if(in_array($rotate, [90, 180, 270])){
	        $classes[] = 'fa-rotate-' . $rotate;
	    }
	    $flip = $tag->get_option('flip', '', true);
	    if(in_array($flip, ['horizontal', 'vertical', 'both'])){
	        $classes[] = 'fa-flip-' . $flip;
	    }
	    $animate = $tag->get_option('animate', '', true);
	    if(in_array($animate, ['beat', 'fade', 'beat-fade', 'bounce', 'flip', 'shake', 'spin'])){
	        $classes[] = 'fa-' . $animate;
	    }
		return $classes;
	}
}

if(!function_exists('__cf7_form_tag_floating_label')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_floating_label($tag = null){
		$content = __cf7_form_tag_content($tag, true);
		$placeholder = __cf7_form_tag_placeholder($tag);
		if(empty($content) and empty($placeholder)){
			return '';
		}
		if(!in_array($tag->basetype, ['date', 'email', 'file', 'number', 'password', 'select', 'tel', 'text', 'textarea', 'url'])){
	        return '';
	    }
		if($placeholder){
			return $placeholder;
		}
		return wp_strip_all_tags($content);
	}
}

if(!function_exists('__cf7_form_tag_has_data_option')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_data_option($tag = null){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return (bool) $tag->get_data_option();
	}
}

if(!function_exists('__cf7_form_tag_has_content')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_content($tag = null){
	    $content = __cf7_form_tag_content($tag, true);
		return ('' !== $content); // An empty string.
	}
}

if(!function_exists('__cf7_form_tag_has_free_text')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_free_text($tag = null){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return $tag->has_option('free_text');
	}
}

if(!function_exists('__cf7_form_tag_has_pipes')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_pipes($tag = null){
		if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
	    if(!WPCF7_USE_PIPE or !$tag->pipes instanceof \WPCF7_Pipes or $tag->pipes->zero()){
	        return false;
	    }
	    foreach($tag->pipes->to_array() as $pipe){
	        if($pipe[0] !== $pipe[1]){
	            return true;
	        }
	    }
		return false;
	}
}

if(!function_exists('__cf7_form_tag_has_option')){
	/**
	 * Alias for WPCF7_FormTag::has_option.
	 *
	 * @return bool
	 */
	function __cf7_form_tag_has_option($tag = null, $option_name = ''){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return $tag->has_option($option_name);
	}
}

if(!function_exists('__cf7_form_tag_id')){
	/**
	 * Important: Avoid WPCF7_FormTag::get_id_option.
	 *
	 * Differs from WPCF7_FormTag::get_id_option in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_form_tag_id($tag = null){
	    if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
		return __cf7_form_tag_option($tag, 'id', 'id');
	}
}

if(!function_exists('__cf7_form_tag_idx')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_idx($tag = null, $contact_form = null){
	    if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
        $uniqid = false;
        if(empty($tag->id)){
            if(empty($tag->name)){
                $id = uniqid($tag->basetype . '_');
                $uniqid = true;
            } else {
                $id = $tag->name;
            }
        } else {
            $id = $tag->id;
        }
        if(wp_doing_ajax()){ // AJAX.
            if(!$uniqid){
                $id .= '-' . uniqid();
            }
        } else {
            if(!$uniqid){
                $o = __cf7_object_number($contact_form);
                if($o){ // Inside the loop.
                    $id .= '-' . $o;
                } else { // Outside the loop.
                    $id .= '-' . uniqid();
                }
            }
        }
        return $id;
	}
}

if(!function_exists('__cf7_form_tag_is_false')){
	/**
	 * Opposite of WPCF7_ContactForm::is_true.
	 *
	 * @return bool
	 */
	function __cf7_form_tag_is_false($tag = null, $option_name = ''){
	    $option_value = __cf7_form_tag_option($tag, $option_name);
	    return __is_false($option_value);
	}
}

if(!function_exists('__cf7_form_tag_is_true')){
	/**
	 * Alias for WPCF7_ContactForm::is_true.
	 *
	 * @return bool
	 */
	function __cf7_form_tag_is_true($tag = null, $option_name = ''){
	    $option_value = __cf7_form_tag_option($tag, $option_name);
	    return __is_true($option_value);
	}
}

if(!function_exists('__cf7_form_tag_option')){
	/**
	 * Alias for WPCF7_FormTag::get_option(@param $single = true).
	 *
	 * Differs from WPCF7_FormTag::get_option in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_form_tag_option($tag = null, $option_name = '', $pattern = ''){
	    if(!__cf7_form_tag_has_option($tag, $option_name)){
	        return '';
	    }
	    return (string) $tag->get_option($option_name, $pattern, true);
	}
}

if(!function_exists('__cf7_form_tag_options')){
	/**
	 * Alias for WPCF7_FormTag::get_option(@param $single = false).
	 *
	 * Differs from WPCF7_FormTag::get_option in that it will always return an array.
	 *
	 * @return array
	 */
	function __cf7_form_tag_options($tag = null, $option_name = '', $pattern = ''){
	    if(!__cf7_form_tag_has_option($tag, $option_name)){
	        return '';
	    }
	    return (array) $tag->get_option($option_name, $pattern, false);
	}
}

if(!function_exists('__cf7_form_tag_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_output($form_tag = '', $callback = '', $priority = 10, $accepted_args = 1){
        $form_tags = (array) $form_tag;
        foreach($form_tags as $form_tag){
            if('*' === $form_tag){
                $hook_name = 'cf7_form_tag_output';
            } else {
                $hook_name = 'cf7_form_tag_' . $form_tag . '_output';
            }
            $hook = [
                'accepted_args' => $accepted_args,
                'callback' => $callback, // Closure?
                'hook_name' => $hook_name,
                'priority' => $priority,
            ];
            $md5 = __md5($hook);
            if(__isset_array_cache('cf7_hooks', $md5)){
                return; // Prevent hook being added twice.
            }
            __set_array_cache('cf7_hooks', $md5, $hook);
            __add_filter_once($hook_name, $callback, $priority, $accepted_args);
            __add_filter_once('do_shortcode_tag', '__cf7_maybe_filter_shortcode_tag_output', 10, 4);
        }
	}
}

if(!function_exists('__cf7_form_tag_placeholder')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_placeholder($tag = null){
		if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
		if(!in_array($tag->basetype, ['date', 'email', 'file', 'number', 'password', 'select', 'tel', 'text', 'textarea', 'url'])){
	        return '';
	    }
		if('select' === $tag->basetype){
			if($tag->has_option('include_blank') or empty($tag->values)){
				if(version_compare(WPCF7_VERSION, '5.7', '>=')){
					return translate('&#8212;Please choose an option&#8212;', 'contact-form-7'); // Drop-down menu: Uses more friendly label text. https://contactform7.com/2022/12/10/contact-form-7-57/
				} else {
					return '---';
				}
			} elseif($tag->has_option('first_as_label') and !empty($tag->values)){
				return (string) $tag->values[0];
			} else {
				return '';
			}
		} else {
			if(($tag->has_option('placeholder') or $tag->has_option('watermark')) and !empty($tag->values)){
				return (string) $tag->values[0];
			} else {
				return '';
			}
		}
	}
}

if(!function_exists('__cf7_get_contact_form_by')){
	/**
	 * @return null|WPCF7_ContactForm
	 */
	function __cf7_get_contact_form_by($field = '', $value = null){
	    if('hash' === $field and version_compare(WPCF7_VERSION, '5.8', '<')){ // https://contactform7.com/2023/08/06/contact-form-7-58/#hash-based-contact-form-identification
			return null;
		}
		if('id' === $field){
			$value = __absint($value);
			if(!$value){
				return null;
			}
		}
		if(in_array($field, ['hash', 'title'])){
			if(!is_string($value)){
				return null;
			}
			$value = trim($value);
			if(!$value){
				return null;
			}
		}
		switch($field){
			case 'hash':
				$contact_form = wpcf7_get_contact_form_by_hash($value);
				break;
			case 'id':
				$contact_form = wpcf7_contact_form($value);
				break;
			case 'title':
				$contact_form = wpcf7_get_contact_form_by_title($value);
				break;
			default:
				return null;
		}
		return $contact_form;
	}
}

if(!function_exists('__cf7_get_submission')){
	/**
	 * @return array|WP_Error
	 */
	function __cf7_get_submission($meta_type = '', $object_id = 0){
	    if(!in_array($meta_type, ['post', 'user'])){
			$error_msg = sprintf(translate('Invalid parameter(s): %s'), 'meta_type') . '.';
	        return __error($error_msg);
	    }
	    if('post' === $meta_type){
	        $post = get_post($object_id);
	        if(empty($post)){
				$error_msg = translate('Invalid post ID.');
	            return __error($error_msg);
	        }
	    } elseif('user' === $meta_type){
	        $user = get_userdata($object_id);
	        if(empty($user)){
				$error_msg = translate('Invalid user ID.');
	            return __error($error_msg);
	        }
	    }
        $data = [
            'fields' => [],
            'files' => [],
            'form' => [],
            'meta' => [],
            'submission' => [],
        ];
        $form_keys = ['demo_mode', 'id', 'locale', 'name', 'skip_mail', 'title'];
        $meta_data = get_metadata($meta_type, $object_id);
        $meta_keys = ['container_post_id', 'current_user_id', 'remote_ip', 'remote_port', 'timestamp', 'unit_tag', 'url', 'user_agent'];
        $submission_keys = ['response', 'status'];
        foreach($meta_data as $key => $values){
            if(__str_starts_with('field_', $key)){
                $meta_key = str_replace('field_', '', $key);
                $data['fields'][$meta_key] = $values;
                continue;
            }
            if(__str_starts_with('file_', $key)){
                $meta_key = str_replace('file_', '', $key);
                $data['files'][$meta_key] = $values;
                continue;
            }
            if(__str_starts_with('form_', $key)){
                $meta_key = str_replace('form_', '', $key);
                if(!in_array($meta_key, $form_keys)){
                    continue;
                }
                $data['form'][$meta_key] = $values;
                continue;
            }
            if(__str_starts_with('meta_', $key)){
                $meta_key = str_replace('meta_', '', $key);
                if(!in_array($meta_key, $meta_keys)){
                    continue;
                }
                $data['meta'][$meta_key] = $values;
                continue;
            }
            if(__str_starts_with('submission_', $key)){
                $meta_key = str_replace('submission_', '', $key);
                if(!in_array($meta_key, $submission_keys)){
                    continue;
                }
                $data['submission'][$meta_key] = $values;
                continue;
            }
        }
	    return $data;
	}
}

if(!function_exists('__cf7_has_additional_setting')){
	/**
	 * @return bool
	 */
	function __cf7_has_additional_setting($name = '', $contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return false;
		}
		$pref = $contact_form->pref($name);
		return !is_null($pref);
	}
}

if(!function_exists('__cf7_has_posted_data')){
	/**
	 * @return bool
	 */
	function __cf7_has_posted_data($key = ''){
		if(empty($key)){
			return false;
		}
		$data = __cf7_posted_data($key);
		return !is_null($data);
	}
}

if(!function_exists('__cf7_has_shortcode_attr')){
	/**
	 * @return void
	 */
	function __cf7_has_shortcode_attr($name = '', $contact_form = null){
        $contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return false;
		}
		return !is_null($contact_form->shortcode_attr($name));
	}
}

if(!function_exists('__cf7_hash_exists')){
	/**
	 * Alias for wpcf7_get_contact_form_by_hash.
	 *
	 * Differs from wpcf7_get_contact_form_by_hash in that it will always return an integer.
	 *
	 * @return int
	 */
	function __cf7_hash_exists($hash = ''){
		global $wpdb;
		if(!is_string($hash)){
	        return 0;
	    }
		$hash = trim($hash);
		if(strlen($hash) < 7){
			return 0;
		}
		$like = $wpdb->esc_like($hash) . '%';
		$query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_hash' AND meta_value LIKE %s";
		$query = $wpdb->prepare($query, $like);
		$post_id = $wpdb->get_var($query);
		return (int) $post_id;
	}
}

if(!function_exists('__cf7_insert_update_submission')){
	/**
	 * @return array|WP_Error
	 */
	function __cf7_insert_update_submission($atts = []){
	    $pairs = [
	        'action' => '',
	        'contact_form' => null,
	        'meta_type' => '',
	        'object_id' => 0,
	        'submission' => null,
	        'upload_path' => '',
	    ];
	    $atts = shortcode_atts($pairs, $atts);
	    extract($atts);
	    if(!in_array($action, ['insert', 'update'])){
			$error_msg = sprintf(translate('Invalid parameter(s): %s'), 'action') . '.';
	        return __error($error_msg);
	    }
        $contact_form = __cf7_contact_form($contact_form);
	    if(is_null($contact_form)){
			$error_msg = translate('The requested contact form was not found.', 'contact-form-7');
	        return __error($error_msg);
	    }
	    if(!in_array($meta_type, ['post', 'user'])){
			$error_msg = sprintf(translate('Invalid parameter(s): %s'), 'meta_type') . '.';
	        return __error($error_msg);
	    }
	    if('post' === $meta_type){
	        $post = get_post($object_id);
	        if(empty($post)){
				$error_msg = translate('Invalid post ID.');
	            return __error($error_msg);
	        }
	    } elseif('user' === $meta_type){
	        $user = get_userdata($object_id);
	        if(empty($user)){
				$error_msg = translate('Invalid user ID.');
	            return __error($error_msg);
	        }
	    }
        $submission = __cf7_submission($submission);
	    if(is_null($submission)){
			$error_msg = sprintf(translate('%s (Invalid)'), 'WPCF7_Submission') . '.';
	        return __error($error_msg);
	    }
        if($submission->get_meta('do_not_store')){
            $error_msg = translate('Erase Personal Data') . ': ' . translate('Many plugins may collect or store personal data either in the WordPress database or remotely. Any Erase Personal Data request should delete data from plugins as well.');
	        return __error($error_msg);
        }
        if('post' === $meta_type){
	        $the_post = wp_is_post_revision($object_id);
	        if($the_post){
	            $object_id = $the_post; // Make sure meta is added to the post, not a revision.
	        }
	    }
	    if('insert' === $action){
	        if('post' === $meta_type){
	            __set_cache('cf7_inserted_post_id', $object_id);
	        } elseif('user' === $meta_type){
	            __set_cache('cf7_inserted_user_id', $object_id);
	        }
	    } elseif('update' === $action){
	        if('post' === $meta_type){
	            __set_cache('cf7_updated_post_id', $object_id);
	        } elseif('user' === $meta_type){
	            __set_cache('cf7_updated_user_id', $object_id);
	        }
            if('post' === $meta_type){
	            $postarr = [
	                'ID' => $object_id,
	            ];
	            $field = 'post_content';
	            $content = $submission->get_posted_data($field);
	            if($content){
	                $postarr['post_content'] = $content;
	            }
	            $field = 'post_excerpt';
	            $excerpt = $submission->get_posted_data($field);
	            if($excerpt){
	                $postarr['post_excerpt'] = $excerpt;
	            }
	            $field = 'post_title';
	            $title = $submission->get_posted_data($field);
	            if($title){
	                $postarr['post_title'] = $title;
	            }
				$save_post_revision = !has_filter('wp_save_post_revision_post_has_changed', '__return_true');
				if($save_post_revision){ // Don't filter twice.
					add_filter('wp_save_post_revision_post_has_changed', '__return_true');
				}
	            $post_id = wp_update_post($postarr, true); // Always save a revision.
				if($save_post_revision){
					remove_filter('wp_save_post_revision_post_has_changed', '__return_true');
				}
	            if(is_wp_error($post_id)){
	                __set_cache('cf7_updated_post_id', 0);
	                return $post_id;
	            }
	        } elseif('user' === $meta_type){
	            $user_id = wp_update_user([
	                'ID' => $object_id,
	            ]);
	            if(is_wp_error($user_id)){
	                __set_cache('cf7_updated_user_id', 0);
	                return $user_id;
	            }
	        }
	    }
        $contact_form_data = [
            'demo_mode' => $contact_form->in_demo_mode(),
            'id' => $contact_form->id(),
	        'locale' => $contact_form->locale(),
	        'name' => $contact_form->name(),
	        'skip_mail' => __cf7_skip_mail($contact_form),
            'title' => $contact_form->title(),
        ];
        foreach($contact_form_data as $key => $value){
            $meta_key = 'form_' . $key;
	        update_metadata($meta_type, $object_id, $meta_key, $value);
	    }
        $meta_data = [
            'container_post_id' => $submission->get_meta('container_post_id'),
			'current_user_id' => $submission->get_meta('current_user_id'),
            'remote_ip' => $submission->get_meta('remote_ip'),
			'remote_port' => $submission->get_meta('remote_port'),
            'timestamp' => $submission->get_meta('timestamp'),
			'unit_tag' => $submission->get_meta('unit_tag'),
			'url' => $submission->get_meta('url'),
			'user_agent' => $submission->get_meta('user_agent'),
        ];
        foreach($meta_data as $key => $value){
            $meta_key = 'meta_' . $key;
	        update_metadata($meta_type, $object_id, $meta_key, $value);
	    }
        $submission_data = [
            'response' => $submission->get_response(),
	        'status' => $submission->get_status(),
        ];
        foreach($submission_data as $key => $value){
            $meta_key = 'submission_' . $key;
	        update_metadata($meta_type, $object_id, $meta_key, $value);
	    }
	    $posted_data = $submission->get_posted_data(); // Filtered posted data.
	    foreach($posted_data as $key => $value){
            $meta_key = 'field_' . $key;
	        if(is_array($value)){
	            delete_metadata($meta_type, $object_id, $meta_key);
	            foreach($value as $single){
	                add_metadata($meta_type, $object_id, $meta_key, $single);
	            }
	        } else {
	            update_metadata($meta_type, $object_id, $meta_key, $value);
	        }
	    }
        $uploaded_files = $submission->uploaded_files();
        if(empty($uploaded_files)){
            return __cf7_get_submission($meta_type, $object_id);
        }
        if(empty($upload_path)){
			$upload_path = __download_dir('cf7-uploads');
			if(is_wp_error($upload_path)){
				return $upload_path;
			}
		} else {
	        $upload_path = __check_dir($upload_path);
	        if(is_wp_error($upload_path)){
	            return $upload_path;
	        }
			$upload_path = __check_upload_dir($upload_path);
			if(is_wp_error($upload_path)){
				return $upload_path;
			}
		}
        $fs = __fs_direct();
		if(is_wp_error($fs)){
			return $fs;
		}
        if('post' === $meta_type){
	        $post_id = $object_id;
	    } else {
	        $post_id = 0;
	    }
	    $uploaded_error = new \WP_Error;
	    foreach($uploaded_files as $key => $value){
            $meta_key = 'file_' . $key;
	        foreach((array) $value as $tmp_name){
	            $original_filename = wp_basename($tmp_name);
	            $filename = wp_unique_filename($upload_path, $original_filename);
	            $file = trailingslashit($upload_path) . $filename;
	            if($fs->copy($tmp_name, $file)){
	                $attachment_id = __sideload($file, $post_id, false);
	                if(is_wp_error($attachment_id)){
                        add_metadata($meta_type, $object_id, $meta_key, $attachment_id->get_error_message());
	                    $uploaded_error->merge_from($attachment_id);
	                } else {
	                    add_metadata($meta_type, $object_id, $meta_key, $attachment_id);
	                }
	            } else {
                    $error_msg = translate('The uploaded file could not be moved to %s.');
	                $error_msg = sprintf($error_msg, $file);
	                add_metadata($meta_type, $object_id, $meta_key, $error_msg);
	                $error = __error($error_msg);
	                $uploaded_error->merge_from($error);
	            }
	        }
	        delete_metadata($meta_type, $object_id, $key); // Hash strings.
	    }
	    if($uploaded_error->has_errors()){
            $error_msg = translate('Error:') . ' ' . implode(' ', array_map('__trailingdotit', $uploaded_error->get_error_messages()));
            return __error($error_msg);
	    }
        return __cf7_get_submission($meta_type, $object_id);
	}
}

if(!function_exists('__cf7_invalid_fields')){
	/**
	 * @return array
	 */
	function __cf7_invalid_fields($fields = [], $contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return [];
		}
		if(!__is_associative_array($fields)){
			return [];
		}
		$invalid = [];
		$tags = wp_list_pluck($contact_form->scan_form_tags('feature=name-attr'), 'type', 'name');
		foreach($fields as $name => $types){
	        if(!isset($tags[$name])){
	            continue;
	        }
	        if(!in_array($tags[$name], (array) $types)){
	            $invalid[] = $name;
	        }
		}
		return $invalid;
	}
}

if(!function_exists('__cf7_is_false')){
	/**
	 * Opposite of WPCF7_ContactForm::is_true.
	 *
	 * @return bool
	 */
	function __cf7_is_false($name = '', $contact_form = null){
		$pref = __cf7_additional_setting($name, $contact_form);
	    return __is_false($pref);
	}
}

if(!function_exists('__cf7_is_form_tag')){
	/**
	 * @return bool
	 */
	function __cf7_is_form_tag($tag = null){
		return ($tag instanceof \WPCF7_FormTag);
	}
}

if(!function_exists('__cf7_is_submission')){
	/**
	 * @return bool
	 */
	function __cf7_is_submission($submission = null){
		return ($submission instanceof \WPCF7_Submission);
	}
}

if(!function_exists('__cf7_is_true')){
	/**
	 * Alias for WPCF7_ContactForm::is_true.
	 *
	 * @return bool
	 */
	function __cf7_is_true($name = '', $contact_form = null){
		$pref = __cf7_additional_setting($name, $contact_form);
	    return __is_true($pref);
	}
}

if(!function_exists('__cf7_localize')){
	/**
	 * @return WPCF7_ContactForm|WP_Error
	 */
	function __cf7_localize($contact_form = null, $overwrite_messages = false){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			$message = translate('The requested contact form was not found.', 'contact-form-7');
			return __error($message, [
				'status' => 404,
			]);
		}
		$contact_form_id = $contact_form->id();
		$locale = get_locale();
		if($locale === get_post_meta($contact_form_id, '_locale', true) and !$overwrite_messages){
			return $contact_form;
		}
		$args = [
			'id' => $contact_form_id,
			'locale' => $locale,
		];
		if($overwrite_messages){
	        $messages = wpcf7_messages();
			$args['messages'] = wp_list_pluck($messages, 'default');
		}
		$contact_form = wpcf7_save_contact_form($args);
		if(!$contact_form){
			return __error(translate('There was an error saving the contact form.', 'contact-form-7'), [
				'status' => 500,
			]);
		}
		return $contact_form;
	}
}

if(!function_exists('__cf7_mail')){
	/**
	 * Alias for WPCF7_Submission::mail.
	 *
	 * @return bool
	 */
	function __cf7_mail($contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return false;
		}
		$skip_mail = __cf7_skip_mail($contact_form);
		if($skip_mail){
			return true;
		}
		$result = \WPCF7_Mail::send($contact_form->prop('mail'), 'mail');
		if(!$result){
			return false;
		}
		$additional_mail = [];
		if($mail_2 = $contact_form->prop('mail_2') and $mail_2['active']){
			$additional_mail['mail_2'] = $mail_2;
		}
		$additional_mail = apply_filters('wpcf7_additional_mail', $additional_mail, $contact_form);
		foreach($additional_mail as $name => $template){
			\WPCF7_Mail::send($template, $name);
		}
		return true;
	}
}

if(!function_exists('__cf7_message')){
	/**
	 * Alias for WPCF7_ContactForm::filter_message.
	 *
	 * @return string
	 */
	function __cf7_message($message = '', $status = ''){
		$message = wpcf7_mail_replace_tags($message);
		$message = apply_filters('wpcf7_display_message', $message, $status);
		$message = wp_strip_all_tags($message);
		if(!$message){
			$messages = wpcf7_messages();
			switch($status){
				case 'aborted':
					$message = translate('Sending mail has been aborted.', 'contact-form-7');
					break;
				case 'acceptance_missing':
					$message = $messages['accept_terms']['default'];
					break;
				case 'mail_failed':
					$message = $messages['mail_sent_ng']['default'];
					break;
				case 'mail_sent':
					$message = $messages['mail_sent_ok']['default'];
					break;
				case 'spam':
					$message = $messages['spam']['default'];
					break;
				case 'validation_failed':
					$message = $messages['validation_error']['default'];
					break;
				default:
					$message = translate('Unknown action.');
			}
		}
		return $message;
	}
}

if(!function_exists('__cf7_missing_fields')){
	/**
	 * @return array
	 */
	function __cf7_missing_fields($fields = [], $contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return [];
		}
	    if(!is_array($fields)){
			return [];
		}
		if(__is_associative_array($fields)){
			$fields = array_keys($fields);
		}
		$missing = [];
		$tags = wp_list_pluck($contact_form->scan_form_tags('feature=name-attr'), 'type', 'name');
		foreach($fields as $name){
			if(isset($tags[$name])){
				continue;
			}
			$missing[] = $name;
		}
		return $missing;
	}
}

if(!function_exists('__cf7_object_number')){
	/**
	 * @return int
	 */
	function __cf7_object_number($contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return 0;
		}
		$pattern = '/^wpcf7-f(\d+)-p(\d+)-o(\d+)$/';
		$unit_tag = $contact_form->unit_tag();
		if(preg_match_all($pattern, $unit_tag, $matches)){
			$o = (int) $matches[3][0];
		} else {
			$pattern = '/^wpcf7-f(\d+)-o(\d+)$/';
			if(preg_match_all($pattern, $unit_tag, $matches)){
				$o = (int) $matches[2][0];
			} else {
				$o = 0;
			}
		}
		return $o;
	}
}

if(!function_exists('__cf7_posted_array')){
	/**
	 * @return array
	 */
	function __cf7_posted_array($key = ''){
		if(empty($key)){
			return [];
		}
		$data = (array) __cf7_posted_data($key);
		$data = wpcf7_array_flatten($data);
		return $data;
	}
}

if(!function_exists('__cf7_posted_data')){
	/**
	 * Alias for WPCF7_Submission::get_posted_data.
	 *
	 * Differs from WPCF7_Submission::get_posted_data in that it will avoid filters.
	 *
	 * @return array|null|string
	 */
	function __cf7_posted_data($key = ''){
	    $data = (array) __get_cache('cf7_posted_data', []);
	    if(empty($data) and !empty($_POST)){
            $data = (array) $_POST;
			$data = wp_unslash($data);
	        $data = __cf7_sanitize_posted_data($data);
	        __set_cache('cf7_posted_data', $data);
	    }
		if(empty($key)){
			return $data;
		}
		if(isset($data[$key])){
			return $data[$key];
		}
		return null;
	}
}

if(!function_exists('__cf7_posted_string')){
	/**
	 * Alias for WPCF7_Submission::get_posted_string.
	 *
	 * Differs from WPCF7_Submission::get_posted_string in that it will avoid filters and returns values in a comma separated string.
	 *
	 * @return string
	 */
	function __cf7_posted_string($key = ''){
		$data = __cf7_posted_array($key);
		$data = implode(', ', $data);
		return $data;
	}
}

if(!function_exists('__cf7_sanitize_posted_data')){
	/**
	 * Alias for WPCF7_Submission::sanitize_posted_data.
	 *
	 * @return array|string
	 */
	function __cf7_sanitize_posted_data($value = []){
		if(is_array($value)){
			$value = array_map('__cf7_sanitize_posted_data', $value);
		} elseif(is_string($value)){
			$value = wp_check_invalid_utf8($value);
			$value = wp_kses_no_null($value);
		}
		return $value;
	}
}

if(!function_exists('__cf7_shortcode_attr')){
	/**
	 * Alias for WPCF7_ContactForm::shortcode_attr.
	 *
	 * Differs from WPCF7_ContactForm::shortcode_attr in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_shortcode_attr($name = '', $contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return '';
		}
		return (string) $contact_form->shortcode_attr($name);
	}
}

if(!function_exists('__cf7_shortcode_attr_is_false')){
	/**
	 * @return void
	 */
	function __cf7_shortcode_attr_is_false($name = '', $contact_form = null){
        $value = __cf7_shortcode_attr($name, $contact_form);
	    return __is_false($value);
	}
}

if(!function_exists('__cf7_shortcode_attr_is_true')){
	/**
	 * @return void
	 */
	function __cf7_shortcode_attr_is_true($name = '', $contact_form = null){
        $value = __cf7_shortcode_attr($name, $contact_form);
	    return __is_true($value);
	}
}

if(!function_exists('__cf7_shortcode_tag_output')){
	/**
	 * @return void
	 */
	function __cf7_shortcode_tag_output($callback = '', $priority = 10, $accepted_args = 1){
        $hook_name = 'cf7_shortcode_tag_output';
        $hook = [
            'accepted_args' => $accepted_args,
            'callback' => $callback, // Closure?
            'hook_name' => $hook_name,
            'priority' => $priority,
        ];
        $md5 = __md5($hook);
        if(__isset_array_cache('cf7_hooks', $md5)){
            return; // Prevent hook being added twice.
        }
        __set_array_cache('cf7_hooks', $md5, $hook);
        __add_filter_once($hook_name, $callback, $priority, $accepted_args);
        __add_filter_once('do_shortcode_tag', '__cf7_maybe_filter_shortcode_tag_output', 10, 4);
	}
}

if(!function_exists('__cf7_skip_mail')){
	/**
	 * @return bool
	 */
	function __cf7_skip_mail($contact_form = null){
		$contact_form = __cf7_contact_form($contact_form);
		if(is_null($contact_form)){
			return false;
		}
		$skip_mail = ($contact_form->in_demo_mode() or $contact_form->is_true('skip_mail') or !empty($contact_form->skip_mail));
		$skip_mail = apply_filters('wpcf7_skip_mail', $skip_mail, $contact_form);
		return (bool) $skip_mail;
	}
}

if(!function_exists('__cf7_submission')){
	/**
	 * Alias for WPCF7_Submission::get_instance.
	 *
	 * Returns the current submission if the specified setting has a falsey value.
	 *
	 * @return null|WPCF7_Submission
	 */
	function __cf7_submission($submission = null){
		$current_submission = \WPCF7_Submission::get_instance();
		if(empty($submission)){ // 0, false, null and other PHP falsey values.
			return $current_submission;
		}
		if(__cf7_is_submission($submission)){
			return $submission;
		}
		return null;
	}
}

if(!function_exists('__is_cf7')){
	/**
	 * @return bool
	 */
	function __is_cf7($contact_form = null){
		return ($contact_form instanceof \WPCF7_ContactForm);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_maybe_filter_shortcode_tag_output')){
	/**
	 * This function MUST be called inside the 'do_shortcode_tag' filter hook.
	 *
	 * @return string
	 */
	function __cf7_maybe_filter_shortcode_tag_output($output, $tag, $attr, $m){
		if(!doing_filter('do_shortcode_tag')){ // Too early or too late.
	        return $output;
	    }
		if('contact-form-7' !== $tag){
			return $output;
		}
		$contact_form = __cf7_contact_form();
		if(is_null($contact_form)){
			return __cf7_error();
		}
		if(__is_plugin_active('bb-plugin/fl-builder.php')){
			if(\FLBuilderModel::is_builder_active()){
				$post_type = get_post_type();
				$post_type_object = get_post_type_object($post_type);
				$post_type_name = $post_type_object->labels->singular_name;
				$branding = \FLBuilderModel::get_branding();
				$message = sprintf(_x('%1$s is currently active for this %2$s.', '%1$s branded builder name. %2$s post type name.', 'fl-builder'), $branding, strtolower($post_type_name));
				return __cf7_error($message);
			}
		}
		$html = __str_get_html($output); // Test for simple_html_dom.
		if(is_wp_error($html)){
			$message = $error->get_error_message();
			return __cf7_error($message);
		}
		$errors = new \WP_Error;
	    do_action_ref_array('cf7_shortcode_tag_errors', [&$errors, $contact_form, $attr]);
	    if($errors->has_errors()){
			$messages = $errors->get_error_messages();
			foreach($messages as $index => $message){
				$messages[$index] = rtrim($message, '.') . '.';
			}
			$message = implode(' ', $messages);
			return __cf7_error($message);
	    }
		$comments = $html->find('comment');
		foreach($comments as $comment){
			$comment->remove();
		}
	    $tags = $contact_form->scan_form_tags('feature=name-attr');
	    foreach($tags as $tag){
			$wrapper = $html->find('.wpcf7-form-control-wrap[data-name="' . $tag->name . '"]', 0);
			if(is_null($wrapper)){
				continue;
			}
	        $outertext = $wrapper->outertext;
            $outertext = apply_filters('cf7_form_tag_output', $outertext, $tag, $contact_form); // *
	        $outertext = apply_filters('cf7_form_tag_' . $tag->basetype . '_output', $outertext, $tag, $contact_form);
			$wrapper->outertext = $outertext;
		}
	    $tags = $contact_form->scan_form_tags('type=submit');
		foreach($tags as $idx => $tag){
			$submit = $html->find('.wpcf7-form-control[type="submit"]', $idx);
			if(is_null($submit)){
				continue;
			}
	        $outertext = $submit->outertext;
			$outertext = apply_filters('cf7_form_tag_submit_output', $outertext, $tag, $contact_form);
			$submit->outertext = $outertext;
		}
	    $output = $html->save();
		$output = apply_filters('cf7_shortcode_tag_output', $output, $contact_form, $attr);
		return $output;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Cookies
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__clear_cookie')){
    /**
     * @return void
     */
    function __clear_cookie($name = ''){
        __unset_cookie($name);
    }
}

if(!function_exists('__set_cookie')){
    /**
     * @return void
     */
    function __set_cookie($name = '', $value = '', $expires = 0){
        if(!$name or !$value or !$expires){
            return;
        }
        $secure = ('https' === parse_url(home_url(), PHP_URL_SCHEME));
        //$value = wp_unslash($value);
        //$value = esc_attr($value);
        setcookie($name, $value, $expires, COOKIEPATH, COOKIE_DOMAIN, $secure);
    }
}

if(!function_exists('__unset_cookie')){
    /**
     * @return void
     */
    function __unset_cookie($name = ''){
        if(!$name){
            return;
        }
        if(!isset($_COOKIE[$name])){
            return;
        }
        $past = time() - YEAR_IN_SECONDS;
        setcookie($name, ' ', $past, COOKIEPATH, COOKIE_DOMAIN);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Date/Time
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__current_time')){
	/**
	 * Alias for current_time.
	 *
	 * Differs from current_time in that it will always return a string.
	 *
	 * If 'offset_or_tz' parameter is an empty string, the output is adjusted with the GMT offset in the WordPress option.
	 *
	 * @return string
	 */
	function __current_time($type = 'U', $offset_or_tz = ''){
		if('timestamp' === $type){
			$type = 'U';
		}
		if('mysql' === $type){
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
		if(false === $datetime){
			return gmdate($format, 0);
		}
		return $datetime->setTimezone(__timezone($totz))->format($format);
	}
}

if(!function_exists('__is_mysql_date')){
	/**
	 * @return bool|string
	 */
	function __is_mysql_date($subject = ''){
		$pattern = '/^\d{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01]) ([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/';
		if(!preg_match($pattern, $subject)){
			return false;
		}
		return $subject;
	}
}

if(!function_exists('__offset_or_tz')){
	/**
	 * @param string $offset_or_tz Optional. Default GMT offset or timezone string. Must be either a valid offset (-12 to 14) or a valid timezone string.
	 *
	 * @return array
	 */
	function __offset_or_tz($offset_or_tz = ''){
		if(is_numeric($offset_or_tz)){
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
		$minutes = ($offset - $hours);
		$sign = (($offset < 0) ? '-' : '+');
		$abs_hour = abs($hours);
		$abs_mins = abs($minutes * 60);
		$tz_offset = sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);
		return $tz_offset;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Debugging
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
            $error_msg = translate('%1$s must be less than or equal to %2$d');
            $error_msg = sprintf($error_msg, '"index"', $debug_count) . '.';
            return __error($error_msg);
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
        if('class' === $context['type'] and !$context['file']){
            $error_msg = 'The "%s" class is defined in the PHP core or in a PHP extension.';
            $error_msg = sprintf($error_msg, $context['name']);
            return $error_msg;
        }
        if('function' === $context['type'] and !$context['file']){
            $error_msg = 'The "%s" function is defined in the PHP core or in a PHP extension.';
            $error_msg = sprintf($error_msg, $context['name']);
            return $error_msg;
        }
        if(!$context['file']){
            $error_msg = translate('File does not exist! Please double check the name and try again.');
            $error_msg = __first_p($error_msg);
            return __error($error_msg);
        }
        return $context['file'];
    }
}

if(!function_exists('__class_context')){
    /**
     * @return array|WP_Error
     */
    function __class_context($class = ''){
        if(!$class){
            $error_msg = translate('The "%s" argument must be a non-empty string.');
            $error_msg = sprintf($error_msg, 'class');
            return __error($error_msg);
        }
        if(!class_exists($class)){
            $error_msg = translate('Invalid parameter(s): %s');
            $error_msg = sprintf($error_msg, 'class') . '.';
            return __error($error_msg);
        }
        $reflector = new \ReflectionClass($class);
        return __reflector_context($reflector);
    }
}

if(!function_exists('__function_context')){
    /**
     * @return array|WP_Error
     */
    function __function_context($function = ''){
        if(empty($function)){
            $error_msg = translate('The "%s" argument must be a non-empty string.');
            $error_msg = sprintf($error_msg, 'function');
            return __error($error_msg);
        }
        if(!function_exists($function)){
            $error_msg = translate('Invalid parameter(s): %s');
            $error_msg = sprintf($error_msg, 'function') . '.';
            return __error($error_msg);
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
            $error_msg = translate('Invalid object type.');
            return __error($error_msg);
        }
        $context = [
            'file' => $reflector->getFileName(),
            'name' => $reflector->getName(),
            'namespace_name' => $reflector->getNamespaceName(),
            'reflector' => $reflector,
            'short_name' => $reflector->getShortName(),
        ];
        return $context;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Dependencies
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__enqueue_asset')){
    /**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return string|WP_Error
     */
    function __enqueue_asset($handle = '', $src = '', $deps = [], $ver = false, $args_media = true){
        global $wp_version;
        if(!doing_action('admin_enqueue_scripts') and !doing_action('login_enqueue_scripts') and !doing_action('wp_enqueue_scripts')){ // Too early or too late.
            $error_msg = translate('Function %1$s was called <strong>incorrectly</strong>. %2$s %3$s');
            $error_msg = sprintf($error_msg, __FUNCTION__, '', '');
            $error_msg = trim($error_msg);
            return __error($error_msg);
        }
        if(!$handle){
            $error_msg = translate('The "%s" argument must be a non-empty string.');
            $error_msg = sprintf($error_msg, 'handle');
            return __error($error_msg);
        }
        $filename = __basename($src);
        $mimes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
        ];
        $filetype = wp_check_filetype($filename, $mimes);
        if(!$filetype['ext']){
            $error_msg = translate('Sorry, you are not allowed to upload this file type.');
            return __error($error_msg);
        }
        if(!is_array($deps)){
            $deps = (array) $deps;
        }
        if('text/css' === $filetype['type']){
            if(is_string($args_media)){
                $media = $args_media; // $media parameter.
            } else {
                $media = 'all'; // All.
            }
            wp_enqueue_style($handle, $src, $deps, $ver, $media);
            return $handle;
        }
        $path = __url_to_dir($src);
        if($path){ // This WordPress installation.
            if($path !== __dependent_file()){
                $deps[] = __slug();
            }
        }
        $l10n = [];
        if(__is_associative_array($args_media)){
            if(version_compare($wp_version, '6.3', '>=') and (isset($args_media['in_footer']) or isset($args_media['strategy']))){
                $args = $args_media; // As of WordPress 6.3, the new $args parameter – that replaces/overloads the prior $in_footer parameter – can be used to specify a script loading strategy.
            } else {
                $l10n = $args_media;
                $args = true; // In footer.
            }
        } else {
            $args = (bool) $args_media; // $in_footer parameter.
        }
        wp_enqueue_script($handle, $src, $deps, $ver, $args);
        if($l10n){
            $object_name = __canonicalize($handle);
            wp_localize_script($handle, $object_name . '_l10n', $l10n);
        }
        return $handle;
    }
}

if(!function_exists('__initialize')){
    /**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return bool|WP_Error
     */
	function __initialize($handle = ''){
        if(!doing_action('admin_enqueue_scripts') and !doing_action('login_enqueue_scripts') and !doing_action('wp_enqueue_scripts')){ // Too early or too late.
            $error_msg = translate('Function %1$s was called <strong>incorrectly</strong>. %2$s %3$s');
            $error_msg = sprintf($error_msg, __FUNCTION__, '', '');
            $error_msg = trim($error_msg);
            return __error($error_msg);
        }
        $object_name = __canonicalize($handle);
        $data = "__get_instance('$object_name');"; // Hardcoded.
        return wp_add_inline_script($handle, $data);
	}
}

if(!function_exists('__local_enqueue_asset')){
    /**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return string|WP_Error
     */
    function __local_enqueue_asset($handle = '', $file = '', $deps = [], $args_media = true){
        if(!file_exists($file)){
            $error_msg = translate('File does not exist! Please double check the name and try again.');
            return __error($error_msg);
        }
        $src = __dir_to_url($file);
        $ver = filemtime($file);
        return __enqueue_asset($handle, $src, $deps, $ver, $args_media);
    }
}

if(!function_exists('__localize')){
    /**
     * @return string
     */
    function __localize($data = []){
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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__dependent_file')){
	/**
	 * @return string
	 */
	function __dependent_file(){
        $handle = __slug();
		$file = plugin_dir_path(__FILE__) . $handle . '.js'; // Hardcoded.
		return $file;
	}
}

if(!function_exists('__enqueue_dependencies')){
	/**
	 * @return void
	 */
	function __enqueue_dependencies(){
		$handle = __slug();
		$file = __dependent_file();
		if(!file_exists($file)){
            return;
        }
		__enqueue_asset('stackframe', 'https://cdn.jsdelivr.net/npm/stackframe@1.3.4/stackframe.min.js', [], '1.3.4');
        __enqueue_asset('error-stack-parser', 'https://cdn.jsdelivr.net/npm/error-stack-parser@2.1.4/error-stack-parser.min.js', ['stackframe'], '2.1.4');
        $deps = ['error-stack-parser', 'jquery', 'underscore', 'utils', 'wp-api', 'wp-hooks'];
        $l10n = [
            'mu_plugins_url' => __dir_to_url(wp_normalize_path(WPMU_PLUGIN_DIR)),
            'plugins_url' => __dir_to_url(wp_normalize_path(WP_PLUGIN_DIR)),
            'site_url' => site_url(),
        ];
        __local_enqueue_asset($handle, $file, $deps, $l10n);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Error Handling
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__error')){
	/**
	 * Alias for new WP_Error::__construct.
	 *
	 * @return WP_Error
	 */
	function __error($message = '', $data = ''){
		if(is_wp_error($message)){
			$data = $message->get_error_data();
			$message = $message->get_error_message();
		}
		if(empty($message)){
			$message = translate('An error occurred.'); // Something went wrong.
		}
		$code = __str_prefix('error');
		return new \WP_Error($code, $message, $data);
	}
}

if(!function_exists('__exit_with_error')){
	/**
	 * @return void
	 */
	function __exit_with_error($message = '', $title = '', $args = []){
		if(is_wp_error($message)){
			$message = $message->get_error_message();
			if($title and !$args){
				$args = $title;
				$title = '';
			}
		}
		if(!$message){
			$message = translate('Error');
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
			$title = translate('An error occurred.'); // Something went wrong.
		}
        $html = '<h1>' . $title . '</h1>';
        $html .= '<p>';
        $html .= $message;
        $html .= '</p>';
        $html .= '<p>';
        $referer = wp_get_referer();
        if($referer){
            $back = translate('Go back');
        } else {
            $back = __go_to(get_bloginfo('title', 'display'));
            $referer = home_url('/');
        }
        $html_link = sprintf('<a href="%s">%s</a>', esc_url($referer), $back);
        $html .= $html_link;
        $html .= '</p>';
        wp_die($html, $title, $args);
	}
}

if(!function_exists('__in_debug')){
	/**
	 * @return bool
	 */
	function __in_debug(){
        return (defined('WP_DEBUG') and WP_DEBUG);
    }
}

if(!function_exists('__seems_error')){
	/**
	 * @return bool|WP_Error
	 */
	function __seems_error($data = []){
		if(!__array_keys_exists(['code', 'data', 'message'], $data)){
			return false;
		}
		$count = count($data);
		if((4 === $count and !array_key_exists('additional_errors', $data)) or 3 !== $count){
			return false;
		}
		if(!$data['code']){
			$data['code'] = __str_prefix('error');
		}
		if(!$data['message']){
			$data['message'] = translate('Error');
		}
	    $error = new \WP_Error($data['code'], $data['message'], $data['data']);
	    if(!empty($data['additional_errors'])){
	        foreach($data['additional_errors'] as $err){
	            if(!__array_keys_exists(['code', 'data', 'message'], $err)){
	        		continue;
	        	}
	            $error->add($err['code'], $err['message'], $err['data']);
	        }
	    }
		return $error;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Filesystem
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
		if(!empty($dir) and (!@is_dir($dir) or !wp_is_writable($dir))){
			$error_msg =  translate('Destination directory for file streaming does not exist or is not writable.');
			return __error($error_msg);
		}
		return $dir;
	}
}

if(!function_exists('__check_upload_dir')){
	/**
	 * @return string|WP_Error
	 */
	function __check_upload_dir($path = ''){
		$path = wp_normalize_path($path);
		$upload_dir = wp_get_upload_dir();
		if($upload_dir['error']){
			return __error($upload_dir['error']);
		}
		$basedir = wp_normalize_path($upload_dir['basedir']);
		if(!__str_starts_with($path, $basedir)){
			$error_msg = sprintf(translate('Unable to locate needed folder (%s).'), translate('The uploads directory'));
			return __error($error_msg);
		}
		return $path;
	}
}

if(!function_exists('__check_upload_size')){
	/**
	 * Alias for WP_REST_Attachments_Controller::check_upload_size.
	 *
	 * @return bool|WP_Error
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
			$error_msg = sprintf(translate('Not enough space to upload. %s KB needed.'), number_format(($file_size - $space_left) / KB_IN_BYTES));
			return __error($error_msg);
		}
		if($file_size > (KB_IN_BYTES * get_site_option('fileupload_maxk', 1500))){
			$error_msg = sprintf(translate('This file is too big. Files must be less than %s KB in size.'), get_site_option('fileupload_maxk', 1500));
			return __error($error_msg);
		}
		if(!function_exists('upload_is_user_over_quota')){
			require_once(ABSPATH . 'wp-admin/includes/ms.php'); // Include multisite admin functions to get access to upload_is_user_over_quota().
		}
		if(upload_is_user_over_quota(false)){
			$error_msg = translate('You have used your space quota. Please delete files before uploading.');
			return __error($error_msg);
		}
		return true;
	}
}

if(!function_exists('__dir')){
	/**
	 * @return string|WP_Error
	 */
	function __dir($subdir = ''){
        $target = __slug();
        $subdir = ltrim($subdir, '/');
	    $subdir = untrailingslashit($subdir);
	    if($subdir){
	        $target .= '/' . $subdir;
	    }
		return __mkdir($target);
	}
}

if(!function_exists('__dir_to_url')){
	/**
	 * @return string
	 */
	function __dir_to_url($path = ''){
		return str_replace(wp_normalize_path(ABSPATH), site_url('/'), wp_normalize_path($path));
	}
}

if(!function_exists('__download_dir')){
	/**
	 * @return string|WP_Error
	 */
	function __download_dir($subdir = ''){
        $dir = 'downloads';
        $subdir = ltrim($subdir, '/');
	    $subdir = untrailingslashit($subdir);
	    if($subdir){
	        $dir .= '/' . $subdir;
	    }
		return __dir($dir);
	}
}

if(!function_exists('__fs_direct')){
	/**
	 * @return WP_Filesystem_Base|WP_Error
	 */
	function __fs_direct(){
		global $wp_filesystem;
		if(!function_exists('get_filesystem_method')){
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		if('direct' !== get_filesystem_method()){
			return __error(translate('Could not access filesystem.')); // TODO: determine the best way to support other filesystem methods.
		}
		if($wp_filesystem instanceof \WP_Filesystem_Base){
			return $wp_filesystem;
		}
		if(!WP_Filesystem()){
			return __error(translate('Filesystem error.'));
		}
		return $wp_filesystem;
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

if(!function_exists('__handle_file')){
	/**
	 * This function works only if the file was uploaded via HTTP POST.
	 *
	 * @return array|WP_Error
	 */
	function __handle_file($file = [], $dir = '', $mimes = null){
		if(empty($file)){
			$error_msg = translate('No data supplied.');
			return __error($error_msg);
		}
		if(!is_array($file)){
			if(!is_scalar($file)){
				$error_msg = translate('Invalid data provided.');
				return __error($error_msg);
			}
			if(empty($_FILES[$file])){
				$error_msg = translate('File does not exist! Please double check the name and try again.');
				return __error($error_msg);
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
		if(empty($files)){
			if(empty($_FILES)){
				$error_msg = translate('No data supplied.');
				return __error($error_msg);
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
		if(empty($file)){
			$error_msg = translate('No data supplied.');
			return __error($error_msg);
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
			$upload_dir = __check_upload_dir($dir);
			if(is_wp_error($upload_dir)){
				return $upload_dir;
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
		if(false === $move_new_file){
			$error_path = str_replace(ABSPATH, '', $dir);
			$error_msg = sprintf(translate('The uploaded file could not be moved to %s.'), $error_path);
			return __error($error_msg);
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

if(!function_exists('__is_extension_allowed')){
	/**
	 * @return bool
	 */
	function __is_extension_allowed($extension = ''){
		foreach(wp_get_mime_types() as $exts => $mime){
			if(preg_match('!^(' . $exts . ')$!i', $extension)){
				return true;
			}
		}
		return false;
	}
}

if(!function_exists('__mkdir')){
	/**
	 * @return string|WP_Error
	 */
	function __mkdir($subdir = ''){
		$upload_dir = wp_get_upload_dir();
		if($upload_dir['error']){
			return __error($upload_dir['error']);
		}
		$path = $upload_dir['basedir'];
	    $subdir = ltrim($subdir, '/');
	    $subdir = untrailingslashit($subdir);
	    if($subdir){
	        $path .= '/' . $subdir;
	    }
		return __mkdir_p($path);
	}
}

if(!function_exists('__mkdir_p')){
	/**
	 * Alias for wp_mkdir_p.
	 *
	 * Differs from wp_mkdir_p in that it will return an error if path wasn't created.
	 *
	 * @return string|WP_Error
	 */
	function __mkdir_p($target = ''){
		$key = md5($target);
		if(__isset_array_cache('mkdir_p', $key)){
			return (string) __get_array_cache('mkdir_p', $key, '');
		}
		if(!wp_mkdir_p($target)){
			return __error(translate('Could not create directory.'));
		}
		if(!wp_is_writable($target)){
			return __error(translate('Destination directory for file streaming does not exist or is not writable.'));
		}
		__set_array_cache('mkdir_p', $key, $target);
		return $target;
	}
}

if(!function_exists('__read_file_chunk')){
	/**
	 * @return string
	 */
	function __read_file_chunk($handle = null, $chunk_size = 0, $chunk_lenght = 0){
		$giant_chunk = '';
		if(is_resource($handle) and $chunk_size){
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

if(!function_exists('__sideload')){
	/**
	 * @return int|WP_Error
	 */
	function __sideload($file = '', $post_id = 0, $generate_attachment_metadata = true){
		if(!@is_file($file)){
			$error_msg =  translate('File doesn&#8217;t exist?');
			return __error($error_msg, $file);
		}
	    $filename = wp_basename($file);
	    $filename = __test_type($file, $filename);
		if(is_wp_error($filename)){
			return $filename;
		}
	    $filetype_and_ext = wp_check_filetype($filename);
	    $attachment_id = wp_insert_attachment([
	        'guid' => __dir_to_url($file),
	        'post_mime_type' => $filetype_and_ext['type'],
	        'post_status' => 'inherit',
	        'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
	    ], $file, $post_id, true);
	    if($generate_attachment_metadata){
	        __maybe_generate_attachment_metadata($attachment_id);
	    }
	    return $attachment_id;
	}
}

if(!function_exists('__test_error')){
	/**
	 * @return bool|WP_Error
	 */
	function __test_error($error = 0){ // A successful upload will pass this test.
		$upload_error_strings = [
			false,
			sprintf(translate('The uploaded file exceeds the %1$s directive in %2$s.'), 'upload_max_filesize', 'php.ini'),
			sprintf(translate('The uploaded file exceeds the %s directive that was specified in the HTML form.'), 'MAX_FILE_SIZE'),
			translate('The uploaded file was only partially uploaded.'),
			translate('No file was uploaded.'),
			'',
			translate('Missing a temporary folder.'),
			translate('Failed to write file to disk.'),
			translate('File upload stopped by extension.'),
		]; // Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
		if($error > 0){
			if(empty($upload_error_strings[$error])){
				$error_msg = translate('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.');
				$error_msg = __first_p($error_msg);
			} else {
				$error_msg = $upload_error_strings[$error];
			}
			return __error($error_msg);
		}
		return true;
	}
}

if(!function_exists('__test_size')){
	/**
	 * @return bool|WP_Error
	 */
	function __test_size($file_size = 0){ // A non-empty file will pass this test.
		if(0 === $file_size){
			if(is_multisite()){
				$error_msg = translate('File is empty. Please upload something more substantial.');
			} else {
				$error_msg = sprintf(translate('File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your %1$s file or by %2$s being defined as smaller than %3$s in %1$s.'), 'php.ini', 'post_max_size', 'upload_max_filesize');
			}
			return __error($error_msg);
		}
		return true;
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
		if((!$type or !$ext) and !current_user_can('unfiltered_upload')){
			$error_msg = translate('Sorry, you are not allowed to upload this file type.');
			return __error($error_msg);
		}
		return $name;
	}
}

if(!function_exists('__test_uploaded_file')){
	/**
	 * @return bool|WP_Error
	 */
	function __test_uploaded_file($tmp_name = ''){ // A properly uploaded file will pass this test.
		if(!is_uploaded_file($tmp_name)){
			$error_msg = translate('Specified file failed upload test.');
			return __error($error_msg);
		}
		return true;
	}
}

if(!function_exists('__upload_dir')){
	/**
	 * @return string|WP_Error
	 */
	function __upload_dir($subdir = ''){
        $dir = 'uploads';
        $subdir = ltrim($subdir, '/');
	    $subdir = untrailingslashit($subdir);
	    if($subdir){
	        $dir .= '/' . $subdir;
	    }
		return __dir($dir);
	}
}

if(!function_exists('__url_to_dir')){
	/**
	 * @return string
	 */
	function __url_to_dir($url = ''){
	    $site_url = site_url('/');
	    if(!__str_starts_with($url, $site_url)){
	        return '';
	    }
		return str_replace($site_url, wp_normalize_path(ABSPATH), $url);
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
        $key = 'github_api_token';
		__set_cache('github_api_token', $key);
		__add_filter_once('http_request_args', '__maybe_add_github_api_token', 10, 2);
    }
}

if(!function_exists('__github_api_token_exists')){
    /**
     * @return void
     */
    function __github_api_token_exists(){
        $key = 'github_api_token';
		return __isset_cache($key);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_add_github_api_token')){
	/**
	 * This function MUST be called inside the 'http_request_args' filter hook.
	 *
	 * @return array
	 */
	function __maybe_add_github_api_token($parsed_args, $url){
		if(!doing_filter('http_request_args')){
	        return $parsed_args;
	    }
		$token = (string) __get_cache('github_api_token', '');
		if(!$token){
            return $parsed_args;
        }
        if(0 !== strpos($url, 'https://api.github.com/')){
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
		if(doing_action('wp_head')){ // Just in time.
	        __echo_hide_recaptcha_badge();
			return;
	    }
		if(did_action('wp_head')){ // Too late.
			return;
		}
		__add_action_once('wp_head', '__maybe_hide_recaptcha_badge');
	}
}

if(!function_exists('__is_google_workspace')){
	/**
	 * @return bool|string
	 */
	function __is_google_workspace($email = ''){
		if(!is_email($email)){
			return false;
		}
		list($local, $domain) = explode('@', $email, 2);
		if('gmail.com' === strtolower($domain)){
			return 'gmail.com';
		}
		if(!getmxrr($domain, $mxhosts)){
			return false;
		}
		if(!in_array('aspmx.l.google.com', $mxhosts)){
			return false;
		}
		return $domain;
	}
}

if(!function_exists('__recaptcha_branding')){
	/**
	 * @return string
	 */
	function __recaptcha_branding(){
		return 'This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a> apply.';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__echo_hide_recaptcha_badge')){
	/**
	 * This function MUST be called inside the 'wp_head' action hook.
	 *
	 * @return void
	 */
	function __echo_hide_recaptcha_badge(){
		if(!doing_action('wp_head')){
	        return;
	    }
        $html = '<style id="' . __str_slug('hide-recaptcha-badge') . '">.grecaptcha-badge { visibility: hidden !important; }</style>';
        echo $html . "\n";
	}
}

if(!function_exists('__maybe_hide_recaptcha_badge')){
	/**
	 * This function MUST be called inside the 'wp_head' action hook.
	 *
	 * @return void
	 */
	function __maybe_hide_recaptcha_badge(){
		if(!doing_action('wp_head')){ // Too early or too late.
	        return;
	    }
		__echo_hide_recaptcha_badge();
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Hooks
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_action')){
    /**
     * Alias for add_action.
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
     * Alias for add_filter.
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

if(!function_exists('__callback_idx')){
    /**
     * @return string
     */
    function __callback_idx($callback = null){
        return _wp_filter_build_unique_id('', $callback, 0);
    }
}

if(!function_exists('__callback_md5')){
    /**
     * @return string
     */
    function __callback_md5($callback = null){
        $idx = __callback_idx($callback);
        $md5 = md5($idx);
        if(!$callback instanceof \Closure){
            return $md5;
        }
        $md5_closure = __md5_closure($callback);
        if(is_wp_error($md5_closure)){
            return $md5;
        }
        return $md5_closure;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__on')){
    /**
     * @return string
     */
    function __on($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        add_filter($hook_name, $callback, $priority, $accepted_args);
        return __callback_idx($callback);
    }
}

if(!function_exists('__one')){
    /**
     * @return string
     */
    function __one($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $md5 = __callback_md5($callback);
        $key = $hook_name . '_' . $md5;
        if(__isset_array_cache('hooks', $key)){
            return __get_array_cache('hooks', $key);
        }
        $idx = __on($hook_name, $callback, $priority, $accepted_args);
        __set_array_cache('hooks', $key, $idx);
        return $idx;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Login forms
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__custom_interim_login_page')){
    /**
     * @return void
     */
    function __custom_interim_login_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'login', $post->ID);
    }
}

if(!function_exists('__custom_login_page')){
    /**
     * @return void
     */
    function __custom_login_page($post_id = 0, $interim_login = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'login', $post->ID);
        __add_action_once('login_form_login', '__maybe_redirect_login_form_login');
        if(!$interim_login){
            return;
        }
        __custom_interim_login_page($interim_login);
    }
}

if(!function_exists('__custom_lostpassword_page')){
    /**
     * @return void
     */
    function __custom_lostpassword_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'lostpassword', $post->ID);
        __add_action_once('login_form_lostpassword', '__maybe_redirect_login_form_lostpassword');
        __add_action_once('login_form_retrievepassword', '__maybe_redirect_login_form_lostpassword');
    }
}

if(!function_exists('__custom_retrievepassword_page')){
    /**
	 * Alias for __custom_lostpassword_page.
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
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'register', $post->ID);
        __add_action_once('login_form_register', '__maybe_redirect_login_form_register');
    }
}

if(!function_exists('__custom_resetpass_page')){
    /**
     * @return void
     */
    function __custom_resetpass_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'resetpass', $post->ID);
        __add_action_once('login_form_resetpass', '__maybe_redirect_login_form_resetpass');
        __add_action_once('login_form_rp', '__maybe_redirect_login_form_resetpass');
    }
}

if(!function_exists('__custom_rp_page')){
    /**
	 * Alias for __custom_resetpass_page.
	 *
     * @return void
     */
    function __custom_rp_page($post_id = 0){
        __custom_resetpass_page($post_id);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_redirect_login_form_login')){
    /**
	 * This function MUST be called inside the 'login_form_login' action hook.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_login(){
        if(!doing_action('login_form_login')){ // Too early or too late.
	        return;
	    }
        $action = (isset($_REQUEST['interim-login']) ? 'interim_login' : 'login');
        if(!__isset_array_cache('login_forms', $action)){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', $action, 0);
        $url = get_permalink($post_id);
        if($_GET){
            $_GET = urlencode_deep($_GET); // This re-URL-encodes things that were already in the query string.
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_lostpassword')){
    /**
	 * This function MUST be called inside the 'login_form_lostpassword' or 'login_form_retrievepassword' action hooks.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_lostpassword(){
        if(!doing_action('login_form_lostpassword') and !doing_action('login_form_retrievepassword')){ // Too early or too late.
	        return;
	    }
        if(!__isset_array_cache('login_forms', 'lostpassword')){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', 'lostpassword', 0);
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
	 * This function MUST be called inside the 'login_form_register' action hook.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_register(){
        if(!doing_action('login_form_register')){ // Too early or too late.
	        return;
	    }
        if(!__isset_array_cache('login_forms', 'register')){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', 'register', 0);
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
	 * This function MUST be called inside the 'login_form_resetpass' or 'login_form_rp' action hooks.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_resetpass(){
        if(!doing_action('login_form_resetpass') and !doing_action('login_form_rp')){ // Too early or too late.
	        return;
	    }
        if(!__isset_array_cache('login_forms', 'resetpass')){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', 'resetpass', 0);
        $url = get_permalink($post_id);
        if($_GET){
            $_GET = urlencode_deep($_GET); // This re-URL-encodes things that were already in the query string.
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// MD5
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__md5')){
	/**
	 * @return string|WP_Error
	 */
	function __md5($data = ''){
		if(is_object($data)){
			if($data instanceof \Closure){
				$md5_closure = __md5_closure($data); // String or WP_Error.
				return $md5_closure;
			}
			$data = __object_to_array($data); // Array or WP_Error.
			if(is_wp_error($data)){
				return $data;
			}
		}
		if(is_array($data)){
			$data = __ksort_deep($data);
			$data = serialize($data);
		}
		return md5($data);
	}
}

if(!function_exists('__md5_closure')){
	/**
	 * @return string|WP_Error
	 */
	function __md5_closure($data = null, $spl_object_hash = false){
		if(!$data instanceof \Closure){
			return __error(translate('Invalid object type.'));
		}
		$serialized = __serialize_closure($data);
        if(is_wp_error($serialized)){
			return $serialized;
		}
		if(!$spl_object_hash){
			$spl_object_hash = spl_object_hash($data);
			$serialized = str_replace($spl_object_hash, 'spl_object_hash', $serialized);
		}
		return md5($serialized);
	}
}

if(!function_exists('__md5_to_uuid4')){
	/**
	 * @return string
	 */
	function __md5_to_uuid4($md5 = ''){
		if(32 !== strlen($md5)){
			return '';
		}
		return substr($md5, 0, 8) . '-' . substr($md5, 8, 4) . '-' . substr($md5, 12, 4) . '-' . substr($md5, 16, 4) . '-' . substr($md5, 20, 12);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Media
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_image_size')){
	/**
	 * @return void
	 */
	function __add_image_size($name = '', $width = 0, $height = 0, $crop = false){
		$image_sizes = get_intermediate_image_sizes();
		$size = sanitize_title($name);
		if(in_array($size, $image_sizes)){
			return; // Does NOT overwrite.
		}
		add_image_size($size, $width, $height, $crop);
		__set_array_cache('image_sizes', $size, $name);
		__add_filter_once('image_size_names_choose', '__maybe_add_image_size_names');
	}
}

if(!function_exists('__attachment_url_to_postid')){
	/**
	 * @return int
	 */
	function __attachment_url_to_postid($url = ''){
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

if(!function_exists('__convert_exts_to_mimes')){
	/**
	 * @return array
	 */
	function __convert_exts_to_mimes($exts = []){
	    if(empty($exts)){
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

if(!function_exists('__fa_file_type')){
	/**
	 * @return string
	 */
	function __fa_file_type($post = null){
		if('attachment' !== get_post_type($post)){
			return '';
		}
		if(wp_attachment_is('audio', $post)){
			return 'audio';
		}
		if(wp_attachment_is('image', $post)){
			return 'image';
		}
		if(wp_attachment_is('video', $post)){
			return 'video';
		}
		$type = get_post_mime_type($post);
		switch($type){
			case 'application/zip':
			case 'application/x-rar-compressed':
			case 'application/x-7z-compressed':
			case 'application/x-tar':
				return 'archive';
				break;
			case 'application/vnd.ms-excel':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				return 'excel';
				break;
			case 'application/pdf':
				return 'pdf';
				break;
			case 'application/vnd.ms-powerpoint':
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
				return 'powerpoint';
				break;
			case 'application/msword':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
				return 'word';
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
		if(!is_null($post_id)){
			return intval($post_id);
		}
		if($check_rewrite_rules){
			return url_to_postid($guid);
		}
		return 0;
	}
}

if(!function_exists('__image_extensions')){
	/**
	 * @return array
	 */
	function __image_extensions(){
		$image_extensions = ['jpg', 'jpeg', 'jpe', 'png', 'gif', 'bmp', 'tiff', 'tif', 'webp', 'ico', 'heic'];
	    return $image_extensions;
	}
}

if(!function_exists('__maybe_generate_attachment_metadata')){
	/**
	 * @return void
	 */
	function __maybe_generate_attachment_metadata($attachment_id = 0){
		$attachment = get_post($attachment_id);
		if(is_null($attachment)){
			return;
		}
		if('attachment' !== $attachment->post_type){
			return;
		}
		wp_raise_memory_limit('image');
		if(!function_exists('wp_generate_attachment_metadata')){
			require_once(ABSPATH . 'wp-admin/includes/image.php');
		}
		wp_maybe_generate_attachment_metadata($attachment);
	}
}

if(!function_exists('__mime_content_type')){
	/**
	 * @return string
	 */
	function __mime_content_type($filename = '', $mimes = null){
        $mime = wp_check_filetype($filename, $mimes);
        if(false === $mime['type'] and function_exists('mime_content_type')){
            $mime['type'] = mime_content_type($filename);
        }
        if(false === $mime['type']){
            return '';
        }
        return $mime['type'];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_add_image_size_names')){
	/**
	 * This function MUST be called inside the 'image_size_names_choose' filter hook.
	 *
	 * @return array
	 */
	function __maybe_add_image_size_names($sizes){
		if(!doing_filter('image_size_names_choose')){
	        return $sizes;
	    }
		$image_sizes = (array) __get_cache('image_sizes', []);
		foreach($image_sizes as $size => $name){
			$sizes[$size] = $name;
		}
		return $sizes;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Meta Box
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__mb_image_switch')){
	/**
	 * @return array|WP_Error
	 */
	function __mb_image_switch($id = '', $args = []){
        if(!$id){
            $error_msg = translate('The "%s" argument must be a non-empty string.');
            $error_msg = sprintf($error_msg, 'id');
            return __error($error_msg);
        }
        $defaults = [
            'columns' => 4,
            'icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj4KICA8cGF0aCBkPSJNNTEyLDMwMS4xOGgtNjUuNjZjLTUuMTIsMjEuMDgtMTMuMjUsNDAuNjYtMjQuNCw1OC4xM2w0Ni4zOCw0Ni4zOC02My4yNSw2My4yNS00Ni4zOC00Ni4zOGMtMTcuNDcsMTAuODQtMzcuMDQsMTguOTctNTcuNTIsMjMuNzl2NjUuNjZoLTkwLjM1di02NS42NmMtMjAuNDgtNC44Mi00MC4wNi0xMi45NS01Ny41Mi0yMy43OWwtNDYuMzgsNDYuMzgtNjMuODUtNjMuODUsNDYuMzgtNDYuMzhjLTEwLjg0LTE3LjQ3LTE4Ljk3LTM3LjA0LTIzLjc5LTU3LjUySDB2LTg5LjQ1aDY1LjM2YzQuODItMjEuMDgsMTMuMjUtNDAuNjYsMjQuMDktNTguNDNsLTQ2LjM4LTQ2LjM4LDYzLjI1LTYzLjI1LDQ2LjM4LDQ2LjM4YzE3LjQ3LTExLjE0LDM3LjM1LTE5LjI4LDU4LjEzLTI0LjRWMGg5MC4zNXY2NS42NmMyMC40OCw0LjgyLDQwLjA2LDEyLjk1LDU3LjUyLDIzLjc5bDQ2LjM4LTQ2LjM4LDYzLjg1LDYzLjg1LTQ2LjM4LDQ2LjM4YzEwLjg0LDE3Ljc3LDE5LjI4LDM3LjM1LDI0LjA5LDU4LjQzaDY1LjM2djg5LjQ1Wk0yNTYsMzQ2LjM1YzUwLDAsOTAuMzUtNDAuMzYsOTAuMzUtOTAuMzVzLTQwLjM2LTkwLjM1LTkwLjM1LTkwLjM1LTkwLjM1LDQwLjM2LTkwLjM1LDkwLjM1LDQwLjM2LDkwLjM1LDkwLjM1LDkwLjM1WiIvPgo8L3N2Zz4=',
            'name' => '',
        ];
        $args = wp_parse_args($args, $defaults);
        if(!$args['name']){
            $args['name'] = $id;
        }
        $field = [
            'after' => '</div></div></div>',
            'before' => '<div style="padding: 6px; margin: 6px 0 0;"><div style="background-color: #f8f9fa; border: 1px solid rgba(0, 0, 0, 0.125); border-radius: 0.25rem; padding: 20px 20px 8px;"><div style="text-align: center;"><img src="' . $args['icon'] . '" style="max-height: 40px;"></div><div style="text-align: center;"><p>' . $args['name'] . '</p></div><div style="text-align: center;">',
            'columns' => $args['columns'],
            'id' => $id,
            'on_label' => translate('Yes'),
            'off_label' => translate('No'),
            'style' => 'square',
            'type' => 'switch',
        ];
        return $field;
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
		if(!is_numeric($maybeint)){
			return 0; // Make sure the value is numeric to avoid casting objects, for example, to int 1.
		}
		return absint($maybeint);
	}
}

if(!function_exists('__breadcrumbs')){
	/**
	 * @return string
	 */
	function __breadcrumbs($breadcrumbs = [], $separator = '>'){
	    $elements = [];
	    foreach($breadcrumbs as $breadcrumb){
	        if(!isset($breadcrumb['text'])){
	            continue;
	        }
	        $text = $breadcrumb['text'];
	        if(isset($breadcrumb['link'])){
	            $href = $breadcrumb['link'];
	            $target = isset($breadcrumb['target']) ? $breadcrumb['target'] : '_self';
	            $element = sprintf('<a href="%1$s" target="%2$s">%3$s</a>', esc_url($href), esc_attr($target), esc_html($text));
	        } else {
	            $element = sprintf('<span>%1$s</span>', esc_html($text));
	        }
	        $elements[] = $element;
	    }
	    $separator = ' ' . trim($separator) . ' ';
		return implode($separator, $elements);
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
		return ($current_screen->id === $id);
	}
}

if(!function_exists('__custom_login_logo')){
    /**
     * @return bool|WP_Error
     */
    function __custom_login_logo($attachment_id = 0, $half = true){
        if(!wp_attachment_is_image($attachment_id)){
            return __error(translate('File is not an image.'));
        }
        $custom_logo = wp_get_attachment_image_src($attachment_id, 'medium');
        $height = $custom_logo[2];
        $width = $custom_logo[1];
        if($width > 300){ // Fix for SVG.
            $r = 300 / $width;
            $width = 300;
            $height *= $r;
        }
        if($half){
            $height = $height / 2;
            $width = $width / 2;
        }
        $custom_login_logo = [$custom_logo[0], $width, $height];
        __set_cache('custom_login_logo', $custom_login_logo);
        __add_action_once('login_enqueue_scripts', '__maybe_replace_login_logo');
        return true;
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
			$error_msg = translate('Function %s used incorrectly in PHP.');
			$error_msg = sprintf($error_msg, 'exec');
			return __error($error_msg);
		}
        try {
            $result = exec($command, $output);
        } catch(\Throwable $t){
            $result = __error($t->getMessage());
        } catch(\Exception $e){
            $result = __error($e->getMessage());
        }
        if(is_wp_error($result)){
            return $result;
        }
        return $output;
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
            if(in_array($name, $boolean_attributes) and '' === $value){
                $value = false;
            }
            if(is_numeric($value)){
                $value = (string) $value;
            }
            if(null === $value or false === $value){
                unset($atts_filtered[$name]);
            } elseif(true === $value){
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
			if($arg['default'] and $arg['name'] and $arg['type']){
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

if(!function_exists('__go_to')){
	/**
	 * @return bool
	 */
	function __go_to($str = ''){
		return trim(str_replace('&larr;', '', sprintf(translate_with_gettext_context('&larr; Go to %s', 'site'), $str)));
	}
}

if(!function_exists('__has_shortcode')){
	/**
	 * @return array
	 */
	function __has_shortcode($content = '', $tag = ''){
	    if(false === strpos($content, '[')){
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

if(!function_exists('__hide_wp')){
    /**
     * @return void
     */
    function __hide_wp(){
        __local_login_header();
        __set_cache('hide_wp', true);
    	__add_action_once('admin_init', '__maybe_hide_wp_from_admin');
        __add_action_once('wp_before_admin_bar_render', '__maybe_hide_wp_from_admin_bar');
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

if(!function_exists('__is_doing_heartbeat')){
	/**
	 * @return bool
	 */
	function __is_doing_heartbeat(){
		return (wp_doing_ajax() and isset($_POST['action']) and 'heartbeat' === $_POST['action']);
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

if(!function_exists('__is_front')){
	/**
	 * @return bool
	 */
    function __is_front(){
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
        if(defined('XMLRPC_REQUEST') and XMLRPC_REQUEST){
            return false; // The current request is a WordPress XML-RPC request.
        }
        if(wp_is_xml_request() or isset($wp_query) and (function_exists('is_feed') and is_feed() or function_exists('is_comment_feed') and is_comment_feed() or function_exists('is_trackback') and is_trackback())){
            return false; // The current request is an XML request, or is expecting an XML response.
        }
        return true;
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
        return ($post_type === (isset($_GET['post_type']) ? $_GET['post_type'] : 'post'));
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
        return ($post_type === (isset($_GET['post_type']) ? $_GET['post_type'] : 'post'));
    }
}

if(!function_exists('__is_revision_or_auto_draft')){
	/**
	 * @return bool
	 */
	function __is_revision_or_auto_draft($post = null){
		return (wp_is_post_revision($post) or 'auto-draft' === get_post_status($post));
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

if(!function_exists('__json_decode')){
	/**
	 * Alias for json_decode.
	 *
	 * Differs from json_decode in that it will return a WP_Error on failure.
	 *
	 * Retrieves the parameters from a JSON-formatted body.
	 *
	 * @return array|stdClass|WP_Error
	 */
	function __json_decode($json = '', $associative = null, $depth = 512, $flags = 0){
		$json = trim($json);
		if($associative or ($flags & JSON_OBJECT_AS_ARRAY)){
			$empty = [];
		} else {
			$empty = new \stdClass;
		}
		if(empty($json)){
			return $empty;
		}
		$params = json_decode($json, $associative, $depth, $flags); // Parses the JSON parameters.
		if(is_null($params) and JSON_ERROR_NONE !== json_last_error()){ // Check for a parsing error.
			$error_data = [
				'json_error_code' => json_last_error(),
				'json_error_message' => json_last_error_msg(),
				'status' => \WP_Http::BAD_REQUEST,
			];
            $error_msg = translate('Invalid JSON body passed.');
			return __error($error_msg, $error_data);
		}
		return $params;
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
        if(!file_exists($mofile)){
            return false;
        }
        return load_textdomain($domain, $mofile, $locale);
	}
}

if(!function_exists('__local_login_header')){
    /**
     * @return void
     */
    function __local_login_header(){
        __set_cache('local_login_header', true);
        __add_filter_once('login_headertext', '__maybe_local_login_headertext');
        __add_filter_once('login_headerurl', '__maybe_local_login_headerurl');
    }
}

if(!function_exists('__object_to_array')){
	/**
	 * @return array|WP_Error
	 */
	function __object_to_array($data = null){
		if(!is_object($data)){
            $error_msg = translate('Invalid data provided.');
			return __error($error_msg, $data);
		}
		$data = wp_json_encode($data);
		return __json_decode($data, true);
	}
}

if(!function_exists('__post_type_labels')){
	/**
	 * @return array
	 */
	function __post_type_labels($singular = '', $plural = '', $all = true){
		if(empty($singular)){
			return [];
		}
		if(empty($plural)){
			$plural = $singular;
		}
		return [
			'name' => $plural,
			'singular_name' => $singular,
			'add_new' => 'Add New',
			'add_new_item' => 'Add New ' . $singular,
			'edit_item' => 'Edit ' . $singular,
			'new_item' => 'New ' . $singular,
			'view_item' => 'View ' . $singular,
			'view_items' => 'View ' . $plural,
			'search_items' => 'Search ' . $plural,
			'not_found' => 'No ' . strtolower($plural) . ' found.',
			'not_found_in_trash' => 'No ' . strtolower($plural) . ' found in Trash.',
			'parent_item_colon' => 'Parent ' . $singular . ':',
			'all_items' => ($all ? 'All ' : '') . $plural,
			'archives' => $singular . ' Archives',
			'attributes' => $singular . ' Attributes',
			'insert_into_item' => 'Insert into ' . strtolower($singular),
			'uploaded_to_this_item' => 'Uploaded to this ' . strtolower($singular),
			'featured_image' => 'Featured image',
			'set_featured_image' => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image' => 'Use as featured image',
			'filter_items_list' => 'Filter ' . strtolower($plural) . ' list',
			'items_list_navigation' => $plural . ' list navigation',
			'items_list' => $plural . ' list',
			'item_published' => $singular . ' published.',
			'item_published_privately' => $singular . ' published privately.',
			'item_reverted_to_draft' => $singular . ' reverted to draft.',
			'item_scheduled' => $singular . ' scheduled.',
			'item_updated' => $singular . ' updated.',
		];
	}
}

if(!function_exists('__properties_exists')){
	/**
	 * @return bool
	 */
	function __properties_exists($properties = [], $object = []){
		if(!is_array($properties) or !is_object($object)){
			return false;
		}
		foreach($properties as $property){
			if(!property_exists($object, $property)){
				return false;
			}
		}
		return true;
	}
}

if(!function_exists('__table')){
    /**
     * @return string
     */
    function __table($data = [], $headers = [], $args = []){
        $data = array_values($data);
        if(!$data){
            return translate('No posts found.');
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
        $message = sprintf(translate('%1$s is proudly powered by %2$s'), get_bloginfo('name'), '<a href="https://wordpress.org/">WordPress</a>');
        $title = translate('Hello world!');
        __exit_with_error($message, $title, 200);
	}
}

if(!function_exists('__validate_redirect_to')){
	/**
	 * @return string
	 */
	function __validate_redirect_to($url = ''){
		$redirect_to = isset($_REQUEST['redirect_to']) ? wp_http_validate_url($_REQUEST['redirect_to']) : false;
		if(!$redirect_to and !empty($url)){
			$redirect_to = wp_http_validate_url($url);
		}
		return (string) $redirect_to;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_hide_wp_from_admin')){
    /**
	 * This function MUST be called inside the 'admin_init' action hook.
	 *
     * @return void
     */
    function __maybe_hide_wp_from_admin(){
        if(!doing_action('admin_init')){ // Too early or too late.
	        return;
	    }
        $hide_wp = (bool) __get_cache('hide_wp', false);
    	if(!$hide_wp){
    		return;
    	}
        remove_action('welcome_panel', 'wp_welcome_panel');
    }
}

if(!function_exists('__maybe_hide_wp_from_admin_bar')){
    /**
	 * This function MUST be called inside the 'wp_before_admin_bar_render' action hook.
	 *
     * @return void
     */
    function __maybe_hide_wp_from_admin_bar(){
        global $wp_admin_bar;
        if(!doing_action('wp_before_admin_bar_render')){ // Too early or too late.
	        return;
	    }
        $hide_wp = (bool) __get_cache('hide_wp', false);
    	if(!$hide_wp){
    		return;
    	}
    	$wp_admin_bar->remove_node('wp-logo');
    }
}

if(!function_exists('__maybe_local_login_headertext')){
    /**
	 * This function MUST be called inside the 'login_headertext' filter hook.
	 *
     * @return string
     */
    function __maybe_local_login_headertext($login_header_text = ''){
        if(!doing_filter('login_headertext')){ // Too early or too late.
	        return $login_header_text;
	    }
    	$local_login_header = (bool) __get_cache('local_login_header', false);
    	if(!$local_login_header){
            return $login_header_text;
    	}
    	return get_option('blogname');
    }
}

if(!function_exists('__maybe_local_login_headerurl')){
    /**
	 * This function MUST be called inside the 'login_headerurl' filter hook.
	 *
     * @return string
     */
    function __maybe_local_login_headerurl($login_header_url = ''){
        if(!doing_filter('login_headerurl')){ // Too early or too late.
	        return $login_header_url;
	    }
        $local_login_header = (bool) __get_cache('local_login_header', false);
    	if(!$local_login_header){
            return $login_header_url;
    	}
        return home_url();
    }
}

if(!function_exists('__maybe_replace_login_logo')){
    /**
	 * This function MUST be called inside the 'login_enqueue_scripts' action hook.
	 *
     * @return string
     */
    function __maybe_replace_login_logo(){
        if(!doing_action('login_enqueue_scripts')){ // Too early or too late.
	        return false;
	    }
        $custom_login_logo = (array) __get_cache('custom_login_logo', []);
        if(!$custom_login_logo){
            return;
        } ?>
        <style type="text/css">
            #login h1 a,
            .login h1 a {
                background-image: url(<?php echo $custom_login_logo[0]; ?>);
                background-size: <?php echo $custom_login_logo[1]; ?>px <?php echo $custom_login_logo[2]; ?>px;
                height: <?php echo $custom_login_logo[2]; ?>px;
                width: <?php echo $custom_login_logo[1]; ?>px;
            }
        </style><?php
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
    	if(empty($nonce)){
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
// PHP Simple HTML DOM Parser
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__file_get_html')){
	/**
	 * @return simple_html_dom|WP_Error
	 */
	function __file_get_html(...$args){
		$remote_lib = __use_simple_html_dom();
		if(is_wp_error($remote_lib)){
			return $remote_lib;
		}
		return file_get_html(...$args);
	}
}

if(!function_exists('__str_get_html')){
	/**
	 * @return simple_html_dom|WP_Error
	 */
	function __str_get_html(...$args){
		$remote_lib = __use_simple_html_dom();
		if(is_wp_error($remote_lib)){
			return $remote_lib;
		}
		return str_get_html(...$args);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__use_simple_html_dom')){
	/**
	 * @return bool|WP_Error
	 */
	function __use_simple_html_dom($ver = '1.9.1'){
        return __use([
            'autoload' => 'simple_html_dom.php',
            'owner' => 'simplehtmldom',
            'repo' => 'simplehtmldom',
            'tag' => $ver,
            //'release' => $ver,
            'validation' => 'simple_html_dom',
        ]);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugin Update Checker
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__build_update_checker')){
	/**
	 * @return Plugin\UpdateChecker|Theme\UpdateChecker|Vcs\BaseChecker|WP_Error
	 */
	function __build_update_checker(...$args){
		$md5 = __md5($args);
		if(__isset_array_cache('update_checkers', $md5)){
			return __get_array_cache('update_checkers', $md5);
		}
		$remote_lib = __use_plugin_update_checker();
		if(is_wp_error($remote_lib)){
			return $remote_lib;
		}
		$update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(...$args);
		__set_array_cache('update_checkers', $md5, $update_checker);
		return $update_checker;
	}
}

if(!function_exists('__set_update_license')){
	/**
	 * @return void
	 */
	function __set_update_license($slug = '', $license = ''){
		if(empty($slug) or empty($license)){
			return;
		}
		__set_array_cache('update_licenses', $slug, $license);
		__add_filter_once('puc_request_info_query_args-' . $slug, '__maybe_set_update_license');
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_set_update_license')){
	/**
	 * This function MUST be called inside the 'puc_request_info_query_args-SLUG' filter hook.
	 *
	 * @return array
	 */
	function __maybe_set_update_license($queryArgs){
		$current_filter = current_filter();
		if(!__str_starts_with($current_filter, 'puc_request_info_query_args-')){ // Too early or too late.
	        return;
	    }
		$slug = str_replace('puc_request_info_query_args-', '', $current_filter);
		if(!__isset_array_cache('update_licenses', $slug)){
			return $queryArgs;
		}
		$queryArgs['license'] = (string) __get_array_cache('update_licenses', $slug, '');
		return $queryArgs;
	}
}

if(!function_exists('__use_plugin_update_checker')){
	/**
	 * @return string|WP_Error
	 */
	function __use_plugin_update_checker($ver = '5.6'){
        return __use([
            'autoload' => 'plugin-update-checker.php',
            'owner' => 'YahnisElsts',
            'repo' => 'plugin-update-checker',
            'tag' => 'v' . $ver,
            'release' => $ver,
            'validation' => 'YahnisElsts\PluginUpdateChecker\v5\PucFactory',
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

if(!function_exists('__is_plugin_active')){
    /**
     * @return bool
     */
    function __is_plugin_active($plugin = ''){
        if(__isset_array_cache('active_plugins', $plugin)){
            return (bool) __get_array_cache('active_plugins', $plugin, false);
        }
        if(!function_exists('is_plugin_active')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $status = is_plugin_active($plugin);
        __set_array_cache('active_plugins', $plugin, $status);
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
            return false;
        }
        $plugin_file = __plugin_file($file);
        if(is_wp_error($plugin_file)){
            return false; // File is not a plugin.
        }
        return (is_admin() and 'plugins.php' === $pagenow and isset($_GET['action'], $_GET['plugin']) and 'deactivate' === $_GET['action'] and plugin_basename($file) === $_GET['plugin']);
    }
}

if(!function_exists('__mu_plugins')){
    /**
     * @return array
     */
    function __mu_plugins(){
        if(__isset_cache('mu_plugins')){
            return (array) __get_cache('mu_plugins', []);
        }
        $mu_plugins = wp_get_mu_plugins();
        __set_cache('mu_plugins', $mu_plugins);
        return $mu_plugins;
    }
}

if(!function_exists('__plugin_basename')){
    /**
     * @return string|WP_Error
     */
    function __plugin_basename($file = ''){
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
        return plugin_basename($plugin_file);
    }
}

if(!function_exists('__plugin_data')){
    /**
     * @return array|WP_Error
     */
    function __plugin_data($file = '', $markup = true, $translate = true){
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
        if(__isset_array_cache('plugin_data', $md5)){
            return (array) __get_array_cache('plugin_data', $md5, []);
        }
        if(!function_exists('get_plugin_data')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $data = get_plugin_data($plugin_file, $markup, $translate);
        __set_array_cache('plugin_data', $md5, $data);
        return $data;
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
        } else {
            if(!file_exists($file)){
                $error_msg = translate('File does not exist! Please double check the name and try again.');
                return __error($error_msg);
            }
        }
        $md5 = md5($file);
        if(__isset_array_cache('plugin_files', $md5)){
            return (string) __get_array_cache('plugin_files', $md5, '');
        }
        $file = wp_normalize_path($file); // $wp_plugin_paths contains normalized paths.
        arsort($wp_plugin_paths);
        foreach($wp_plugin_paths as $dir => $realdir){
            if(!__str_starts_with($file, $realdir)){
                continue;
            }
            $file = $dir . substr($file, strlen($realdir));
            break;
        }
        $mu_plugin_dir = wp_normalize_path(WPMU_PLUGIN_DIR);
        $plugin_dir = wp_normalize_path(WP_PLUGIN_DIR);
        if(preg_match('#^' . preg_quote($plugin_dir, '#') . '/#', $file)){ // File is a plugin.
            $dir = $plugin_dir;
            $file = preg_replace('#^' . preg_quote($plugin_dir, '#') . '/#', '', $file); // Get relative path from plugins directory.
            $mu_plugin = false;
        } elseif(preg_match('#^' . preg_quote($mu_plugin_dir, '#') . '/#', $file)){ // File is a must-use plugin.
            $dir = $mu_plugin_dir;
            $file = preg_replace('#^' . preg_quote($mu_plugin_dir, '#') . '/#', '', $file); // Get relative path from must-use plugins directory.
            $mu_plugin = true;
        } else { // File is not a plugin.
            $error_msg = translate('Plugin not found.');
            return __error($error_msg);
        }
        $file = trim($file, '/'); // Note: This may not actually be necessary.
        $parts = explode('/', $file);
        if(count($parts) < 2){ // The entire plugin consists of just a single PHP file, like Hello Dolly.
            if($mu_plugin or __is_plugin_active($file)){ // Plugin is a must-use plugin or plugin is active.
                $file = $dir . '/' . $file;
                __set_array_cache('plugin_files', $md5, $file);
                return $file;
            }
            $error_msg = translate('Plugin not found.');
            return __error($error_msg); // Plugin is inactive.
        }
        $dir_path = trailingslashit($parts[0]);
        if($mu_plugin){ // Rarely needed.
            $mu_plugins = __mu_plugins();
            foreach($mu_plugins as $mu_plugin){
                if(!__str_starts_with($mu_plugin, $dir_path)){
                    continue;
                }
                $file = $dir . '/' . $mu_plugin;
                __set_array_cache('plugin_files', $md5, $file);
                return $file; // Plugin is a must-use plugin.
            }
            $error_msg = translate('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.');
            $error_msg = __first_p($error_msg);
            return __error($error_msg); // An unexpected error occurred.
        }
        $active_plugins = (array) get_option('active_plugins', []);
        foreach($active_plugins as $active_plugin){
            if(!__str_starts_with($active_plugin, $dir_path)){
                continue;
            }
            $file = $dir . '/' . $active_plugin;
            __set_array_cache('plugin_files', $md5, $file);
            return $file; // Plugin is active.
        }
        $active_sitewide_plugins = (array) get_site_option('active_sitewide_plugins', []);
        $active_sitewide_plugins = array_keys($active_sitewide_plugins);
        foreach($active_sitewide_plugins as $active_sitewide_plugin){
            if(!__str_starts_with($active_sitewide_plugin, $dir_path)){
                continue;
            }
            $file = $dir . '/' . $active_sitewide_plugin;
            __set_cache($key, $file);
            return $file; // Plugin is active for the entire network.
        }
        $error_msg = translate('Plugin not found.');
        return __error($error_msg); // Plugin is inactive.
    }
}

if(!function_exists('__plugin_folder')){
    /**
     * @return string|WP_Error
     */
    function __plugin_folder($file = ''){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        $basename = __plugin_basename($file);
        if(is_wp_error($basename)){
            return $basename;
        }
        $parts = explode('/', $basename);
        if(count($parts) < 2){ // The entire plugin consists of just a single PHP file, like Hello Dolly.
            $error_msg = 'The entire plugin consists of just a single PHP file.';
            return __error($error_msg); // Ignore.
        }
        return $parts[0];
    }
}

if(!function_exists('__plugin_load')){
    /**
     * @return object|WP_Error
     */
    function __plugin_load($file = ''){
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
        $plugin_slug = __plugin_slug('', $plugin_file);
        $class_file = plugin_dir_path($plugin_file) . 'class-' . $plugin_slug . '.php';
        if(!file_exists($class_file)){
            $error_msg = translate('File does not exist! Please double check the name and try again.');
            return __error($error_msg);
        }
        require_once($class_file);
        $class_name = __plugin_prefix('', $plugin_file);
        $plugin = __get_instance($class_name);
        return $plugin;
    }
}

if(!function_exists('__plugin_meta')){
    /**
     * @return string|WP_Error
     */
    function __plugin_meta($key = '', $file = '', $markup = false, $translate = false){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return $file;
            }
        }
        $data = __plugin_data($file, $markup, $translate);
        if(is_wp_error($data)){
            return $data;
        }
        if(isset($data[$key])){
            $arr = $data;
        } elseif(isset($data['sections'], $data['sections'][$key])){
            $arr = $data['sections'];
        } else {
            $error_msg = '"' . $key . '" ' . translate('(not found)');
            return __error($error_msg);
        }
        return $arr[$key];
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
                return ''; // Silence is golden.
            }
        }
        $plugin_folder = __plugin_folder($file);
        if(is_wp_error($plugin_folder)){
            return ''; // Silence is golden.
        }
        return __str_prefix($str, $plugin_folder);
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
                return ''; // Silence is golden.
            }
        }
        $plugin_folder = __plugin_folder($file);
        if(is_wp_error($plugin_folder)){
            return ''; // Silence is golden.
        }
        return __str_slug($str, $plugin_folder);
    }
}

if(!function_exists('__plugin_update_checker')){
    /**
     * @return void
     */
    function __plugin_update_checker($file = ''){
        if(!$file){
            $file = __caller_file(1); // One level above.
            if(is_wp_error($file)){
                return;
            }
        }
        $plugin_file = __plugin_file($file);
        if(is_wp_error($plugin_file)){
            return;
        }
        $update_uri = __plugin_meta('UpdateURI', $plugin_file);
        if(is_wp_error($update_uri)){
            return;
        }
        if(!wp_http_validate_url($update_uri)){
            return;
        }
        $plugin_slug = __plugin_slug('', $plugin_file);
        if(doing_action('plugins_loaded')){ // Just in time.
            $update_checker = __build_update_checker($update_uri, $plugin_file, $plugin_slug);
            if(is_wp_error($update_checker)){
                return;
            }
            $constant_name = strtoupper(__plugin_prefix('license', $plugin_file));
            if(!defined($constant_name)){
                return;
            }
            $constant_value = constant($constant_name);
            __set_update_license($plugin_slug, $constant_value);
			return;
	    }
        if(did_action('plugins_loaded')){ // Too late.
			return;
		}
        $puc = [
            'full_path' => $plugin_file,
            'metadata_url' => $update_uri,
            'slug' => $plugin_slug,
        ];
        $md5 = __md5($puc);
		if(__isset_array_cache('pucs', $md5)){
            return; // Prevent admin notice being added twice.
        }
		__set_array_cache('pucs', $md5, $puc);
		__add_action_once('plugins_loaded', '__maybe_plugin_update_checker', 0); // Highest priority.
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_plugin_update_checker')){
	/**
	 * This function MUST be called inside the 'plugins_loaded' action hook.
	 *
	 * @return void
	 */
	function __maybe_plugin_update_checker(){
		if(!doing_action('plugins_loaded')){ // Too early or too late.
	        return;
	    }
	    $pucs = (array) __get_cache('pucs', []);
		if(empty($pucs)){
			return;
		}
		foreach($pucs as $md5 => $puc){
            $update_checker = __build_update_checker($puc['metadata_url'], $puc['full_path'], $puc['slug']);
            if(is_wp_error($update_checker)){
                continue;
            }
            $constant_name = strtoupper(__plugin_prefix('license', $puc['full_path']));
            if(!defined($constant_name)){
                continue;
            }
            $constant_value = constant($constant_name);
            __set_update_license($puc['slug'], $constant_value);
		}
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugins cache
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_plugin_array_cache')){
	/**
	 * @return mixed
	 */
	function __get_plugin_array_cache($array_key = '', $key = '', $default = null){
		$array = (array) __get_cache($array_key, []);
		return isset($array[$key]) ? $array[$key] : $default;
	}
}

if(!function_exists('__get_plugin_cache')){
	/**
	 * @return mixed
	 */
	function __get_plugin_cache($key = '', $default = null){
		$group = __plugin_group();
		$value = wp_cache_get($key, $group, false, $found);
		if($found){
			return $value;
		}
	    return $default;
	}
}

if(!function_exists('__isset_plugin_array_cache')){
	/**
	 * @return bool
	 */
	function __isset_plugin_array_cache($array_key = '', $key = ''){
		$array = (array) __get_cache($array_key, []);
		return isset($array[$key]);
	}
}

if(!function_exists('__isset_plugin_cache')){
	/**
	 * @return bool
	 */
	function __isset_plugin_cache($key = ''){
		$group = __plugin_group();
		$value = wp_cache_get($key, $group, false, $found);
	    return $found;
	}
}

if(!function_exists('__set_plugin_array_cache')){
	/**
	 * @return bool
	 */
	function __set_plugin_array_cache($array_key = '', $key = '', $data = null){
		$array = (array) __get_cache($array_key, []);
		$array[$key] = $data;
		return __set_cache($array_key, $array);
	}
}

if(!function_exists('__set_plugin_cache')){
	/**
	 * @return bool
	 */
	function __set_plugin_cache($key = '', $data = null){
		$group = __plugin_group();
		return wp_cache_set($key, $data, $group);
	}
}

if(!function_exists('__unset_plugin_array_cache')){
	/**
	 * @return bool
	 */
	function __unset_plugin_array_cache($array_key = '', $key = ''){
		$array = (array) __get_cache($array_key, []);
		if(isset($array[$key])){
			unset($array[$key]);
		}
		return __set_cache($array_key, $array);
	}
}

if(!function_exists('__unset_plugin_cache')){
	/**
	 * @return bool
	 */
	function __unset_plugin_cache($key = ''){
		$group = __plugin_group();
		return wp_cache_delete($key, $group);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__plugin_group')){
    /**
     * @return string
     */
    function __plugin_group(){
        $file = __caller_file(2); // Two levels above.
        $group = __plugin_prefix('', $file);
        return $group;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugins hooks
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__add_plugin_action')){
    /**
     * @return string
     */
    function __add_plugin_action($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $hook_name = __plugin_hook_name($hook_name);
        return __on($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__add_plugin_action_once')){
    /**
     * @return string
     */
    function __add_plugin_action_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $hook_name = __plugin_hook_name($hook_name);
        return __one($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__add_plugin_filter')){
    /**
     * @return string
     */
    function __add_plugin_filter($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $hook_name = __plugin_hook_name($hook_name);
        return __on($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__add_plugin_filter_once')){
    /**
     * @return string
     */
    function __add_plugin_filter_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $hook_name = __plugin_hook_name($hook_name);
        return __one($hook_name, $callback, $priority, $accepted_args);
    }
}

if(!function_exists('__apply_plugin_filters')){
    /**
     * @return mixed
     */
    function __apply_plugin_filters($hook_name = '', $value = null, ...$arg){
        $hook_name = __plugin_hook_name($hook_name);
        return apply_filters($hook_name, $value, ...$arg);
    }
}

if(!function_exists('__did_plugin_action')){
    /**
     * @return bool
     */
    function __did_plugin_action($hook_name = ''){
        $hook_name = __plugin_hook_name($hook_name);
        return did_action($hook_name);
    }
}

if(!function_exists('__did_plugin_filter')){
    /**
     * @return bool
     */
    function __did_plugin_filter($hook_name = ''){
        $hook_name = __plugin_hook_name($hook_name);
        return did_filter($hook_name);
    }
}

if(!function_exists('__do_plugin_action')){
    /**
     * @return void
     */
    function __do_plugin_action($hook_name = '', ...$arg){
        $hook_name = __plugin_hook_name($hook_name);
        do_action($hook_name, ...$arg);
    }
}

if(!function_exists('__do_plugin_action_ref_array')){
    /**
     * @return void
     */
    function __do_plugin_action_ref_array($hook_name = '', $args = []){
        $hook_name = __plugin_hook_name($hook_name);
        do_action_ref_array($hook_name, $args);
    }
}

if(!function_exists('__doing_plugin_action')){
    /**
     * @return bool
     */
    function __doing_plugin_action($hook_name = ''){
        $hook_name = __plugin_hook_name($hook_name);
        return doing_filter($hook_name);
    }
}

if(!function_exists('__doing_plugin_filter')){
    /**
     * @return bool
     */
    function __doing_plugin_filter($hook_name = ''){
        $hook_name = __plugin_hook_name($hook_name);
        return doing_filter($hook_name);
    }
}

if(!function_exists('__has_plugin_action')){
    /**
     * @return bool
     */
    function __has_plugin_action($hook_name = '', $callback = false){
        $hook_name = __plugin_hook_name($hook_name);
        return has_filter($hook_name, $callback);
    }
}

if(!function_exists('__has_plugin_filter')){
    /**
     * @return bool
     */
    function __has_plugin_filter($hook_name = '', $callback = false){
        $hook_name = __plugin_hook_name($hook_name);
        return has_filter($hook_name, $callback);
    }
}

if(!function_exists('__remove_plugin_action')){
    /**
     * @return bool
     */
    function __remove_plugin_action($hook_name = '', $callback = null, $priority = 10){
        $hook_name = __plugin_hook_name($hook_name);
        return remove_filter($hook_name, $callback, $priority);
    }
}

if(!function_exists('__remove_plugin_filter')){
    /**
     * @return bool
     */
    function __remove_plugin_filter($hook_name = '', $callback = null, $priority = 10){
        $hook_name = __plugin_hook_name($hook_name);
        return remove_filter($hook_name, $callback, $priority);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__plugin_hook_name')){
    /**
     * @return string
     */
    function __plugin_hook_name($hook_name = ''){
        $file = __caller_file(2); // Two levels above.
        $hook_name = __plugin_prefix($hook_name, $file);
        return $hook_name;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Queries
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_post')){
	/**
	 * @return null|WP_Post
	 */
	function __get_post($post = null, $output = OBJECT, $filter = 'raw'){
        if(!is_array($post)){
            return get_post($post, $output, $filter);
        }
        $post['fields'] = 'all';
        $post['posts_per_page'] = 1;
        $posts = get_posts($post);
        if(!$posts){
            return null;
        }
        return get_post($posts[0], $output, $filter);
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
			$parsed_args['post_status'] = ('attachment' === $parsed_args['post_type']) ? 'inherit' : 'publish';
		}
		if(!empty($parsed_args['numberposts']) and empty($parsed_args['posts_per_page'])){
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
        if(!__is_front()){
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
	 * Alias for wp_get_current_user, get_user_by, get_userdata.
	 *
	 * @return bool|WP_User
	 */
	function __get_user($user = null){
	    if(is_null($user)){
	        return (is_user_logged_in() ? wp_get_current_user() : false);
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
	    $defaults = [
	        'count_total' => false,
	    ];
	    $parsed_args = wp_parse_args($args, $defaults);
	    $query = new \WP_User_Query($parsed_args);
	    return $query;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Remote
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__download_tmp')){
	/**
	 * Alias for download_url.
	 *
	 * @return string|WP_Error
	 */
	function __download_tmp($url = '', $timeout = 300, $signature_verification = false){
		$filename = download_url($url, $timeout, $signature_verification);
        if(is_wp_error($filename)){
            return $filename;
        }
        if('tmp' !== pathinfo($filename, PATHINFO_EXTENSION)){
            return $filename;
        }
        $url_filename = __basename($url);
        $check = wp_check_filetype($url_filename);
        if(empty($check['ext'])){
            return $filename;
        }
        $new_filename = dirname($filename) . '/' . $url_filename;
        if(rename($filename, $new_filename)){
            $filename = $new_filename;
        }
        if($filename !== $new_filename and file_exists($new_filename)){
            @unlink($new_filename);
        }
        return $filename;
	}
}

if(!function_exists('__download_url')){
	/**
	 * @return string|WP_Error
	 */
	function __download_url($url = '', $timeout = 300, $signature_verification = false){
		$filename = __download_tmp($url, $timeout, $signature_verification);
        if(is_wp_error($filename)){
            return $filename;
        }
        $download_dir = __download_dir();
        if(is_wp_error($download_dir)){
            return $download_dir;
        }
        $basename = wp_basename($filename);
        $unique_filename = wp_unique_filename($download_dir, $basename);
        $new_filename = $download_dir . '/' . $unique_filename;
        if(rename($filename, $new_filename)){
            $filename = $new_filename;
        }
        if($filename !== $new_filename and file_exists($new_filename)){
            @unlink($new_filename);
        }
        $check = wp_check_filetype($filename);
        if(empty($check['ext'])){
            @unlink($filename);
            $error_msg = translate('Sorry, you are not allowed to upload this file type.');
		    return __error($error_msg);
        }
        return $filename;
	}
}

if(!function_exists('__get_content_type')){
	/**
	 * Alias for WP_REST_Request::get_content_type.
	 *
	 * Retrieves the Content-Type of the request or response.
	 *
	 * @return array
	 */
	function __get_content_type($r = []){
		$content_type = (array) wp_remote_retrieve_header($r, 'Content-Type');
		if(!$content_type){
			return $content_type;
		}
		$value = $content_type[0];
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
		$data = array_map('trim', $data);
		return $data;
	}
}

if(!function_exists('__get_status_message')){
	/**
	 * @return string
	 */
	function __get_status_message($code = 0){
		if(!$code){
			$error_msg = translate('An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.');
			$error_msg = __first_p($error_msg);
			return $error_msg;
		}
		$message = get_status_header_desc($code);
		$message = trim($message);
		if($message){
			return $message;
		}
		if(__is_success($code)){
			return sprintf(translate('SUCCESS: %s'), $code);
		}
		return sprintf(translate('FAILED: %s'), $code);
	}
}

if(!function_exists('__is_cloudflare')){
	/**
	 * @return bool
	 */
	function __is_cloudflare(){
		return isset($_SERVER['CF-ray']); // TODO: Check for Cloudflare Enterprise.
	}
}

if(!function_exists('__is_content_type')){
	/**
	 * @return bool
	 */
	function __is_content_type($content_type = []){
		if(!__array_keys_exists(['parameters', 'subtype', 'type', 'value'], $content_type)){
			return false;
		}
		$count = count($content_type);
		if(4 !== $count){
			return false;
		}
	    return true;
	}
}

if(!function_exists('__is_json_content_type')){
	/**
	 * Checks if the request or response has specified a JSON Content-Type.
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

if(!function_exists('__is_success')){
	/**
     * Alias for is_success.
	 *
	 * @return bool
	 */
	function __is_success($sc = 0){
        $sc = __absint($sc);
		return ($sc >= 200 && $sc < 300);
	}
}

if(!function_exists('__is_wp_http_request')){
	/**
	 * @return bool
	 */
	function __is_wp_http_request($args = []){
		if(!is_array($args)){
			return false;
		}
		if(!$args){
			return true;
		}
		$wp_http_request_args = ['body', 'blocking', 'compress', 'cookies', 'decompress', 'filename', 'headers', 'httpversion', 'limit_response_size', 'method', 'redirection', 'reject_unsafe_urls', 'sslcertificates', 'sslverify', 'stream', 'timeout', 'user-agent'];
		$wp_http_request = true;
		foreach(array_keys($args) as $arg){
			if(!in_array($arg, $wp_http_request_args)){
				$wp_http_request = false;
				break;
			}
		}
		if(!isset($args['method'])){
			return $wp_http_request;
		}
		if(!in_array($args['method'], ['DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE'])){
			return false;
		}
		return true;
	}
}

if(!function_exists('__is_wp_http_requests_response')){
	/**
	 * @return bool
	 */
	function __is_wp_http_requests_response($response = []){
		if(!__array_keys_exists(['body', 'cookies', 'filename', 'headers', 'http_response', 'response'], $response)){
			return false; // https://developer.wordpress.org/reference/classes/wp_http/request/#return
		}
		if(!$response['http_response'] instanceof \WP_HTTP_Requests_Response){
			return false; // https://developer.wordpress.org/reference/classes/wp_http_requests_response/
		}
	    return true;
	}
}

if(!function_exists('__parse_response')){
	/**
	 * @return stdClass
	 */
	function __parse_response($raw_response = []){
		$response = new \stdClass;
		$response->body = '';
		$response->code = 0;
		$response->cookies = [];
		$response->filename = '';
		$response->headers = [];
		$response->json = false;
		$response->json_params = [];
		$response->message = '';
		$response->response = [];
		$response->status = false;
		$response->wp_error = null;
		if(is_wp_error($raw_response)){
			$response->message = $raw_response->get_error_message();
			$response->wp_error = $raw_response;
		} elseif(__is_wp_http_requests_response($raw_response)){
			$array = $raw_response['response']->to_array();
			$response->body = trim($array['body']);
			$response->code = absint($array['response']['code']);
			$response->cookies = $array['cookies'];
			$response->filename = $array['filename'];
			$response->headers = $array['headers'];
			$response->json = __is_json_content_type($raw_response);
			$response->message = trim($array['response']['message']);
			$response->response = $raw_response;
			$response->status = __is_success($response->code);
			if(!$response->message){
				$response->message = __get_status_message($response->code);
			}
			if(!$response->status){
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
					$maybe_error = __seems_error($json_params);
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
			$error_msg = translate('Invalid data provided.');
			$response->message = $error_msg;
			$response->wp_error = __error($error_msg, $raw_response);
		}
		return $response;
	}
}

if(!function_exists('__remote_country')){
	/**
	 * @return string
	 */
	function __remote_country(){
		switch(true){
			case !empty($_SERVER['HTTP_CF_IPCOUNTRY']):
				$country = $_SERVER['HTTP_CF_IPCOUNTRY']; // Cloudflare.
				break;
			case is_callable(['wfUtils', 'IP2Country']):
				$country = \wfUtils::IP2Country(__remote_ip()); // Wordfence.
				break;
			default:
				$country = '';
		}
		return strtoupper($country); // ISO 3166-1 alpha-2.
	}
}

if(!function_exists('__remote_delete')){
	/**
	 * @return object
	 */
	function __remote_delete($url = '', $args = []){
		return __remote_request('DELETE', $url, $args);
	}
}

if(!function_exists('__remote_get')){
	/**
	 * @return object
	 */
	function __remote_get($url = '', $args = []){
		return __remote_request('GET', $url, $args);
	}
}

if(!function_exists('__remote_head')){
	/**
	 * @return object
	 */
	function __remote_head($url = '', $args = []){
		return __remote_request('HEAD', $url, $args);
	}
}

if(!function_exists('__remote_ip')){
	/**
	 * @return string
	 */
	function __remote_ip($default = ''){
		switch(true){
			case !empty($_SERVER['HTTP_CF_CONNECTING_IP']):
				$ip = $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare.
				break;
			case (__is_plugin_active('wordfence/wordfence.php') and is_callable(['wfUtils', 'getIP'])):
				$ip = \wfUtils::getIP(); // Wordfence.
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
				return $default;
		}
		if(false === strpos($ip, ',')){
			$ip = trim($ip);
		} else {
			$ip = explode(',', $ip);
			$ip = array_map('trim', $ip);
			$ip = array_filter($ip);
			if(empty($ip)){
				return $default;
			}
			$ip = $ip[0];
		}
		if(!\WP_Http::is_ip_address($ip)){
			return $default;
		}
		return $ip;
	}
}

if(!function_exists('__remote_options')){
	/**
	 * @return object
	 */
	function __remote_options($url = '', $args = []){
		return __remote_request('OPTIONS', $url, $args);
	}
}

if(!function_exists('__remote_patch')){
	/**
	 * @return object
	 */
	function __remote_patch($url = '', $args = []){
		return __remote_request('PATCH', $url, $args);
	}
}

if(!function_exists('__remote_post')){
	/**
	 * @return object
	 */
	function __remote_post($url = '', $args = []){
		return __remote_request('POST', $url, $args);
	}
}

if(!function_exists('__remote_put')){
	/**
	 * @return object
	 */
	function __remote_put($url = '', $args = []){
		return __remote_request('PUT', $url, $args);
	}
}

if(!function_exists('__remote_request')){
	/**
	 * @return stdClass
	 */
	function __remote_request($method = '', $url = '', $args = []){
        $args = wp_parse_args($args);
		$args['method'] = $method;
		$args = __sanitize_remote_args($args, $url);
		$response = wp_remote_request($url, $args);
		return __parse_response($response);
	}
}

if(!function_exists('__remote_trace')){
	/**
	 * @return object
	 */
	function __remote_trace($url = '', $args = []){
		return __remote_request('TRACE', $url, $args);
	}
}

if(!function_exists('__sanitize_remote_args')){
	/**
	 * @return array
	 */
	function __sanitize_remote_args($args = [], $url = ''){
		$args = wp_parse_args($args);
        if(isset($args['method'])){
            $args['method'] = strtoupper($args['method']);
        }
		if(!__is_wp_http_request($args)){
			return [
				'body' => $args,
			];
		}
		if(isset($args['timeout'])){
			$args['timeout'] = __sanitize_timeout($args['timeout']);
		}
		if(empty($args['cookies'])){
			if(!empty($url)){
				$location = wp_sanitize_redirect($url);
				if(wp_validate_redirect($location)){
					$args['cookies'] = $_COOKIE;
				}
			}
		}
		if(empty($args['user-agent'])){
			if(empty($_SERVER['HTTP_USER_AGENT'])){
				$args['user-agent'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36'; // Example Chrome UA string: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/User-Agent#chrome_ua_string
			} else {
				$args['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
			}
		}
		if(isset($args['body']) and is_array($args['body']) and __is_json_content_type($args)){
			$args['body'] = wp_json_encode($args['body']);
		}
		return $args;
	}
}

if(!function_exists('__sanitize_timeout')){
	/**
	 * @return int
	 */
	function __sanitize_timeout($timeout = 0){
		$timeout = (int) $timeout;
		if($timeout < 0){
			$timeout = 0;
		}
		$max_execution_time = (int) ini_get('max_execution_time');
		if(0 !== $max_execution_time){
			if(0 === $timeout or $timeout > $max_execution_time){
				$timeout = $max_execution_time - 1;
			}
		}
		if(__is_cloudflare()){
			if(0 === $timeout or $timeout > 98){
				$timeout = 98; // If the max_execution_time is set to greater than 98 seconds, reduce it a bit to prevent edge-case timeouts that may happen before the page is fully loaded. TODO: Check for Cloudflare Enterprise. See: https://developers.cloudflare.com/support/troubleshooting/cloudflare-errors/troubleshooting-cloudflare-5xx-errors/#error-524-a-timeout-occurred.
			}
		}
		return $timeout;
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
		$rule = [
			'plugin_file' => $plugin_file,
			'query' => str_replace(site_url('/'), '', $query),
			'regex' => str_replace(site_url('/'), '', $regex),
		];
		$md5 = __md5($rule);
		if(doing_action('generate_rewrite_rules')){ // Just in time.
			if(__isset_array_cache('external_rules', $md5)){
				return; // Already exists.
			}
			__maybe_add_external_rule($rule);
			__add_action_once('admin_notices', '__maybe_add_external_rules_notice');
			return;
		}
		if(did_action('generate_rewrite_rules')){ // Too late.
			return;
		}
		__set_array_cache('external_rules', $md5, $rule);
	    __add_action_once('generate_rewrite_rules', '__maybe_add_external_rules');
		__add_action_once('admin_notices', '__maybe_add_external_rules_notice');
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
		if(__isset_cache('rewrite_rules')){
			$rewrite_rules = (array) __get_cache('rewrite_rules', []);
			return $rewrite_rules;
		}
		$rewrite_rules = array_filter(extract_from_markers(get_home_path() . '.htaccess', 'WordPress'));
		__set_cache('rewrite_rules', $rewrite_rules);
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

if(!function_exists('__is_external_rule')){
	/**
	 * @return bool
	 */
	function __is_external_rule($rule = []){
		if(!__array_keys_exists(['plugin_file', 'query', 'regex'], $rule)){
			return false;
		}
		$count = count($rule);
		if(3 !== $count){
			return false;
		}
	    return true;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_add_external_rule')){
	/**
	 * This function MUST be called inside the 'generate_rewrite_rules' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rule($rule = []){
		global $wp_rewrite;
		if(!doing_action('generate_rewrite_rules')){ // Too early or too late.
	        return;
	    }
		if(!__is_external_rule($rule)){
			return;
		}
		if(__is_plugin_deactivating($rule['plugin_file'])){
			return;
		}
		$wp_rewrite->add_external_rule($rule['regex'], $rule['query']);
	}
}

if(!function_exists('__maybe_add_external_rules')){
	/**
	 * This function MUST be called inside the 'generate_rewrite_rules' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rules($wp_rewrite){
		if(!doing_action('generate_rewrite_rules')){ // Too early or too late.
	        return;
	    }
		$external_rules = (array) __get_cache('external_rules', []);
	    if(!$external_rules){
	        return;
	    }
	    foreach($external_rules as $rule){
			__maybe_add_external_rule($rule);
	    }
	}
}

if(!function_exists('__maybe_add_external_rules_notice')){
	/**
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rules_notice(){
		if(!doing_action('admin_notices')){ // Too early or too late.
	        return;
	    }
		if(!current_user_can('manage_options')){
			return;
		}
		$external_rules = (array) __get_cache('external_rules', []);
	    if(!$external_rules){
	        return;
	    }
	    $add_admin_notice = false;
		foreach($external_rules as $rule){
			if(!__external_rule_exists($rule['regex'], $rule['query'])){
				$add_admin_notice = true;
				break;
			}
		}
		if(!$add_admin_notice){
	        return;
		}
	    $message = sprintf(translate('You should update your %s file now.'), '<code>.htaccess</code>');
	    $message .= ' ';
	    $message .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('options-permalink.php')), translate('Flush permalinks')) . '.';
	    __add_admin_notice($message);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Roles and Capabilities
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__clone_role')){
	/**
	 * @return void|WP_Role
	 */
	function __clone_role($source = '', $destination = '', $display_name = ''){
		$role = get_role($source);
		if(is_null($role)){
			return;
		}
		$destination = __canonicalize($destination);
		return add_role($destination, $display_name, $role->capabilities);
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
		$key = sanitize_title($key);
		$key = str_replace('-', '_', $key);
		return $key;
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
	function __implode_and($array = [], $separator = ''){
        if(!$separator){
            $separator = trim(sprintf(translate('%1$s and %2$s'), '', ''));
        }
		return __implode_comma($array, $separator);
	}
}

if(!function_exists('__implode_comma')){
	/**
	 * @return string
	 */
	function __implode_comma($array = [], $separator = ''){
		if(!is_array($array)){
			return '';
		}
		if(empty($array)){
			return '';
		}
        if(1 === count($array)){
			return $array[0];
		}
        $comma = wp_get_list_item_separator();
        $separator = trim($separator);
        if(!$separator){
            return implode($comma, $array);
        }
		$last = array_pop($array);
		return implode($comma, $array) . ' ' . $separator . ' ' . $last;
	}
}

if(!function_exists('__implode_or')){
	/**
	 * @return string
	 */
	function __implode_or($array = [], $separator = ''){
        if(!$separator){
            $separator = trim(sprintf(translate('%1$s or %2$s'), '', ''));
        }
		return __implode_comma($array, $separator);
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
                    $error_msg = translate('Error');
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
        if(empty($dot_chr)){
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
		if(false === strpos($str, '%')){
			return $str;
		}
		$subject = $wpdb->prepare($str, ...$args);
		$subject = $wpdb->remove_placeholder_escape($subject);
		return str_replace("'", '', $subject);
	}
}

if(!function_exists('__remove_whitespaces')){
	/**
	 * @return string
	 */
	function __remove_whitespaces($str = ''){
		return trim(preg_replace('/[\r\n\t\s]+/', ' ', $str));
	}
}

if(!function_exists('__str_ends_with')){
	/**
     * Polyfill for `str_ends_with()` function added in PHP 8.0 and WordPress 5.9.
     *
	 * @return string
	 */
	function __str_ends_with($haystack = '', $needle = ''){
        if(function_exists('str_ends_with')){
            return str_ends_with($haystack, $needle);
        }
		if('' === $haystack){
			return '' === $needle;
		}
        $len = strlen($needle);
        return substr($haystack, -$len, $len) === $needle;
	}
}

if(!function_exists('__str_prefix')){
	/**
	 * @return string
	 */
	function __str_prefix($str = '', $prefix = ''){
		$prefix = str_replace('\\', '_', $prefix); // Fix namespaces.
		$prefix = __canonicalize($prefix);
		$prefix = rtrim($prefix, '_');
		if(!$prefix){
			$prefix = __prefix();
		}
		$str = __remove_whitespaces($str);
		if(!$str){
			return $prefix;
		}
		if(0 === strpos($str, $prefix)){
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
		$slug = str_replace('_', '-', $slug); // Fix canonicalized.
		$slug = str_replace('\\', '-', $slug); // Fix namespaces.
		$slug = sanitize_title($slug);
		$slug = rtrim($slug, '-');
		if(!$slug){
			$slug = __slug();
		}
		$str = __remove_whitespaces($str);
		if(!$str){
			return $slug;
		}
		if(0 === strpos($str, $slug)){
			return $str; // Text is already slugged.
		}
		return $slug . '-' . $str;
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
			if((($length + $word_length) <= $line_length) or empty($oputput[$index])){
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
			if((($length + $word_length) <= $line_length) or empty($oputput[$index])){
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

if(!function_exists('__str_starts_with')){
	/**
     * Polyfill for `str_starts_with()` function added in PHP 8.0 and WordPress 5.9.
     *
	 * @return string
	 */
	function __str_starts_with($haystack = '', $needle = ''){
        if(function_exists('str_starts_with')){
            return str_starts_with($haystack, $needle);
        }
		if('' === $needle){
			return true;
		}
		return 0 === strpos($haystack, $needle);
	}
}

if(!function_exists('__trailingdotit')){
	/**
	 * @return string
	 */
	function __trailingdotit($text = ''){
        $text = sanitize_textarea_field($text);
		if(__str_ends_with($text, '.') or __str_ends_with($text, '?') or __str_ends_with($text, '!')){
			return $text;
		}
		return $text . '.';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// TGM Plugin Activation
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__tgmpa_register')){
	/**
	 * @return void
	 */
	function __tgmpa_register($plugins = [], $config = []){
        $lib = __use_tgm_plugin_activation();
		if(is_wp_error($lib)){
			return; // Silence is golden.
		}
		if(doing_action('tgmpa_register')){ // Just in time.
			__tgmpa($plugins, $config);
			return;
		}
		if(did_action('tgmpa_register')){ // Too late.
			return;
		}
		$tgmpa = [
			'config' => $config,
			'plugins' => $plugins,
		];
		$md5 = __md5($tgmpa);
		__set_array_cache('tgmpa', $md5, $tgmpa);
		__add_action_once('tgmpa_register', '__maybe_tgmpa_register');
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_tgmpa_register')){
	/**
	 * @return void
	 */
	function __maybe_tgmpa_register(){
		$tgmpa = (array) __get_cache('tgmpa', []);
		if(empty($tgmpa)){
			return;
		}
		foreach($tgmpa as $args){
			__tgmpa($args['plugins'], $args['config']);
		}
	}
}

if(!function_exists('__tgmpa')){
	/**
	 * This function MUST be called inside the 'tgmpa_register' action hook.
	 *
	 * @return void
	 */
	function __tgmpa($plugins = [], $config = []){
		if(!doing_action('tgmpa_register')){
			return; // Too early or too late.
		}
		tgmpa($plugins, $config);
	}
}

if(!function_exists('__use_tgm_plugin_activation')){
	/**
	 * @return bool|WP_Error
	 */
	function __use_tgm_plugin_activation($ver = '2.6.1'){
        return __use([
            'autoload' => 'class-tgm-plugin-activation.php',
            'owner' => 'TGMPA',
            'repo' => 'TGM-Plugin-Activation',
            'tag' => $ver,
            //'release' => $ver,
            'validation' => 'TGM_Plugin_Activation',
        ]);
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
		if(__isset_array_cache('theme_is', $name)){
			return (bool) __get_array_cache('theme_is', $name, false);
		}
    	$current_theme = wp_get_theme();
		$theme_is = ($name === $current_theme->get('Name'));
		__set_array_cache('theme_is', $name, $theme_is);
    	return $theme_is;
    }
}

if(!function_exists('__theme_is_child_of')){
    /**
     * @return bool
     */
    function __theme_is_child_of($template = ''){
		if(__isset_array_cache('theme_is_child_of', $template)){
			return (bool) __get_array_cache('theme_is_child_of', $template, false);
		}
		$current_theme = wp_get_theme();
		$theme_is_child_of = ($template === $current_theme->get('Template'));
		__set_array_cache('theme_is_child_of', $template, $theme_is_child_of);
    	return $theme_is_child_of;
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__include_theme_functions')){
	/**
	 * This function MUST be called inside the 'after_setup_theme' action hook.
	 *
	 * @return void
	 */
	function __include_theme_functions(){
		if(!doing_action('after_setup_theme')){ // Too early or too late.
	        return;
	    }
        // Load the functions for the active theme, for both parent and child theme if applicable.
		$filename = __slug() . '.php';
        foreach(wp_get_active_and_valid_themes() as $theme){
        	if(file_exists($theme . '/' . $filename)){
        		require_once $theme . '/' . $filename;
        	}
        }
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Urchin Tracking Module Parameters
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__current_utm_param')){
    /**
     * @return string
     */
    function __current_utm_param($name = ''){
        $utm_params = __current_utm_params();
        if(!isset($utm_params[$name])){
            return '';
        }
        return $utm_params[$name];
    }
}

if(!function_exists('__current_utm_params')){
    /**
     * @return array
     */
    function __current_utm_params(){
        if(__at_least_one_utm_get_param()){
            return __utm_params_from_get(); // 1. GET
        }
        return __utm_params_from_cookie(); // 2. COOKIE
    }
}

if(!function_exists('__utm_param_name')){
    /**
     * @return string
     */
    function __utm_param_name($name = ''){
        $pairs = __utm_pairs();
        if(!array_key_exists($name, $pairs)){
            return '';
        }
        return $pairs[$name];
    }
}

if(!function_exists('__utm_parameters')){
    /**
     * @return void
     */
    function __utm_parameters(){
        __set_cache('utm_parameters', true);
		if(doing_action('after_setup_theme')){ // Just in time.
			__maybe_set_utm_cookie();
			return;
		}
		if(did_action('after_setup_theme')){ // Too late.
			return;
		}
		__add_action_once('after_setup_theme', '__maybe_set_utm_cookie');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__at_least_one_utm_get_param')){
    /**
     * @return void
     */
    function __at_least_one_utm_get_param(){
        $at_least_one = false;
        $utm_params = __utm_params_from_get();
        foreach($utm_params as $key => $value){
            if(!$value){
                continue;
            }
            $at_least_one = true;
            break;
        }
        return $at_least_one;
    }
}

if(!function_exists('__maybe_set_utm_cookie')){
    /**
     * This function MUST be called inside the 'after_setup_theme' action hook.
	 *
	 * @return void
     */
    function __maybe_set_utm_cookie(){
		if(!doing_action('after_setup_theme')){ // Too early or too late.
	        return;
	    }
        $utm_parameters = (bool) __get_cache('utm_parameters', false);
        if(!$utm_parameters){
            return;
        }
        if(!__at_least_one_utm_get_param()){
            return;
        }
        __maybe_unset_utm_cookie();
        $cookie_lifetime = time() + (2 * MONTH_IN_SECONDS); // https://support.google.com/analytics/answer/7667196?hl=en_US&utm_id=ad
        $utm_params = __utm_params_from_get();
        $value = build_query($utm_params);
        $name = __utm_cookie_name();
        __set_cookie($name, $value, $cookie_lifetime);
    }
}

if(!function_exists('__maybe_unset_utm_cookie')){
    /**
     * @return void
     */
    function __maybe_unset_utm_cookie(){
        $name = __utm_cookie_name();
        __unset_cookie($name);
    }
}

if(!function_exists('__utm_cookie_name')){
    /**
     * @return string
     */
    function __utm_cookie_name(){
        return __str_prefix('utm_parameters');
    }
}

if(!function_exists('__utm_keys')){
    /**
     * @return array
     */
    function __utm_keys(){
        return array_keys(__utm_pairs());
    }
}

if(!function_exists('__utm_pairs')){
    /**
     * @return array
     */
    function __utm_pairs(){
        return [
            'utm_campaign' => 'Name',
            'utm_content' => 'Content',
            'utm_id' => 'ID',
            'utm_medium' => 'Medium',
            'utm_source' => 'Source',
            'utm_term' => 'Term',
        ];
    }
}

if(!function_exists('__utm_params_from_cookie')){
    /**
     * @return array
     */
    function __utm_params_from_cookie(){
        $name = __utm_cookie_name();
        $value = '';
        if(isset($_COOKIE[$name])){
            $value = $_COOKIE[$name];
        }
        wp_parse_str($value, $values);
        $utm_params = [];
        foreach(__utm_keys() as $key){
            if(isset($values[$key])){
                $utm_params[$key] = $values[$key];
            } else {
                $utm_params[$key] = '';
            }
        }
        return $utm_params;
    }
}

if(!function_exists('__utm_params_from_get')){
    /**
     * @return array
     */
    function __utm_params_from_get(){
        $utm_params = [];
        foreach(__utm_keys() as $key){
            if(isset($_GET[$key])){
                $utm_params[$key] = $_GET[$key];
            } else {
                $utm_params[$key] = '';
            }
        }
        return $utm_params;
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
        if(__isset_cache('wf_bulk_countries')){
            return (array) __get_cache('wf_bulk_countries', []);
        }

        if(!defined('WORDFENCE_PATH')){
            return [];
        }
        $file = WORDFENCE_PATH . 'lib/wfBulkCountries.php';
        if(!file_exists($file)){
            return [];
        }
        include $file; /** @var array $wfBulkCountries */
        asort($wfBulkCountries);
        __set_cache('wf_bulk_countries', $wfBulkCountries);
        return $wfBulkCountries;
    }
}

if(!function_exists('__wf_code_execution_protection')){
    /**
     * @return bool
     */
    function __wf_code_execution_protection(){
        /*if(!class_exists('wfConfig')){
            return false;
        }
        return wfConfig::get('disableCodeExecutionUploads');*/
        global $is_apache;
        if(!$is_apache){
            return false;
        }
        $upload_dir = wp_get_upload_dir();
		if($upload_dir['error']){
			return __error($upload_dir['error']);
		}
        $basedir = $upload_dir['basedir'];
        $htaccess = $basedir . '/.htaccess';
        if(!file_exists($htaccess)){
            return false;
        }
        if(!function_exists('extract_from_markers')){
            require_once(ABSPATH . 'wp-admin/includes/misc.php');
        }
        $existing_rules = array_filter(extract_from_markers($htaccess, 'Wordfence code execution protection'));
        if(empty($existing_rules)){
            return false;
        }
        return true;
    }
}

if(!function_exists('__wf_countries')){
    /**
     * @return array
     */
    function __wf_countries($preferred_countries = []){
        $wf_countries = __wf_bulk_countries();
        if(!$wf_countries){
            return $wf_countries;
        }
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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Zoom
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__zoom_access_token')){
    /**
     * @return string|WP_Error
     */
    function __zoom_access_token(){
        if(__isset_cache('zoom_access_token')){
            return (string) __get_cache('zoom_access_token', '');
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
            $message = sprintf(translate('Missing parameter(s): %s'), 'access_token') . '.';
            return __error($message);
        }
        $access_token = $response->json_params['access_token'];
        if(!$access_token){
            $message = sprintf(translate('Invalid parameter(s): %s'), 'access_token') . '.';
            return __error($message);
        }
        __set_cache('zoom_access_token', $access_token);
        return $access_token;
    }
}

if(!function_exists('__zoom_api_url')){
    /**
     * @return string
     */
    function __zoom_api_url($endpoint = ''){
        $base = 'https://api.zoom.us/v2';
        if(__str_starts_with($endpoint, $base)){
            $endpoint = str_replace($base, '', $endpoint);
        }
        $endpoint = ltrim($endpoint, '/');
        $endpoint = untrailingslashit($endpoint);
        $endpoint = trailingslashit($base) . $endpoint;
        return $endpoint;
    }
}

if(!function_exists('__zoom_app_credentials')){
    /**
     * @return array|WP_Error
     */
    function __zoom_app_credentials($app_credentials = []){
        if(__isset_cache('zoom_app_credentials')){
            return (array) __get_cache('zoom_app_credentials', []);
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
            $message = sprintf(translate('Missing parameter(s): %s'), __implode_and($missing)) . '.';
            return __error($message);
        }
        __set_cache('zoom_app_credentials', $app_credentials);
        return $app_credentials;
    }
}

if(!function_exists('__zoom_oauth_token')){
    /**
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
        $expiration = 59 * MINUTE_IN_SECONDS; // The token’s time to live is 1 hour. https://developers.zoom.us/docs/internal-apps/s2s-oauth/
        $expiration = $expiration - 10;
        set_transient($transient, $oauth_token, $expiration);
        return $oauth_token;
    }
}

if(!function_exists('__zoom_delete')){
    /**
     * @return object
     */
    function __zoom_delete($endpoint = '', $args = [], $timeout = 10){
        return __zoom_request('DELETE', $endpoint, $args, $timeout);
    }
}

if(!function_exists('__zoom_get')){
    /**
     * @return object
     */
    function __zoom_get($endpoint = '', $args = [], $timeout = 10){
        return __zoom_request('GET', $endpoint, $args, $timeout);
    }
}

if(!function_exists('__zoom_patch')){
    /**
     * @return object
     */
    function __zoom_patch($endpoint = '', $args = [], $timeout = 10){
        return __zoom_request('PATCH', $endpoint, $args, $timeout);
    }
}

if(!function_exists('__zoom_post')){
    /**
     * @return object
     */
    function __zoom_post($endpoint = '', $args = [], $timeout = 10){
        return __zoom_request('POST', $endpoint, $args, $timeout);
    }
}

if(!function_exists('__zoom_put')){
    /**
     * @return object
     */
    function __zoom_put($endpoint = '', $args = [], $timeout = 10){
        return __zoom_request('PUT', $endpoint, $args, $timeout);
    }
}

if(!function_exists('__zoom_request')){
    /**
     * @return object
     */
    function __zoom_request($method = '', $endpoint = '', $args = [], $timeout = 10){
        $oauth_token = __zoom_oauth_token();
        if(is_wp_error($oauth_token)){
            return $oauth_token;
        }
        $url = __zoom_api_url($endpoint);
        if(!is_array($args)){
            $args = wp_parse_args($args);
        }
        $args = [
            'body' => $args,
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

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Use
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__dirlist')){
	/**
	 * @return array|bool
	 */
	function __dirlist($path = '', $include_hidden = true, $recursive = false){
		$fs = __fs_direct();
		if(is_wp_error($fs)){
			return false;
		}
		return $fs->dirlist($path, $include_hidden, $recursive);
	}
}

if(!function_exists('__delete')){
	/**
	 * @return bool
	 */
	function __delete($path = '', $recursive = false, $type = false){
		$fs = __fs_direct();
		if(is_wp_error($fs)){
			return false;
		}
		return $fs->delete($path, $recursive, $type);
	}
}

if(!function_exists('__download_and_extract')){
	/**
	 * @return string|WP_Error
	 */
	function __download_and_extract($url = '', $subdir = ''){
		$filename = __basename($url);
		$filetype = wp_check_filetype($filename);
		if($filetype['ext'] !== 'zip' or $filetype['type'] !== 'application/zip'){
			$error_msg = translate('Incompatible Archive.');
            return __error($error_msg);
		}
		$md5 = md5($url);
		$uuid = __md5_to_uuid4($md5);
	    $key = 'pkg_' . $uuid;
	    if(__isset_cache($key)){
	        return (string) __get_cache($key, '');
	    }
		$name = __str_prefix($key);
		$value = get_option($name);
		if($value !== false){
			if(__dirlist($value, false)){
				__set_cache($key, $value);
				return $value;
			}
			__delete($value, true);
			delete_option($name);
		}
		if(!$subdir){
			$subdir = 'pkg';
		}
		$dir = __dir($subdir);
		if(is_wp_error($dir)){
			return $dir;
		}
		$to = $dir . '/' . $uuid;
		if(__dirlist($to, false)){
			__set_cache($key, $to);
			return $to;
		}
        $file = __download_url($url);
		if(is_wp_error($file)){
			return $file;
		}
		$result = unzip_file($file, $to);
		__delete($file);
		if(is_wp_error($result)){
			__delete($to, true);
			return $result;
		}
		if(!__dirlist($to, false)){
			__delete($to, true);
			$error_msg = translate('Empty archive.');
            return __error($error_msg);
		}
		__set_cache($key, $to);
        update_option($name, $to);
		return $to;
	}
}

if(!function_exists('__remote_lib')){
	/**
	 * @return string|WP_Error
	 */
	function __remote_lib($url = '', $expected_dir = '', $subdir = ''){
		if(!$subdir){
			$subdir = 'lib';
		}
		$to = __download_and_extract($url, $subdir);
		if(is_wp_error($to)){
			return $to;
		}
		if(!$expected_dir){
			return $to;
		}
		$expected_dir = ltrim($expected_dir, '/');
		$expected_dir = untrailingslashit($expected_dir);
		$expected_dir = $to . '/' . $expected_dir;
		return $expected_dir;
	}
}

if(!function_exists('__use')){
	/**
	 * @return string|WP_Error
	 */
	function __use($args = []){
        $defaults = [
            'autoload' => '',
            'owner' => '',
            'release' => '',
            'repo' => '',
			'subdir' => '',
            'tag' => '',
            'validation' => '',
        ];
        $args = wp_parse_args($args, $defaults);
        $missing = [];
        if(!$args['owner']){
            $missing[] = 'owner';
        }
        if(!$args['repo']){
            $missing[] = 'repo';
        }
        if(!$args['tag']){
            $missing[] = 'tag';
        }
        if($missing){
            $message = sprintf(translate('Missing parameter(s): %s'), __implode_and($missing)) . '.';
            return __error($message);
        }
        if(!$args['release']){
            $args['release'] = $args['tag'];
        }
        $key = 'use-' . $args['owner'] . '/' . $args['repo'] . '-' . $args['release'];
		if(__isset_cache($key)){
			return (string) __get_cache($key, '');
		}
        $url = 'https://github.com/' . $args['owner'] . '/' . $args['repo'] . '/archive/refs/tags/' . $args['tag'] . '.zip';
        $expected_dir = $args['repo'] . '-' . $args['release'];
        /*if(!$args['subdir']){
            $args['subdir'] = 'use';
        }*/
        $dir = __remote_lib($url, $expected_dir, $args['subdir']);
        if(is_wp_error($dir)){
            return $dir;
        }
        if($args['validation']){
            $valid = true;
            foreach((array) $args['validation'] as $class){
                if(class_exists($class)){
                    $valid = false;
                    break;
                }
            }
            if(!$valid){
                __set_cache($key, $dir);
        		return $dir;
            }
        }
        if($args['autoload']){
            foreach((array) $args['autoload'] as $basename){
                $basename = ltrim($basename, '/');
                $file = $dir . '/' . $basename;
        		if(file_exists($file)){
        			require_once($file);
        		}
            }
        }
        if($args['validation']){
            $missing = [];
            foreach((array) $args['validation'] as $class){
                if(!class_exists($class)){
                    $missing[] = $class;
                }
            }
            if($missing){
                $message = sprintf(translate('Missing parameter(s): %s'), __implode_and($missing)) . '.';
                return __error($message);
            }
        }
		__set_cache($key, $dir);
		return $dir;
	}
}

if(!function_exists('__use_font_awesome')){
	/**
	 * @return string|WP_Error
	 */
	function __use_font_awesome($ver = '6.7.2'){
        return __use([
            'owner' => 'FortAwesome',
            'repo' => 'Font-Awesome',
            'tag' => $ver,
        ]);
	}
}

if(!function_exists('__enqueue_font_awesome')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return string|WP_Error
     */
	function __enqueue_font_awesome($deps = [], $version = '6.7.2'){
        $dir = __use_font_awesome($version);
        if(is_wp_error($dir)){
            return $dir;
        }
        $base_path = __dir_to_url($dir) . '/css';
        $handle = __enqueue_asset('font-awesome', $base_path . '/fontawesome.min.css', $deps, $version);
        return $handle;
	}
}

if(!function_exists('__use_ace')){
	/**
	 * @return string|WP_Error
	 */
	function __use_ace($ver = '1.5.0'){
        return __use([
            'owner' => 'ajaxorg',
            'repo' => 'ace-builds',
            'tag' => 'v' . $ver,
            'release' => $ver,
        ]);
	}
}

if(!function_exists('__enqueue_ace')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return string|WP_Error
     */
	function __enqueue_ace($deps = [], $version = '1.5.0'){
        $dir = __use_ace($version);
        if(is_wp_error($dir)){
            return $dir;
        }
        $base_path = __dir_to_url($dir) . '/src-min';
        $handle = __enqueue_asset('ace', $base_path . '/ace.js', $deps, $version);
        if(is_wp_error($handle)){
            return $handle;
        }
		$handle = __enqueue_asset('ace-language-tools', $base_path . '/ext-language_tools.js', [$handle], $version);
        if(is_wp_error($handle)){
            return $handle;
        }
		$data = '_.isUndefined(ace)||(ace.config.set("basePath","' . $base_path . '"),ace.require("ace/ext/language_tools"));';
        wp_add_inline_script($handle, $data);
        return $handle;
	}
}

if(!function_exists('__use_jquery_scrollspy')){
	/**
	 * @return string|WP_Error
	 */
	function __use_jquery_scrollspy($ver = '0.1.3'){
        return __use([
            'owner' => 'thesmart',
            'repo' => 'jquery-scrollspy',
            'tag' => $ver,
        ]);
	}
}

if(!function_exists('__enqueue_jquery_scrollspy')){
	/**
     * This function MUST be called inside the 'admin_enqueue_scripts', 'login_enqueue_scripts' or 'wp_enqueue_scripts' action hooks.
     *
     * @return string|WP_Error
     */
	function __enqueue_jquery_scrollspy($deps = [], $version = '0.1.3'){
        $dir = __use_jquery_scrollspy($version);
        if(is_wp_error($dir)){
            return $dir;
        }
        $base_path = __dir_to_url($dir);
        $handle = __enqueue_asset('jquery-scrollspy', $base_path . '/scrollspy.js', $deps, $version);
        return $handle;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Classes
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_instance')){
	/**
	 * @return object|WP_Error
	 */
	function __get_instance($class = ''){
	    return __instance($class, true);
	}
}

if(!function_exists('__new_instance')){
	/**
	 * @return object|WP_Error
	 */
	function __new_instance($class = ''){
	    return __instance($class, false);
	}
}

if(!class_exists('__Base')){ // Hardcoded.
    class __Base { // Hardcoded.

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static private $instances = [];

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        /**
         * @return object
         */
        static public function get_instance(){
            $class_name = get_called_class();
			$idx = md5($class_name);
            if(isset(self::$instances[$idx])){
                return self::$instances[$idx];
            }
            self::$instances[$idx] = new $class_name();
            return self::$instances[$idx];
        }

        /**
         * @return object
         */
        static public function new_instance(){
            $class_name = get_called_class();
            $instance = new $class_name();
            return $instance;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        /**
         * @return void
         */
        protected function __construct(){
            if(is_callable([$this, 'loader'])){
                call_user_func([$this, 'loader']);
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        /**
         * @return string
         */
        public function get_class_name(){
            return get_called_class();
        }

        /**
         * @return string
         */
        public function __prefix($str = ''){
            $name = $this->get_class_name();
            return __str_prefix($str, $name);
        }

        /**
         * @return string
         */
        public function __slug($str = ''){
            $name = $this->get_class_name();
            return __str_slug($str, $name);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
