<?php
	/**
	 * @file
	 * XML Support.
	 * 
	 * Adds xml support for the BCT platform including XML export views for data objects and API.
	 *
	 * @section IMPORTANT SECURITY NOTE:
	 *
	 * As with all object export code, by default it will export all fields attached to an object. If any of
	 * these are sensitive you should provide an export view for the specific object and hide these fields.
	 * 
	 * @package xml
	 * @license The MIT License (see LICENCE.txt), other licenses available.
	 * @author Marcus Povey <marcus@marcus-povey.co.uk>
	 * @copyright Marcus Povey 2009-2012
	 * @link http://www.marcus-povey.co.uk
	 */

	/**
	 * @class XmlElement
	 * A class representing an XML element for import.
	 */
	class XmlElement
	{
		/** The name of the element */
		public $name;

		/** The attributes */
		public $attributes;

		/** CData */
		public $content;

		/** Child elements */
		public $children;
	};

	function xml_init()
	{
		
	}

	/**
	 * Serialise an array into XML.
	 * @param array $array The array
	 * @return xml
	 */
	function xml_serialise_array(array $array) { return __xml_array_to_xml($array); }

	/**
	 * Serialise an object.
	 * @param object $object The object to serialise
	 * @param string $root_tag Optional root tag name, if not specified the class name will be used.
	 * @return xml
	 */
	function xml_serialise_object($object, $root_tag = '') { return __xml_object_to_xml($object, $root_tag); }

	/**
	 * Parse an XML file into an object.
	 * Based on code from http://de.php.net/manual/en/function.xml-parse-into-struct.php by
	 * efredricksen at gmail dot com
	 *
	 * @param string $xml The XML.
	 */
	function xml_unserialise($xml)
	{
		$parser = xml_parser_create();

		// Parse $xml into a structure
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $tags);

		xml_parser_free($parser);

		$elements = array();
		$stack = array();

		foreach ($tags as $tag) {
			$index = count($elements);

			if ($tag['type'] == "complete" || $tag['type'] == "open") {
				$elements[$index] = new XmlElement;
				$elements[$index]->name = $tag['tag'];
				$elements[$index]->attributes = $tag['attributes'];
				$elements[$index]->content = $tag['value'];

				if ($tag['type'] == "open") {
					$elements[$index]->children = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]->children;
				}
			}

			if ($tag['type'] == "close") {
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
		}

		return $elements[0];
	}

	function __xml_object_to_xml($data, $name = "", $n = 0)
	{
		$classname = ($name=="" ? get_class($data) : $name);

		$output = "";

		if (($n==0) || ( is_object($data) && !($data instanceof stdClass)))
		    $output = "<$classname>";

		foreach ($data as $key => $value) {
			$output .= "<$key type=\"".gettype($value)."\">";

			if (is_object($value)) {
				$output .= __xml_object_to_xml($value, $key, $n+1);
			} else if (is_array($value)) {
				$output .= __xml_array_to_xml($value, $n+1);
			} else if (gettype($value) == "boolean") {
				$output .= $value ? "true" : "false";
			} else {
				$output .= "<![CDATA[$value]]>";
			}

			$output .= "</$key>\n";
		}

		if (($n==0) || ( is_object($data) && !($data instanceof stdClass))) {
			$output .= "</$classname>\n";
		}

		return $output;
	}

	function __xml_array_to_xml(array $data, $n = 0)
	{
		$output = "";

		if ($n==0)
		    $output = "<array>\n";

		foreach ($data as $key => $value) {
			$item = "array_item";

			if (is_numeric($key)) {
				$output .= "<$item key=\"$key\" type=\"".gettype($value)."\">";
			} else {
				$item = $key;
				$output .= "<$item type=\"".gettype($value)."\">";
			}

			if (is_object($value)) {
				$output .= __xml_object_to_xml($value, "", $n+1);
			} else if (is_array($value)) {
				$output .= __xml_array_to_xml($value, $n+1);
			} else if (gettype($value) == "boolean") {
				$output .= $value ? "true" : "false";
			} else {
				$output .= "<![CDATA[$value]]>";
			}

			$output .= "</$item>\n";
		}

		if ($n==0) {
			$output = "</array>\n";
		}

		return $output;
	}
	
	register_event('system', 'init', 'xml_init');