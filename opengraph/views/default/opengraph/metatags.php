<?php
	global $CONFIG;
	
	// Detect url
	$url = current_page_url();
	$item = getObjectByUrl($url);
	if (!$item) $item = getObject(input_get('guid'));
	if (!$item) $item = getObject(input_get('item_guid'));
	if (!$item) $item = getObject(input_get('object_guid'));
	if (!$item) $item = getObject(input_get('user_guid'));
	
	// Detect context
	$context = page_get_context();
	if (!isset($CONFIG->opengraph[$context])) $context = '__default__';
	if (!$context) $context = '__default__';

	// Create some defaults for all pages
	if (!$CONFIG->opengraph) $CONFIG->opengraph = array();
	if (!isset($CONFIG->opengraph[$context])) $CONFIG->opengraph[$context] = array();
	if (!$CONFIG->opengraph[$context]['og:url']) $CONFIG->opengraph[$context]['og:url'] = $url;
	if (!$CONFIG->opengraph[$context]['og:site_name']) $CONFIG->opengraph[$context]['og:site_name'] = $CONFIG->name;
	if (!$CONFIG->opengraph[$context]['og:description']) $CONFIG->opengraph[$context]['og:description'] = $CONFIG->description;
	if (!$CONFIG->opengraph[$context]['og:title']) $CONFIG->opengraph[$context]['og:title'] = $vars['title'];

	
	// Is object page?
	if (($item) && ($item instanceof BCTObject)) {
	    
	    $CONFIG->opengraph[$context]['og:image'] = $item->getIcon('large');
	    $CONFIG->opengraph[$context]['og:url'] = $item->getUrl();

	    if ($item->location) $CONFIG->opengraph[$context]['og:locality'] = $item->location;
	    if ($item->latitude) $CONFIG->opengraph[$context]['og:latitude'] = $item->latitude;
	    if ($item->longitude) $CONFIG->opengraph[$context]['og:longitude'] = $item->longitude;

	    $desc = $item->description;
	    if (!$desc) $desc = $item->title;
	    if (!$desc) $desc = $item->name;
	    if (!$desc) $desc = $CONFIG->opengraph[$context]['og:description'];
	    if ($desc) $CONFIG->opengraph[$context]['og:description'] = $desc;
	
	}

	if (isset($CONFIG->opengraph[$context]))
	{
		foreach ($CONFIG->opengraph[$context] as $name => $graphcontent)
		{
?>
	<meta name="<?php echo $name; ?>" content="<?php echo addslashes($graphcontent); ?>" />
<?php 
		}
	}
