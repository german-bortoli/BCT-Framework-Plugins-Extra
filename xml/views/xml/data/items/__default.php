<?php

    if ($vars['item']->canView())
	echo xml_serialise_object($vars['item']->safeExport(), get_class($vars['item']));
