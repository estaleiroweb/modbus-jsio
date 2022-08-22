<?php

namespace EstaleiroWeb\Modbus\Types;

use EstaleiroWeb\Traits\GetSet;

class MbTypeAny {
	use GetSet;
	protected $readonly = [
		'raw' => null,
		'aRaw' => [],
		'len' => null,
		'unit' => 'byte',
		'precision' => 2,
		'unsigned' => false,
	];
	protected $protect = [];
	public function __construct($val = null) {
		$this->raw = $val;
	}
	public function __toString() {
		return "{$this->raw}";
	}
	public function __invoke($val = null) {
		return $this->hex($val);
	}
	public function getHex($separator = '') {
		return implode($separator, $this->hex());
	}
	public function setHex($val, $ret = false) {
		if (!$ret) {
			$raw = $this->setHex($val, true);
			$this->raw = $raw == '' ? null : $raw;
			return $this;
		}
		if (is_null($val)) return '';
		if (is_object($val)) $val = (array)$val;
		if (is_array($val)) {
			$raw = '';
			foreach ($val as $k => $v) $raw .= $this->setHex($v, true);
			return $raw;
		}
		if ($val == '') return '';
		if (preg_match($er = '/[^0-9a-f]+/i', $val)) {
			return $this->setHex(preg_split($er, $val), true);
		}
		$c = strlen($val);
		if ($c & 1) {
			$val = "0$val";
			$c++;
		}
		if ($c <= 2) return chr(hexdec($val));
		return $this->setHex(chunk_split($val, 2), true);
	}
	public function setLen($val) {
		$val = (int)$val;
		if ($val >= 1) {
			$this->readonly['len'] = $val;
			if ($this->aRaw) $this->raw = $this->raw;
		}
		return $this;
	}
	public function setRaw(&$val) {
		($len = $this->len) || ($len = strlen($val));
		$arr = [];
		for ($i = 0; $i < $len; $i++) $arr[] = ord($val[$i]);
		$this->readonly['aRaw'] = $arr;
		$this->readonly['raw'] = substr($val, 0, $len);
		$val = substr($val, $len + 1);
		return $this;
	}
	public function setPrecision($val) {
		$this->readonly['precision'] = (int)$val;
		return $this;
	}
	public function setUnsigned($val) {
		$this->readonly['unsigned'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		return $this;
	}

	public function hex($val = null) {
		if (is_null($val)) return array_map(
			function ($v, $k) {
				return str_pad(dechex($v), 2, 0, STR_PAD_LEFT);
			},
			$this->aRaw
		);
		return $this->setHex($val);
	}
}
