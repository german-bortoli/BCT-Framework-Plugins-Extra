<?php

	global $CONFIG;
	$item = $vars['item'];

?>
<div id="widget_<?php echo $item->guid ?>" class="widget_edit handler_<?php echo $item->handler; ?> context_<?php echo $item->context; ?>">
	<div class="title"><?php echo _echo('widget:'.$item->handler); ?></div>

<?php 
	echo view('input/form', array(
	
		'body' =>
			view('input/hidden', array('name' => 'widget_guid', 'value' => $item->guid)) . 
			view("widgets/{$item->handler}/edit", $vars) .
			view('input/submit', array('name' => 'submit', 'value' => 'save')),

		'action' => $CONFIG->wwwroot . 'action/widgets/save'
				
	));
?>
	<div class="controls">todo: delete</div>
</div>