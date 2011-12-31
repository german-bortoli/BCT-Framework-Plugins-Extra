<?php
	global $CONFIG;

	$item = $vars['item'];
?>
<div class="profile edit<?php echo " " . strtolower(get_class($vars['item'])); ?>">
		<?php echo view('profile/icon', $vars + array('size' => 'large')); ?><!--  convert to edit -->
		
		<h1><a href="<?php echo $item->getUrl(); ?>"><?php echo $item->getName(); ?></a></h1>

		<div class="body">
		<?php 
		echo view('input/form', array(
			'name' => 'profile',
			'action' => $CONFIG->wwwroot . 'action/profile/edit',
			'body' => 
				view('input/formbody', array(
					'fields' => $CONFIG->profile,
					'name' => 'profile',
					'values' => $item
				)) . 
				view('input/submit', array('name' => 'submit', 'value' => _echo('profile:save')))
		));
		
		?>
		</div>
</div>