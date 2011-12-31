<?php

	$owner = getObject($vars['item']->owner_guid);

	if ($vars['item']->canView()) {
?>BEGIN:VEVENT 
UID:<?php echo $vars['item']->guid; ?>@<?php echo $_SERVER['SERVER_NAME']; ?> 
DTSTAMP:<?php echo date("Ymd\THis\Z", $vars['item']->getCreatedTs()); ?> 
ORGANIZER;CN=<?php echo $owner ?  $owner->getName() : ''; ?>:MAILTO:<?php echo $owner ?  $owner->getEmail() : ''; ?> 
DTSTART:<?php echo date("Ymd\THis\Z", $vars['item']->start); ?> 
DTEND:<?php echo date("Ymd\THis\Z", $vars['item']->end); ?> 
SUMMARY:<?php echo $vars['item']->title; ?> 
DESCRIPTION:<?php echo $vars['item']->description; ?> 
END:VEVENT
<?php } ?>