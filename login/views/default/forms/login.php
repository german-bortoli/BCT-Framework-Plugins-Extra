<?php

	global $CONFIG;

	$url_base = $CONFIG->wwwroot;
	if ((!isset($CONFIG->login_http_only)) || (!$CONFIG->login_http_only))
	    $url_base = str_replace('http://', 'https://', $url_base);
?>
<div class="loginform">
	<form action="<?php echo $url_base; ?>action/login" method="POST">
		<?php echo view('input/securitytoken'); ?>
		
		<p><label><?php echo _echo('login:username'); ?> <?php echo view('input/text', array('name' => 'username', 'required' => true)) ?></label></p>
		<p><label><?php echo _echo('login:password'); ?> <?php echo view('input/password', array('name' => 'password', 'required' => true)) ?></label></p>
		<p><?php echo view('input/submit', array('internalname' => 'submit', 'value' => _echo('login'))) ?></p>
	</form>
</div>