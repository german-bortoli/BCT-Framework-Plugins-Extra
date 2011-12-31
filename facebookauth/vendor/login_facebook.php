<?php
session_start();

if(!empty($_SESSION)){
	header("Location: home.php");
}
mysql_connect('localhost', 'root', '');
mysql_select_db('desec');

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
		$user = $facebook->api('/me');
	} catch (Exception $e){}
	
	if(!empty($user)){
		# We have an active session, let's check if we have already registered the user
		$query = mysql_query("SELECT * FROM users WHERE oauth_provider = 'facebook' AND oauth_uid = ". $user['id']);
		$result = mysql_fetch_array($query);
		
		# If not, let's add it to the database
		if(empty($result)){
			$query = mysql_query("INSERT INTO users (oauth_provider, oauth_uid, username) VALUES ('facebook', {$user['id']}, '{$user['name']}')");
			$query = msyql_query("SELECT * FROM users WHERE id = " . mysql_insert_id());
			$result = mysql_fetch_array($query);
		}
		// this sets variables in the session 
		$_SESSION['id'] = $result['id'];
		$_SESSION['oauth_uid'] = $result['oauth_uid'];
		$_SESSION['oauth_provider'] = $result['oauth_provider'];
		$_SESSION['username'] = $result['username'];
	} else {
		# For testing purposes, if there was an error, let's kill the script
		die("There was an error.");
	}
} else {
	# There's no active session, let's generate one
	$login_url = $facebook->getLoginUrl();
	header("Location: ".$login_url);
}