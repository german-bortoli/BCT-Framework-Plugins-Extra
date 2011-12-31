<?php

	/**
	 * @file
	 * Recaptcha plugin.
	 * 
	 * Implements recaptcha, use view ('captcha') to display the captcha on the form and
	 * populate $CONFIG->recaptcha_actions with a list of actions you want to validate.
	 * 
	 * Populate $CONFIG->recaptcha_publickey and $CONFIG->recaptcha_privatekey with your key details.
	 * 
	 * 
	 * @package recaptcha
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	require_once(dirname(__FILE__) . "/vendor/recaptcha-php-1.10/recaptchalib.php");
	
	function recaptcha_init()
	{
		global $CONFIG;
		
		if (is_array($CONFIG->recaptcha_actions))
		{
			foreach ($CONFIG->recaptcha_actions as $action)
				register_event('action', $action, 'recaptcha_action_event_handler');
		}
		
	}
	
	/**
	 * Validate the captcha, preventing the action from executing if the captcha fails by forwarding
	 * the user back to the source form.
	 */
	function recaptcha_action_event_handler($class, $event, $parameters) 
	{
		global $CONFIG;
		
		if (($class == 'action') && (in_array($event, $CONFIG->recaptcha_actions)))
		{
			$resp = recaptcha_check_answer ($CONFIG->recaptcha_privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                input_get('recaptcha_challenge_field'),
                                input_get('recaptcha_response_field')
			);
			
			if (!$resp->is_valid) {
				error_message(_echo('recaptcha:failed'));
				
				log_echo("(reCAPTCHA said: " . $resp->error . ")", 'ERROR');
				
				forward($_SERVER['HTTP_REFERER']);
				
				return false;
			}
		}
	}
	
	register_event('system', 'init', 'recaptcha_init');
?>