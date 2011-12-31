<?php
# We require the library
require("facebook.php");

# Creating the facebook object
$facebook = new Facebook(array(
	'appId'  => 'YOUR_APP_ID',
	'secret' => 'YOUR_APP_SECRET',
	'cookie' => true
));

# Let's see if we have an active session
$session = $facebook->getSession();

if(!empty($session)) {
	# Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
	try{
		$uid = $facebook->getUser();
		
		# req_perms is a comma separated list of the permissions needed
		$url = $facebook->getLoginUrl(array(
			'req_perms' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos'
		));
		header("Location: {$url} ");
	} catch (Exception $e){}
} else {
	# There's no active session, let's generate one
	$login_url = $facebook->getLoginUrl();
	header("Location: ".$login_url);
}