<?php
	/**
	 * @file
	 * Twitter authentication.
	 * 
	 * This plugin allows twitter users to log in to a BCT platform site.
	 * 
	 * @note Define the following:
	 *	$CONFIG->twitter_appid
	 *	$CONFIG->twitter_secret
	 *
	 * @note Visit http://dev.twitter.com/apps to obtain your keys
	 * 
	 * @package twitterauth
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 * @link http://dev.twitter.com/apps
	 */

	require_once(dirname(__FILE__) . '/vendor/twitter-async/EpiCurl.php');
	require_once(dirname(__FILE__) . '/vendor/twitter-async/EpiOAuth.php');
	require_once(dirname(__FILE__) . '/vendor/twitter-async/EpiTwitter.php');
	
	/** Twitter oauth exception */
	class TwitterException extends BCTPlatformException {}
	
	/**
	 * Twitter user class.
	 */
	class TwitterUser extends User
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('twitter'); 
		}	

		public function setPassword($password) { return false; }
		
		/**
		 * Return an icon for the object.
		 * 
		 * Objects should override the 'property:icon' hook for the class, alternatively override
		 * getIcon directly.
		 *
		 * @param string $size 
		 */
		public function getIcon($size = 'small')
		{
			global $CONFIG;
			
			$iconurl = '';
			
			switch ($size)
			{
				case 'large' : $iconurl = str_replace('_normal', '', $this->profile_image_url);  break;
				case 'medium' : $iconurl = str_replace('_normal', '_bigger', $this->profile_image_url); break;
				case 'small' :
				case 'square' :
				default: $iconurl = $this->profile_image_url;
			}
			
			return $this->__send_object_hook('property:icon', $iconurl, array('size' => $size)); // TODO: Return default icon..?

		}
		
	}

	/**
	 * Initialisation function
	 *
	 */
	function twitterauth_init()
	{
		global $CONFIG;
		
		register_page('twitter', 'twitter_pagehandler');	
		
		// Twitter profile fields to extract, and what to call them...
		$CONFIG->twitter_profile_fields = array();
		
		$CONFIG->twitter_profile_fields['name'] = 'name';
		$CONFIG->twitter_profile_fields['screen_name'] = 'screenname';
		$CONFIG->twitter_profile_fields['id'] = 'username';
		$CONFIG->twitter_profile_fields['location'] = 'location';
		$CONFIG->twitter_profile_fields['url'] = 'link';
		
		$CONFIG->twitter_profile_fields['profile_image_url'] = 'profile_image_url';
	}
	
	
	/**
	 * Page handler
	 */
	function twitter_pagehandler($key, $pages)
	{
		if ($pages[0])
		{
			switch ($pages[0])
			{
				case 'login' : require_once(dirname(__FILE__) . '/login.php'); break;
				case 'success' : require_once(dirname(__FILE__) . '/callback.php'); break;
			}
		}
	}
	
	register_event('system', 'init', 'twitterauth_init');