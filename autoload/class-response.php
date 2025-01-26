<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// This class’ access is marked private. This means it is not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!class_exists('__Response')){
	final class __Response {

		public $body = '', $code = 0, $cookies = [], $headers = [], $is_json = false, $is_success = false, $json_params = [], $message = '', $raw_response = [], $status = '', $wp_error = null;

		/**
		 * @return void
		 */
	    public function __construct($response = []){
            $this->raw_response = $response;
            if(is_wp_error($response)){
                $this->message = $response->get_error_message();
                $this->wp_error = $response;
            } elseif(!__is_wp_http_requests_response($response)){
                $error_msg = translate('Invalid data provided.');
                $this->message = $error_msg;
                $this->wp_error = __error($error_msg, $response);
            } else {
                $this->body = trim(wp_remote_retrieve_body($response));
                $this->code = absint(wp_remote_retrieve_response_code($response));
                $this->cookies = wp_remote_retrieve_cookies($response);
                $this->headers = wp_remote_retrieve_headers($response);
                $this->is_json = __is_json_content_type($response);
                $this->is_success = __is_success($this->code);
                $this->message = __get_response_message($response);
                $this->status = __get_status_message($response);
                $this->wp_error = new \WP_Error;
                if(!$this->is_success){
                    $this->wp_error = __error($this->message, $this->raw_response);
                }
                if($this->is_json){
                    $json_params = __json_decode($this->body, true);
                    if(is_wp_error($json_params)){
                        if($this->is_success){
                            $this->is_success = false;
                            $this->message = $json_params->get_error_message();
                            $this->wp_error = $json_params;
                        } else {
                            $this->wp_error->merge_from($json_params);
                            $this->message = $json_params->get_error_message();
                        }
                    } else {
                        $this->json_params = $json_params;
                        $maybe_error = __seems_error($json_params);
                        if(is_wp_error($maybe_error)){
                            if($this->is_success){
                                $this->is_success = false;
                                $this->message = $maybe_error->get_error_message();
                                $this->wp_error = $maybe_error;
                            } else {
                                $this->wp_error->merge_from($maybe_error);
                                $this->message = $maybe_error->get_error_message();
                            }
                        }
                    }
                }
            }
		}

		/**
		 * @return string
		 */
	    public function body(){
			return $this->body;
		}

		/**
		 * @return int
		 */
	    public function code(){
			return $this->code;
		}

		/**
		 * @return array
		 */
	    public function cookies(){
			return $this->cookies;
		}

		/**
		 * @return array
		 */
	    public function headers(){
			return $this->headers;
		}

		/**
		 * @return bool
		 */
	    public function is_json(){
			return $this->is_json;
		}

		/**
		 * @return bool
		 */
	    public function is_success(){
			return $this->is_success;
		}

		/**
		 * @return string
		 */
	    public function json_param($name = ''){
            if(!$name){
                return '';
            }
			if(!array_key_exists($name, $this->json_params)){
				return '';
			}
			return $this->json_params[$name];
		}

		/**
		 * @return array
		 */
	    public function json_params(){
			return $this->json_params;
		}

		/**
		 * @return string
		 */
	    public function message(){
			return $this->message;
		}

		/**
		 * @return array
		 */
	    public function raw_response(){
			return $this->raw_response;
		}

		/**
		 * @return string
		 */
	    public function status(){
			return $this->status;
		}

		/**
         * Backward compatibility.
         *
		 * @return WP_Error
		 */
	    public function to_wp_error(){
            return $this->wp_error;
		}

		/**
		 * @return WP_Error
		 */
	    public function wp_error(){
            return $this->wp_error;
		}

	}
}
