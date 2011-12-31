<?php
	/**
	 * @file
	 * Facebook authentication.
	 * 
	 * This plugin allows facebook users to log in to a BCT platform site.
	 * 
	 * @note You need to define the following in settings.php:
	 *	$CONFIG->facebook_appid
	 *	$CONFIG->facebook_secret
	 *	$CONFIG->facebook_ex_perms = array ( array of parameters of requested information - see http://developers.facebook.com/docs/authentication/permissions);
	 *
	 * @note You need to set up your application ID by visiting http://developers.facebook.com/apps/ and hitting "Create New App"
	 *
	 * @note Plugin requires php5-curl module
	 * @package facebookauth
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 * @link http://developers.facebook.com/setup/
	 */

	require_once(dirname(__FILE__) . '/vendor/facebook.php');
	
	
	/** OpenID exception */
	class FacebookException extends BCTPlatformException {}
	
	/**
	 * Open ID user class.
	 */
	class FacebookUser extends User
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('facebook'); 
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
			$id = $this->getUsername();
			
			switch ($size)
			{
				case 'large' : $iconurl = "https://graph.facebook.com/$id/picture?type=large"; break;
				case 'medium' : $iconurl = "https://graph.facebook.com/$id/picture?type=small"; break;
				case 'small' :
				case 'square' :
				default: $iconurl = "https://graph.facebook.com/$id/picture?type=square";
			}
			
			return $this->__send_object_hook('property:icon', $iconurl, array('size' => $size)); // TODO: Return default icon..? 
		}
		
	}

	/**
	 * Initialisation function
	 *
	 */
	function facebookauth_init()
	{
		global $CONFIG;
		
		register_page('facebook', 'facebook_pagehandler');	
		
		if (!$CONFIG->facebook_ex_perms)
			$CONFIG->facebook_ex_perms = array();
			
		// Add some default permissions
		$CONFIG->facebook_ex_perms[] = 'email';
		$CONFIG->facebook_ex_perms[] = 'user_location';
		
		// Facebook profile fields to extract, and what to call them...
		$CONFIG->facebook_profile_fields = array();
		
		$CONFIG->facebook_profile_fields['name'] = 'name';
		$CONFIG->facebook_profile_fields['email'] = 'email';
		$CONFIG->facebook_profile_fields['user_location'] = 'location';
		$CONFIG->facebook_profile_fields['user_hometown'] = 'hometown';
		$CONFIG->facebook_profile_fields['first_name'] = 'first_name';
		$CONFIG->facebook_profile_fields['last_name'] = 'last_name';
		$CONFIG->facebook_profile_fields['link'] = 'link';
	}
	
	// TODO: Check permission
	// TODO: Publish to feed
	
	/**
	 * Page handler
	 */
	function facebook_pagehandler($key, $pages)
	{
		if ($pages[0])
		{
			switch ($pages[0])
			{
				case 'login' : include(dirname(__FILE__) . '/fb_login.php'); break;
				case 'sorry' : include(dirname(__FILE__) . '/fb_sorry.php'); break;
			}
		}
	}
	
	register_event('system', 'init', 'facebookauth_init');