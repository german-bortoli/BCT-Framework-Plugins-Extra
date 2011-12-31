<?php
	$en = array(

		'memcache:notinstalled' => 'PHP memcache module not installed, you must install php5-memcache',
		'memcache:noservers' => 'No memcache servers defined, please populate the $CONFIG->memcache_servers variable',
		'memcache:versiontoolow' => 'Memcache needs at least version %s to run, you are running %s',
		'memcache:noaddserver' => 'Multiple server support disabled, you may need to upgrade your PECL memcache library',

	);

	register_language($en, 'en');