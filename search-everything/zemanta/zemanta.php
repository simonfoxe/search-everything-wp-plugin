<?php

define('SE_ZEMANTA_PLUGIN_VERSION_OPTION', 'zemanta_plugin_version');
define('SE_ZEMANTA_PLUGIN_FLASH_META', 'zemanta_plugin_flash');

if(!class_exists('WP_Http')) {
	require_once(ABSPATH . WPINC . '/class-http.php');
}

require_once(ABSPATH . 'wp-admin/includes/image.php');

class SEZemanta {

	var $version = '1.2.4';
	var $api_url = 'http://api.zemanta.com/services/rest/0.0/';
	var $api_key = '';
	var $flash_data = null;

	public function __construct()
	{
		global $wp_version;
		
		add_action('admin_init', array($this, 'init'));
		
		register_activation_hook(dirname(__FILE__) . '/zemanta.php', array($this, 'activate'));

	}
	
	/**
	* admin_init
	*
	* Initialize plugin
	*
	*/
	public function init() {
		if (defined('ZEMANTA_PLUGIN_VERSION_OPTION')) { // Make sure this doesn't clash with the Editorial Assistant
			return;
		}

		if(!$this->check_dependencies())
			add_action('admin_notices', array($this, 'warning'));
	}


	/**
	* admin_head
	*
	* Add any assets to the edit page
	*/
	public function assets() 
	{	
		$this->render('assets', array(
			'api_key' => $this->api_key,
			'version' => $this->version,
			'features' => $this->supported_features
		));
	}

	/**
	* warning for no api key
	*
	* Display api key warning
	*/
	public function warning_no_api_key()
	{
		$this->render('message', array(
			'type' => 'error'
			,'message' => __('You have no Zemanta API key and the plugin was unable to retrieve one. You can still use Zemanta, '.
			'but until the new key is successfully obtained you will not be able to customize the widget or remove '.
			'this warning. You may try to deactivate and activate the plugin again to make it retry to obtain the key.', 'zemanta')
			));
	}

	/**
	* warning
	*
	* Display plugin warning
	*/
	public function warning()
	{
		$this->render('message', array(
			'type' => 'updated fade'
			,'message' => __('Zemanta needs either the cURL PHP module or allow_url_fopen enabled to work. Please ask your server administrator to set either of these up.', 'zemanta')
			));
	}


	/**
	* api
	*
	* API Call
	*
	* @param array $arguments Arguments to pass to the API
	*/
	public function api($arguments)
	{
		$arguments = array_merge($arguments, array(
			'api_key'=> $this->api_key
			));
		
		if (!isset($arguments['format']))
		{
			$arguments['format'] = 'xml';
		}
		
		return wp_remote_post($this->api_url, array('method' => 'POST', 'body' => $arguments));
	}
	
	/**
	* ajax_error
	* 
	* Helper function to throw WP_Errors to ajax as json
	*/
	public function ajax_error($wp_error) {
		if(is_wp_error($wp_error)) {
			die(json_encode(array(
				'error' => array(
					'code' => $wp_error->get_error_code(),
					'message' => $wp_error->get_error_message(),
					'data' => $wp_error->get_error_data()
				)
			)));
		}
	}
	


	/**
	* fetch_api_key
	*
	* Get API Key
	*/
	public function fetch_api_key() 
	{
		$response = $this->api(array(
			'method' => 'zemanta.auth.create_user',
			'partner_id' => 'wordpress-se'
			));

		if(!is_wp_error($response))
		{
			if(preg_match('/<status>(.+?)<\/status>/', $response['body'], $matches))
			{
				if($matches[1] == 'ok' && preg_match('/<apikey>(.+?)<\/apikey>/', $response['body'], $matches))
					return $matches[1];
			}
		}

		return '';
	}


	/**
	* check_dependencies
	*
	* Return true if CURL and DOM XML modules exist and false otherwise
	*
	* @return boolean
	*/
	protected function check_dependencies() 
	{
		return ((function_exists('curl_init') || ini_get('allow_url_fopen')) && (function_exists('preg_match') || function_exists('ereg')));
	}

	

	/**
	* render
	*
	* Render HTML/JS/CSS to screen
	*
	* @param string $view File to display
	* @param array $arguments Arguments to pass to file
	* @param boolean $return Whether or not to return the output or print it
	*/
	protected function render($view, $arguments = array(), $return = false) 
	{		
		$view_file = untrailingslashit(dirname(__FILE__)) . '/views/' . $view . '.php';

		extract($arguments, EXTR_SKIP);

		if ($return)
			ob_start();

		if(file_exists($view_file))
			include($view_file);
		else
			echo '<pre>View Not Found: ' . $view . '</pre>';

		if ($return)
			return ob_get_clean();
	}

}

//
// End of file zemanta.php
//
