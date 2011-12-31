<?php 
	$lang = $vars['language'];
	if (!$lang) $lang = language_get_current();
?>
<iframe
	class="output-map"
	id="<?php echo $vars['id']; ?>"
 
	width="<?php echo $vars['width'] ? $vars['width'] : '425'; ?>" 
	height="<?php echo $vars['height'] ? $vars['height'] : '350'; ?>" 
	frameborder="0" 
	scrolling="no" 
	marginheight="0" 
	marginwidth="0" 
	
	src="http://maps.google.co.uk/maps?hl=<?php echo $lang; ?>&q=<?php echo urlencode($vars['value']); ?>&ie=UTF8&output=embed">
</iframe>