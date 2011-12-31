<?php
	/**
	 * @file
	 * Basic gravatar plugin.
	 * 
	 * This plugin provides functions for handling gravatar images as well as a new view('output/gravatar').
	 * 
	 * @note If you want gravatar to provide default icons set $CONFIG->gravatar_default_icon to one of the following, 'retro' ,'mm', 'identicon','monsterid', 'wavatar', 'unicorns' or the url of an image file.
	 * 
	 * @package gravatar
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	function gravatar_init()
	{
		global $CONFIG;
		
		// Add default icons
		if ((isset($CONFIG->gravatar_default_icon)) || ($CONFIG->gravatar_default_icon!==false)) {
			register_hook('obj:user', 'property:icon', 'gravatar_default_icon', 999);
		}
	}

	/**
	 * When passed an email address this function will return a gravatar URL for the given size
	 *
	 * @param string $email Email address of user
	 * @param int $size The size of the image
	 * @param string $rating The rating of image ('r', 'g', 'pg', 'x')
	 * @param string $default The default url image, or one of the following 'retro' ,'mm', 'identicon','monsterid', 'wavatar', 'unicorns'
	 */
	function gravatar_url_from_email($email, $size = 40, $rating = 'g', $default = 'monsterid') 
	{
		// Sanity check size
		if (($size<0) || ($size>512))
			$size = 40;
			
		// Rating
		if (!in_array($rating, array('g','pg','r','x')))
			$rating = 'g';
			
		// default
		switch ($default)
		{
			case 'unicornify':
			case 'unicorns': $default =  'http://unicornify.appspot.com/avatar/' . md5($email) . "?s=$size";
			break;
			case 'identicon' :
			case 'monsterid' :
			case 'retro' :
			case 'mm' :
			case 'wavatar' : break;
			default: if (strpos($default, 'http')===false) $default = 'monsterid'; // if we're not passing a url, then string is invalid so use monster icons ('cos they're cool)
		}
			
		return 'http://www.gravatar.com/avatar/'.md5($email) . '.jpg?s=' . urlencode($size) . '&r=' . urlencode($rating) . '&d='.urlencode($default);
	}

	function gravatar_default_icon($namespace, $hook, $parameters, $return_value)
	{
		global $CONFIG;
		
		if (!$return_value)
		{
			$size = $parameters['size'];
			switch($size)
			{
				case 'large' :
					$size = 512;
				break;
				case 'medium' :
					$size = 128;
				break;
				case 'small' :
				default: 
					$size = 40;
				break;
			}
					
			$object = $parameters['object'];
			if (($object) && ($object instanceof User))
			{
				$email = $object->getEmail();
				if ($email)
					return gravatar_url_from_email($email, $size, 'g', $CONFIG->gravatar_default_iconset ? $CONFIG->gravatar_default_iconset : 'monsterid');
			}	
		}
	}
	
	register_event('system', 'init', 'gravatar_init');
