
@file
Adds SEO to pages.

This adds SEO headers to static and object pages, static pages are defined by a $CONFIG array
and seo for object pages are derived.

\code
$CONFIG->seo = array(

'mypage' => array(
'keywords' => 'cool, seo, highchair, context specific',
'description' => 'Override a specific page context'
),
'__default__' => array(
'keywords' => 'cool, seo, highchair',
'description' => 'This is the default fallback seo included on all pages unless overridden'
)
)
\endcode

@package seo
@license The MIT License (see LICENCE.txt), other licenses available.
@author Marcus Povey <marcus@marcus-povey.co.uk>
@copyright Marcus Povey 2009-2012
@link http://www.marcus-povey.co.uk

