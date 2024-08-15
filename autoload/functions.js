// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Hardcoded
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return __Singleton|null
 */
function __get_instance(class_name = ''){
    if(!class_name){
        return null;
    }
    if(!_.isFunction(window[class_name])){
        return null;
    }
    if(!__is_subclass_of(window[class_name], '__singleton')){ // Hardcoded.
        return null;
    }
    return window[class_name].get_instance();
}

/**
 * @return string
 */
function __namespace(){
    return 'luisdelcid/magic-functions/__'; // The unique namespace identifying the callback in the form `vendor/plugin/function`. Hardcoded.
}

/**
 * @return string
 */
function __prefix(){
    return 'magic_functions'; // Hardcoded.
}

/**
 * @return string
 */
function __slug(){
    return 'magic-functions'; // Hardcoded.
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Debug
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __caller_url(index = 0){
    var debug_backtrace = null;
    index = __absint(index) + 1;
    debug_backtrace = __debug_backtrace(index);
    if(_.isNull(debug_backtrace)){
        return '';
    }
    if(_.isUndefined(debug_backtrace.fileName)){
        return '';
    }
    return debug_backtrace.fileName;
}

/**
 * @return object
 */
function __debug_backtrace(index = 0){
    var backtrace = [],
        error = null,
        fake_function = null,
        limit = 0;
    try {
        fake_function();
    } catch(e){
        error = e;
    }
    backtrace = __debug_context(error);
    if(_.isEmpty(backtrace)){
        return null;
    }
    index = __absint(index) + 1;
    limit = index + 1;
    if(limit > backtrace.length){
        return null;
    }
    return backtrace[index];
}

/**
 * @return array
 */
function __debug_context(error = null){
    var backtrace = [];
    if(!_.isError(error)){
        return backtrace;
    }
    jQuery.each(ErrorStackParser.parse(error), function(index, value){
        var stackframe = {
            args: [],
            columnNumber: 0, 
            fileName: '',
            functionName: '',
            isEval: false,
            isNative: false,
            lineNumber: 0,
            source: '',
        };
        jQuery.each(stackframe, function(key, property){
            if(!_.isUndefined(value[key])){
                stackframe[key] = value[key];
            } 
        });
        backtrace.push(stackframe);
    });
    return backtrace;
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Hooks
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return void
 */
function __add_action(hook_name = '', callback = null, priority = 10){
    wp.hooks.addAction(hook_name, __namespace(), callback, priority);
}

/**
 * @return void
 */
function __add_filter(hook_name = '', callback = null, priority = 10){
    wp.hooks.addFilter(hook_name, __namespace(), callback, priority);
}

/**
 * @return mixed
 */
function __apply_filters(hook_name = '', value = null, ...arg){
    return wp.hooks.applyFilters(hook_name, value, ...arg);
}

/**
 * @return void
 */
function __current_action(){
    wp.hooks.currentAction();
}

/**
 * @return void
 */
function __current_filter(){
    wp.hooks.currentFilter();
}

/**
 * @return int|void
 */
function __did_action(hook_name = ''){
    return wp.hooks.didAction(hook_name);
}

/**
 * @return int|void
 */
function __did_filter(hook_name = ''){
    return wp.hooks.didFilter(hook_name);
}

/**
 * @return void
 */
function __do_action(hook_name = '', ...arg){
    wp.hooks.doAction(hook_name, ...arg);
}

/**
 * @return void
 */
function __doing_action(hook_name = ''){
    wp.hooks.doingAction(hook_name);
}

/**
 * @return void
 */
function __doing_filter(hook_name = ''){
    wp.hooks.doingFilter(hook_name);
}

/**
 * @return bool
 */
function __has_action(hook_name = ''){
    return wp.hooks.hasAction(hook_name, __namespace());
}

/**
 * @return bool
 */
function __has_filter(hook_name = ''){
    return wp.hooks.hasFilter(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_action(hook_name = ''){
    return wp.hooks.removeAction(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_all_actions(hook_name = ''){
    return wp.hooks.removeAllActions(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_all_filters(hook_name = ''){
    return wp.hooks.removeAllFilters(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_filter(hook_name = ''){
    return wp.hooks.removeFilter(hook_name, __namespace());
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Magic methods
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @see JavaScript Magic Methods
 * @link https://gist.github.com/loilo/4d385d64e2b8552dcc12a0f5126b6df8
 *
 * @return object
 */
function __add_magic_methods(clazz){
    /**
     * A toggle switch for the __isset method
     * Needed to control "prop in instance" inside of getters
     */
    let issetEnabled = true;
    const classHandler = Object.create(null);
    /**
     * Trap for class instantiation
     */
    classHandler.construct = (target, args, receiver) => {
        /**
         * Wrapped class instance
         */
        const instance = Reflect.construct(target, args, receiver);
        /**
         * Instance traps
         */
        const instanceHandler = Object.create(null);
        /**
         * __get()
         * Catches "instance.property"
         */
        const get = Object.getOwnPropertyDescriptor(clazz.prototype, '__get');
        if(get){
            instanceHandler.get = (target, name, receiver) => {
                // We need to turn off the __isset() trap for the moment to establish compatibility with PHP behaviour
                // PHP's __get() method doesn't care about its own __isset() method, so neither should we
                issetEnabled = false;
                const exists = Reflect.has(target, name);
                issetEnabled = true;
                if(exists){
                    return Reflect.get(target, name, receiver);
                } else {
                    return get.value.call(target, name);
                }
            }
        }
        /**
         * __set()
         * Catches "instance.property = ..."
         */
        const set = Object.getOwnPropertyDescriptor(clazz.prototype, '__set');
        if(set){
            instanceHandler.set = (target, name, value, receiver) => {
                if(name in target){
                    Reflect.set(target, name, value, receiver);
                } else {
                    return target.__set.call(target, name, value);
                }
            }
        }
        /**
         * __isset()
         * Catches "'property' in instance"
         */
        const isset = Object.getOwnPropertyDescriptor(clazz.prototype, '__isset');
        if(isset){
            instanceHandler.has = (target, name) => {
                if(!issetEnabled){
                    return Reflect.has(target, name);
                }
                return isset.value.call(target, name);
            }
        }
        /**
         * __unset()
         * Catches "delete instance.property"
         */
        const unset = Object.getOwnPropertyDescriptor(clazz.prototype, '__unset');
        if(unset){
            instanceHandler.deleteProperty = (target, name) => {
                return unset.value.call(target, name);
            }
        }
        return new Proxy(instance, instanceHandler);
    }
    /**
     * __getStatic()
     * Catches "class.property"
     */
    if(Object.getOwnPropertyDescriptor(clazz, '__getStatic')){
        classHandler.get = (target, name, receiver) => {
            if(name in target){
                return target[name];
            } else {
                return target.__getStatic.call(receiver, name);
            }
        }
    }
    /**
     * __setStatic()
     * Catches "class.property = ..."
     */
    if(Object.getOwnPropertyDescriptor(clazz, '__setStatic')){
        classHandler.set = (target, name, value, receiver) => {
            if(name in target){
                return target[name];
            } else {
                return target.__setStatic.call(receiver, name, value);
            }
        }
    }
    return new Proxy(clazz, classHandler);
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Miscellaneous
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return int
 */
function __absint(maybeint = 0){
    if(!_.isNumber(maybeint)){
        return 0; // Make sure the value is numeric to avoid casting objects, for example, to int 1.
    }
    return Math.abs(parseInt(maybeint));
}

/**
 * @return bool
 */
function __is_false(data){
    return (-1 < jQuery.inArray(String(data), ['0', 'false', 'off']));
}

/**
 * @return bool
 */
function __is_subclass_of(func = null, class_name = ''){
    if(!_.isFunction(func)){
        return false;
    }
    if(!class_name){
        return false;
    }
    while(func && func !== Function.prototype){
        if(func === window[class_name]){
            return true;
        }
        func = Object.getPrototypeOf(func);
    }
    return false;
}

/**
 * @return bool
 */
function __is_true(data){
    return (-1 < jQuery.inArray(String(data), ['1', 'on', 'true']));
}

/**
 * @return mixed
 */
function __object_property(key = '', object_name = ''){
    if(!key){
        return null;
    }
    if(!object_name){
        object_name = __str_prefix('l10n');
    }
    if(_.isUndefined(window[object_name])){
        return null;
    }
    if(_.isUndefined(window[object_name][key])){
        return null;
    }
    return window[object_name][key];
}

/**
 * @return int
 */
function __rem_to_px(count){
    var unit = parseInt(jQuery('html').css('font-size'));
    if(!unit){
        unit = 16;
    }
    if(!_.isNumber(count)){
        return unit;
    }
    if(count > 0){
        return (count * unit);
    }
    return unit;
}

/**
 * @return void
 */
function __test(){
    console.log('Hello, World!');
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Page Visibility API
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __document_has_visibility_event(){
    var event_name = __document_visibility_change_event(),
        events = jQuery._data(document, 'events'),
        has_visibility_event = false;
    if('undefined' !== typeof(events[event_name])){
        jQuery.each(events[event_name], function(index, value){
            if('__document_visibility_change' !== value.handler.name){ // Hardcoded.
                return true;
            }
            has_visibility_event = true;
            return false;
        });
    }
    return has_visibility_event;
}

/**
 * @return string
 */
function __document_visibility_change_event(){
    var visibilityChange = '';
    if('undefined' !== typeof(document.hidden)){ // Opera 12.10 and Firefox 18 and later support
        visibilityChange = 'visibilitychange';
    } else if('undefined' !== typeof(document.webkitHidden)){
        visibilityChange = 'webkitvisibilitychange';
    } else if('undefined' !== typeof(document.msHidden)){
        visibilityChange = 'msvisibilitychange';
    } else if('undefined' !== typeof(document.mozHidden)){ // Deprecated
        visibilityChange = 'mozvisibilitychange';
    }
    return visibilityChange;
}

/**
 * @return bool
 */
function __is_document_hidden(){
    var hidden = false;
    if('undefined' !== typeof(document.hidden)){ // Opera 12.10 and Firefox 18 and later support
        hidden = document.hidden;
    } else if('undefined' !== typeof(document.webkitHidden)){
        hidden = document.webkitHidden;
    } else if('undefined' !== typeof(document.msHidden)){
        hidden = document.msHidden;
    } else if('undefined' !== typeof(document.mozHidden)){ // Deprecated
        hidden = document.mozHidden;
    }
    return hidden;
}

/**
 * @return void
 */
function __listen_for_visibilitychange(){
    var $this = this;
    jQuery(function($){
        var event_name = __document_visibility_change_event();
        if(__document_has_visibility_event()){
            return;
        }
        jQuery(document).on(event_name, $this, __do_visibilitychange_action); // Hardcoded.
    });
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __do_visibilitychange_action(event){
    var $this = event.data;
    __do_action('visibilitychange', __is_document_hidden()); // Hidden.
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugin hooks
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return void
 */
function __add_plugin_action(hook_name = '', callback = null, priority = 10){
    hook_name = __plugin_hook_name(hook_name);
    wp.hooks.addAction(hook_name, __namespace(), callback, priority);
}

/**
 * @return void
 */
function __add_plugin_filter(hook_name = '', callback = null, priority = 10){
    hook_name = __plugin_hook_name(hook_name);
    wp.hooks.addFilter(hook_name, __namespace(), callback, priority);
}

/**
 * @return mixed
 */
function __apply_plugin_filters(hook_name = '', value = null, ...arg){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.applyFilters(hook_name, value, ...arg);
}

/**
 * @return void
 */
function __current_plugin_action(){
    wp.hooks.currentAction();
}

/**
 * @return void
 */
function __current_plugin_filter(){
    wp.hooks.currentFilter();
}

/**
 * @return int|void
 */
function __did_plugin_action(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.didAction(hook_name);
}

/**
 * @return int|void
 */
function __did_plugin_filter(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.didFilter(hook_name);
}

/**
 * @return void
 */
function __do_plugin_action(hook_name = '', ...arg){
    hook_name = __plugin_hook_name(hook_name);
    wp.hooks.doAction(hook_name, ...arg);
}

/**
 * @return void
 */
function __doing_plugin_action(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    wp.hooks.doingAction(hook_name);
}

/**
 * @return void
 */
function __doing_plugin_filter(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    wp.hooks.doingFilter(hook_name);
}

/**
 * @return bool
 */
function __has_plugin_action(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.hasAction(hook_name, __namespace());
}

/**
 * @return bool
 */
function __has_plugin_filter(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.hasFilter(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_plugin_action(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.removeAction(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_all_plugin_actions(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.removeAllActions(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_all_plugin_filters(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.removeAllFilters(hook_name, __namespace());
}

/**
 * @return int|void
 */
function __remove_plugin_filter(hook_name = ''){
    hook_name = __plugin_hook_name(hook_name);
    return wp.hooks.removeFilter(hook_name, __namespace());
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __plugin_hook_name(hook_name = ''){
    var url = __caller_url(2); // Two levels above.
    hook_name = __plugin_prefix(hook_name, url);
    return hook_name;
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Plugins
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __plugin_folder(url = ''){
    var folder = '',
        mu_plugins_url = __mu_plugins_url(),
        path = '',
        plugins_url = __plugins_url();
    if(!url){
        url = __caller_url(1); // One level above.
    }
    if(!url){
        return '';
    }
    if(0 === url.indexOf(mu_plugins_url)){
         path = url.substr(mu_plugins_url.length, url.length - 1); // File is a must-use plugin.
     } else if(0 === url.indexOf(plugins_url)){
         path = url.substr(plugins_url.length, url.length - 1); // File is a plugin.
     } else {
        return ''; // File is not a plugin.
    }
    folder = path.split('/', 3);
    if(folder.length < 3){
        return ''; // The entire plugin consists of just a single PHP file, like Hello Dolly or file is the plugin's main file.
    }
    return folder[1];
}

/**
 * @return string
 */
function __plugin_prefix(str = '', url = ''){
    var plugin_folder = '';
    if(!url){
        url = __caller_url(1); // One level above.
    }
    plugin_folder = __plugin_folder(url);
    if(!plugin_folder){
        return '';
    }
    return __str_prefix(str, plugin_folder);
}

/**
 * @return string
 */
function __plugin_slug(str = '', url = ''){
    var plugin_folder = '';
    if(!url){
        url = __caller_url(1); // One level above.
    }
    plugin_folder = __plugin_folder(url);
    if(!plugin_folder){
        return '';
    }
    return __str_slug(str, plugin_folder);
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Strings
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __canonicalize(str = ''){
    str = __sanitize_title(str);
    str = str.replaceAll('-', '_');
    return str;
}

/**
 * https://github.com/locutusjs/locutus/blob/master/src/php/strings/ltrim.js
 *
 * @return string
 */
function __ltrim(str, charlist){
    charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([[\]().?/*{}+$^:])/g, '$1');
    const re = new RegExp('^[' + charlist + ']+', 'g');
    return (str + '').replace(re, '');
}

/**
 * TODO: Improve.
 *
 * @return string
 */
function __remove_accents(str = ''){
    str = str.replace(new RegExp('[àáâãäå]', 'g'), 'a');
    str = str.replace(new RegExp('[èéêë]', 'g'), 'e');
    str = str.replace(new RegExp('[ìíîï]', 'g'), 'i');
    str = str.replace(new RegExp('[òóôõö]', 'g'), 'o');
    str = str.replace(new RegExp('[ùúûü]', 'g'), 'u');
    str = str.replace(new RegExp('[ñ]', 'g'), 'n');
    return str;
}

/**
 * @return string
 */
function __remove_whitespaces(str = ''){
    str = str.replace(/[\r\n\t ]+/g, ' ').trim();
    return str;
}

/**
 * https://github.com/locutusjs/locutus/blob/master/src/php/strings/rtrim.js
 *
 * @return string
 */
function __rtrim(str, charlist){
    charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([[\]().?/*{}+$^:])/g, '\\$1');
    const re = new RegExp('[' + charlist + ']+$', 'g');
    return (str + '').replace(re, '');
}

/**
 * @return string
 */
function __sanitize_title(str = ''){
    str = __remove_accents(str);
    str = __sanitize_title_with_dashes(str);
    return str;
}

/**
 * TODO: Improve.
 *
 * @return string
 */
function __sanitize_title_with_dashes(str = ''){
    str = str.toLowerCase();
    str = str.replace(/\s+/g, ' ');
    str = str.trim();
    str = str.replaceAll(' ', '-');
    str = str.replace(/[^a-z0-9-_]/g, '');
    return str;
}

/**
 * @return string
 */
function __str_prefix(str = '', prefix = ''){
    prefix = prefix.replaceAll('\\', '_'); // Fix namespaces.
    prefix = __canonicalize(prefix);
    prefix = __rtrim(prefix, '_');
    if(!prefix){
        prefix = __prefix();
    }
    str = __remove_whitespaces(str);
    if(!str){
        return prefix;
    }
    if(0 === str.indexOf(prefix)){
        return str; // Text is already prefixed.
    }
    return prefix + '_' + str;
}

/**
 * @return string
 */
function __str_slug(str = '', slug = ''){
    slug = slug.replaceAll('_', '-'); // Fix canonicalized.
    slug = slug.replaceAll('\\', '-'); // Fix namespaces.
    slug = __sanitize_title(slug);
    slug = __rtrim(slug, '-');
    if(!slug){
        slug = __slug();
    }
    str = __remove_whitespaces(str);
    if(!str){
        return slug;
    }
    if(0 === str.indexOf(slug)){
        return str; // Text is already slugged.
    }
    return slug + '-' + str;
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Urchin Tracking Module
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __current_utm_param(name = ''){
    var utm_params = __current_utm_params();
    if(_.isUndefined(utm_params[name])){
        return '';
    }
    return utm_params[name];
}

/**
 * @return object
 */
function __current_utm_params(){
    if(__at_least_one_utm_get_param()){
        return __utm_params_from_get(); // 1. GET
    }
    return __utm_params_from_cookie(); // 2. COOKIE
}

/**
 * @return string
 */
function __utm_param_name(name = ''){
    var utm_pairs = __utm_pairs();
    if(_.isUndefined(utm_pairs[name])){
        return '';
    }
    return utm_pairs[name];
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __at_least_one_utm_get_param(){
    var at_least_one = false,
        utm_params = __utm_params_from_get();
    jQuery.each(utm_params, function(key, value){
        if(!value){
            return true; // Continue.
        }
        at_least_one = true;
        return false; // Break.
    });
    return utm_params;
}

/**
 * @return string
 */
function __utm_cookie_name(){
    return __str_prefix('utm_parameters');
}

/**
 * @return array
 */
function __utm_keys(){
    return _.keys(__utm_pairs());
}

/**
 * @return object
 */
function __utm_pairs(){
    var utm_pairs = {
        utm_campaign: 'Name',
        utm_content: 'Content',
        utm_id: 'ID',
        utm_medium: 'Medium',
        utm_source: 'Source',
        utm_term: 'Term',
    };
    return utm_pairs;
}

/**
 * @return object
 */
function __utm_params_from_cookie(){
    var utm_params = {},
        value = wpCookies.get(__utm_cookie_name()),
        values = {};
    if(_.isUndefined(value) || _.isNull(value)){
        value = '';
    }
    values = __parse_str(value);
    jQuery.each(__utm_keys(), function(index, key){
        if(_.isUndefined(values[key])){
            utm_params[key] = '';
        } else {
            utm_params[key] = values[key];
        }
    });
    return utm_params;
}

/**
 * @return object
 */
function __utm_params_from_get(){
    var query_args = __get_query_args(),
        utm_params = {};
    jQuery.each(__utm_keys(), function(index, key){
        if(_.isUndefined(query_args[key])){
            utm_params[key] = '';
        } else {
            utm_params[key] = query_args[key];
        }
    });
    return utm_params;
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// URLs
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/**
 * @return string
 */
function __add_query_arg(key, value, url){
    var a = {},
        href = '';
    a = __get_a(url);
    if(a.protocol){
        href += a.protocol + '//';
    }
    if(a.hostname){
        href += a.hostname;
    }
    if(a.port){
        href += ':' + a.port;
    }
    if(a.pathname){
        if(a.pathname[0] !== '/'){
            href += '/';
        }
        href += a.pathname;
    }
    if(a.search){
        var search = [],
            search_object = __parse_str(a.search);
        jQuery.each(search_object, function(k, v){
            if(k != key){
                search.push(k + '=' + v);
            }
        });
        if(search.length > 0){
            href += '?' + search.join('&') + '&';
        } else {
            href += '?';
        }
    } else {
        href += '?';
    }
    href += key + '=' + value;
    if(a.hash){
        href += a.hash;
    }
    return href;
}

/**
 * @return string
 */
function __add_query_args(args, url){
    var a = {},
        href = '';
    a = __get_a(url);
    if(a.protocol){
        href += a.protocol + '//';
    }
    if(a.hostname){
        href += a.hostname;
    }
    if(a.port){
        href += ':' + a.port;
    }
    if(a.pathname){
        if(a.pathname[0] !== '/'){
            href += '/';
        }
        href += a.pathname;
    }
    if(a.search){
        var search = [],
            search_object = __parse_str(a.search);
        jQuery.each(search_object, function(k, v){
            if(!(k in args)){
                search.push(k + '=' + v);
            }
        });
        if(search.length > 0){
            href += '?' + search.join('&') + '&';
        } else {
            href += '?';
        }
    } else {
        href += '?';
    }
    jQuery.each(args, function(k, v){
        href += k + '=' + v + '&';
    });
    href = href.slice(0, -1);
    if(a.hash){
        href += a.hash;
    }
    return href;
}

/**
 * @return object
 */
function __get_a(url){
    var a = document.createElement('a');
    if('undefined' !== typeof(url) && '' !== url){
        a.href = url;
    } else {
        a.href = jQuery(location).attr('href');
    }
    return a;
}

/**
 * @return string
 */
function __get_query_arg(key, url){
    var search_object = {};
    search_object = __get_query_args(url);
    if('undefined' !== typeof(search_object[key])){
        return search_object[key];
    }
    return '';
}

/**
 * @return object
 */
function __get_query_args(url){
    var a = {};
    a = __get_a(url);
    if(a.search){
        return __parse_str(a.search);
    }
    return {};
}

/**
 * @return string
 */
function __mu_plugins_url(){
    var mu_plugins_url = __object_property('mu_plugins_url');
    return (_.isNull(mu_plugins_url) ? '' : mu_plugins_url);
}

/**
 * @return object
 */
function __parse_str(str){
    var i = 0, search_object = {},
        search_array = str.replace('?', '').split('&');
    for(i = 0; i < search_array.length; i ++){
        search_object[search_array[i].split('=')[0]] = search_array[i].split('=')[1];
    }
    return search_object;
}

/**
 * @return object|string
 */
function __parse_url(url, component){
    var a = {},
        components = {},
        keys = ['protocol', 'hostname', 'port', 'pathname', 'search', 'hash'];
    a = __get_a(url);
    if(typeof component === 'undefined' || component === ''){
        jQuery.map(keys, function(c){
            components[c] = a[c];
        });
        return components;
    } else if(jQuery.inArray(component, keys) !== -1){
        return a[component];
    } else {
        return '';
    }
}

/**
 * @return string
 */
function __plugins_url(){
    var plugins_url = __object_property('plugins_url');
    return (_.isNull(plugins_url) ? '' : plugins_url);
}

/**
 * @return string
 */
function __site_url(){
    var site_url = __object_property('site_url');
    return (_.isNull(site_url) ? '' : site_url);
}
