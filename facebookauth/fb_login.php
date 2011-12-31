<?php
	global $CONFIG;
	
	// Creating the facebook object
	$facebook = new Facebook(array(
		'appId'  => $CONFIG->facebook_appid,
		'secret' => $CONFIG->facebook_secret,
		'cookie' => true
	));

	// Let's see if we have an active session
	$session = $facebook->getSession();

	if ($session)
	{
		// Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
		try{
			$uid = $facebook->getUser();
			$user = $facebook->api('/me');
		} catch (Exception $e){}
		
		if ($user)
		{
			// We have an active session, let's check if we have already registered the user
			$user_obj = user_get_by_username($uid);
			
			// If no user, lets create a new one
			if (!$user_obj)
			{
				$user_obj = new FacebookUser();
				$user_obj->setUsername($user['id']);
			}
			
			// Check for updates and extract profile fields
			foreach ($CONFIG->facebook_profile_fields as $field => $alias)
			{
				if ($user[$field])
				{
				    if ($user_obj->$alias != $user[$field])
					$user_obj->$alias = $user[$field];
				}
			}
			
			if (($user_obj->save()) && ($user_obj->login()))
	        	message(sprintf(_echo('facebook:ok:login'), $user['name']));
	        else
	        	error_message(_echo('facebook:error:loginsaved'));
	        
	        forward();
		}
		else
			error_message(_echo('facebook:error:loginfail'));
	}
	else
		forward($facebook->getLoginUrl(array(
			'req_perms' => implode(',', $CONFIG->facebook_ex_perms),
			'next' => $CONFIG->wwwroot . 'facebook/login/',  
			'cancel_url' => $CONFIG->wwwroot . 'facebook/sorry/',
		))); // No session, so go get one.
	