<?php

	$english = array (
		'openssl:generatekeypair:ok' => 'New keypair generated',
		'openssl:generatekeypair:failed' => 'There was a problem generating a new keypair',
		'openssl:api:timeout' => 'API call time to far in the past or future',
		'openssl:api:noshash' => 'API call does not contain a hash',
		'openssl:api:hashinvalid' => 'API hash invalid. Make sure you have sha1 encoded the url string of API call variables + timestamp. E.g. $hash = sha1(\'method=foo&var1=bar\' . time())',
		'openssl:api:signatureinvalid' => 'Signature is invalid.'
	);
	
	register_language($english, 'en');
?>