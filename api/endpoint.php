<?php
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	global $CONFIG;
	
	// Get parameter variables
	$method = input_get('method');
	
	$result = null;
	
	try {
		
		// Authenticate client connection somehow (openssl keys, oauth etc etc etc)
		// This makes sure that only connections from previously permitted services 
		// can connect.
		if (!trigger_hook('api', 'authenticate:connection', null, false))
		{
			header("HTTP/1.0 403 Forbidden");
	        header("Status: 403 Forbidden");
	        
			throw new SecurityException(_echo('api:error:clientauthentication'));
		}
		
		// Execute query (note, unless some further authorisation takes place this 
		// query will be executed as a logged out user). 
		$result = api_execute($method, api_get_parameters_for_method($method));

	} 
	catch (Exception $e)
	{
		// Catch any exception, turn it into a valid response
		$result = FailureAPIResult::getInstance($e->getMessage(), $e->getCode(), $e);	
	}
	
	echo $result;
?>