
@file
OpenID support.

This plugin provides OpenID login support.

Currently it only provides client support, but Server support could be a future enhancement. This
plugin was written to kill two birds with one stone - build support for BCT & research OpenID
for a client.

Uses the OpenID library from JanRain, documentation can be found at:

http://openidenabled.com/files/php-openid/docs/2.1.3/


@section Important note

Due to a bug in the JanRain library, Open ID will not work when passed through the standard BCT page
handler.

Until this issue is addressed in the core library, you must modify your .htaccess file accordingly:

...

\code
# These rules must be last
RewriteCond %{REQUEST_URI} !/plugins/openid/return.php(.*)$
RewriteCond %{REQUEST_URI} !/_(.*)$
RewriteRule ^(.*)$ _pages/page_handler.php?page=$1 [QSA]
\endcode

Or you can apply the htaccess_dist.patch file included with this plugin.

@package openid
@license The MIT License (see LICENCE.txt), other licenses available.
@author Marcus Povey <marcus@marcus-povey.co.uk>
@copyright Marcus Povey 2009-2012
@link http://www.marcus-povey.co.uk
@link http://openidenabled.com/files/php-openid/docs/2.1.3/

