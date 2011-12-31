<?php

    /**
     * @file
     * Memcache support.
     *
     * This module adds a memcache cache to the available caching options available
     * to other plugins.
     *
     * @note Requires php5-memcache module to be installed.
     *
     * @package login
     * @license The MIT License (see LICENCE.txt), other licenses available.
     * @author Marcus Povey <marcus@marcus-povey.co.uk>
     * @copyright Marcus Povey 2009-2012
     * @link http://www.marcus-povey.co.uk
     */

    /**
     * Memcache wrapper class.
     */
    class MemcacheCache extends Cache {

	/**
	 * Minimum version of memcached needed to run
	 */
	private static $MINSERVERVERSION = '1.1.12';

	/**
	 * Memcache object
	 */
	private $memcache;

	/**
	 * Expiry of saved items (default timeout after a day to prevent anything getting too stale)
	 */
	private $expires = 86400;

	/**
	 * The version of memcache running
	 */
	private $version = 0;

	/**
	 * Connect to memcache.
	 *
	 * @param string $cache_id The namespace for this cache to write to - note,
	 *	    namespaces of the same name are shared between all processes!
	 */
	function __construct($namespace = 'default')
	{
	    global $CONFIG;

	    $this->setNamespace($namespace);

	    // Do we have memcache?
	    if (!class_exists('Memcache')) {
		    throw new ConfigurationException(_echo('memcache:notinstalled'));
	    }

	    // Create memcache object
	    $this->memcache = new Memcache;

	    // Now add servers
	    if (!$CONFIG->memcache_servers) {
		    throw new ConfigurationException(_echo('memcache:noservers'));
	    }

	    if (is_callable($this->memcache, 'addServer')) {
		    foreach ($CONFIG->memcache_servers as $server) {
			    if (is_array($server)) {
				    $this->memcache->addServer(
					    $server[0],
					    isset($server[1]) ? $server[1] : 11211,
					    isset($server[2]) ? $server[2] : true,
					    isset($server[3]) ? $server[3] : null,
					    isset($server[4]) ? $server[4] : 1,
					    isset($server[5]) ? $server[5] : 15,
					    isset($server[6]) ? $server[6] : true
				    );

			    } else {
				    $this->memcache->addServer($server, 11211);
			    }
		    }
	    } else {
		    log_echo(_echo('memcache:noaddserver'), 'ERROR');

		    $server = $CONFIG->memcache_servers[0];
		    if (is_array($server)) {
			    $this->memcache->connect($server[0], $server[1]);
		    } else {
			    $this->memcache->addServer($server, 11211);
		    }
	    }

	    // Get version
	    $this->version = $this->memcache->getversion();
	    if (version_compare($this->version, HighchairMemcache::$MINSERVERVERSION, '<')) {
		    throw new ConfigurationException(sprintf(_echo('memcache:versiontoolow'), self::$MINSERVERVERSION, $this->version));
	    }

	    // Set some defaults
	    if (isset($CONFIG->memcache_expires))
		$this->setCacheVariable('expires', $CONFIG->memcache_expires);
	}

	/**
	 * Set the default expiry.
	 *
	 * @param int $expires The lifetime as a unix timestamp or time from now. Defaults forever.
	 */
	public function setDefaultExpiry($expires = 0) {
	    $this->setCacheVariable('expires', $expires);
	}

	/**
	 * Combine a key with the namespace.
	 * Memcache can only accept <250 char key. If the given key is too long it is shortened.
	 *
	 * @param string $key The key
	 * @return string The new key.
	 */
	private function make_memcache_key($key) {
	    $prefix = $this->getNamespace() . ":";

	    if (strlen($prefix.$key)> 250) {
		    $key = md5($key);
	    }

	    return $prefix.$key;
	}

	public function save($key, $data) {
		$key = $this->make_memcache_key($key);

		$result = $this->memcache->set($key, $data, null, $this->getCacheVariable('expires'));
		if (!$result) {
			log_echo("MEMCACHE: FAILED TO SAVE $key", 'ERROR');
		}

		return $result;
	}

	public function load($key, $offset = 0, $limit = null) {
		$key = $this->make_memcache_key($key);

		$result = $this->memcache->get($key);
		if (!$result) {
			log_echo("MEMCACHE: FAILED TO LOAD $key", 'ERROR');
		}

		return $result;
	}

	public function delete($key) {
		$key = $this->make_memcache_key($key);

		return $this->memcache->delete($key, 0);
	}

	public function clear() {
		// DISABLE clearing for now - you must use delete on a specific key.
		return true;

		//TODO: Namespaces as in #532
	}

	public function size() {
	    $stats = $this->memcache->getstats();
	    return $stats['curr_items'];
	}
    }

    /**
     * Memcache factory.
     *
     */
    function memcache_factory($class, $hook, $parameters, $return_value)
    {
	global $CONFIG;

	// If caching is disabled, don't bother going any further
	if ((isset($CONFIG->disable_cache)) && ($CONFIG->disable_cache))
		return false;

	// If we already have a cache for this, don't create a new one.
	if ($return_value)
		return $return_value;

	// Otherwise we see if we can create a cache
	switch ($hook)
	{
	    case 'cache:memcache' :
		return new MemcacheCache();
	}
    }

    function memcache_boot()
    {
	global $CONFIG;

	register_factory('cache:memcache', 'memcache_factory');
    }


    register_event('system', 'boot', 'memcache_boot');
