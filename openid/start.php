<?php	
	/**
	 * @file
	 * OpenID support.
	 * 
	 * This plugin provides OpenID login support. 
	 * 
	 * Currently it only provides client support, but Server support could be a future enhancement. This
	 * plugin was written to kill two birds with one stone - build support for BCT & research OpenID
	 * for a client.
	 * 
	 * Uses the OpenID library from JanRain, documentation can be found at:
	 * 
	 * 	http://openidenabled.com/files/php-openid/docs/2.1.3/
	 * 
	 * 
	 * @section Important note
	 *
	 * Due to a bug in the JanRain library, Open ID will not work when passed through the standard BCT page
	 * handler.
	 * 
	 * Until this issue is addressed in the core library, you must modify your .htaccess file accordingly:
	 * 
	 * ...
	 * 
	 * \code
	 * # These rules must be last
	 * RewriteCond %{REQUEST_URI} !/plugins/openid/return.php(.*)$
	 * RewriteCond %{REQUEST_URI} !/_(.*)$  
	 * RewriteRule ^(.*)$ _pages/page_handler.php?page=$1 [QSA]
	 * \endcode
	 * 
	 * Or you can apply the htaccess_dist.patch file included with this plugin.
	 * 
	 * @package openid
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 * @link http://openidenabled.com/files/php-openid/docs/2.1.3/
	 */

	

	/** OpenID exception */
	class OpenIDException extends BCTPlatformException {}
	
	/**
	 * Open ID user class.
	 */
	class OpenIDUser extends User
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('openid'); 
		}	

		public function setPassword($password) { return false; }
		public function getUsername() { return $this->nickname; }
		public function setUsername($username)
		{
			$username = trim($username);
			
			if (!$username) return false;
			
			$this->nickname = $username;
		}
	}
	
	function openid_init()
	{
		global $CONFIG;
		
		// OpenID page handlers, generates intermittent pages
		register_page('openid', 'openid_pagehandler');
		
		
		$path_extra = dirname(__FILE__) . '/vendor/php-openid-2.1.3/';
		$path = ini_get('include_path');
		$path = $path_extra . PATH_SEPARATOR . $path;
		ini_set('include_path', $path);
		
		/** Include OpenID libraries */
		require_once(dirname(__FILE__) . '/vendor/php-openid-2.1.3/Auth/OpenID.php');
    	require_once(dirname(__FILE__) . '/vendor/php-openid-2.1.3/Auth/OpenID/Consumer.php');
		require_once(dirname(__FILE__) . '/vendor/php-openid-2.1.3/Auth/OpenID/FileStore.php');
		require_once(dirname(__FILE__) . '/vendor/php-openid-2.1.3/Auth/OpenID/SReg.php');
		require_once(dirname(__FILE__) . '/vendor/php-openid-2.1.3/Auth/OpenID/PAPE.php');
		
		$CONFIG->openid_providers = array (
			'claimid' => "http://www.claimid.com/{USERNAME}",
			'livejournal' => 'http://{USERNAME}.livejournal.com',
			'aim' => 'http://openid.aol.com/{USERNAME}',
			'wordpress' => 'http://{USERNAME}.wordpress.com'
		);
	}
	
	function openid_pagehandler($key, $pages)
	{
		if ($pages[0])
		{
			switch ($pages[0])
			{
				case 'login' : include(dirname(__FILE__) . '/auth.php'); break;
				case 'authreturn' : include(dirname(__FILE__) . '/return.php'); break;
			}
		}
	}
	
	function openid_get_provider_url($provider, $username)
	{
		global $CONFIG;
		
		$url = $CONFIG->openid_providers[$username];
		if ($url) return false;

		return str_replace('{USERNAME}', $username, $url);
	}
	
	register_event('system', 'init', 'openid_init');
?>