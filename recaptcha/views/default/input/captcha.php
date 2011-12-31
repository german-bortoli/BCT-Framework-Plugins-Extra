<div class="captcha recaptcha">
<?php
	global $CONFIG;
	
	$ssl = false;
	if ($vars['use_ssl']) $ssl = $vars['use_ssl'];
	
	echo recaptcha_get_html($CONFIG->recaptcha_publickey, null, $ssl);
?>
</div>