<?php

class __Singleton {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private $instances = [];

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return self
	 */
	static public function get_instance(){
		$class = get_called_class();
		$md5 = md5($class);
		if(isset(self::$instances[$md5])){
			return self::$instances[$md5];
		}
		self::$instances[$md5] = new $class;
		return self::$instances[$md5];
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

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}
