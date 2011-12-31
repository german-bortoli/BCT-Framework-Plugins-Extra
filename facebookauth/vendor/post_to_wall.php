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
		
		# let's check if the user has granted access to posting in the wall
		$api_call = array(
			'method' => 'users.hasAppPermission',
			'uid' => $uid,
			'ext_perm' => 'publish_stream'
		);
		$can_post = $facebook->api($api_call);
		if($can_post){
			# post it!
			# $facebook->api('/'.$uid.'/feed', 'post', array('message' => 'Saying hello from my Facebook app!'));
			
			# using all the arguments
			$facebook->api('/'.$uid.'/feed', 'post', array(
				'message' => 'The message',
				'name' => 'The name',
				'description' => 'The description',
				'caption' => 'The caption',
				'picture' => 'http://i.imgur.com/yx3q2.png',
				'link' => 'http://net.tutsplus.com/'
			));
			echo 'Posted!';
		} else {
			die('Permissions required!');
		}
	} catch (Exception $e){}
} else {
	# There's no active session, let's generate one
	$login_url = $facebook->getLoginUrl();
	header("Location: ".$login_url);
}