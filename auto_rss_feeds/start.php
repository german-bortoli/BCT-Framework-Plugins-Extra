<?php
	/**
	 * @file
	 * Adds automatic RSS feeds to a list view. 
	 * 
	 * @package auto_rss_feeds
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	function auto_rss_feeds_init()
	{
		extend_view('metatags', 'auto_rss_feeds/rssheader');
	}
	
	register_event('system', 'init', 'auto_rss_feeds_init');