<?php
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	global $CONFIG;
	
	
	// Create consumer object, using temporary file store
	$consumer = new Auth_OpenID_Consumer(
		new Auth_OpenID_FileStore($CONFIG->temp . 'openid')
	);
	
	$return_to = $CONFIG->wwwroot.'plugins/openid/return.php';//$CONFIG->wwwroot.'openid/authreturn';
	$response = $consumer->complete($return_to);
	
	switch ($response->status)
	{
		case Auth_OpenID_CANCEL :
			error_message(_echo('openid:error:cancelled'));
			forward();
		break;
		case Auth_OpenID_FAILURE :
			error_message(sprintf(_echo('openid:error:failed'), $response->message));
			forward();
		break;
		case Auth_OpenID_SUCCESS :
			
			// This means the authentication succeeded; extract the
	        // identity URL and Simple Registration data (if it was
	        // returned).
	        $openid = $response->getDisplayIdentifier();
	        $esc_identity = htmlentities($openid);
		
	        $success = sprintf(_echo('openid:ok'), $esc_identity, $esc_identity);

			if ($response->endpoint->canonicalID) {
            	$escaped_canonicalID = escape($response->endpoint->canonicalID);
            	$success .= '  ' . sprintf(_echo('openid:xriid'), $escaped_canonicalID) . ' ';
        	}    
	        
			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
	        $sreg = $sreg_resp->contents();
	
	        $email = htmlentities($sreg['email']);
	        $nickname = htmlentities($sreg['nickname']);
	        $fullname = htmlentities($sreg['fullname']);
	        
	        $user = user_get_by_username($openid);
	        if (!$user)
	        { // Doesn't exist, so create.
	        	$user = new OpenIDUser();
	        	$user->username = $openid;	
	        }
	        
			if ($fullname) $user->name = $fullname;
			if ($email) $user->email = $email;
			if ($nickname) $user->nickname = $nickname;
			
			$displayname = $openid;
			if ($nickname) $displayname = $user->nickname;
			if ($fullname) $displayname = $user->name;
	        
	        if (($user->save()) && ($user->login()))
	        	message(sprintf(_echo('openid:ok:login'), $displayname));
	        else
	        	error_message(_echo('openid:error:loginsaved'));
	        
	        forward();

		break;
		default: 
			error_message(_echo('openid:error:unknown'));
			forward();
	}
