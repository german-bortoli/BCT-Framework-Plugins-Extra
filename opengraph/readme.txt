
@file
Adds Open Graph support to pages.

This adds open graph headers to static and object pages, static pages are defined by a $CONFIG array
and seo for object pages are derived.

\code
$CONFIG->opengraph = array(

'mypage' => array(
'og:title' => 'My Page',
'og:image' => 'http://foo.com/image.jpg',
'og:type' => 'organisation',
),
'__default__' => array(
...
)
)
\endcode

Where appropriate some variables have defaults and if not specified are included
with information from the environment. These include:

og:site_name - Site name, obtained from $CONFIG->name
og:url - Obtained from current page
og:title - Optained from the current page
og:description - Description obtained from $CONFIG->description


@package opengraph
@license The MIT License (see LICENCE.txt), other licenses available.
@author Marcus Povey <marcus@marcus-povey.co.uk>
@copyright Marcus Povey 2009-2012
@link http://www.marcus-povey.co.uk
@see http://ogp.me/
@see http://developers.facebook.com/docs/opengraph/

