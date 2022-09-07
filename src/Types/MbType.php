<?php

namespace EstaleiroWeb\Modbus\Types;

use EstaleiroWeb\Traits\GetSet;
use Exception;
use ModbusTcpClient\Utils\Charset;

abstract class MbType {
	use GetSet;
	/**
	 * To 32bits we have 2 registers 16+16bits
	 * In diferent vendors the first 16bits is High or Low part of 32bits
	 * The result will depends of the endians too to 8+8bit format (machine/big/little endian order)
	 * Done this step of the endian, the int value will be low_part+(higth_part*65535)
	 * @example "0x89ABCDEF"
	 * 	- to $lowWFirst & LOW_W_FIRST_32 = 0 ["higth"=>"0x89AB","low"=>"0xCDEF",]
	 * 	- to $lowWFirst & LOW_W_FIRST_32 = 1 ["higth"=>"0xCDEF","low"=>"0x89AB",]
	 */
	public const LOW_W_FIRST_32 = 1;
	/**
	 * idem $lowWFirst32
	 */
	public const LOW_W_FIRST_64 = 2;
	/**
	 * Used by WAGO 750-XXX as endianness.
	 *
	 * When bytes for little endian are in 'ABCD' order then Big Endian Low Word First is in 'BADC' order
	 * This mean that high word (BA) is first and low word (DC) for double word is last and bytes in words are in big endian order.
	 */
	public const BYTE_OTHER_RANGES = [
		'bit' => [
			'bits' => 1,
			'bytes' => 1,
			'signed' => [0, -1], // 0x00~0x71
			'unsigned' => [0, 1],  // 0x00~0x01
		],
		'float' => [
			'bits' => 32,
			'bytes' => 4,
			'signed' => [-3.402823466E+38, -1.175494351E-38],
			'unsigned' => [1.175494351E-38, 3.402823466E+38],
		],
		'double' => [
			'bits' => 64,
			'bytes' => 8,
			'signed' => [-1.7976931348623157E+308, -2.2250738585072014E-308],
			'unsigned' => [2.2250738585072014E-308, 1.7976931348623157E+308],
		],
		'timestamp' => [
			'bits' => 16,
			'bytes' => 4,
			'unsigned' => ['1970-01-01 00:00:01.000000 UTC', '2038-01-19 03:14:07.999999 UTC'],
		],
		'datetime' => [
			'bits' => 32,
			'bytes' => 4,
			'unsigned' => ['1000-01-01 00:00:00.000000', '9999-12-31 23:59:59.999999'],
		],
		'date' => [
			'bits' => 16,
			'bytes' => 4,
			'unsigned' => ['1000-01-01', '9999-12-31'],
		],
		'time' => [
			'bits' => 16,
			'bytes' => 4,
			'unsigned' => ['-838:59:59.000000', '838:59:59.999999'],
		],
		'year4' => [
			'bits' => 8,
			'bytes' => 1,
			'unsigned' => [1901, 2155],
		],
	];
	/**
	 * String groups to unpack formats
	 */
	public const ENDIANS = [
		'be' => ['id' => 0, 'descr' => 'big endian byte order',],
		'le' => ['id' => 1, 'descr' => 'little endian byte order',],
		'mb' => ['id' => 2, 'descr' => 'machine byte order',],
		'md' => ['id' => 3, 'descr' => 'machine dependent size and byte order',],
		'bi' => ['id' => 4, 'descr' => 'big endian byte invert order',],
		'li' => ['id' => 5, 'descr' => 'little endian byte invert order',],
	];
	/**
	 * String groups id to unpack formats
	 */
	public const ENDIANS_ID = [
		0 => 'be',
		1 => 'le',
		2 => 'mb',
		3 => 'md',
		4 => 'bi',
		5 => 'li',
	];
	public const ORDER_NIBBLE = 1;
	public const ORDER_ENDIAN_BIG = 2;
	public const ORDER_WORD4_HIGH = 4;
	public const ORDER_WORD8_HIGH = 8;
	/**
	 * String groups id to pack/unpack formats
	 */
	public const ORDER = [
		1 => ['descr' => '1(8bits) Nibble order (0:low, 1:high) first', 'ex' => ['89AB=>89AB', '89AB=>98BA']],
		2 => ['descr' => '2(16bits) Endian byte order (0:little, 1:big) first', 'ex' => ['89 AB=>AB 89', '89 AB=>89 AB']],
		4 => ['descr' => '4(32bits) Word byte order (0:low, 1:high) first', 'ex' => ['89AB CDEF=>CDEF 89AB', '89AB CDEF=>89AB CDEF']],
		8 => ['descr' => '8(64bits) Word byte order (0:low, 1:high) first', 'ex' => ['01234567 89ABCDEF=>89ABCDEF 01234567', '01234567 89ABCDEF=>01234567 89ABCDEF']],
	];
	/**
	 * All formats to unpack function
	 */
	public const FORMATS = [
		'c' => 'signed char',
		'C' => 'unsigned char',

		'n' => 'unsigned short (always 16 bit, big endian byte order)',
		'v' => 'unsigned short (always 16 bit, little endian byte order)',
		's' => 'signed short (always 16 bit, machine byte order)',
		'S' => 'unsigned short (always 16 bit, machine byte order)',

		'N' => 'unsigned long (always 32 bit, big endian byte order)',
		'V' => 'unsigned long (always 32 bit, little endian byte order)',
		'l' => 'signed long (always 32 bit, machine byte order)',
		'L' => 'unsigned long (always 32 bit, machine byte order)',
		'i' => 'signed integer (32 bit machine dependent size and byte order)',
		'I' => 'unsigned integer (32 bit machine dependent size and byte order)',

		'J' => 'unsigned long long (always 64 bit, big endian byte order)',
		'P' => 'unsigned long long (always 64 bit, little endian byte order)',
		'q' => 'signed long long (always 64 bit, machine byte order)',
		'Q' => 'unsigned long long (always 64 bit, machine byte order)',

		'G' => 'float (machine dependent size, big endian byte order)',
		'E' => 'double (machine dependent size, big endian byte order)',
		'g' => 'float (machine dependent size, little endian byte order)',
		'e' => 'double (machine dependent size, little endian byte order)',
		'f' => 'float (machine dependent size and representation)',
		'd' => 'double (machine dependent size and representation)',

		'H' => 'Hex string, high nibble first',
		'h' => 'Hex string, low nibble first',

		'a' => 'NUL-padded string',
		'Z' => 'NUL-padded string',
		'A' => 'SPACE-padded string',
		'x' => 'NUL byte',
		'X' => 'Back up one byte',
		'@' => 'NUL-fill to absolute position',

		'int' => 'integer by bits',
		'dec' => 'float/double',
	];
	/**
	 * @see ENDIANS_ID and ENDIANS const
	 *
	 * @var int
	 */
	/**
	 * readonly
	 *
	 * @var array GetSet Trait variable
	 */
	protected $readonly = [
		'bytes' => 1,
		'order' => self::ORDER_ENDIAN_BIG | self::ORDER_ENDIAN_BIG | self::ORDER_WORD4_HIGH | self::ORDER_WORD8_HIGH,
		'endian' => null,
		'lowWFirst' => null,
		'val' => null,
		'unsigned' => false,
		'zerofill' => false,
		'len' => null,
		'precision' => null,
		'dec' => null,
		'min' => null,
		'max' => null,
		'source' => null,
	];
	/**
	 * protect
	 *
	 * @var array GetSet Trait variable
	 */
	protected $protect = [];

