<?php

foreach(glob(plugin_dir_path(dirname(__FILE__)) . 'autoload/*.php') as $magic_functions_file){
    require_once($magic_functions_file);
}
unset($magic_functions_file);
