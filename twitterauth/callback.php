<?php

	global $CONFIG;
	
	$twitterObj = new EpiTwitter($CONFIG->twitter_appid, $CONFIG->twitter_secret);  
	$twitterObj->setToken($_GET['oauth_token']);  
	$token = $twitterObj->getAccessToken();  
	$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);  
	setcookie('twitter_oauth_token', $token->oauth_token);  
	setcookie('twitter_oauth_token_secret', $token->oauth_token_secret);  
	
	$creds = $twitterObj->get('/account/verify_credentials.json');
	
	if ($creds)
	{
		$user_obj = user_get_by_username($creds['id']);
		
		if (!$user_obj)
		{
			$user_obj = new TwitterUser();
			$user_obj->setUsername($creds['id']);
		}
		
		// Check for updates and extract profile fields
		foreach ($CONFIG->twitter_profile_fields as $field => $alias)
		{
			if ($creds[$field])
			{
			    if ($user_obj->$alias != $creds[$field])
				$user_obj->$alias = $creds[$field];
			}
		}
		
		if (($user_obj->save()) && ($user_obj->login()))
        	message(sprintf(_echo('twitterauth:ok:login'), $creds['name']));
        else
        	error_message(_echo('twitterauth:error:loginsaved'));
        
        forward();
	}
	else
	{
		error_message(_echo('twitterauth:error:loginfail'));
		forward();
	}