	/**
	 * __construct
	 *
	 * @param ?int $order
	 * @see init method
	 * @return void
	 */
	final public function __construct($order = null) {
		$this->order = $order;
		$this->init();
	}
	public function __toString() {
		return $this->val;
	}
	public function __invoke(&$val = null) {
		if (is_null($val)) return $this->val;
		$bytes = $this->bytes;
		$this->val = substr($val, 0, $bytes);
		$val = substr($val, $bytes);
		return $this;
	}
	/**
	 * getValue
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->val;
	}
	/**
	 * setOrder
	 *
	 * @param  int|null $val see key values of the ORDER* const
	 * @return self
	 */
	public function setOrder($val) {
		if ($this->checkOrder($val)) {
			$this->readonly['order'] = $val;
		}
		return $this;
	}
	/**
	 * setVal
	 *
	 * @param  mixed $val Value to use in the type
	 * @return self
	 */
	public function setVal($val) {
		$this->readonly['val'] = $val;
		return $this;
	}
	/**
	 * setLen
	 *
	 * @param  int|null $val Value to use in the type
	 * @return self
	 */
	public function setLen($val) {
		$val = (int)$val;
		if ($val > 0) $this->readonly['len'] = $val;
		return $this;
	}
	/**
	 * setSource
	 *
	 * @param  object|array|string|int|null $val Value to use in the type
	 * @return self
	 */
	public function setSource($val) {
		if (is_array($val)) {
			$this->readonly['source'] = $val;
			return $this;
		}
		if (is_object($val)) return $this->setSource((array)$val);
		try {
			$js = json_decode($val);
			if (!is_null($js)) return $this->setSource($js);
		} catch (Exception $e) {
		}
		try {
			$js = json_decode('[' . $val . ']');
			if (!is_null($js)) return $this->setSource($js);
		} catch (Exception $e) {
		}
		try {
			$js = json_decode('{' . $val . '}');
			if (!is_null($js)) return $this->setSource($js);
		} catch (Exception $e) {
		}
		try {
			$val = preg_replace(['/\'([^\'])\'\s*:/', '/(\w+)\s*:/'], '"\1":', $val);
			$js = json_decode('{' . $val . '}');
		} catch (Exception $e) {
		}
		return $this->setSource($js);
	}
	public function getHex($separator = '') { //TODO
		return unpack('H*', $this())[1];
	}
	public function setHex($val) { //TODO
		return $this;
	}


