<?php

    global $CONFIG;

    if ($CONFIG->debug)
	include_once(dirname(dirname(dirname(dirname(__FILE__)))). '/vendor/h5f/h5f.js');
    else
	include_once(dirname(dirname(dirname(dirname(__FILE__)))). '/vendor/h5f/h5f.min.js');
		