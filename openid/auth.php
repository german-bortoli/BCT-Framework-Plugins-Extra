<?php
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	global $CONFIG;
	
	$identifier = openid_get_provider_url(input_get('service'), input_get('username'));
	
	if (!$identifier)
		$identifier = input_get('identifier');
	$policies = input_get('policies');
	
	// Get URL
	if (!$identifier) {
		error_message(_echo('openid:error:nourl'));
		forward();
	}
	
	// Create consumer object, using temporary file store
	$consumer = new Auth_OpenID_Consumer(
		new Auth_OpenID_FileStore($CONFIG->temp . 'openid')
	);
	
	// Begin the OpenID authentication process.
    $auth_request = $consumer->begin($identifier);
    
    if (!$auth_request) {
		error_message(_echo('openid:error:notopenid'));
		forward();
    }
    
	$sreg_request = Auth_OpenID_SRegRequest::build(
                                     // Required
                                     array('nickname'),
                                     // Optional
                                     array('fullname', 'email'));

    if ($sreg_request) {
        $auth_request->addExtension($sreg_request);
    }

	$pape_request = new Auth_OpenID_PAPE_Request($policies);
    if ($pape_request) {
        $auth_request->addExtension($pape_request);
    }

 	if ($auth_request->shouldSendRedirect()) {
        
 		$redirect_url = $auth_request->redirectURL(	
 			$CONFIG->wwwroot,
 			$CONFIG->wwwroot.'plugins/openid/return.php'//$CONFIG->wwwroot.'openid/authreturn'
 		);

		if (Auth_OpenID::isFailure($redirect_url)) {
            error_message(sprintf(_echo('openid:error:couldnotredirect'), $redirect_url->message));
            forward();
        } else {
            // Send redirect.
            forward($redirect_url);
        }
    } else {
        // Generate form markup and render it. TODO: Do this in a better and more consistent way?
        $form_id = 'openid_message';
        $form_html = $auth_request->htmlMarkup($CONFIG->wwwroot,
 												$CONFIG->wwwroot.'plugins/openid/return.php',//$CONFIG->wwwroot.'openid/authreturn',
                                           		false, array('id' => $form_id));

        // Display an error if the form markup couldn't be generated;
        // otherwise, render the HTML.
        if (Auth_OpenID::isFailure($form_html)) {
            error_message(sprintf(_echo('openid:error:couldnotredirect'), $form_html->message));
            forward();
		} else {
            output_page('OpenID',$form_html);
        }
    }
    