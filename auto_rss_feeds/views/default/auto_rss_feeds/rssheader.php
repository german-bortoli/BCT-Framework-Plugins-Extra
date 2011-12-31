<?php
	global $__PAGE_CONTAINS_LIST;
	
	if ($__PAGE_CONTAINS_LIST) {
?>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php
	
	$url = current_page_url();
	
	if (substr_count($url, '?')) 
		$url .= "&view=rss";
	else 
		$url .= "?view=rss";
	
	echo $url;
?>" />
<?php
	}
?>