<?php
	$name = $vars['name'];
	
	static $id;
	
	$id++;

	$cnttxt = "";
	if ($id)
		$cnttxt = "_$id";
?>
<script type="text/javascript">
	function foundLocation<?php echo $cnttxt;?>(position)
	{
		var lat = position.coords.latitude;
		var long = position.coords.longitude;

		var object_lat = document.getElementById('lat<?php echo $cnttxt; ?>');
		var object_long = document.getElementById('long<?php echo $cnttxt; ?>');

		object_lat.value = lat;
		object_long.value = long;
	}
	
	function noLocation<?php echo $cnttxt;?>()
	{
		alert('<?php echo _echo('geocoder:notfound'); ?>');
	}
</script>
<input type="hidden" name="<?php echo $name; ?>_latitude" id="lat<?php echo $cnttxt; ?>" value="" />
<input type="hidden" name="<?php echo $name; ?>_longitude" id="long<?php echo $cnttxt; ?>" value="" />
<script type="text/javascript">
	navigator.geolocation.getCurrentPosition(foundLocation<?php echo $cnttxt;?>, noLocation<?php echo $cnttxt;?>);
</script>
<?php 
	$id++;
?>