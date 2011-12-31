<?php
	/**
	 * @file
	 * Geocoder functionality.
	 * 
	 * This plugin defines a geocoder interface as well as a google geocoder and a new view input/geolocation 
	 * which uses HTML5 to detect a location.
	 * 
	 * @package geocoder
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	abstract class Geocoder {
		
		private $cache;
		
		/**
		 * Cache a location, saving lookup time.
		 * @param $location The location
		 * @param $encoded array Latitude/longitude array
		 * @return bool
		 */
		protected function cache($location, array $encoded)
		{
			if (!$this->cache) {
				$this->cache = factory('cache');
				
				if ($this->cache)
					$this->cache->setCacheVariable('namespace', 'geocoder');
			}
				
			if (!$this->cache)
				return false;
				
			return $this->cache->save(md5($location), serialize($encoded));
		}
		
		/**
		 * Return a cached location.
		 * @param string $location The location
		 * @return lat/long array|false
		 */
		protected function getCachedLocation($location)
		{
			if (!$this->cache) {
				$this->cache = factory('cache');
				
				if ($this->cache)
					$this->cache->setCacheVariable('namespace', 'geocoder');
			}
				
			if (!$this->cache)
				return false;
					
			return unserialize($this->cache->load(md5($location)));
		}
		
		/**
		 * Geocode a location, producing an array of latitude and longitude (and potentially elevation)
		 * 
		 * @param $location Current location
		 * @return array|false
		 */
		abstract public function geocode($location);
	}

	/**
	 * Google geocoder.
	 */
	class GoogleGeocoder extends Geocoder {
		public function geocode($location) 
		{
			global $CONFIG;
			
			if ($result = $this->getCachedLocation($location))
				return $result;
			
			if (!$CONFIG->google_maps_key)
				throw new ConfigurationException(_echo('geocoder:exception:googlekey'));
				
	
			// Desired address
		   	$address = "http://maps.google.com/maps/geo?q=".urlencode($location)."&output=json&key=" . $CONFIG->google_maps_key;
		
		   	// Retrieve the URL contents
	   		$result = file_get_contents($address);
	   		$obj = json_decode($result);
	   		
	   		if (!$obj)
	   			return false;
	   		
	   		$obj = $obj->Placemark[0]->Point->coordinates;
   		
	   		$result = array('lat' => $obj[1], 'long' => $obj[0]);
	   		
	   		$this->cache($location, $result);
	   		
	   		return $result;
		}
	}
	
	/**
	 * Geocoder factory.
	 */
	function geocoder_factory($class, $hook, $parameters, $return_value)
	{		
		// If we already have a geocoder, don't create a new one.
		if ($return_value) 
			return $return_value;
		
		// Otherwise we see if we can create a geocoder
		switch ($hook)
		{
			// Default factory for the viewpaths cache
			case 'geocoder' : 
			case 'geocoder:google' : 
				return new GoogleGeocoder();
		}	
	}
	
	function geocoder_init()
	{
		// Register some factories
		register_factory('geocoder', 'geocoder_factory');		
		register_factory('geocoder:google', 'geocoder_factory');
		
		// Enable geo-rss
		extend_view('page/extensions/xmlns', 'geocoder/extensions/xmlns');
		extend_view('data/extensions/item', 'geocoder/extensions/item');
		
		// Extend Javascript
		extend_view('js', 'geocoder/js');
	}

	register_event('system', 'init', 'geocoder_init');
?>