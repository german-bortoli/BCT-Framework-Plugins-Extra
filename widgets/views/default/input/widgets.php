<?php
	global $CONFIG;
	
	$name = $vars['name'];
	$context = $vars['context'];
	$owner_guid = $vars['owner_guid'];
	
	if (!$name) $name = $context;
	if (!$context) $context = $name;
	if (!$context) $name = $context = 'default';
	
	$vars['body'] = view('widgets/editformbody', array(
		'context' => $context
	));
	
	$vars['action'] = $CONFIG->wwwroot . 'action/widgets/add';
	
	$user = user_get_current();
	if ((!$owner_guid) && ($user)) $owner_guid = $user->guid; 
?>
<div class="widget_edit_panel context_<?php echo $context; ?>">
	<div class="widget_edit_containers">
	<?php
		// Get widgets for context
		$widgets = getObjects('obj:widget%', array('context' => $context, 'owner_guid' => $owner_guid), array('orderby' => 'order', 'order' => 'ASC'));
		
		// Loop and encapsulate in widget containers
		if ($widgets)
			foreach ($widgets as $widget)
				echo view('widgets/editcontainer', array('item' => $widget));
		
	?>
	</div>
	<div class="widget_edit_form">
		<?php echo view('input/form', $vars); ?>
	</div>
</div>