<?php
	$lang = $vars['language'];
	if (!$lang) $lang = language_get_current();

	$vars['href'] = "http://maps.google.co.uk/maps?hl=$lang&q=". $vars['value'] . "&ie=UTF8";
	$vars['target'] = '_blank'; 

	echo view('output/url', $vars);