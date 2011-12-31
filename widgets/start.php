<?php

	/**
	 * @file
	 * Basic Widget Framework.
	 * 
	 * Provides a basic widgeting framework, simplifying certain page construction.
	 * 
	 * Widgets are views in views/default/widgets/HANDLER/view.php and edit.php
	 * 
	 * @package Widgets
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	/**
	 * Widget object.
	 * 
	 * This class represents a widget in the system.
	 */
	class Widget extends Object
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('widget');
		}
		
		/**
		 * Set the context of the widget.
		 * 
		 * This function sets the context for which this widget will be displayed.
		 *
		 * @param string $context The context.
		 */
		public function setContext($context) { $this->context = $context; }
		
		/**
		 * Set the handler for this widget.
		 * 
		 * The handler exists in views/default/widgets/HANDLER/ and consists of edit.php and view.php which provide 
		 * edit and display a widget respectively.
		 *
		 * @param string $handler
		 */
		public function setHandler($handler) { $this->handler = $handler; }
		
		/**
		 * Set the order in which the widget will be displayed.
		 *
		 * @param int $order Order
		 */
		public function setOrder($order = 1) { $this->order = (int)$order; }
		
		/**
		 * Override canEdit to test against owner_guid.
		 *
		 * @see BCTObject::canEdit()
		 * @param User $user
		 * @return bool
		 */
		public function canEdit(User $user = null) 
		{
			$default = false;
			if (!$user) $user = user_get_current();
			if (($user) && ($user->guid == $this->owner_guid)) $default = true;
			return $this->__send_object_hook('canedit', $default);		
		}
	}

	function widgets_init() 
	{ 
		global $CONFIG;
		
		register_action('widgets/save', 'widgets_save_widget');
		register_action('widgets/add', 'widgets_add_to_panel');
		register_action('widgets/remove', 'widgets_remove_from_panel');
		
		if ($CONFIG->debug)
			widgets_register_handler('default', 'widget_example');
	}
	
	/**
	 * Register a widget handler.
	 *
	 * @param mixed $contexts One or more contexts on which this widget will be available.
	 * @param string $handler Name of the handler
	 */
	function widgets_register_handler($contexts, $handler)
	{
		global $CONFIG;
		
		if (!$handler) return false;
		if (!$contexts) return false;
		if (!is_array($contexts)) $contexts = array($contexts);
		
		if (!$CONFIG) $CONFIG->_WIDGET_HANDLERS = array();
		
		foreach ($contexts as $context) 
		{
			if (!$CONFIG->_WIDGET_HANDLERS[$context]) 
				$CONFIG->_WIDGET_HANDLERS[$context] = array();
				
			$CONFIG->_WIDGET_HANDLERS[$context][] = $handler;
		}
		
		return true;
	}

	/**
	 * Save the details of a widget.
	 *
	 */
	function widgets_save_widget($widget_guid, $params)
	{
		$widget = getObject((int)$widget_guid);
		if (($widget) && ($widget->canEdit()))
		{
			if (($params) && (is_array($params)))
			{
				foreach ($params as $param => $value)
					$widget->$param = $value;
			}
			
			if ($widget->save()) return true;
		}
		
	}
	
	/**
	 * Add a widget to the panel.
	 *
	 * @param string $context
	 * @param string $handler
	 */
	function widgets_add_to_panel($context, $handler)
	{
		$user = user_get_current();
		if (($context) && ($handler) && ($user)) {
			$widget = new Widget();
			
			$widget->setHandler($handler);
			$widget->setContext($context);
			$widget->setOrder();
			$widget->owner_guid = $user->guid;
		
			if ($widget->save()) return true;
		}
		
	}

	/**
	 * Remove a widget from the panel.
	 *
	 * @param int $widget_guid
	 */
	function widgets_remove_from_panel($widget_guid)
	{
		$widget = getObject((int)$widget_guid);
		if ($widget->canEdit())
			$widget->delete();
			
		return true;
	}
	
	register_event('system', 'init', 'widgets_init');
