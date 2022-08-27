<?php

namespace EstaleiroWeb\Modbus\Types;

use EstaleiroWeb\Traits\GetSet;

class MbTypeAny {
	use GetSet;

	public const NUM_RANGES = [
		'bit' => [
			'bits' =>   1,
			'bytes' =>   1,
			'signed' =>   [0, -1], // 0x00~0x71
			'unsigned' => [0, 1],  // 0x00~0x01
		],
		'byte' => [
			'bits' =>   8,
			'bytes' =>   1,
			'signed' =>   [-128, 127], // 0x80~0x7F
			'unsigned' => [0, 255],    // 0x00~0xFF
		],
		'int16' => [
			'bits' =>   16,
			'bytes' =>   2,
			'signed' => [-32768, 32767], // 0x8000~0x7FFF
			'unsigned' => [0, 65535],   // 0x0000~0xFFFF
		],
		'int24' => [
			'bits' =>   24,
			'bytes' =>   3,
			'signed' => [-8388608, 8388607], // 0x800000~0x7FFFFF
			'unsigned' => [0, 16777215],     // 0x000000~0xFFFFFF
		],
		'int32' => [
			'bits' =>   32,
			'bytes' =>   4,
			'signed' =>   [-2147483648, 2147483647], // 0x80000000~0x7FFFFFFF
			'unsigned' => [0, 4294967295],           // 0x00000000~0xFFFFFFFF
		],
		'int64' => [
			'bits' =>   64,
			'bytes' =>   8,
			'signed' =>   [-9223372036854775808, 9223372036854775807], // 0x8000000000000000~0x7FFFFFFFFFFFFFFF
			'unsigned' => [0, 18446744073709551615],                   // 0x0000000000000000~0xFFFFFFFFFFFFFFFF
		],
		'float' => [
			'bits' =>   32,
			'bytes' =>   4,
			'signed' =>   [-3.402823466E+38, -1.175494351E-38],
			'unsigned' => [1.175494351E-38, 3.402823466E+38],
		],
		'double' => [
			'bits' =>   64,
			'bytes' =>   8,
			'signed' =>   [-1.7976931348623157E+308, -2.2250738585072014E-308],
			'unsigned' => [2.2250738585072014E-308, 1.7976931348623157E+308],
		],
		'timestamp' => [
			'bits' =>   16,
			'bytes' =>   4,
			'unsigned' => ['1970-01-01 00:00:01.000000 UTC', '2038-01-19 03:14:07.999999 UTC'],
		],
		'datetime' => [
			'bits' =>   32,
			'bytes' =>   4,
			'unsigned' => ['1000-01-01 00:00:00.000000', '9999-12-31 23:59:59.999999'],
		],
		'date' => [
			'bits' =>   16,
			'bytes' =>   4,
			'unsigned' => ['1000-01-01', '9999-12-31'],
		],
		'time' => [
			'bits' =>   16,
			'bytes' =>   4,
			'unsigned' => ['-838:59:59.000000', '838:59:59.999999'],
		],
		'year4' => [
			'bits' =>   8,
			'bytes' =>   1,
			'unsigned' => [1901, 2155],
		],
	];
	/**
	 * readonly
	 *
	 * @var array GetSet Trait variable
	 */
	protected $readonly = [
		'unit' => 'byte',
		'unpack' => 'A',
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
