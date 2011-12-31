<?php
	global $CONFIG;

	$size = $vars['size'];
	if (!$size) $size = 40;
	
	$rating = $vars['rating'];
	if (!$rating) $rating = 'g';
	
	$default = $vars['default'];
	if (!$default) $default = $CONFIG->gravatar_default_iconset ? $CONFIG->gravatar_default_iconset : 'monsterid';
	
	
	$email = $vars['email'];
	
	
?>
<img class="gravatar avatar" src="<?php echo gravatar_url_from_email($email, $size, $rating, $default); ?>" /> 