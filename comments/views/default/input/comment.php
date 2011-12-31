<?php
	/**
	 * Comment form.
	 * This view uses either an object item or a manual comment ID.
	 */


	global $CONFIG;
	
	$item = $vars['item'];

	$comments = getObjects(
		'annotation:comment%', 
		array('annotating_guid' => $item->getGUID())
	);	
	
	if ($comments)
		echo $comments;		
	
	echo view('comments/form', array('annotating_guid' => $item->getGUID()));
?>