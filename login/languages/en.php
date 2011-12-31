<?php
	$en = array(
			'login' => 'Log In',
			'logout' => 'Log Out',
			'register' => 'Register',

			'login:fullname' => 'Full name',
			'login:username' => 'Username',
			'login:email' => 'Email',
			'login:password' => 'Password',
			'login:password2' => 'Password again (for verification)',
	
	
			'login:message:loggedout' => 'Logged out',
			'login:message:loggedout:error' => 'Sorry, there was a problem logging you out.',
			'login:message:loggedin' => 'Welcome %s',
			'login:message:loggedin:error' => 'Sorry, there was a problem logging you in.',
	
			'login:register:missing' => 'Please fill out all the fields',
			'login:register:invalidemail' => 'Sorry, the email address you entered is invalid.',
			'login:register:invalidpassword' => 'The passwords you entered is either too short, or not the same.',
			'login:register:invalidusername' => 'Username provided contains invalid characters',
			'login:register:couldnotsave' => 'There was a problem creating the new user, please try again.',
			'login_register:userexists' => 'Username already registered, please use a different name',
	
			'login:register:ok' => 'User successfully registered!'
	);
	
	register_language($en, 'en');
?>