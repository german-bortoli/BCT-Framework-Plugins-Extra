<h1><?php echo sprintf(_echo('profile:user:editicon'), $vars['user']->getName()); ?></h1>
<div class="editform">
    <?php
	echo view('input/form', array(
		'name' => 'icon',
		'action' => $CONFIG->wwwroot . 'action/profile/editicon',
		'enctype' => "multipart/form-data",
		'body' =>
			view('input/hidden', array('name' => 'item_guid', 'value' => $vars['user']->getGUID())) .
			view('input/formbody', array(
				'fields' => array(
				    'icon' => 'file'
				),
				'name' => 'icon'
			)) .
			view('input/submit', array('name' => 'submit', 'value' => _echo('profile:save')))
	));
?>
</div>