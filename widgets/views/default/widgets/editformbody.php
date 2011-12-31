<?php
	global $CONFIG;

	echo view('input/hidden', array('name' => 'context', 'value' => $vars['context']));

	?>
	<select name="handler" class="input-select">
		<option value=""></option>
		<?php
			$context = $vars['context'];
			foreach ($CONFIG->_WIDGET_HANDLERS[$context] as $handler)
				echo "<option value=\"$handler\">"._echo("widget:$handler")."</option>";	

		?>
	</select>
	<?php
	
	
	echo view('input/submit', array('name' => 'submit', 'value' => 'save'));