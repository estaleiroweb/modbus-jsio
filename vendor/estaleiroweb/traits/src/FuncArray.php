<?php

namespace EstaleiroWeb\Traits;

use SimpleXMLElement;

trait FuncArray {
	protected function array2xml($val, &$xml = null) {
		static $fn, $c = 'content';
		if (is_null($xml)) $xml = '<root />';
		if (is_string($xml)) {
			$xml = new SimpleXMLElement($xml);
			$this->array2xml($val, $xml);
			return $xml->asXML();
		}
		if (is_null($fn)) $fn = function_exists('array_is_list') ? 'array_is_list' : function (array $arr) {
			$arr = array_keys($arr);
			$comp = range(0, count($arr) - 1);
			return $arr !== $comp;
		};
		if (is_object($val)) $val = (array)$val;
		if (is_array($val)) {
			$a = $fn($val);
			foreach ($val as $k => $v) {
				$key = $a ? $c : "$k";
				if (is_object($v) || is_array($v)) {
					$subnode = $xml->addChild($key);
					$this->array2xml($v, $subnode);
				} else $xml->addChild($key, "$v");
			}
		} else {
			$xml->addChild($c, "$val");
			//$this->array2xml($val, $xml);
		}

		/*
		foreach ($val as $key => $value) {
			if (is_array($value)) {
				if (!is_numeric($key)) {
					$subnode = $xml->addChild("$key");
					$this->array2xml($value, $xml);
				} else {
					$this->array2xml($value, $xml);
				}
			} else {
				$xml->addChild("$key", "$value");
			}
		}

		/*
		foreach ($array as $key => $value) {
			if (preg_match("/^[0-9]/", $key))
				$key = "node-{$key}";
			$key = preg_replace("/[^a-z0-9_\-]+/i", '', $key);

			if ($key === '')
				$key = '_';

			$a = $xml->createElement($key);
			$node->appendChild($a);

			if (!is_array($value))
				$a->appendChild($xml->createTextNode($value));
			else
				$this->array2xml($value, $a, $xml);
		}*/
	}
}
