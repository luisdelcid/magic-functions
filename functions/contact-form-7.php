<?php

/**
 * @return array
 */
function __cf7_additional_setting($name = '', $contact_form = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return [];
	}
	return $contact_form->additional_setting($name, false); // Differs from WPCF7_ContactForm::additional_setting in that it will always return an array.
}

/**
 * @return null|WPCF7_ContactForm
 */
function __cf7_contact_form($contact_form = null){
	$current_contact_form = wpcf7_get_current_contact_form(); // null or WPCF7_ContactForm
	if(empty($contact_form)){ // 0, false, null and other PHP falsey values return the current contact form
		return $current_contact_form;
	}
	if($contact_form instanceof \WPCF7_ContactForm){
		return $contact_form;
	}
	if(is_numeric($contact_form) or $contact_form instanceof \WP_Post){
		$contact_form = wpcf7_contact_form($contact_form); // null or WPCF7_ContactForm (replace the current contact form)
		if(!is_null($current_contact_form)){
			wpcf7_contact_form($current_contact_form->id()); // restore the current contact form
		}
		return $contact_form; // null or WPCF7_ContactForm
	}
	if(!is_string($contact_form)){
        return null;
    }
	$contact_form = wpcf7_get_contact_form_by_title($contact_form); // null or WPCF7_ContactForm (replace the current contact form)
	if(!is_null($current_contact_form)){
		wpcf7_contact_form($current_contact_form->id()); // restore the current contact form
	}
	return $contact_form; // null or WPCF7_ContactForm
}

/**
 * @return bool
 */
function __cf7_fake_mail($contact_form = null, $submission = null){
	if(!did_action('wpcf7_before_send_mail')){
		return false; // too early
	}
	if(did_action('wpcf7_mail_failed') or did_action('wpcf7_mail_sent')){
		return false; // too late
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
		return false; // try to prevent conflicts with other statuses
	}
	if(__cf7_skip_mail($contact_form) or __cf7_send_mail($contact_form)){ // skip or send
		$message = $contact_form->message('mail_sent_ok');
		$message = wp_strip_all_tags($message);
		$submission->set_response($message);
		$submission->set_status('mail_sent');
		return true;
	}
	$message = $contact_form->message('mail_sent_ng');
	$message = wp_strip_all_tags($message);
	$submission->set_response($message);
	$submission->set_status('mail_failed');
	return false;
}

/**
 * @return bool
 */
function __cf7_has_posted_data($key = ''){
    $posted_data = __cf7_posted_data();
	if(!$key){
		return false;
	}
	return isset($posted_data[$key]);
}

/**
 * @return bool
 */
function __cf7_has_pref($name = '', $contact_form = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return false;
	}
	$pref = $contact_form->pref($name);
	return !is_null($pref);
}

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
        if(!in_array($tags[$name], (array) $types)){
            $invalid[] = $name;
        }
	}
	return $invalid;
}

/**
 * @return bool
 */
function __cf7_is_b4($contact_form = null){
	$bootstrap = (int) __cf7_pref('bootstrap', $contact_form);
	return (4 === $bootstrap);
}

/**
 * @return bool
 */
function __cf7_is_false($name = '', $contact_form = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return false;
	}
    return in_array($contact_form->pref($name), ['0', 'false', 'off'], true); // Opposite of WPCF7_ContactForm::is_true.
}

/**
 * @return bool
 */
function __cf7_is_true($name = '', $contact_form = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return false;
	}
	return $contact_form->is_true($name); // Alias for WPCF7_ContactForm::is_true.
}

/**
 * @return WPCF7_ContactForm|WP_Error
 */
function __cf7_localize($contact_form = null, $overwrite_messages = false){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return __error(__('The requested contact form was not found.', 'contact-form-7'), [
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
	$contact_form = wpcf7_save_contact_form($args); // false or WPCF7_ContactForm
	if(!$contact_form){
		return __error(__('There was an error saving the contact form.', 'contact-form-7'), [
			'status' => 500,
		]);
	}
	return $contact_form;
}

/**
 * @return array
 */
function __cf7_metadata($contact_form = null, $submission = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return [];
	}
	$submission = __cf7_submission($submission);
	if(is_null($submission)){
		return [];
	}
	$metadata = [
        'contact_form_id' => $contact_form->id(),
        'contact_form_locale' => $contact_form->locale(),
        'contact_form_name' => $contact_form->name(),
        'contact_form_title' => $contact_form->title(),
        'container_post_id' => $submission->get_meta('container_post_id'),
        'current_user_id' => $submission->get_meta('current_user_id'),
        'remote_ip' => $submission->get_meta('remote_ip'),
        'remote_port' => $submission->get_meta('remote_port'),
        'submission_response' => $submission->get_response(),
        'submission_status' => $submission->get_status(),
        'timestamp' => $submission->get_meta('timestamp'),
        'unit_tag' => $submission->get_meta('unit_tag'),
        'url' => $submission->get_meta('url'),
        'user_agent' => $submission->get_meta('user_agent'),
    ];
	return $metadata;
}

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
		if(!isset($tags[$name])){
			$missing[] = $name;
		}
	}
	return $missing;
}

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

/**
 * This function’s access is marked private. This means it is not intended for use by plugin or theme developers, only in other core functions.
 *
 * @return array
 */
function __cf7_posted_data(){
    $posted_data = (array) __get_cache('posted_data', []);
    if(!$posted_data){
        $posted_data = array_filter((array) $_POST, function($key){
            return '_' !== substr($key, 0, 1);
        }, ARRAY_FILTER_USE_KEY);
        $posted_data = __cf7_sanitize_posted_data($posted_data);
        __set_cache('posted_data', $posted_data);
    }
	return $posted_data;
}

