<?php
	global $CONFIG;

	// What menu are we're using
	$menu = $vars['menu'];
	if (!$menu) $menu = 'default';
	
	// Menu details
	if (isset($vars['menu_details']))
		$menu_details = $vars['menu_details'];
	else
		$menu_details = $CONFIG->menus[$menu];
?>
<div class="menu">
	<div class="<?php echo $menu; ?>">
		<ul>
		<?php
			foreach ($menu_details as $context => $url)
			{
				if (strpos($url, 'http')===false)
					$url = $CONFIG->wwwroot . $url;
			?>
				<li class="<?php 
				
					echo $context; 
				
					if (page_get_context() == $context)
					    echo " selected";
						
				?>"><a href="<?php echo $url; ?>"><?php echo _echo("menu:label:$menu:$context"); ?></a></li>
			<?php	
			}
		?>
		</ul>
	</div>
</div>