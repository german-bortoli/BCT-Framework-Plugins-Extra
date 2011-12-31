/**
 * MAP geocoder stuff
 */
 
function geocoder_update_map(id, input_field)
{
	var map = document.getElementById(id);
	var location_ctl = document.getElementById(input_field);
	
	map.src = "http://maps.google.co.uk/maps?&hl=en&&q=" + location_ctl.value + "&ie=UTF8&output=embed";
	map.reload();
}