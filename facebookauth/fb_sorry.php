<?php

	$title = _echo('facebook:permission:sorry');
	
	$body .= view('facebook/sorry');
	
	output_page($title, $body);