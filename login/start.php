<?php
	/**
	 * @file
	 * Basic login and register forms and actions.
	 * 
	 * This tool provides a basic login and register action and forms to go along with it. This 
	 * provides basic user creation and registration. Extend this for a more complete user experience.
	 *
	 * @note This will use HTTPS by default, to use HTTP only set $CONFIG->login_http_only = true;
	 * 
	 * @package login
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */


	function login_init()
	{
		global $CONFIG;
		
		// Register actions
		register_action('login', 'login_action');
		register_action('logout', 'login_logout_action', false);
		register_action('register', 'login_register_action');
		
	}
	
	function login_register_action($name, $username, $email, $password, $password2)
	{
		global $CONFIG;
		
		$name = trim($name);
		$username = trim($username);
		$email = trim($email);
		$password = trim($password);
		$password2 = trim($password2);
		
		if (($name) && ($username) && ($email) && ($password) && ($password2))
		{
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
			{
				if (($password) && (strlen($password)>4) && ($password == $password2))
				{
					$username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
					if ($username)
					{
						if (!user_get_by_username($username))
						{
							$user = new User();
							$user->name = $name;
							$user->setUsername($username);
							$user->email = $email;
							$user->setPassword($password);
							
							if ($user->save())
							{
								message(_echo('login:register:ok'));
								return $CONFIG->wwwroot;
							}
							else
								error_message(_echo('login:register:couldnotsave'));
						}
						else
							error_message(_echo('login_register:userexists'));
					}
					else
						error_message(_echo('login:register:invalidusername'));
				}
				else
					error_message(_echo('login:register:invalidpassword'));
			}
			else
				error_message(_echo('login:register:invalidemail'));
		}
		else
			error_message(_echo('login:register:missing'));
	}
	
	function login_action($username, $password)
	{
		global $CONFIG;
		
		$user = user_get_by_username($username);
		if (($user) && ($user->isPasswordCorrect($password))) {
			message(sprintf(_echo('login:message:loggedin'), $user->name));
			$user->login();
			
			return $CONFIG->wwwroot;	
		}
		else
			error_message(_echo('login:message:loggedin:error'));
	}
	
	function login_logout_action()
	{
		$user = user_get_current();
		if (($user) && ($user instanceof User))
		{
			message(_echo('login:message:loggedout'));
			$user->logout();
			
			return true;
		}
		else
			error_message(_echo('login:message:loggedout:error'));
	}
	
	register_event('system', 'init', 'login_init');
	