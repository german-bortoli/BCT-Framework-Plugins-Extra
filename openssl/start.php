<?php
	/**
	 * @file
	 * OpenSSL.
	 * 
	 * Provides various OpenSSL functionality, or will do, but at least
	 * for now it provides public / private key authentication for the API service
	 * connection.
	 * 
	 * @section API connection authentication.
	 * 
	 * This works by passing header variables when sending your query, these 
	 * are:
	 * 
	 *  - OPENSSL_KEY_ID : The GUID of the public key or keypair object used for
	 * 					verification.
	 * 
	 *  - OPENSSL_API_TIMESTAMP : UNIX timestamp - this prevents replay and is
	 * 						   also used in the hash. Must be within ten minutes
	 * 						   either side. 
	 *  - OPENSSL_API_HASH : Hex encoded Sha1 hash of api method + api call variables
	 * 					  and their values, urlencoded, and in call order + above 
	 * 					  timestamp. E.g. $hash = sha1('method=foo&var1=bar' . time())
	 *  - OPENSSL_API_SIGNATURE : Base64 encoded signature of the hash generated
	 * 						   using your private key. Use OPENSSL_PKCS1_PADDING.
	 * 
	 * 
	 * @package openssl
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	/**
	 * OpenSSL key
	 */
	abstract class OpenSSLKeypair extends Object 
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('opensslkeykeypair'); 
		}
		
		/**
		 * Generate a new private key.
		 * @param int $private_key_bits Number of bits, defaults to 1024.
		 * @param keytype $private_key_type Key type
		 * @return bool
		 */
		public function generate($private_key_bits = 1024, $private_key_type = OPENSSL_KEYTYPE_RSA) 
		{
			$keys = openssl_pkey_new(array(
				'private_key_bits' => $private_key_bits,
			 	'private_key_type' => $private_key_type,
			));
			if (!$keys) return false;

			openssl_pkey_export($keys, $private);
			$keydetails = openssl_pkey_get_details($privateKey);
			if (!$keydetails) return false;
			
			
			$public = $keydetails['key'];
			
			$this->private = $private;
			$this->public = $public;
			
			return true;
		}
		
		/**
		 * Return the public OpenSSL key
		 * @return OpenSSL public key
		 */
		public function getPublic() {
			return $this->public;
		}
		
		/**
		 * Return the private OpenSSL key
		 * @return OpenSSL private key
		 */
		public function getPrivate() {
			return $this->private;
		}
		
		public static function generateOpenSSLKeypair()
		{
			$keypair = new OpenSSLKeypair();
			$keypair->generate();
			
			return $keypair;
		}
	}

	/**
	 * Public key.
	 */
	class OpenSSLPublicKey extends Object
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('opensslpublickey'); 
		}
		
		public function generate(OpenSSLKeypair $keys) {
			
			// If we've stupidly been passed a blank key then create it
			if ((!$keys) || (!$keys->getPublic()))
			{
				$keys = new OpenSSLKeypair();
				$keys->generate();
			}
			
			$this->setKey($keys->getPublic());
		}
		
		public function setKey($data) { $this->key = $data; }
		
		public function getKey() { return $this->key; }
		public function getPublic() { return $this->getKey(); }
	}
	
	/**
	 * Private key.
	 */
	class OpenSSLPrivateKey extends Object
	{
		public function __construct() 
		{ 
			parent::__construct(); 
			
			$this->setType('opensslprivatekey'); 
		}
		
		public function generate(OpenSSLKeypair $keys) {
			
			// If we've stupidly been passed a blank key then create it
			if ((!$keys) || (!$keys->getPrivate()))
			{
				$keys = new OpenSSLKeypair();
				$keys->generate();
			}
			
			$this->setKey($keys->getPrivate());
		}
		
		public function setKey($data) { $this->key = $data; }
		
		public function getKey() { return $this->key; }
		public function getPrivate() { return $this->getKey(); }
	}
	
	
	/**
	 * OpenSSL initialisation.
	 *
	 */
	function openssl_init()
	{	
		// Provide OpenSSL public key authentication to the API
		register_hook('api', 'authenticate:connection', 'openssl_api_auth_connection');
		
		// Keypair generation action
		register_action('openssl/generatekeypair', 'openssl_action_generate_keypair');
	}
	
	/**
	 * Action to generate keypair.
	 *
	 */
	function openssl_action_generate_keypair()
	{
		$user = user_get_current();
		if ($user)
		{
			// User logged in, generate keypair.
			$keypair = OpenSSLKeypair::generateOpenSSLKeypair();
			
			// Tag it with user ID
			$keypair->owner_guid = $user->guid;
			
			// Save
			if ($keypair->save()) {
				message(_echo('openssl:generatekeypair:ok'));
		
				return true;
			} else
				error_message(_echo('openssl:generatekeypair:failed'));
		}
	}
	
	/**
	 * Authentication of API via openssl public key.
	 */
	function openssl_api_auth_connection($namespace, $hook, $parameters, $return_value)
	{
		// Get key id
		$key_guid = $_SERVER['HTTP_OPENSSL_KEY_ID'];
		$key = getObject($key_guid);
		
		if ($key)
		{
			if (($key instanceof OpenSSLKeypair) || ($key instanceof OpenSSLPublicKey))
			{
				// Get key
				$pubkey = $key->getPublic();
				
				// Get header components
				$time = $_SERVER['HTTP_OPENSSL_API_TIMESTAMP']; // Unix timestamp
				$hash = $_SERVER['HTTP_OPENSSL_API_HASH']; // Sha1 Hash of all names&variables, URL encoded IN ORDER as it appears on the url line, eg method=foo&var1=bar + timestamp
				$signature = $_SERVER['HTTP_OPENSSL_API_SIGNATURE']; // base64 encoded RSA signature of above
				
				
				// Verify timestamp (you have 10 minutes either side to allow for clock drift)
				$minute = 60;
				$now = time();
				if (($time < $now-($minute*10)) || ($time > $now+($minute*10)))
				{
					log_echo(_echo('openssl:api:timeout'));
					return false;
				}
				
				// Verify hash
				if (!$hash) {
					log_echo(_echo('openssl:api:noshash'));
					return false;
				}
				
				$qs_array = call_plugin_function('api_get_parameters_for_method', input_get('method'));
				$qs = array();
				foreach ($qs_array as $k => $v)
					$qs[] = urlencode($k)."=".urlencode($v);
				
				if ($hash != sha1(
					implode('&', $qs).
					$time
				))
				{
					log_echo(_echo('openssl:api:hashinvalid'));
					return false;
				}
	
				// Verify signature on hash
				openssl_public_decrypt($signature, $decrypted, openssl_pkey_get_public($pubkey));
				if ($decrypted != $hash)
				{
					log_echo('openssl:api:signatureinvalid');
					return false;
				}
				
				// Got this far, so signature and hash are both valid, connection is authentic
				return true;
			}
			
			return false;
		}
		
		
		// Possibly authenticated elsewhere, so leave it for the default return.
	}


	register_event('system', 'init', 'openssl_init');