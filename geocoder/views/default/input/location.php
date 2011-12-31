<?php
	/**
	 * Input of a location.
	 * 
	 * This is not the same as a geolocator, as sometimes you want to specify a location which is not
	 * your current one!
	 */


	// TODO: Make this more fancy, perhaps with a map picker, geocode address
	
	global $loc_map_div_id;
	$loc_map_div_id++;

	if (!$vars['class']) $vars['class'] = 'input-location';

?>
<div class="<?php echo $vars['class']; ?>">
	<div id="loc_map_div_<?php echo $loc_map_div_id; ?>" <?php if (!$vars['value']) { ?>style="display:none;"<?php } ?>>
	<?php echo view('output/map', array(
		'value' => $vars['value'],
		'id' => "loc_map_$loc_map_div_id"
	)); ?>
	</div>
<?php 	
	if (!$vars['id']) $vars['id'] = "location_ctl_$loc_map_div_id";
	
	$vars['onblur'] = "
		var map_div = document.getElementById('loc_map_div_$loc_map_div_id');
		map_div.style.display = 'block';
		
		geocoder_update_map('loc_map_$loc_map_div_id', '{$vars['id']}');
	";

	echo view('input/text', $vars);
?>
</div>