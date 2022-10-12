<?php

namespace EstaleiroWeb\Modbus\Types;

use EstaleiroWeb\Traits\GetSet;
use Exception;
use ModbusTcpClient\Utils\Charset;

/**
 * MbType
 * - @var int $bytes Default=1
 * - @var int $val|$value Contains the value in type of class
 * - @var int $raw Contians the value in string binary getted or to write in modbus slave device
 * - @var int $rawOrdered Contians the value in string binary ordered by order=NE48 (Nibble=High,Endian=Big,Word4=High,Word8=High)
 * - @var int $unsigned Defines if numeric value is signed or unsigned. Default=false
 * - @var int $zerofill String pad left with "0" to numeric values using $len properties. Default=false
 * - @var int $len Length to show format in __toString method
 * - @var int $precision Numer of decimals has this number
 * - @var int|string $min Minimal limit of value
 * - @var int|string $max Maximus limit of value
 * - @var array|string $source array or string enum/set/json format that denifes which values can be $val or relation numeber to string value
 * - @var string bitOrder 
 * - @var bool bitOrderNibble Default=true
 * - @var bool bitOrderEndian Default=true
 * - @var int bitOrderWord4 Default=1
 * - @var int bitOrderWord8 Default=1
 */
abstract class MbType {
	use GetSet;

	/**
	 * Bit order first off for 4 or 8 words
	 * This is same BIT_ORDER_WORD_HIGH when ENDIAN is big first, but
	 * When Endian is little first, all words is applied in this case
	 * @example 89ABCDEF=>89ABCDEF, 0123456789ABCDEF=>0123456789ABCDEF
	 * @var int const
	 */
	public const BIT_ORDER_WORD_NONE = 0;
	/**
	 * Bit order High first for 4 or 8 words
	 * When Endian is little first, thist one will applied in separated 4/8 blocks
	 * @example 89ABCDEF=>89ABCDEF, 0123456789ABCDEF=>0123456789ABCDEF
	 * @var int const
	 */
	public const BIT_ORDER_WORD_HIGH = 1;
	/**
	 * Bit order Low first for 4 or 8 words
	 * When Endian is little first, thist one will applied in separated 4/8 blocks
	 * @example 89ABCDEF=>CDEF89AB, 0123456789ABCDEF=>89ABCDEF01234567
	 * @var int const
	 */
	public const BIT_ORDER_WORD_LOW = 2;
	/**
	 * Used by WAGO 750-XXX as endianness.
	 *
	 * When bytes for little endian are in 'ABCD' order then Big Endian Low Word First is in 'BADC' order
	 * This mean that high word (BA) is first and low word (DC) for double word is last and bytes in words are in big endian order.
	 * 
	 * @var array const
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
	 * Documentation of all formats to pack/unpack function
	 * 
	 * @see pack and unpack functions
	 * @var array const
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
	 * readonly
	 *
	 * @var array GetSet Trait variable
	 */
	protected $readonly = [
		'bytes' => 1,

		'val' => null,
		'raw' => null,
		'rawOrdered' => null,

		'unsigned' => false,
		'zerofill' => false,
		'len' => null,
		'precision' => null,
		'min' => null,
		'max' => null,
		'source' => null,

		'bitOrder' => null,
		'bitOrderNibble' => true,
		'bitOrderEndian' => true,
		'bitOrderWord4' => 1,
		'bitOrderWord8' => 1,

		'log' => [],
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
	final public function __construct($val = null) {
		$this->init();
		$this->val = $val;
	}
	/**
	 * Shows formated value
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->val;
	}
	/**
	 * Get or set raw by binary string
	 *
	 * @param  string|null $cbin binary string will droped into $bytes length to set raw
	 * @return string|self When $cbin is null return raw otherise set this one
	 */
	public function __invoke(&$cbin = null) {
		if (is_null($cbin)) return $this->raw;
		$this->raw = $cbin;
		$cbin = substr($cbin, $this->bytes);
		return $this;
	}

	/**
	 * Set $val/$value and $raw properties by normal value
	 *
	 * @param  string|int|float|double|null $val Value to use in the type
	 * @return self
	 */
	public function setVal($val) {
		$val = substr($val, 0, $this->bytes);
		$this->readonly['val'] =
			$this->readonly['raw'] =
			$this->readonly['rawOrdered'] = $val;
		return $this;
	}
	/**
	 * Set $val/$value and $raw properties by string binary
	 *
	 * @param  string|int|float|double|null $val Value to use in the type
	 * @return self
	 */
	public function setRaw($cbin) {
		return $this->setVal($cbin);
	}
	/**
	 * Set $bytes properties
	 *
	 * @param  int $val Value to use in the type
	 * @return self
	 */
	public function setBytes($val) {
		$this->readonly['bytes'] = (int)$val;
		return $this;
	}
	/**
	 * Set $len properties
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
		if (is_null($val) || is_array($val)) {
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
	/**
	 * Clear or add log
	 *
	 * @param  string|null $val
	 * @return self
	 */
	public function setLog($val = null) {
		if ($val) $this->readonly['log'] = [];
		else $this->readonly['log'][] = $val;
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
}


$a = new MbTypeInt;
$a = new MbType;
