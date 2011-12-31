<div class="openid">
	<form method="POST" action="<?php echo $CONFIG->wwwroot ?>openid/login">
		<?php echo view('input/securitytoken'); ?>
		
		<?php //TODO: Nice selector for common services ?>
		
		<p>
			<label><?php echo _echo('openid:label:username'); ?>:<br />
				<input type="text" name="username" value="" />
			</label>
		</p>
		<p>
			<label><?php echo _echo('openid:label:service'); ?>:<br /></label>
		
			<?php foreach ($CONFIG->openid_providers as $service => $url) 
			{
			?>
				<label><input type="radio" name="service" class="openid_selector_<?php echo $service?>" value="<?php echo $service?>" /><?php echo _echo('openid:service:'.$service); ?></label><br />
			<?php 	
			}
			?>	
				<label><input type="radio" name="service" value="" class="openid_selector_default" checked="true" /><?php echo _echo('openid:service:default'); ?></label><br />
		</p>
	
		
		<label><?php echo _echo('openid:label:idurl'); ?>: <?php echo view('input/url', array('name' => 'identifier')); ?></label>
		<?php 
			$pape = $vars['pape_uris'];
			if (is_array($pape))
			{
				echo _echo('openid:label:pape');
				
				foreach ($pape as $uri)
				{
		?>
				<label><input type="checkbox" name="policies[]" value="<?php echo $uri; ?>" /><?php echo $uri; ?></label><br/>
		<?php 
				}
			}
		?>
		
		<input type="submit" value="<?php echo _echo('openid:label:submit');?>" />
	</form>
</div>