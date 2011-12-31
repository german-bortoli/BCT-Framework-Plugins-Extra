<?php
	if ($vars['edit'])
		echo view('profile/edit', $vars);
	else
	{
		if ($vars['full'])
			echo view('profile/full', $vars);
		else
			echo view('profile/list', $vars);
	}