<?php

	global $CONFIG;

	$url_base = $CONFIG->wwwroot;
	if ((!isset($CONFIG->login_http_only)) || (!$CONFIG->login_http_only))
	    $url_base = str_replace('http://', 'https://', $url_base);
?>
<div class="registerform">
	<form action="<?php echo $url_base; ?>action/register" method="POST">
		<?php echo view('input/securitytoken'); ?>
		
		<p><label><?php echo _echo('login:fullname'); ?> <?php echo view('input/text', array('name' => 'name', 'required' => true, 'autocomplete' => 'on')) ?></label></p>
		<p><label><?php echo _echo('login:username'); ?> <?php echo view('input/text', array('name' => 'username', 'required' => true, 'autocomplete' => 'on')) ?></label></p>
		<p><label><?php echo _echo('login:email'); ?> <?php echo view('input/email', array('name' => 'email', 'required' => true, 'autocomplete' => 'on')) ?></label></p>
		<p><label><?php echo _echo('login:password'); ?> <?php echo view('input/password', array('name' => 'password', 'required' => true)) ?></label></p>
		<p><label><?php echo _echo('login:password2'); ?> <?php echo view('input/password', array('name' => 'password2', 'required' => true)) ?></label></p>
		<?php echo view('input/captcha'); ?>
		<p><?php echo view('input/submit', array('name' => 'submit', 'value' => _echo('register'))) ?></p>
	</form>
</div>