/**
 * @return string
 */
function __cf7_pref($name = '', $contact_form = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return '';
	}
	$pref = $contact_form->pref($name);
	if(is_null($pref)){
		return ''; // Differs from WPCF7_ContactForm::pref in that it will always return a string.
	}
	return $pref;
}

/**
 * @return array|string
 */
function __cf7_raw_posted_data($key = ''){
    $posted_data = __cf7_posted_data();
    if(!$key){
        return $posted_data;
    }
    if(!isset($posted_data[$key])){
		return ''; // Differs from WPCF7_Submission::get_posted_data in that it will always return a string if not available.
	}
	return $posted_data[$key];
}

/**
 * @return array|string
 */
function __cf7_sanitize_posted_data($value = []){
    if(is_array($value)){
        $value = array_map(__FUNCTION__, $value);
    } elseif(is_string($value)){
        $value = wp_check_invalid_utf8($value);
        $value = wp_kses_no_null($value);
    }
	return $value;
}

/**
 * @return bool
 */
function __cf7_send_mail($contact_form = null){
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

/**
 * @return string
 */
function __cf7_shortcode_attr($name = '', $contact_form = null){
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return '';
	}
	$att = $contact_form->shortcode_attr($name);
	if(is_null($att)){
		return '';
	}
	return $att;
}

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

/**
 * @return null|WPCF7_Submission
 */
function __cf7_submission($submission = null){
	$current_submission = \WPCF7_Submission::get_instance();
	if(empty($submission)){ // 0, false, null and other PHP falsey values return the current submission
		return $current_submission;
	}
	if($submission instanceof \WPCF7_Submission){
		return $submission;
	}
	return null;
}

/**
 * @return string
 */
function __cf7_tag_content_as_label($tag = null){
	if(!$tag instanceof \WPCF7_FormTag){
		return '';
	}
	switch($tag->basetype){
		case 'checkbox':
		case 'file':
		case 'date':
		case 'email':
		case 'number':
		case 'password':
		case 'radio':
		case 'range':
		case 'select':
		case 'tel':
		case 'text':
		case 'url':
			$content = $tag->content;
			break;
		case 'textarea':
			if($tag->has_option('content_as_content')){
				return '';
			}
			$content = $tag->content;
			break;
		default:
			$content = '';
	}
	$content = __remove_whitespaces($content);
	return $content;
}

/**
 * @return string
 */
function __cf7_tag_fa($tag = null){
	$class = __cf7_tag_fa_class($tag);
	if(!$class){
		return '';
	}
	return '<i class="' . $class . '"></i>';
}

/**
 * @return array
 */
function __cf7_tag_fa_classes($tag = null){
	if(!$tag instanceof \WPCF7_FormTag){
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
	if($tag->has_option('fa_fw')){
	    $classes[] = 'fa-fw';
	}
	return $classes;
}

/**
 * @return string
 */
function __cf7_tag_fa_class($tag = null){
    $classes = __cf7_tag_fa_classes($tag);
	return implode(' ', $classes);
}

/**
 * @return bool
 */
function __cf7_tag_has_data_option($tag = null){
	if(!$tag instanceof \WPCF7_FormTag){
		return false;
	}
	return (bool) $tag->get_data_option();
}

/**
 * @return bool
 */
function __cf7_tag_has_free_text($tag = null){
	if(!$tag instanceof \WPCF7_FormTag){
		return false;
	}
	return $tag->has_option('free_text');
}

/**
 * @return bool
 */
function __cf7_tag_has_pipes($tag = null){
	if(!$tag instanceof \WPCF7_FormTag){
		return false;
	}
	if(WPCF7_USE_PIPE and $tag->pipes instanceof \WPCF7_Pipes and !$tag->pipes->zero()){
		$pipes = $tag->pipes->to_array();
		foreach($pipes as $pipe){
			if($pipe[0] !== $pipe[1]){
				return true;
			}
		}
	}
	return false;
}

/**
 * @return bool
 */
function __cf7_tag_is_fl($tag = null, $contact_form = null){
	if(!$tag instanceof \WPCF7_FormTag){
		return false;
	}
	$contact_form = __cf7_contact_form($contact_form);
	if(is_null($contact_form)){
		return false;
	}
	$content_as_labels = __cf7_is_true('content_as_labels', $contact_form);
	$content_label = __cf7_tag_content_as_label($tag);
	$floating_labels = __cf7_is_true('floating_labels', $contact_form);
	$placeholder = __cf7_tag_placeholder($tag);
	return ($floating_labels and (($content_as_labels and $content_label) or $placeholder));
}

/**
 * @return string
 */
function __cf7_tag_placeholder($tag = null){
	if(!$tag instanceof \WPCF7_FormTag){
		return '';
	}
	switch($tag->basetype){
        case 'date':
        case 'email':
		case 'file':
        case 'number':
        case 'password':
        case 'tel':
        case 'text':
		case 'textarea':
        case 'url':
			if($tag->has_option('placeholder') or $tag->has_option('watermark')){
				return (string) reset($tag->values);
            } else {
				return '';
			}
            break;
        case 'select':
			if($tag->has_option('include_blank') or empty($tag->values)){
				return __('&#8212;Please choose an option&#8212;', 'contact-form-7'); // TODO: determine which plugin versions use older labels for backward compatibility.
			} elseif($tag->has_option('first_as_label')){
				return (string) reset($tag->values);
			} else {
				return '';
			}
			break;
		default:
			return '';
    }
}
