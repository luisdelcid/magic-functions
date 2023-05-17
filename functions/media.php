<?php

/**
 * @return void
 */
function __add_image_size($name = '', $width = 0, $height = 0, $crop = false){
	$image_sizes = get_intermediate_image_sizes();
	$size = sanitize_title($name);
	if(in_array($size, $image_sizes)){
		return;
	}
	$image_sizes = (array) __get_cache('image_sizes', []);
	$image_sizes[$size] = $name;
	__set_cache('image_sizes', $image_sizes);
	add_image_size($size, $width, $height, $crop);
	__one('image_size_names_choose', '__maybe_add_custom_image_sizes');
}

/**
 * @return void
 */
function __add_larger_image_sizes(){
	__add_image_size('HD', 1280, 1280);
	__add_image_size('Full HD', 1920, 1920);
	__add_image_size('4K', 3840, 3840);
}

/**
 * @return int
 */
function __attachment_url_to_postid($url = ''){
	$post_id = __guid_to_postid($url);
	if($post_id){
		return $post_id;
	}
	preg_match('/^(.+)(\-\d+x\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // resized
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
	preg_match('/^(.+)(\-scaled)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // scaled
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
	preg_match('/^(.+)(\-e\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches); // edited
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

/**
 * @return string
 */
function __fa_file_type($post = null){
	if('attachment' !== get_post_status($post)){
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

/**
 * @return void
 */
function __fix_audio_video_ext(){
    __set_cache('fix_audio_video_ext', true);
	__one('wp_check_filetype_and_ext', '__maybe_fix_audio_video_ext', 10, 5);
}

/**
 * @return int
 */
function __guid_to_postid($guid = '', $check_rewrite_rules = false){
	global $wpdb;
	$query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $guid);
	$post_id = $wpdb->get_var($query);
	if(null !== $post_id){
		return intval($post_id);
	}
	if($check_rewrite_rules){
		return url_to_postid($guid);
	}
	return 0;
}

/**
 * @return void
 */
function __maybe_add_custom_image_sizes($sizes){
    $image_sizes = (array) __get_cache('image_sizes', []);
	if(!$image_sizes){
		return;
	}
	foreach($image_sizes as $size => $name){
		$sizes[$size] = $name;
	}
	return $sizes;
}

/**
 * @return void
 */
function __maybe_fix_audio_video_ext($wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime){
    $fix_audio_video_ext = (bool) __get_cache('fix_audio_video_ext', false);
	if(!$fix_audio_video_ext){
		return $wp_check_filetype_and_ext;
	}
    if($wp_check_filetype_and_ext['ext'] and $wp_check_filetype_and_ext['type']){
        return $wp_check_filetype_and_ext;
    }
    if(0 !== strpos($real_mime, 'audio/') and 0 !== strpos($real_mime, 'video/')){
        return $wp_check_filetype_and_ext;
    }
    $filetype = wp_check_filetype($filename);
    if(!in_array(substr($filetype['type'], 0, strcspn($filetype['type'], '/')), ['audio', 'video'])){
        return $wp_check_filetype_and_ext;
    }
    $wp_check_filetype_and_ext['ext'] = $filetype['ext'];
    $wp_check_filetype_and_ext['type'] = $filetype['type'];
    return $wp_check_filetype_and_ext;
}

/**
 * @return bool
 */
function __maybe_generate_attachment_metadata($attachment_id = 0){
	$attachment = get_post($attachment_id);
	if(null === $attachment){
		return false;
	}
	if('attachment' !== $attachment->post_type){
		return false;
	}
	wp_raise_memory_limit('image');
	if(!function_exists('wp_generate_attachment_metadata')){
		require_once(ABSPATH . 'wp-admin/includes/image.php');
	}
	wp_maybe_generate_attachment_metadata($attachment);
	return true;
}

/**
 * @return void
 */
function __maybe_sanitize_file_name($filename){
    $sanitize_file_names = (bool) __get_cache('sanitize_file_names', false);
	if(!$sanitize_file_names){
		return $filename;
	}
	return __sanitize_file_name($filename);
}

/**
 * @return string
 */
function __sanitize_file_name($filename = ''){
	return implode('.', array_map(function($piece){
		return preg_replace('/[^A-Za-z0-9_-]/', '', $piece);
	}, explode('.', $filename)));
}

/**
 * @return void
 */
function __sanitize_file_names(){
	__set_cache('sanitize_file_names', true);
	__one('sanitize_file_name', '__maybe_sanitize_file_name');
}
