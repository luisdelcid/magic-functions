<?php

/**
 * This function’s access is marked private. This means it is not intended for use by plugin or theme developers, only in other core functions.
 *
 * @return string
 */
function ___caller_class(){
    $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    if(3 > count($debug)){
        return '';
    }
    $caller = shortcode_atts([
        'args' => [],
        'class' => '',
        'file' => '',
        'function' => '',
		'line' => 0,
        'object' => null,
        'type' => '',
    ], $debug[2]);
    return $caller['class'];
}

/**
 * This function’s access is marked private. This means it is not intended for use by plugin or theme developers, only in other core functions.
 *
 * @return string
 */
function ___is_subclass($class = ''){
    return is_subclass_of($class, 'Magic_Class'); // Hardcoded.
}

/*$called_class = ___caller_class();
if(___is_subclass($called_class)){

}*/

class Magic_Class {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private $instances = [];

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return self
	 */
	static public function get_instance(){
		$class = get_called_class();
        $class_name = md5($class);
		if(isset(self::$instances[$class_name])){
			return self::$instances[$class_name];
		}
		self::$instances[$class_name] = new $class;
		return self::$instances[$class_name];
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return void
	 */
	protected function __construct(){
        if(is_callable([$this, 'load'])){
			call_user_func([$this, 'load']);
		}
	}

    /**
     * @return string
     */
    protected function caller_file(){
    	$debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    	if(2 > count($debug)){
    		return '';
    	}
    	return (isset($debug[1]['file']) ? $debug[1]['file'] : '');
    }

    /**
     * @return string
     */
    protected function enqueue($filename = '', $deps = [], $in_footer_l10n_media = true){ // Le está agregando el main...
    	$mimes = [
    		'css' => 'text/css',
    		'js' => 'application/javascript',
    	];
    	$filename = wp_basename($filename);
    	$filetype = wp_check_filetype($filename, $mimes);
    	if(!$filetype['type']){
    		return '';
    	}
        $caller_file = $this->caller_file();
        $file = plugin_dir_path($caller_file) . $filename; // Relative to the caller file.
    	if(!file_exists($file)){
    		return '';
    	}
    	$handle = wp_basename($filename, '.' . $filetype['ext']);
        if('main' === $handle){
            $handle = ''; // Fix.
        }
        $handle = $this->slug($handle);
    	$is_script = false;
    	if('application/javascript' === $filetype['type']){
    		$deps[] = $this->helper->slug('singleton'); // Parent.
    		$in_footer_media = true;
    		$is_script = true;
    		$l10n = [];
    		if($this->helper->is_associative_array($in_footer_l10n_media)){
    			$l10n = $in_footer_l10n_media;
    		} else {
                $in_footer_media = (bool) $in_footer_l10n_media;
            }
    	} else { // text/css
    		$in_footer_media = 'all';
    		if(is_string($in_footer_l10n_media)){
    			$in_footer_media = $in_footer_l10n_media;
    		}
    	}
    	$this->helper->local_enqueue($handle, $file, $deps, $in_footer_media);
        if(!$is_script){
            return $handle;
        }
        if(!$l10n){
            return $handle;
        }
        $object_name = $this->helper->canonicalize($handle);
        wp_localize_script($handle, $object_name . '_l10n', $l10n);
        $data = $this->helper->prefix(false) . '().singleton(\'' . $object_name . '\');';
        wp_add_inline_script($handle, $data);
    	return $handle;
    }

	/**
	 * @return void
	 */
	protected function prefix($str = ''){
        $class = get_called_class();
        $reflection = new \ReflectionClass($class);
        $class = $reflection->getShortName();
        return $this->helper->str_prefix($str, $class);
	}

	/**
	 * @return void
	 */
	protected function slug($str = ''){
        $class = get_called_class();
        $reflection = new \ReflectionClass($class);
        $class = $reflection->getShortName();
        return $this->helper->str_slug($str, $class);
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // hooks
    //
	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * @return string
     */
    function add_action($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        return $this->add_filter($hook_name, $callback, $priority, $accepted_args);
    }

    /**
     * @return string
     */
    function add_action_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        return $this->add_filter_once($hook_name, $callback, $priority, $accepted_args);
    }

    /**
     * @return string
     */
    function add_filter($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $hook_name = $this->hook_name($hook_name);
        return $this->on($hook_name, $callback, $priority, $accepted_args);
    }

    /**
     * @return string
     */
    function add_filter_once($hook_name = '', $callback = null, $priority = 10, $accepted_args = 1){
        $hook_name = $this->hook_name($hook_name);
        return $this->one($hook_name, $callback, $priority, $accepted_args);
    }

    /**
     * @return mixed
     */
    function apply_filters($hook_name = '', $value = null, ...$arg){
    	$hook_name = $this->hook_name($hook_name);
        return apply_filters($hook_name, $value, ...$arg);
    }

    /**
     * @return bool
     */
    function did_action($hook_name = ''){
    	$hook_name = $this->hook_name($hook_name);
    	return did_action($hook_name);
    }

    /**
     * @return bool
     */
    function did_filter($hook_name = ''){
    	$hook_name = $this->hook_name($hook_name);
    	return did_filter($hook_name);
    }

    /**
     * @return void
     */
    function do_action($hook_name = '', ...$arg){
    	$hook_name = $this->hook_name($hook_name);
    	do_action($hook_name, ...$arg);
    }

    /**
     * @return void
     */
    function do_action_ref_array($hook_name = '', $args = []){
        global $wp_filter, $wp_actions, $wp_current_filter;
        $hook_name = $this->hook_name($hook_name);
        if(!isset($wp_actions[$hook_name])){
            $wp_actions[$hook_name] = 1;
        } else {
            ++ $wp_actions[$hook_name];
        }
        if(isset($wp_filter['all'])){ // Do 'all' actions first.
            $wp_current_filter[] = $hook_name;
            $all_args = func_get_args(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
            _wp_call_all_hook($all_args);
        }
        if(!isset($wp_filter[$hook_name])){
            if(isset($wp_filter['all'])){
                array_pop($wp_current_filter);
            }
            return;
        }
        if(!isset($wp_filter['all'])){
            $wp_current_filter[] = $hook_name;
        }
        $wp_filter[$hook_name]->do_action($args);
        array_pop($wp_current_filter);
    }

    /**
     * @return bool
     */
    function doing_action($hook_name = ''){
        return $this->doing_filter($hook_name);
    }

    /**
     * @return bool
     */
    function doing_filter($hook_name = ''){
    	$hook_name = $this->hook_name($hook_name);
        return doing_filter($hook_name);
    }

    /**
     * @return bool|int
     */
    function has_action($hook_name = '', $callback = false){
        return $this->has_filter($hook_name, $callback);
    }

    /**
     * @return bool|int
     */
    function has_filter($hook_name = '', $callback = false){
    	$hook_name = $this->hook_name($hook_name);
        return has_filter($hook_name, $callback);
    }

    /**
     * @return string
     */
    function hook_name($hook_name = ''){
        return $this->prefix($hook_name);
    }

    /**
     * @return bool
     */
    function remove_action($hook_name = '', $callback = null, $priority = 10){
        return $this->remove_filter($hook_name, $callback, $priority);
    }

    /**
     * @return bool
     */
    function remove_filter($hook_name = '', $callback = null, $priority = 10){
    	$hook_name = $this->hook_name($hook_name);
        return remove_filter($hook_name, $callback, $priority);
    }

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}
