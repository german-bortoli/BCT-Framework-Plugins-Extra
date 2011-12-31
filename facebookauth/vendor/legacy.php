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
		
		# users.getInfo
		$api_call = array(
			'method' => 'users.getinfo',
			'uids' => $uid,
			'fields' => 'uid, first_name, last_name, pic_square, pic_big, sex'
		);
		$users_getinfo = $facebook->api($api_call);
		print_r($users_getinfo);
		
		# FQL
		$fql_query  =   array(
			'method' => 'fql.query',
			'query' => 'SELECT uid, first_name, last_name, pic_square, pic_big, sex FROM user WHERE uid = ' . $uid
		);
		$fql_info = $facebook->api($fql_query);
		print_r($fql_info);
		
	} catch (Exception $e){}
} else {
	# There's no active session, let's generate one
	$login_url = $facebook->getLoginUrl();
	header("Location: ".$login_url);
}