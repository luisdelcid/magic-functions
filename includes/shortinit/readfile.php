<?php

require_once(rtrim(dirname(__FILE__), '/\\') . '/magic-functions.php');
if(!isset($_GET['file'], $_GET['levels'], $_GET['md5'])){
	__404();
}
$abspath = __dirname(__FILE__, $_GET['levels']);
$loader = $abspath . '/wp-load.php';
if(!file_exists($loader)){
    __404();
}
define('SHORTINIT', true);
require_once($loader);
error_reporting(0);
nocache_headers();
$basedir = ABSPATH . 'wp-content/uploads';
$subdir = (isset($_GET['yyyy'], $_GET['mm']) ? ('/' . $_GET['yyyy'] . '/' . $_GET['mm']) : (isset($_GET['subdir']) ? '/' . $_GET['subdir'] : ''));
$file = $basedir . $subdir . '/' . $_GET['file'];
if(!is_file($file)){
	__404();
}
$post_id = __attachment_file_to_postid($file);
if(!$post_id){
    __serve_file($file);
}
$option = __str_prefix('hide_uploads_subdir_' . $_GET['md5']);
$value = (array) get_option($option, []);
$exclude_other_media = (isset($value['exclude_other_media']) ? (array) $value['exclude_other_media'] : []);
if($exclude_other_media and in_array($post_id, $exclude_other_media)){
	__serve_file($file);
}
$post = __get_post($post_id);
$post_status = __get_post_status($post_id);
$exclude_public_media = (isset($value['exclude_public_media']) ? (bool) $value['exclude_public_media'] : false);
if($exclude_public_media and 'publish' === $post_status){
	__serve_file($file);
}
$user_id = __get_current_user_id();
if(!$user_id){
    __404();
}
$capability = (isset($value['capability']) ? (string) $value['capability'] : 'edit_others_posts');
if(__current_user_can($capability)){
    __serve_file($file);
}
if($user_id === $post->post_author){
    __serve_file($file);
}
__404();
