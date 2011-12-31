<?php
    global $CONFIG;

    $exception = $vars['exception'];

    $class = get_class($exception);
    $class_lower = strtolower($class);
    $message = $exception->getMessage();

    $stdclass = new stdClass;
    $stdclass->message = $message;
    if ($CONFIG->debug) {
	$stdclass->trace = $exception->getTrace();
	$stdclass->debug = var_export($exception, true);
    }
    echo xml_serialise_object($stdclass, $class);
