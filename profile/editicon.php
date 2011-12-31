<?php
    $user_guid = input_get('user_guid');

    $user = getObject($user_guid);
    if ((!$user) || ( !($user instanceof User)) || (!$user->canEdit()))
	    forward();

    $title = sprintf(_echo('profile:user:editicon'), $user->getName());

    output_page($title, view('profile/forms/editicon', array('user' => $user)));