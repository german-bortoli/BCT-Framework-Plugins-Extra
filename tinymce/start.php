<?php
	/**
	 * @file
	 * Tiny MCE support.
	 * 
	 * @package tinymce
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	function tinymce_init()
	{
	}
	
	register_event('system', 'init', 'tinymce_init');
	