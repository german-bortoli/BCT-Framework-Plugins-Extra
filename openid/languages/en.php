<?php

	$english = array (
		'openid:label:idurl' => 'Identity URL',
		'openid:label:pape' => 'Request these PAPE policies',
		'openid:label:submit' => 'Login',
	
		'openid:error:nourl' => 'No OpenID URL found',
		'openid:error:notopenid' => 'Authentication error, this is not a valid OpenID.',
		'openid:error:couldnotredirect' => 'Could not redirect to server: ',
	
		'openid:error:unknown' => 'Unknown authentication return, you shouldn\'t see this!',
		'openid:error:cancelled' => 'OpenID authentication cancelled',
		'openid:error:failed' => 'OpenID authentication failed: %s',
	
		'openid:ok' => 'You have successfully verified <a href="%s">%s</a> as your identity.',
		'openid:xriid' => '(XRI CanonicalID: %s)',
	
		'openid:ok:login' => 'Welcome %s!',
		'openid:error:loginfail' => 'Sorry, there was a problem updating your OpenID user',
	
		'openid:service:claimid' => 'ClaimID',
		'openid:service:livejournal' => 'Livejournal',
		'openid:service:aim' => 'AIM',
		'openid:service:wordpress' => 'Wordpress.com',
		'openid:service:flickr' => 'Flickr',
		'openid:service:default' => 'Other...',
	
		'openid:label:openid' => 'Open ID Login',
		'openid:label:username' => 'Username',
		'openid:label:service' => 'OpenID Service',
		'openid:label:url' => 'Alternative OpenID handler',
	);
	
	register_language($english, 'en');
?>