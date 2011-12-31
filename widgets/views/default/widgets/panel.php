<?php
	$context = $vars['context'];
	$owner_guid = $vars['owner_guid'];
	$user = user_get_current();
	
	if (!$context) $context = 'default';
	if ((!$owner_guid) && ($user)) $owner_guid = $user->guid; 
		
	$objects = getObjects('obj:widget%', array('context' => $context, 'owner_guid' => $owner_guid), array('orderby' => 'order', 'order' => 'ASC'));
?>
<div class="widget_panel context_<?php echo $context; ?>">	
	<?php echo $objects; ?>
</div>