<?php

if(!class_exists('__Toolbox')){ // Hardcoded.
    class __Toolbox { // Hardcoded.

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        protected $active_tools = [], $available_tools = [], $dir = '', $meta_boxes = [], $namespace = '', $parent = '', $prefix = '', $slug = '', $title = '';

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	/**
    	 * @return array
    	 */
    	protected function get_active_tools(){
            $field_id = $this->get_field_id();
            $value = (array) get_option(__str_prefix('', $this->prefix), []);
            if(!isset($value[$field_id])){
                return [];
            }
            return (array) $value[$field_id];
        }

    	/**
    	 * @return string
    	 */
    	protected function get_field_id(){
            return __str_slug('available-tools', $this->slug);
        }

    	/**
    	 * @return array
    	 */
    	protected function get_fields(){
            if(!$this->available_tools){
                if(!is_dir($this->dir)){
                    return [
                        [
                            'columns' => 12,
                            'name' => translate('Available Tools'),
                            'std' => '<p>' . translate('The directory does not exist.') . '</p>',
                            'type' => 'custom_html',
                        ],
                    ];
                }
                return [
                    [
                        'columns' => 12,
                        'name' => translate('Available Tools'),
                        'std' => '<p>' . translate('Not available') . '.</p>',
                        'type' => 'custom_html',
                    ],
                ];
            }
            return [
                [
                    'columns' => 12,
                    'id' => $this->get_field_id(),
                    'name' => translate('Available Tools') . ' (' . count($this->available_tools) . ')',
                    'options' => array_combine($this->available_tools, $this->available_tools), // Fix options.
                    'type' => 'checkbox_list',
                ],
            ];
        }

    	/**
    	 * @return array
    	 */
    	protected function get_meta_box(){
            $meta_box = [
                'fields' => $this->get_fields(),
                'id' => $this->get_meta_box_id(),
                'settings_pages' => $this->get_settings_page_id(),
                'title' => translate('Tools'),
            ];
            if($this->meta_boxes){
                $meta_box['context'] = 'side';
            }
            return $meta_box;
        }

    	/**
    	 * @return string
    	 */
    	protected function get_meta_box_id($tool = ''){
            if(!$tool){
                return __str_slug('meta-box', $this->slug);
            }
            return __str_slug($tool . '-meta-box', $this->slug);
        }

    	/**
    	 * @return string
    	 */
    	protected function get_settings_page_id(){
            return __str_slug('', $this->slug);
        }

        /**
    	 * @return array
    	 */
    	protected function get_tool_fields($singleton = null){
            if(is_wp_error($singleton)){
                return [
                    [
                        'name' => translate('Something went wrong.'),
                        'std' => $singleton->get_error_message(),
                        'type' => 'custom_html',
                    ],
                ];
            }
            if(!is_callable([$singleton, 'tool_fields'])){
                return []; // Silence is golden.
            }
            $tool_fields = $singleton->tool_fields();
            if(!$tool_fields){
                return []; // Silence is golden.
            }
            foreach($tool_fields as $index => $tool_field){
                /*if(isset($tool_field['columns'])){
                    unset($tool_fields[$index]['columns']); // Fix columns.
                }*/
                $tool_fields[$index]['columns'] = 12;
                if(isset($tool_field['options'])){
                    if(!__is_associative_array($tool_field['options'])){
                        $tool_fields[$index]['options'] = array_combine($tool_field['options'], $tool_field['options']); // Fix options.
                    }
                }
            }
            return $tool_fields;
        }

    	/**
    	 * @return void
    	 */
    	protected function load_active_tools(){
            $active_tools = $this->get_active_tools();
            if(!$active_tools){
                return;
            }
            foreach($active_tools as $tool){
                $loader_file = $this->dir . '/' . $tool . '/' . $tool . '.php';
                if(!file_exists($loader_file)){
                    continue;
                }
                require_once($loader_file);
                if($this->namespace){
                    $class = $this->namespace . '\\' . __canonicalize($tool);
                } else {
                    $class = __canonicalize($tool);
                }
                $singleton = __get_instance($class);
                $this->active_tools[$tool] = $singleton;
            }
        }

    	/**
    	 * @return void
    	 */
    	protected function set_available_tools($tools_path = ''){
            if(!is_dir($this->dir)){
                return;
            }
            $available_tools = [];
            foreach(glob($this->dir . '/*', GLOB_ONLYDIR) as $tool_dir){
                $tool = wp_basename($tool_dir);
                $loader_file = $tool_dir . '/' . $tool . '.php';
                if(!file_exists($loader_file)){
                    continue;
                }
                $available_tools[] = $tool;
            }
            $this->available_tools = $available_tools;
        }

    	/**
    	 * @return void
    	 */
    	protected function set_meta_boxes(){
            if(!$this->active_tools){
                return;
            }
            foreach($this->active_tools as $tool => $singleton){
                $tool_fields = $this->get_tool_fields($singleton);
                if(!$tool_fields){
                    continue;
                }
                $this->meta_boxes[$tool] = $tool_fields;
            }
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	/**
    	 * @return void
    	 */
    	public function __construct($atts = []){
            $pairs = [
                'dir' => '',
                'namespace' => '',
                'parent' => '',
                'prefix' => '',
                'slug' => '',
                'title' => '',
            ];
            $atts = shortcode_atts($pairs, $atts);
            $dir = untrailingslashit($atts['dir']);
            $namespace = trim($atts['namespace']);
            $parent = trim($atts['parent']);
            $prefix = trim($atts['prefix']);
            $slug = trim($atts['slug']);
            $title = trim($atts['title']);
            if($dir and $parent and $prefix and $slug and $title){
                $this->dir = untrailingslashit($atts['dir']);
                $this->namespace = $namespace;
                $this->parent = $parent;
                $this->prefix = $prefix;
                $this->slug = $slug;
                $this->title = $title;
                add_filter('mb_settings_pages', [$this, '_mb_settings_pages']);
                add_filter('rwmb_meta_boxes', [$this, '_rwmb_meta_boxes']);
                $this->load_active_tools();
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	/**
    	 * @return array
    	 */
    	public function _mb_settings_pages($settings_pages){
            $this->set_meta_boxes();
            $settings_page = [
                'columns' => ($this->meta_boxes ? 2 : 1),
                'id' => $this->get_settings_page_id(),
                'menu_title' => $this->title,
                //'message' => translate('Settings saved.'),
                'message' => 'Settings saved. Some changes may not occur until you refresh the page.',
                'option_name' => __str_prefix('', $this->prefix),
                'page_title' => sprintf(_x('%s Settings', '%s stands for custom branded "Page Builder" name.', 'fl-builder'), $this->title),
                'submit_button' => translate('Save Changes'),
            ];
            if($this->parent){
                $settings_page['parent'] = $this->parent;
            }
            $settings_pages[] = $settings_page;
    		return $settings_pages;
    	}

    	/**
    	 * @return array
    	 */
    	public function _rwmb_meta_boxes($meta_boxes){
            $this->set_available_tools();
            $meta_boxes[] = $this->get_meta_box();
            if(!$this->meta_boxes){
                return $meta_boxes;
            }
            $field_id = $this->get_field_id();
            $settings_page_id = $this->get_settings_page_id();
            foreach($this->meta_boxes as $tool => $fields){
                $meta_boxes[] = [
                    'fields' => $fields,
                    'hidden' => [$field_id, 'not contains', $tool],
                    'id' => $this->get_meta_box_id($tool),
                    'settings_pages' => $settings_page_id,
                    'title' => $tool,
                ];
            }
    		return $meta_boxes;
    	}

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	/**
    	 * @return bool
    	 */
    	public function is_tool_active($tool = ''){
    		return in_array($tool, $this->active_tools);
    	}

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
