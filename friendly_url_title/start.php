<?php
	/**
	 * @file
	 * Default friendly url title handler.
	 * 
	 * @note This module requires iconv support.
	 * 
	 * @package friendly_url_title
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	
	function friendly_url_title_init()
	{
		// Register hook
		register_hook('filter', 'friendlyurltitle', 'friendly_url_title_hook', 999);
	}
	
	function friendly_url_title_hook($namespace, $hook, $parameters, $return_value)
	{
		global $CONFIG;
		
		$return_value = iconv('UTF-8', 'ASCII//TRANSLIT', $return_value); // Attempt the transliteration of non-ascii chars
		
		$return_value = preg_replace("/[^\w ]/","", $return_value);
		
		$return_value = str_replace(" ","-", $return_value);
		$return_value = str_replace("--","-", $return_value);
		$return_value = trim($return_value);
		$return_value = strtolower($return_value);
		
		return $return_value;
	}
	
	register_event('system', 'init', 'friendly_url_title_init');
