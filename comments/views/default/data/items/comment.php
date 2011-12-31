<?php

	global $CONFIG;
	
	$item = $vars['item'];
	
	$email = $item->email;
	$homepage = $item->homepage;
	$name = $item->name;
	$comment = $item->comment;
	$created_ts = $item->created_ts;
	
	// get icon url or if appropriate, otherwise 

?>
<div id="comment-<?php echo $item->guid; ?>" class="comment">
	<div class="comment-author vcard">
	<?php
		if ((view_exists('output/gravatar') && ($email)))
			echo view('output/gravatar', array('email' => $email));
	?>
		<cite class="fn">
			<?php if ($homepage) { ?><a class="url" rel="external nofollow" href="<?php echo $homepage; ?>"><?php } ?><?php echo $name; ?><?php if ($homepage) { ?></a><?php } ?>
		</cite>
		<span class="says"><?php echo _echo('comments:says'); ?>:</span>
	</div>
	<div class="comment-meta">
		<?php echo date('F j, Y g:i a', $created_ts); ?>
		<?php
			if ($item->canEdit())
			{
				echo " (".view('output/confirmurl', array(
					'href' => $CONFIG->wwwroot.'action/comment/delete?comment=' . $item->guid,
					'value' => _echo('comments:remove'),
					'is_action' => true
				)).")";
			}
		?>
	</div>
	<?php echo view('output/longtext', array('value' => $comment)); ?>
</div>