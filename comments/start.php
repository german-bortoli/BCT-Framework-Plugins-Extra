<?php

	/**
	 * @file
	 * Generic comment plugin.
	 * 
	 * This plugin provides functions for commenting on data items, and it works by extending specified item 
	 * views with a comment data item and any comments previously listed. 
	 * 
	 * @note Populate $CONFIG->comments_on_items with a list of data types, requires that an item has a valid GUID
	 *	in order to map comments correctly.
	 * 
	 *	Alternatively, you can add comments manually by displaying the input/comment view and passing an object
	 *	which you wish to comment on.
	 * 
	 * @package comments
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	/**
	 *	Generic comment object
	 */
	class Comment extends Annotation
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('comment'); 
		}
	}

	function comments_init()
	{
		global $CONFIG;
		
		if (is_array($CONFIG->comments_on_items))
		{
			foreach ($CONFIG->comments_on_items as $view)
				extend_view('data/items/' . strtolower($view), 'input/comment');
		}
		
		// register actions
		register_action('comment/add', 'comments_add_action');
		//register_action('comment/delete', 'comments_delete_action');
		
		// Add some CSS
		extend_view('css', 'comments/css');
	}
	
	/**
	 * Create action.
	 *
	 * @param mixed $annotating_guid GUID of the object being commented on
	 * @param string $name Name of commenter
	 * @param string $email Email 
	 * @param string $homepage Homepage
	 * @param string $text The comment text
	 */
	function comments_add_action(
		$annotating_guid,
		$name,
		$email,
		$homepage,
		$text
	)
	{
		if (
			($annotating_guid) &&
			($name) &&
			($email) &&
			($text)
		)
		{
			$comment_on = getObject((int)$annotating_guid);
			
			if ($comment_on)
			{
				$comment = new Comment();
				$comment->name = $name;
				$comment->email = $email;
				$comment->homepage = $homepage;
				$comment->comment = $text;
				$comment->annotate($comment_on);
				
				// Get user ID of any logged in users.
				if ($user = user_get_current()) $comment->owner_guid = $user->guid;
				
				if ($comment->save())
					message(_echo('comments:posted:ok'));
				else
					error_message(_echo('comments:posted:notok'));
			}
			else
				error_message(_echo('comments:posted:notok'));
		}
		else
			error_message(_echo('comments:posted:notok'));
	}
	
	/**
	 * Delete action.
	 *
	 * @param int $comment_guid
	 */
	function comments_delete_action($comment_guid)
	{
		$comment_guid = (int)$comment_guid;
		
		if (($comment = getObject($comment_guid)) && ($comment->canEdit()))
			$comment->delete();
	}

	
	register_event('system', 'init', 'comments_init', 1000);
