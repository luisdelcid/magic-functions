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
 * @return string
 */
function __add_query_arg(key, value, url){
	var a = {}, href = '';
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
		var search = [], search_object = __parse_str(a.search);
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
	var a = {}, href = '';
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
		var search = [], search_object = __parse_str(a.search);
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
 * @return mixed
 */
function __apply_filters(hook_name = '', value = null, ...arg){
	return wp.hooks.applyFilters(hook_name, value, ...arg);
}

/**
 * @return array
 */
function __current_utm(){
	var object_name = __prefix('utm');
    return 'undefined' !== typeof(window[object_name]) ? window[object_name] : {};
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
 * @return string
 */
function __document_visibility(){
	var hidden = '';
	if('undefined' !== typeof(document.hidden)){ // Opera 12.10 and Firefox 18 and later support
		hidden = 'hidden';
	} else if('undefined' !== typeof(document.webkitHidden)){
		hidden = 'webkitHidden';
	} else if('undefined' !== typeof(document.msHidden)){
		hidden = 'msHidden';
	} else if('undefined' !== typeof(document.mozHidden)){ // Deprecated
		hidden = 'mozHidden';
	}
    return hidden ? document[hidden] : false;
}

/**
 * @return string
 */
function __document_visibility_change(){
	__do_action('visibilitychange', __document_visibility()); // hidden
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
 * @return void
 */
function __enable_document_visibility(){
	jQuery(function($){
		var event_name = __document_visibility_change_event(), events = jQuery._data(document, 'events');
		if('undefined' !== typeof(events[event_name])){
			jQuery.each(events[event_name], function(index, value){
				if('__document_visibility_change' === value.handler.name){
					return;
				}
			});
		}
		jQuery(document).on(event_name, __document_visibility_change);
	});
}

/**
 * @return string
 */
function __error_url(error){
 	if(!error instanceof Error){
 		return '';
 	}
 	if('undefined' === typeof(error.stack)){
 		return '';
 	}
 	var urls = [];
 	jQuery.each(error.stack.split("\n"), function(index, value){
 		var result = (/(http[s]?:\/\/.*):\d+:\d+/g).exec(value); // array or null
 		if(result && result.length > 1){
 			urls.push(result[1]);
 		}
 	});
 	if(urls.length < 2){
 		return '';
 	}
	return urls[1];
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
 * @return bool
 */
function __is_false(data){
	return (-1 < jQuery.inArray(String(data), ['0', 'false', 'off']));
}

/**
 * @return bool
 */
function __is_true(data){
	return (-1 < jQuery.inArray(String(data), ['1', 'on', 'true']));
}

/**
 * @return string
 */
function __mu_plugins_url(){
	var object_name = __prefix('l10n');
	var object = 'undefined' !== typeof(window[object_name]) ? window[object_name] : {};
    return 'undefined' !== typeof(object.mu_plugins_url) ? object.mu_plugins_url : '';
}

/**
 * @return string
 */
function __namespace(){
    return 'luisdelcid/magicfunctions/__'; // Hardcoded. The unique namespace identifying the callback in the form `vendor/plugin/function`.
}

/**
 * @return object
 */
function __parse_str(str){
	var i = 0, search_object = {}, search_array = str.replace('?', '').split('&');
	for(i = 0; i < search_array.length; i ++){
		search_object[search_array[i].split('=')[0]] = search_array[i].split('=')[1];
	}
	return search_object;
}

/**
 * @return object|string
 */
function __parse_url(url, component){
	var a = {}, components = {}, keys = ['protocol', 'hostname', 'port', 'pathname', 'search', 'hash'];
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
function __plugin_folder(file = ''){
 	var fake_function = null, folder = '', mu_plugins_url = __mu_plugins_url(), path = '', plugins_url = __plugins_url();
 	if(!file){
 		try {
 			fake_function();
 		} catch(error){
 			file = __error_url(error);
 		}
 	} else if(file instanceof Error){
 		file = __error_url(file);
 	}
 	if(!file){
 		return '';
 	}
 	if(0 === file.indexOf(mu_plugins_url)){
         path = file.substr(mu_plugins_url.length, file.length - 1);
     } else if(0 === file.indexOf(plugins_url)){
         path = file.substr(plugins_url.length, file.length - 1);
     } else {
 		return '';
 	}
 	folder = path.split('/', 3);
 	if(folder.length < 3){
 		return '';
 	}
 	return folder[1];
}

/**
 * @return string
 */
function __plugins_url(){
	var object_name = __prefix('l10n');
	var object = 'undefined' !== typeof(window[object_name]) ? window[object_name] : {};
    return 'undefined' !== typeof(object.plugins_url) ? object.plugins_url : '';
}

/**
 * This function assumes that the prefix value is a valid class name.
 *
 * @return string
 */
function __prefix(str = '', prefix = 'magic_functions'){ // Hardcoded.
    if(!prefix){
		return '';
	}
	prefix = prefix.toLowerCase().replaceAll('\\', '-'); // fix namespaces
    if(false === str){
        return prefix;
    }
    if(prefix === str){
        return str;
    }
    prefix += '_';
    if(0 === str.indexOf(prefix)){
        return str;
    }
    return prefix + str;
}

/**
 * @return int
 */
function __rem_to_px(count){
	var unit = parseInt(jQuery('html').css('font-size'));
    if(!unit){
        unit = 16;
    }
	if(typeof count !== 'undefined' && count > 0){
		return (count * unit);
	} else {
		return unit;
	}
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
function __remove_filter(hook_name = ''){
	return wp.hooks.removeFilter(hook_name, __namespace());
}

/**
 * @return string
 */
function __site_url(){
	var object_name = __prefix('l10n');
	var object = 'undefined' !== typeof(window[object_name]) ? window[object_name] : {};
    return 'undefined' !== typeof(object.site_url) ? object.site_url : '';
}

/**
 * This function assumes that the slug value is a valid class name.
 *
 * @return string
 */
function __slug(str = '', slug = 'magic-functions'){ // Hardcoded.
	if(!slug){
		return '';
	}
	slug = slug.toLowerCase().replaceAll('_', '-').replaceAll('\\', '-'); // fix canonicalized and namespaces
	if(true === str){
		return slug + '-';
	}
    if(!str){
		return slug;
	}
    if(slug === str){
        return str;
    }
    slug += '-';
    if(0 === str.indexOf(slug)){
        return str;
    }
    return slug + str;
}

/**
 * @return void
 */
function __test(){
	console.log('Hello, World!');
}
