<?php
	/**
	 * @file
	 * ICAL Support.
	 * 
	 * Adds ical support for the BCT platform
	 * 
	 * @package ical
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	function ical_init()
	{
		
	}
	
	register_event('system', 'init', 'ical_init');