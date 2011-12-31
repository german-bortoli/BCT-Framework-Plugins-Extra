<?php
	$item = $vars['item'];
	
	if (($item->longitude) && ($item->latitude))
	{
?>
<georss:point> <?php echo $item->latitude; ?> <?php echo $item->longitude; ?> </georss:point>
<?php 
	}
?>