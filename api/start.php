<?php
	/**
	 * @file
	 * API.
	 * 
	 * This plugin adds REST like API support to a BCT Framework site.
	 * 
	 * API Plugin aware tools can then expose functions by using the api_expose() command.
	 *
	 * Logged in user capacity / authorised clients etc is to be provided by other plugins.
	 * 
	 * @package api
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	/**
	 * API specific exception.
	 */
	class APIException extends BCTPlatformException {}

	/**
	 * Abstract API Result class.
	 * 
	 * We're using Objects here so we can use the output methods, but
	 * we never intend to save them (although conceivably the client may want to).
	 * 
	 * Setting types just incase.
	 */
	abstract class APIResult extends Object
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('api'); 
		}
		
		/**
		 * Set a status code and optional message.
		 *
		 * @param int $status The status code.
		 * @param string $message The message.
		 */
		protected function setStatusCode($status, $message = "")
		{
			$this->status_code = $status;
			$this->message = $message;
		}
		
		/**
		 * Set the result.
		 *
		 * @param mixed $result
		 */
		protected function setResult($result) { $this->result = $result; }
		
		protected function getStatusCode() { return $this->status_code; }
		protected function getStatusMessage() { return $this->message; }
		protected function getResult() { return $this->result; }
	}
	
	/**
	 * Success message.
	 */
	class SuccessAPIResult extends Object
	{
		public static $RESULT_SUCCESS 	= 0;  // Do not change this from 0
		
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('success');	 
		}
		
		public static function getInstance($result)
		{	
			$object = new APISuccessResult();
			$object->setResult($result);
			$object->setStatusCode(SuccessAPIResult::$RESULT_SUCCESS);
			
			return $object;
		}
	}
	
	/**
	 * Failure message.
	 */
	class FailureAPIResult extends Object
	{
		public static $RESULT_FAIL 		= -1 ; // Fail with no specific code
		
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('success');	 
		}
		
		/**
		 * Get a new instance of the ErrorResult.
		 *
		 * @param string $message
		 * @param int $code
		 * @param Exception $exception Optional exception for generating a stack trace.
		 */
		public static function getInstance($message, $code = "", Exception $exception = NULL)
		{	
			$object = new FailureAPIResult();
			
			if (!$code)
				$code = FailureAPIResult::$RESULT_FAIL;
				
			if ($exception!=NULL)
				$object->setResult($exception->__toString());
				
			$object->setStatusCode($code, $message);
			
			return $object;
		}
	}
	
	/**
	 * Expose a specific function with parameters, parameters will be detected by reflection.
	 *
	 * @param string $api_call The API Call
	 * @param string $handler The handler
	 * @return bool
	 */
	function api_expose($api_call, $handler)
	{
		global $CONFIG;
		
		if (!isset($CONFIG->_API))
			$CONFIG->_API = array();
			
		if (!is_callable($handler)) return false;
			
		$details = array();
		$details['handler'] = $handler;
		
		// Detect parameters
		if (strpos($handler, '::')!==false)
		    $reflection = new ReflectionMethod($handler);
		else
		    $reflection = new ReflectionFunction($handler);
		$parameters = $reflection->getParameters();
		$param_array = array();
		if ($parameters)
		{
			foreach ($parameters as $param)
				$param_array[] = $param->name;
			
			$details['parameters'] = $param_array;
		}
		
		$CONFIG->_API[$api_call] = $details;
		
		return true;
	}
	
	
	/**
	 * Call an API method on a remote server.
	 * 
	 * Note, you can expect to have to provide extra parameters for authentication.
	 * 
	 * @param $endpoint The endpoint url (usually http://server.com/api/)
	 * @param $method The method to call
	 * @param $parameters Associative array of name/value pairs which form the parameters of this api call
	 * @param $call_details Extra details for calling, some defined:
	 * 							'method' => 'GET' | 'POST'
	 * 							'headers' => Array of HTTP headers to send.
	 * 							'postdata' => If 'method' == POST then this is the data to send. 
	 * @return APIResult|false
	 */
	function api_call($endpoint, $method, array $parameters = null, array $call_details = null)
	{
		if (!$endpoint) return false;
		if (!$method) return false;
		
		// Set defaults
		if (!is_array($call_details))
			$call_details = array();
		
		if (!$call_details['method']) $call_details['method'] = 'GET';
		if (!$call_details['headers']) $call_details['headers'] = array();
		
		// Construct parameters
		if (!$parameters) $parameters = array();
		$parameters['method'] = $method;
		$parameters['view'] = 'php';
		
		$params = array();
		
		foreach ($parameters as $k => $v) {
		    if (is_array($v))
			foreach ($v as $v_k => $v_v)
			    $params[] = urlencode($k)."[$v_k]=".urlencode($v_v);
		    else
			$params[] = urlencode($k).'='.urlencode($v);
		}
			
		$endpoint .= '?' . implode('&', $params);
		
		
		// Now construct stream
		$http = array (
			'method' => strtoupper($call_details['method']),
			'header' => implode("\r\n", $call_details['headers']) . "\r\n"
		);
		if (strtoupper($call_details['method'])=='POST') $http['content'] = $call_details['postdata'];
		
		// Execute query
		$ctx = stream_context_create(array(
			'http' => $http 
		));
		
		$fp = @fopen($endpoint, 'rb', false, $ctx);
		if (!$fp) return false;
		$response = @stream_get_contents($fp);
		fclose($fp);
		
		if (!$response) return false;
		$response = unserialize($response);
		if ((!$response) || (!($response instanceof APIResult))) return false;
		
		return $response;
	}
	
	/**
	 * Execute an api command.
	 *
	 * @param string $method Method being executed.
	 * @param array $params Optional parameters
	 */
	function api_execute($method, array $params = null)
	{
		global $CONFIG;
		
		if (isset($CONFIG->_API[$method]))
		{
			$serialised_parameters = array();
			
			if (isset($CONFIG->_API[$method]['parameters'])) {
				foreach ($CONFIG->_API[$method]['parameters'] as $k)
				{
					if (!isset($params[$k]))
						throw new APIException(sprintf(_echo('api:error:parameternotfound'), $k));
						
					if (is_array($params[$k])) {
						$array = array();
						foreach ($params[$k] as $key => $element)
							$array[addslashes($key)] = $element;
						$serialised_parameters[] = $array;
					}
					else
						$serialised_parameters[] = $params[$k];
				}
			}
				
			// Execute
			$function = $CONFIG->_API[$method]['handler'];
			//$func_params = implode(",", $serialised_parameters);

			$result = call_user_func_array($function, $serialised_parameters);//eval("return $function($func_params);");
			
			// If this function returns an api result itself, just return it
			if ($result instanceof APIResult) 
				return $result; 
				
			// Return is false, so this is a parsing error
			if ($result === FALSE)
				throw new APIException(sprintf(_echo('api:error:parseerror'), $function, $func_params));
				
			// No return
			if ($result ===  NULL)
				throw new APIException(sprintf(_echo('api:error:functionnoreturn'), $function, $func_params)); // If no value
			
			// Otherwise assume that the call was successful and return it as a success object.
			return SuccessAPIResult::getInstance($result); 	
		}
		else 
		{
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
                
			throw new APIException(sprintf(_echo('api:error:apicallnotfound'), $method));
		}
	}
	
	/**
	 * Retrieve a sanitised list of expected API parameters and their values.
	 * @param $method String
	 * @return array
	 */
	function api_get_parameters_for_method($method)
	{
		global $CONFIG;

		$method = sanitise_string($method);
		$sanitised = array();
		
		foreach ($CONFIG->_INPUT as $k => $v)
		{
			if (isset($CONFIG->_API[$method]['parameters'][$k]))
				$sanitised[$k] = input_get($k); // Make things go through the sanitiser	
		}
	
		return $sanitised;
	}
	
	/**
	 * Exposed API function, list all API calls installed. 
	 *
	 * @return array
	 */
	function api_call_listapi()
	{
		global $CONFIG;
		
		return $CONFIG->_API;
	}
	
	/**
	 * Generate an API enpoint URL.
	 *
	 * @param string $page
	 * @param array $subpages Array of subpages.
	 */
	function api_endpoint_handler($page, array $subpages)
	{
		global $CONFIG;
		
		// Authenticate user if possible, if successful this user is logged in
		$user = trigger_hook(
			'api', 'authenticate:user', 
			array(
				'method' => get_input('method'), // Pass method being triggered.
				'pages' => $subpages // Pass list of URL pages
			), 
			false);
			
		if (($user) && ($user instanceof User))
			$user->login(); // If a user is returned, then log that user in.


		include_once(dirname(__FILE__) . '/endpoint.php');
	}
	
	/**
	 * API initialisation.
	 *
	 */
	function api_init()
	{
		// Expose API listing function
		api_expose('system.api.list', 'api_call_listapi');
		
		// Create page
		register_page('api', 'api_endpoint_handler');
		
	}


	register_event('system', 'init', 'api_init');
	