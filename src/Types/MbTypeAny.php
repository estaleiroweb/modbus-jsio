<?php

namespace EstaleiroWeb\Modbus\Types;

use EstaleiroWeb\Traits\GetSet;

class MbTypeAny {
	use GetSet;

	/**
	 * storages
	 *
	 * @var array associative array Bytes=>[configuration]]
	 */
	protected $storages = [
		2 => [
			'hl' => ['descr' => 'hi 8bits-low 8bits', 'seq' => [1, 0,]],
			'lh' => ['descr' => 'low 8bits-hi 8bits', 'seq' => [0, 1,]],
		],
		4 => [
			'hl'  => ['descr' => 'Hi 16bits-Low 16bits 2B(hl)', 'seq' => [3, 2, 1, 0,]],
			'lh'  => ['descr' => 'Low 16bits-Hi 16bits 2B(lh)', 'seq' => [0, 1, 2, 3,]],
			'hlr' => ['descr' => 'Hi 16bits-Low 16bits 2B(lh)', 'seq' => [2, 3, 0, 1,]],
			'lhr' => ['descr' => 'Low 16bits-Hi 16bits 2B(hl)', 'seq' => [1, 0, 3, 2,]],
		],
		8 => [
			'hl'   => ['descr' => 'Hi 32bits-Low 32bits 4B(hl)', 'seq' => [7, 6, 5, 4, 3, 2, 1, 0,]],
			'lh'   => ['descr' => 'Low 32bits-Hi 32bits 4B(lh)', 'seq' => [0, 1, 2, 3, 4, 5, 6, 7,]],
			'hli'  => ['descr' => 'Hi 32bits-Low 32bits 4B(lh)', 'seq' => [4, 5, 6, 7, 0, 1, 2, 3,]],
			'lhi'  => ['descr' => 'Low 32bits-Hi 32bits 4B(hl)', 'seq' => [3, 2, 1, 0, 7, 6, 5, 4,]],
			'hlr'  => ['descr' => 'Hi 32bits-Low 32bits 4B(lhr)', 'seq' => [5, 4, 7, 6, 1, 0, 3, 2,]],
			'lhr'  => ['descr' => 'Low 32bits-Hi 32bits 4B(hlr)', 'seq' => [2, 3, 0, 1, 6, 7, 4, 5,]],
			'hlir' => ['descr' => 'Hi 32bits-Low 32bits 4B(hlr)', 'seq' => [1, 0, 3, 2, 5, 4, 7, 6,]],
			'lhir' => ['descr' => 'Low 32bits-Hi 32bits 4B(lhr)', 'seq' => [6, 7, 4, 5, 2, 3, 0, 1,]],
		],
	];
	/**
	 * readonly
	 *
	 * @var array GetSet Trait variable
	 */
	protected $readonly = [
		'unit' => 'byte',
		'store' => 'hl',
		'raw' => null,
		'aRaw' => [],
		'len' => null,
		'precision' => null,
		'unsigned' => null,
		'dec' => null,
	];
	/**
	 * protect
	 *
	 * @var array GetSet Trait variable
	 */
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
	public function setStorage($val) {
		$len = $this->len;
		if ($len) {
		}
		return $this;
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
