<?php

	$item = $vars['item'];
	$size = $vars['size'];
	if (!$size) $size = 'small';

        if (($vars['edit']) && ($item->canEdit()))
        {
            // Edit mode icon display
?>
<div class="icon edit">
	<a href="<?php echo $item->getUrl(); ?>/editicon"><img src="<?php echo $item->getIcon($size); ?>" /></a>

	<p><a href="<?php echo $item->getUrl(); ?>/editicon"><?php echo _echo('profile:editicon'); ?></a></p>
</div>
<?php
        }
        else
        {
?>
<div class="icon">
	<a href="<?php echo $item->getUrl(); ?>"><img src="<?php echo $item->getIcon($size); ?>" /></a>
</div>
<?php
        }