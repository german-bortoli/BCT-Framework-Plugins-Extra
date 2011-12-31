<?php

	global $CONFIG;

	$name = "";
	$email = "";
	$url = "";
	$user_guid = "";
	
	$user = user_get_current();
	if ($user)
	{
		$name = $user->getName();
		$email = $user->getEmail();
		$url = $user->homepage;
		$user_guid = $user->guid;
	}
?>
<div class="comment-form">
	<form method="post" action="<?php echo $CONFIG->wwwroot; ?>action/comment/add">
		<?php echo view('input/securitytoken'); ?>
		<?php echo view('input/hidden', array('name' => 'annotating_guid', 'value' => $vars['annotating_guid'])); ?>
		<div class="name"><label><?php echo _echo('comments:name'); ?>: <?php echo view('input/text', array('name' => 'name', $name)); ?></label></div>
		<div class="email"><label><?php echo _echo('comments:email'); ?>: <?php echo view('input/text', array('name' => 'email', $email)); ?></label></div>
		<div class="homepage"><label><?php echo _echo('comments:homepage'); ?>: <?php echo view('input/text', array('name' => 'homepage', $url)); ?></label></div>
		
		<div class="comment">
		<label><?php echo _echo('comments:comment'); ?>:
		<?php
			echo view('input/longtext', array('name' => 'text'));
		?>
		</label>
		</div>
		<?php echo view('input/captcha'); ?>
		<?php echo view('input/submit', array('value' => _echo('comments:post'))); ?>
	</form>
</div>