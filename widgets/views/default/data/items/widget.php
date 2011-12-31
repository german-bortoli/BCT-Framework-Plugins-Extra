<?php

	$item = $vars['item'];

?>
<div id="widget_<?php echo $item->guid ?>" class="widget handler_<?php echo $item->handler; ?> context_<?php echo $item->context; ?>">
<?php echo view("widgets/{$item->handler}/view", $vars); ?>
</div>