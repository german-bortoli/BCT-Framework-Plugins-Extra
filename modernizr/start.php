<?php
	/**
	 * @file
	 * Modernizr library support
	 * 
	 * @package modernizr
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	function modernizr_init()
	{
		plugin_depends('js_page');
		
		extend_view('js', 'modernizr/js');
	}

	register_event('system', 'init', 'modernizr_init');
