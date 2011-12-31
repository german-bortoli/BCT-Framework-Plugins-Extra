<?php

	global $CONFIG;
	
	$twitterObj = new EpiTwitter($CONFIG->twitter_appid, $CONFIG->twitter_secret);  
	$authenticateUrl = $twitterObj->getAuthenticateUrl();
	forward($authenticateUrl);