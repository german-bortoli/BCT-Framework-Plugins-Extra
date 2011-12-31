<?php
	$user_guid = input_get('user_guid');
	
	$user = getObject($user_guid);
	if ((!$user) || ( !($user instanceof User)))
		forward();
	
	$title = sprintf(_echo('profile:user'), $user->getName());
	
	output_page($title, $user->draw(array('full' => true)));
	