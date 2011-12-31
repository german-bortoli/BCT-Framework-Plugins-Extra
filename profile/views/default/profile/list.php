<?php
	global $CONFIG;

	$item = $vars['item'];
?>
<div class="profile list<?php echo " " . strtolower(get_class($vars['item'])); ?>">
		<?php echo view('profile/icon', $vars + array('size' => 'small')); ?>
		<h1><a href="<?php echo $item->getUrl(); ?>"><?php echo $item->getName(); ?></a></h1>
</div>