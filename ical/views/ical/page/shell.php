<?php
	header('Content-Type: text/calendar');
	header("Content-Disposition: attachment; filename=\"{$vars['title']}.ics\"");
	global $version, $release, $codename, $CONFIG;
	
	
?>BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Barcamp Transparency Framework//NONSGML <?php echo "BCT $release $codename ($version)"; ?>//EN
<?php echo $vars['body']; ?>
END:VCALENDAR