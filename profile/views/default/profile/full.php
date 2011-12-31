<?php
	global $CONFIG;

	$item = $vars['item'];
?>
<div class="profile full<?php echo " " . strtolower(get_class($vars['item'])); ?>">
		<?php echo view('profile/icon', $vars + array('size' => 'large')); ?>
		<h1><a href="<?php echo $item->getUrl(); ?>"><?php echo $item->getName(); ?></a></h1>

		<div class="body">
		<?php echo view('output/formbody', array(
			'fields' => $CONFIG->profile,
			'name' => 'profile',
			'values' => $item
		)); ?>
		</div>
		
		<?php if ($item->canEdit()) { ?>
		<div class="edit_menu">
			<a href="<?php echo $item->getUrl(); ?>/edit"><?php echo _echo('profile:edit'); ?></a>
		</div>
		<?php } ?>
		
		<?php
		/*
		TODO: List basic profile fileds, allow edit and complete of missing (AJAX)
		*/
		?>
</div>