var __singleton = class __Singleton {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static #instances = [];
	static #is_internal_constructing = false;

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return this
	 */
	static get_instance(){
        if(_.isUndefined(__singleton.#instances[this.name])){ // Hardcoded.
            __singleton.#is_internal_constructing = true; // Hardcoded.
		    __singleton.#instances[this.name] = new this; // Hardcoded.
        }
		return __singleton.#instances[this.name]; // Hardcoded.
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return void
	 */
	constructor(){
        this.constructor_name = this.constructor.name;
        if(__singleton.#is_internal_constructing){ // Hardcoded.
            if(_.isFunction(this.loader)){
                this.loader();
            }
		} else {
			throw new TypeError('This class is not constructable.');
        }
        __singleton.#is_internal_constructing = false; // Hardcoded.
	}

	/**
	 * @return mixed
	 */
	l10n(key = ''){
        var object_name = this.prefix('l10n');
        return __object_property(key, object_name);
    }

	/**
	 * @return string
	 */
	get_name(){
		return this.constructor_name;
	}

	/**
	 * @return string
	 */
	prefix(str = ''){
		var name = this.get_name();
        return __str_prefix(str, name);
	}

	/**
	 * @return string
	 */
	slug(str = ''){
		var name = this.get_name();
        return __str_slug(str, name);
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // Hooks
    //
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * @return void
     */
    add_action(hook_name = '', callback = null, priority = 10){
        hook_name = this.prefix(hook_name);
        wp.hooks.addAction(hook_name, __namespace(), callback, priority);
    }

    /**
     * @return void
     */
    add_filter(hook_name = '', callback = null, priority = 10){
        hook_name = this.prefix(hook_name);
        wp.hooks.addFilter(hook_name, __namespace(), callback, priority);
    }

    /**
     * @return mixed
     */
    apply_filters(hook_name = '', value = null, ...arg){
        hook_name = this.prefix(hook_name);
        return wp.hooks.applyFilters(hook_name, value, ...arg);
    }

    /**
     * @return int|void
     */
    did_action(hook_name = ''){
        hook_name = this.prefix(hook_name);
        return wp.hooks.didAction(hook_name);
    }

    /**
     * @return int|void
     */
    did_filter(hook_name = ''){
        hook_name = this.prefix(hook_name);
        return wp.hooks.didFilter(hook_name);
    }

    /**
     * @return void
     */
    do_action(hook_name = '', ...arg){
        hook_name = this.prefix(hook_name);
        wp.hooks.doAction(hook_name, ...arg);
    }

    /**
     * @return void
     */
    doing_action(hook_name = ''){
        hook_name = this.prefix(hook_name);
        wp.hooks.doingAction(hook_name);
    }

    /**
     * @return void
     */
    doing_filter(hook_name = ''){
        hook_name = this.prefix(hook_name);
        wp.hooks.doingFilter(hook_name);
    }

    /**
     * @return bool
     */
    has_action(hook_name = ''){
        hook_name = this.prefix(hook_name);
        return wp.hooks.hasAction(hook_name, __namespace());
    }

    /**
     * @return bool
     */
    has_filter(hook_name = ''){
        hook_name = this.prefix(hook_name);
        return wp.hooks.hasFilter(hook_name, __namespace());
    }

    /**
     * @return int|void
     */
    remove_action(hook_name = ''){
        hook_name = this.prefix(hook_name);
        return wp.hooks.removeAction(hook_name, __namespace());
    }

    /**
     * @return int|void
     */
    remove_filter(hook_name = ''){
        hook_name = this.prefix(hook_name);
        return wp.hooks.removeFilter(hook_name, __namespace());
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // REST API
    //
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return string
	 */
	rest_namespace(version = 1){
		var namespace = '',
            plugin_slug = '',
            url = __caller_url(1); // One level above.
        plugin_slug = __plugin_slug('', url);
        if(!plugin_slug){
            return '';
        }
        version = __absint(version);
        namespace = plugin_slug + '/v' + version;
        return namespace;
	}

	/**
	 * @return string
	 */
	rest_route(route = ''){
		var plugin_slug = '',
            search = '',
            slug = '',
            url = __caller_url(1); // One level above.
        plugin_slug = __plugin_slug('', url);
        if(!plugin_slug){
            return '';
        }
        slug = this.slug();
        search = plugin_slug + '-'; // With trailing dash.
        if(slug.startsWith(search)){
            slug = slug.replace(search, '');
        }
        if(!route){
            return slug;
        }
        return __rtrim(slug, '/') + '/' + __ltrim(route, '/');
	}

	/**
	 * @return string
	 */
	rest_url(route = ''){
        route = this.rest_route(route);
        if(!route){
            return '';
        }
        return (wpApiSettings.root + this.rest_namespace() + '/' + route);
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}
