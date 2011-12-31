<?php
	/**
	 * @file
	 * Generic Profile.
	 * 
	 * @note Define $CONFIG->profile = array ('field' => 'type') to define the contents of the profile.
	 * 
	 * @package profile
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	function profile_init()
	{
		// Create page
		register_page('profile', 'profile_page_pagehandler');
		
		// URL handler for users
		register_hook('obj:user', 'property:url', 'profile_url_handler'); // User
		register_hook('obj:user:*', 'property:url', 'profile_url_handler'); // All sub user types
		register_hook('object', 'geturl', 'profile_geturl_handler'); // Reverse map url

                // Profile will provide icons for user objects
                register_hook('obj:user', 'property:icon', 'profile_default_icon');
		
		// Register edit action
		register_action('profile/edit', 'profile_edit_action');
                register_action('profile/editicon', 'profile_editicon_action');

                // Register icon output action
		register_action('profile/icon', 'profile_icon_action', false);
		
		// CSS
		extend_view('css', 'profile/css');
	}
	
	function profile_page_pagehandler($page, array $subpages)
	{
		input_set('user_guid', $subpages[0]);
		
		if (isset($subpages[2]))
		{
			switch($subpages[2])
			{
                                case 'editicon' : require_once(dirname(__FILE__) . '/editicon.php'); break;

				case 'edit' : require_once(dirname(__FILE__) . '/edit.php'); break;
				
				case 'view' :
				default : require_once(dirname(__FILE__) . '/view.php'); break; 
			}
		}
		else
			require_once(dirname(__FILE__) . '/view.php'); 
	}
	
	function profile_url_handler($namespace, $event, $parameters) 
	{
		global $CONFIG;
		$user = $parameters['object']; 
		
		if ($user instanceof User)
			return $CONFIG->wwwroot . 'profile/' . $user->guid . '/' . friendly_url_title($user->getName());
	}
	
	function profile_geturl_handler($namespace, $event, $parameters) 
	{
		$url = $parameters['url'];
		
		$match = array();
		preg_match("/".str_replace('/','\/', $CONFIG->wwwroot)."profile\/([0-9]*)/", $url, $match);
		$result = getObject((int)$match[1]);
		if ($result) return $result;
	}
	
	function profile_edit_action($item_guid)
	{
		global $CONFIG;
		
		$user = getObject($item_guid);

		if (($user) && ($user instanceof User) && ($user->canEdit()))
		{
			foreach ($CONFIG->profile as $field => $type)
			{
				$user->$field = input_get($field);
			}
			
			if ($user->save()) 
				message(_echo('profile:save:ok'));
			else
				error_message(_echo('profile:save:error'));
		}
	}

        function profile_editicon_action($item_guid)
	{
		global $CONFIG;

		$user = getObject($item_guid);

		if (($user) && ($user instanceof User) && ($user->canEdit()))
		{
		    $original = input_get_image('icon');
		    $master = input_get_image('icon', 512, 512);
		    $large = input_get_image('icon', 512, 512, true);
		    $medium = input_get_image('icon', 128, 128, true);
		    $small = input_get_image('icon', 40, 40, true);

		    $filestore = factory('filestore:user:icon', array('user' => $user));

		    if (($filestore) && ($original) && ($master) && ($large) && ($medium) && ($small))
		    {
			$time = time();

			$filestore->writeAll("profile/o_{$time}{$_FILES['icon']['name']}.png", $original);
			$user->icon_original = "o_{$time}{$_FILES['icon']['name']}.png";

			$filestore->writeAll("profile/ma_{$time}{$_FILES['icon']['name']}.png", $master);
			$user->icon_master = "ma_{$time}{$_FILES['icon']['name']}.png";

			$filestore->writeAll("profile/l_{$time}{$_FILES['icon']['name']}.png", $large);
			$user->icon_large = "l_{$time}{$_FILES['icon']['name']}.png";

			$filestore->writeAll("profile/m_{$time}{$_FILES['icon']['name']}.png", $medium);
			$user->icon_medium = "m_{$time}{$_FILES['icon']['name']}.png";
			
			$filestore->writeAll("profile/s_{$time}{$_FILES['icon']['name']}.png", $small);
			$user->icon_small = "s_{$time}{$_FILES['icon']['name']}.png";

			$user->icon_lastmodified = $time;

			if ($user->save()) {
			    message(_echo('profile:icon:save:ok'));
			    forward($user->getUrl());
			}
			else
			    error_message(_echo('profile:icon:save:error'));
		    }
		    else
			error_message(_echo('profile:icon:save:error'));
		}
	}

        /**
         * Icon to display a profile icon.
         * @param int $item_guid User to generate the icon for
         * @param string $size The size of the icon
         */
        function profile_icon_action($item_guid, $size)
        {
            global $CONFIG;

            $user = getObject($item_guid);
            if ((!$user) || (!($user instanceof User))) {
                header("HTTP/1.0 404 Not Found");
                header("Status: 404 Not Found");
                exit;
            }

            switch($size)
            {
		    case 'original' :
		    case 'master' :
                    case 'large' :
                    case 'medium' :
                    case 'small' : break;
                    default: $size = 'small';
            }

            // Load correctly sized image for image
	    $filestore = factory('filestore:user:icon', array('user' => $user));
	    if (!$filestore) {
                header("HTTP/1.0 404 Not Found");
                header("Status: 404 Not Found");
                exit;
            }

	    $thumb = "icon_$size";
            $filename = $user->$thumb;
            $last_modified_time = $user->icon_lastmodified;
	    $filedata = $filestore->readAll('profile/' . $filename);
            if (!$filedata) {
                header("HTTP/1.0 404 Not Found");
                header("Status: 404 Not Found");
                exit;
            }

            // Make sure we're not sending stuff that is cached
            $etag = md5($filedata);

            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
            header("ETag: \"$etag\"");

            if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time || trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                header("HTTP/1.1 304 Not Modified");
                exit;
            }

            // Output, set headers and output
            header("Pragma: public");
            header("Cache-Control: public");
            header("Content-type: image/png");
            header("Content-Disposition: inline; filename=\"$filename\"");
            header("Content-Length: ".strlen($filedata));
            header('Expires: ' . date('r',time() + 864000));

            $split_output = str_split($filedata, 1024);

            foreach($split_output as $chunk)
                    echo $chunk;
        }

        function profile_default_icon($namespace, $hook, $parameters, $return_value)
	{
		global $CONFIG;

		if (!$return_value)
		{
			$size = $parameters['size'];
			if (!$size) $size = 'small';

			$object = $parameters['object'];

			$thumb = "icon_$size";

			if (($object) && ($object instanceof User) && (isset($object->$thumb)))
			{
                            return "{$CONFIG->wwwroot}action/profile/icon?item_guid={$object->guid}&size=$size";
			}
		}
	}
	
	register_event('system', 'init', 'profile_init');
	