	/**
	 * init
	 * inicialize arguments of class by associative/list array 
	 *
	 * @return self
	 */
	protected function init() {
		return $this;
	}
	public static function type($val) {
		$conf = self::checkType($val);
		if (!$conf) return;
		$class = $conf['class'];
		$obj = new $class;
		unset($conf['class']);
		unset($conf['type']);
		if (key_exists($k = 'bytes', $conf)) {
			$obj->$k = $conf[$k];
			unset($conf[$k]);
		}
		foreach ($conf as $k => $v) $obj->$k = $v;
		return $obj;
	}
	private static function checkType($val) {
		static $arr = [];
		if (
			is_null($val) ||
			!preg_match('/^\s*(\w+)\s*(.*?)\s*$/im', $val, $ret)
		) return false;
		if (!$arr) {
			$er = '/^(' . __CLASS__ . ')(.*)\.php$/i';
			$dList = scandir(__DIR__);
			foreach ($dList as $file) {
				if (preg_match($er, $file, $ret)) {
					$k = strtolower($ret[2]);
					$arr[$k] = [
						'type' => $ret[2],
						'class' => $ret[1] . $ret[2],
					];
				}
			}
		}
		$type = strtolower($ret[1]);
		$definition = $ret[2];
		if (!key_exists($type, $arr)) return false;
		$out = $arr[$type];
		if (preg_match('/^\(([^\(\)]*)\)\s*(.*)/m', $definition, $ret)) {
			if (preg_match('/(\d+)(?:,(\d+))/', $ret[1], $r)) {
				$out['len'] = $r[1];
				if (key_exists(2, $r)) $out['precision'] = $r[2];
			} else {
				$out['source'] = $ret[1];
			}
			$definition = $ret[2];
		}
		$er = '/^\s*(\w+)\s*(?:=\s*(\([^\(\)]*\)|\S*))/';
		while ($definition) {
			if (!preg_match($er, $definition, $ret)) break;
			if (@$ret[2] == '') $ret[2] = true;
			elseif ($ret[2][0] == '(') $ret[2] = substr($ret[2], 1, -1);
			$out[$ret[1]] = $ret[2];
		}
		return $out;
	}

	protected function checkOrder(&$val) {
		static $masc = self::ORDER_NIBBLE | self::ORDER_ENDIAN_BIG | self::ORDER_WORD4_HIGH | self::ORDER_WORD8_HIGH;

		if (is_null($val)) return false;
		$v = (int)$val;
		if ($v >= 0 && $v <= 15) {
			$val = $v & $masc;
			return true;
		}
		return false;
	}
	protected function orderWord($cbin) {
		$order = $this->order;
		$bytes = $this->bytes;
		if (!($order | self::ORDER_NIBBLE)) {
			$cbin = pack('H*', unpack('h*', $cbin)[1]);
		}
		if ($bytes < self::ORDER_ENDIAN_BIG) return $cbin;
		if ($order | self::ORDER_ENDIAN_BIG) {
			$cbin = implode('', array_map(function ($v) {
				return strrev($v);
			}, str_split($cbin, $bytes)));
		}
		if ($bytes < self::ORDER_WORD4_HIGH) return $cbin;
		if ($order | self::ORDER_WORD4_HIGH) {
			$cbin = $this->orderWordCallback($cbin, $bytes);
		}
		if ($bytes < self::ORDER_WORD8_HIGH) return $cbin;
		if ($order | self::ORDER_WORD8_HIGH) {
			$cbin = $this->orderWordCallback($cbin, $bytes);
		}
		return $cbin;
	}
	private function orderWordCallback($cbin, $bytes) {
		static $fn;
		if (!$fn) $fn = function ($v) use ($bytes) {
			return substr($v, $bytes, $bytes) . substr($v, 0, $bytes);
		};
		return implode('', array_map($fn, str_split($cbin, $bytes * 2)));
	}

	public static function cBinHex($cbin) { //ex: "\xF1\x23" => F123
		return unpack('H*', $cbin)[1];
	}
	public static function cInvert($cbin) { //ex: "\xF1\x23" => "\x1F\x32"
		return pack('H*', unpack('h*', $cbin)[1]);
	}
	public static function swap16($hex) { //ex: F123 => 23F1
		$n = 2;
		return
			substr($hex, $n, $n) .
			substr($hex, 0, $n);
	}
	public static function swap32($hex) { //ex: F123 4567 => 23F1 6745
		$n = 4;
		return
			self::swap16(substr($hex, 0, $n)) .
			self::swap16(substr($hex, $n, $n));
	}
	public static function swap64($hex) { //ex: F123 4567 89AB CDEF => 23F1 6745 AB89 EFCD
		$n = 8;
		return
			self::swap32(substr($hex, 0, $n)) .
			self::swap32(substr($hex, $n, $n));
	}
	public static function cSwap16($cbin) { //ex: "\xF3\x12" => "\x12\xF3"
		return pack('H*', self::swap16(self::cBinHex($cbin)));
	}
	public static function cSwap32($cbin) {
		return pack('H*', self::swap32(self::cBinHex($cbin)));
	}
	public static function cSwap64($cbin) {
		return pack('H*', self::swap64(self::cBinHex($cbin)));
	}
}
