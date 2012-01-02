
@file
Facebook authentication.

This plugin allows facebook users to log in to a BCT platform site.

@note You need to define the following in settings.php:
$CONFIG->facebook_appid
$CONFIG->facebook_secret
$CONFIG->facebook_ex_perms = array ( array of parameters of requested information - see http://developers.facebook.com/docs/authentication/permissions);

@note You need to set up your application ID by visiting http://developers.facebook.com/apps/ and hitting "Create New App"

@note Plugin requires php5-curl module
@package facebookauth
@license The MIT License (see LICENCE.txt), other licenses available.
@author Marcus Povey <marcus@marcus-povey.co.uk>
@copyright Marcus Povey 2009-2012
@link http://www.marcus-povey.co.uk
@link http://developers.facebook.com/setup/

