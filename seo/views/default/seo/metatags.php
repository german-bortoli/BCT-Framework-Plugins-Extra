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
	if (!isset($CONFIG->seo[$context])) $context = '__default__';
	if (!$context) $context = '__default__';
	
	
	// Is object page?
	if (($item) && ($item instanceof BCTObject)) {
?>		
		<meta name="keywords" content="<?php 
		
			$tags = $item->tags;
			if (!$tags) $tags = $item->keywords;
			if (!$tags) $tags = $CONFIG->seo[$context]['keywords'];
			if (is_array($tags)) $tags = implode(', ', $tags);
		
			echo $tags;
			
		?>" />
		<meta name="description" content="<?php 
		
			$desc = $item->description;
			if (!$desc) $desc = $item->title;
			if (!$desc) $desc = $item->name;
			if (!$desc) $desc = $CONFIG->seo[$context]['description'];
			
			$desc = addslashes($desc);

                        $desc_array = explode(' ', $desc);
                        $desc = "";
                        foreach ($desc_array as $word)
                        {
                            if (strlen($desc)<250) $desc .= "$word ";
                        }

                        echo $desc;
			
		?>" />
<?php 		
	}
	else
	{
		
		if (isset($CONFIG->seo[$context]))
		{
			
			foreach ($CONFIG->seo[$context] as $name => $seocontent)
			{
?>
		<meta name="<?php echo $name; ?>" content="<?php echo addslashes($seocontent); ?>" />
<?php 
			}
		}

	}
?>
<link rel="canonical" href="<?php echo $url; ?>" />