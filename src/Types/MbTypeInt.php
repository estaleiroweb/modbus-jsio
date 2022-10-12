<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt extends MbType {
	/**
	 * Documentation of interger bit order (Nibble,Endian|Word 4/8)
	 * 
	 * @see pack and unpack functions
	 * @var array const
	 */
	public const INT_BIT_ORDER = [
		'Nibble' => [
			'descr' => '1(8bits) Nibble order (0:low, 1:high) first',
			'ex' => [[
				'bytes' => 1,
				'from' => '8F',
				0 => '8F',
				1 => 'F8',
			], [
				'bytes' => 2,
				'from' => 'CD8F',
				0 => 'CD8F',
				1 => 'DCF8',
			], [
				'bytes' => 4,
				'from' => '89ABCD8F',
				0 => '89ABCD8F',
				1 => '98BACD8F',
			], [
				'bytes' => 8,
				'from' => '0123456789ABCD8F',
				0 => '0123456789ABCD8F',
				1 => '1032547698BACD8F',
			],],
		],
		'Endian' => [
			'descr' => '2(16bits) Endian byte order (0:little, 1:big) first',
			'ex' => [[
				'conf' => ['Nibble' => 0,],
				'bytes' => 2,
				'from' => 'CD 8F',
				0 => '8F CD',
				1 => 'CD 8F',
			],],
		],
		'Word4' => [
			'descr' => '4(32bits) Word byte order (0:off, 1:high, 2:low) first',
			'ex' => [[
				'conf' => ['Nibble' => 0, 'Endian' => 1,],
				'bytes' => 4,
				'from' => '89AB CD8F',
				0 => '89AB CD8F',
				1 => '89AB CD8F',
				2 => 'CD8F 89AB',
			], [
				'conf' => ['Nibble' => 0, 'Endian' => 0,],
				'bytes' => 4,
				'from' => '89AB CD8F',
				0 => '8FCD AB89',
				1 => 'AB89 8FCD',
				2 => '8FCD AB89',
			],],
		],
		'Word8' => [
			'descr' => '8(64bits) Word byte order (0:off, 1:high, 2:low) first',
			'ex' => [[
				'conf' => ['Nibble' => 0, 'Endian' => 1, 'Word4' => [0, 1],],
				'bytes' => 8,
				'from' => '01234567 89ABCDEF',
				0 => '01234567 89ABCDEF',
				1 => '01234567 89ABCDEF',
				2 => '89ABCDEF 01234567',
			], [
				'conf' => ['Nibble' => 0, 'Endian' => 1, 'Word4' => 2,],
				'bytes' => 8,
				'from' => '01234567 89ABCDEF',
				0 => '45670123 CDEF89AB',
				1 => '45670123 CDEF89AB',
				2 => 'CDEF89AB 45670123',
			], [
				'conf' => ['Nibble' => 0, 'Endian' => 0, 'Word4' => 0,],
				'bytes' => 8,
				'from' => '01234567 89ABCDEF',
				0 => 'EFCDAB89 67452301',
				1 => '67452301 EFCDAB89',
				2 => 'EFCDAB89 67452301',
			], [
				'conf' => ['Nibble' => 0, 'Endian' => 0, 'Word4' => 1,],
				'bytes' => 8,
				'from' => '01234567 89ABCDEF',
				0 => '23016745 AB89EFCD',
				1 => '23016745 AB89EFCD',
				2 => 'AB89EFCD 23016745',
			], [
				'conf' => ['Nibble' => 0, 'Endian' => 0, 'Word4' => 2,],
				'bytes' => 8,
				'from' => '01234567 89ABCDEF',
				0 => '67452301 EFCDAB89',
				1 => '67452301 EFCDAB89',
				2 => 'EFCDAB89 67452301',
			],],
		],
	];
	/**
	 * Documentation of interger formats to pack/unpack function
	 * 
	 * @see pack and unpack functions
	 * @var array const
	 */
	public const INT_FORMATS = [
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
	];
	/**
	 * Detaild of Int Ranges
	 * 
	 * @var array const
	 */
	public const INT_RANGES = [
		1 => [
			'signed' => ['len' => 3, 'endian' => 'c', 'min' => -128, 'max' => 127,],
			'unsigned' => ['len' => 3, 'endian' => 'C', 'min' => 0, 'max' => 255,],
		], // 0x80~0x7F ~ 0x00~0xFF
		2 => [
			'signed' => ['len' => 6, 'endian' => 's', 'min' => -32768, 'max' => 32767,],
			'unsigned' => ['len' => 5, 'endian' => 'S', 'min' => 0, 'max' => 65535,],
		], // 0x8000~0x7FFF ~ 0x0000~0xFFFF
		3 => [
			'signed' => ['len' => 8, 'endian' => 'l', 'min' => -8388608, 'max' => 8388607,],
			'unsigned' => ['len' => 8, 'endian' => 'L', 'min' => 0, 'max' => 16777215,],
		], // 0x800000~0x7FFFFF ~ 0x000000~0xFFFFFF
		4 => [
			'signed' => ['len' => 11, 'endian' => 'l', 'min' => -2147483648, 'max' => 2147483647,],
			'unsigned' => ['len' => 10, 'endian' => 'L', 'min' => 0, 'max' => 4294967295,],
		], // 0x80000000~0x7FFFFFFF ~ 0x00000000~0xFFFFFFFF
		8 => [
			'signed' => ['len' => 20, 'endian' => 'q', 'min' => -9223372036854775808, 'max' => 9223372036854775807,],
			'unsigned' => ['len' => 20, 'endian' => 'Q', 'min' => 0, 'max' => 18446744073709551615,],
		], // 0x8000000000000000~0x7FFFFFFFFFFFFFFF ~ 0x0000000000000000~0xFFFFFFFFFFFFFFFF
	];

	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 4;
		return $this->rebuildBitOrder();
	}
	public function __toString() {

		return $this->val;
		//printf("%d\n",$a); //standard integer representation 
		//printf("%e\n",$a); //scientific notation 
		//PHP_VERSION = 8.1.9
		/*
			printf("%%b = '%b'\n", $n); // binary representation
			printf("%%c = '%c'\n", $c); // print the ascii character, same as chr() function
			printf("%%d = '%d'\n", $n); // standard integer representation
			printf("%%e = '%e'\n", $n); // scientific notation PHP_VERSION >= 5.2.1.
			printf("%%u = '%u'\n", $n); // unsigned integer representation of a positive integer
			printf("%%u = '%u'\n", $u); // unsigned integer representation of a negative integer
			printf("%%f = '%f'\n", $n); // floating point representation
			printf("%%o = '%o'\n", $n); // octal representation
			printf("%%s = '%s'\n", $n); // string representation
			printf("%%x = '%x'\n", $n); // hexadecimal representation (lower-case)
			printf("%%X = '%X'\n", $n); // hexadecimal representation (upper-case)

			printf("%%+d = '%+d'\n", $n); // sign specifier on a positive integer
			printf("%%+d = '%+d'\n", $u); // sign specifier on a negative integer
		*/
	}

	public function setVal($val) {
		$conf = $this->getMinMax();
		$this->rebuildVal($val, $conf);

		$raw = pack($conf['endian'], $val);
		$raw = strrev($raw);
		$this->readonly['rawOrdered'] = $raw;
		$this->readonly['raw'] = $this->orderWord($raw);
		return $this;
	}
	public function setRaw($cbin) {
		$conf = $this->getMinMax();
		$raw = substr($cbin, 0, $conf['bytes']);
		$this->readonly['raw'] = $raw;

		$raw = $this->orderWord($raw);
		$this->readonly['rawOrdered'] = $raw;
		$raw = strrev($raw);
		$val = $v = unpack($conf['endian'], $raw)[1];
		$this->rebuildVal($val, $conf);
		if ($v !== $val) {
			$this->val = $val;
			$this->log = "Rebuild val form $v to $val";
		}
		return $this;
	}
	public function setBytes($val) {
		$val = (int)$val;
		if (
			$val &&
			key_exists($val, self::INT_RANGES)
		) $this->readonly['bytes'] = min(PHP_INT_SIZE, $val);
		return $this;
	}
	/**
	 * Set $unsigned properties
	 *
	 * @param  bool $val Value to use in the type
	 * @return self
	 */
	public function setUnsigned($val) {
		$this->readonly['unsigned'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		return $this;
	}
	/**
	 * Set $zerofill properties
	 *
	 * @param  bool $val Value to use in the type
	 * @return self
	 */
	public function setZerofill($val) {
		$this->readonly['zerofill'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		return $this;
	}
	/**
	 * Set $bitOrder and  $bitOrderNibble, $bitOrderEndian, $bitOrderWord4, $bitOrderWord8 properties by string pattern
	 * - (bitOrderNibble|Nibble|N)[=[boolean]]
	 * - (bitOrderEndian|Endian|E)[=[boolean]]
	 * - (bitOrderWord4|Word4|4)[=[0|1|2|high|low|big|little]]
	 * - (bitOrderWord8|Word8|8)[=[0|1|2|high|low|big|little]]
	 * @see rebuildBitOrder to see $bitOrder final format
	 *
	 * @param  array|string $val
	 * @return self
	 */
	public function setBitOrder($val) {
		$arr = [
			'bitOrderNibble' => [
				'er' => '/\s*(?:bitOrder)?N(?:ibble)?\s*=?\s*(.+)?\s*/i',
				'fn' => 'Bool',
			],
			'bitOrderEndian' => [
				'er' => '/\s*(?:bitOrder)?E(?:ndian)?\s*=?\s*(.+)?\s*/i',
				'fn' => 'Bool',
			],
			'bitOrderWord4' => [
				'er' => '/\s*(?:bitOrder)?(?:Word)?4\s*=\s*?(.+)?\s*/',
				'fn' => 'HighLow',
			],
			'bitOrderWord8' => [
				'er' => '/\s*(?:bitOrder)?(?:Word)?8\s*=\s*?(.+)?\s*/',
				'fn' => 'HighLow',
			],
		];
		if (is_array($val)) {
			if (array_is_list($val)) {
				$fnPre = 'List';
				$keys = null;
			} else {
				$fnPre = 'Assoc';
				$keys = array_keys($val);
			}
			$v = null;
			foreach ($arr as $arg => $cnf) {
				$r = call_user_func_array(
					[$this, 'callbackOrder_' . $fnPre],
					[$val, $keys, $cnf['er'], $v]
				);
				$this->readonly[$arg] = call_user_func_array(
					[$this, 'callbackOrder_' . $cnf['fn']],
					[$r, $v,]
				);
			}
		} elseif (is_string($val)) {
			foreach ($arr as $arg => $cnf) {
				$r = $this->callbackOrder_String(
					$val,
					$cnf['er'],
					$v
				);
				$this->readonly[$arg] = call_user_func_array(
					[$this, 'callbackOrder_' . $cnf['fn']],
					[$r, $v,]
				);
			}
		} else return;
		return $this->rebuildBitOrder();
	}
	/**
	 * Set $bitOrderNibble properties
	 *
	 * @param  bool|string $val bool or string converted bool Value to use in the type
	 * @return self
	 */
	public function setBitOrderNibble($val = null) {
		$this->readonly['bitOrderNibble'] = is_null($val) || (bool)$val;
		return $this->rebuildBitOrder();
	}
	/**
	 * Set $bitOrderEndian properties
	 *
	 * @param  bool|string $val bool or string converted bool Value to use in the type
	 * @return self
	 */
	public function setBitOrderEndian($val = null) {
		$this->readonly['bitOrderEndian'] = is_null($val) || (bool)$val;
		return $this->rebuildBitOrder();
	}
	/**
	 * Set $bitOrderWord4 properties
	 *
	 * @param  string|int $val (0|1|2|h|l|high|low|big|little) Value to use in the type
	 * @return self
	 */
	public function setBitOrderWord4($val = null) {
		$this->readonly['bitOrderWord4'] = $this->rebuildHighLow($val);
		return $this->rebuildBitOrder();
	}
	/**
	 * Set $bitOrderWord8 properties
	 *
	 * @param  string|int $val (0|1|2|h|l|high|low|big|little) Value to use in the type
	 * @return self
	 */
	public function setBitOrderWord8($val = null) {
		$this->readonly['bitOrderWord8'] = $this->rebuildHighLow($val);
		return $this->rebuildBitOrder();
	}
	/**
	 * Get calculated $minmax propertie
	 *
	 * @return array $conf Configuration Range
	 */
	public function getMinMax() {
		$conf = $this->getRange();
		$minC = $this->min;
		$maxC = $this->max;
		$len = $this->len;
		if ($len) {
			if ($this->readonly['unsigned']) {
				$min = 0;
				$max = (int)str_repeat(9, $len);
			} else {
				$l = $len - 1;
				if ($len) {
					$min = $max = (int)str_repeat(9, $l);
					$min *= -1;
				} else {
					$min = 0;
					$max = 9;
				}
			}
			if (is_null($minC)) $minC = $min;
			if (is_null($maxC)) $maxC = $max;
		} else {
			if (is_null($minC)) $minC = $conf['min'];
			if (is_null($maxC)) $maxC = $conf['max'];
		}
		$conf['min'] = max($min, $minC, $conf['min']);
		$conf['max'] = max($max, $maxC, $conf['max']);
		if ($conf['max'] < 0) $conf['max'] = PHP_INT_MAX;
		return $conf;
	}
	/**
	 * Get calculated $range propertie
	 *
	 * @return array $conf Configuration Range
	 */
	public function getRange() {
		$unsigned = $this->unsigned;
		$signed = $unsigned ? 'unsigned' : 'signed';
		$bytes = $this->bytes;
		$conf = self::INT_RANGES[$bytes][$signed];
		$conf['bytes'] = $bytes;
		$conf['signed'] = $signed;
		$conf['unsigned'] = $unsigned;
		return $conf;
	}
	
	/**
	 * Callback function to setBitOrder method
	 *
	 * @param  string $val values passed
	 * @param  string $er Regular expression to math key
	 * @param  string|int $v Value refereced return
	 * @return bool
	 */
	private function callbackOrder_String($val, $er, &$v = null) {
		if (!preg_match($er, $val, $ret)) return false;
		$v = @$ret[1];
		return true;
	}
	/**
	 * Callback function to setBitOrder method
	 *
	 * @param  array $val values passed
	 * @param  array $keys keys of $val
	 * @param  string $er Regular expression to math key
	 * @param  string|int $v Value refereced return
	 * @return bool
	 */
	private function callbackOrder_List(&$val, &$keys, $er, &$v = null) {
		$a = preg_grep($er, $val);
		if (!$a) return false;
		$k = key($a);
		unset($val[$k]);
		return $this->callbackOrder_String($a[$k], $er, $v);
	}
	/**
	 * Callback function to setBitOrder method
	 *
	 * @param  array $val values passed
	 * @param  array $keys keys of $val
	 * @param  string $er Regular expression to math key
	 * @param  string|int $v Value refereced return
	 * @return bool
	 */
	private function callbackOrder_Assoc(&$val, &$keys, $er, &$v = null) {
		$a = preg_grep($er, $keys);
		if (!$a) return false;
		$k = key($a);
		$v = $val[$k];
		unset($keys[$k]);
		unset($val[$k]);
		return true;
	}
	/**
	 * Callback function to setBitOrder method
	 *
	 * @param  bool $r callbackOrder_List|callbackOrder_Assoc return value
	 * @param  string|int $v Word 4/8 value
	 * @return int See rebuildHighLow method
	 */
	private function callbackOrder_Bool($r, $v) {
		return ($r &&
			(is_null($v) ||
				!preg_match('/^\s*(off|f(alse)?|l(ow)?|litte)\s*$/i', $v)
			)
		) ? 1 : 0;
	}	
	/**
	 * Callback function to setBitOrder method
	 *
	 * @param  bool $r callbackOrder_List|callbackOrder_Assoc return value
	 * @param  string|int $v Word 4/8 value
	 * @return int See rebuildHighLow method
	 */
	private function callbackOrder_HighLow($r, $v) {
		return $r ? $this->rebuildHighLow($v) : 0;
	}
	
	/**
	 * Convert string|int value of the $bitOrderWordX value
	 *
	 * @param  string|int $val Bit Order Word value to convert int
	 * @return int 0, 1 or 2 (off, High, Low)
	 */
	protected function rebuildHighLow($val) {
		if (is_null($val) || preg_match('/^\s*(1|h(igh)?|on|true|up|big)\s*$/i', $val)) return 1;
		if (preg_match('/^\s*(2|l(ow)?|little)\s*$/i', $val)) return 2;
		return 0;
	}
	/**
	 * set $bitOrder by $bitOrderNibble, $bitOrderEndian, $bitOrderWord4, $bitOrderWord8 properties by string pattern
	 * 
	 * ```php
	 *   $bitOrder = $bitOrderNibble ? 'N' : '';
	 *   $bitOrder .= $bitOrderEndian ? 'E' : '';
	 *   $bitOrder .= $bitOrderWord4 ==1 ? '4h' : '';
	 *   $bitOrder .= $bitOrderWord4 ==2 ? '4l' : '';
	 *   $bitOrder .= $bitOrderWord8 ==1 ? '8h' : '';
	 *   $bitOrder .= $bitOrderWord8 ==2 ? '8l' : '';
	 * ```
	 * 
	 * @example NE48 or 48 or 4 or NE or E4l or NB4l8l
	 *
	 * @return self
	 */
	protected function rebuildBitOrder() {
		static $arr = [
			self::BIT_ORDER_WORD_NONE => '',
			self::BIT_ORDER_WORD_HIGH => '',
			self::BIT_ORDER_WORD_LOW => 'l'
		];
		$out = $this->readonly['bitOrderNibble'] ? 'N' : '';
		$out .= $this->readonly['bitOrderEndian'] ? 'E' : '';
		$out .= ($k = $this->readonly['bitOrderWord4']) ? '4' . $arr[$k] : '';
		$out .= ($k = $this->readonly['bitOrderWord8']) ? '8' . $arr[$k] : '';
		$this->readonly['bitOrder'] = $out;
		return $this;
	}
	/**
	 * Rebuild $val propertie with limits defined based:
	 * - min, max, source
	 *
	 * @param  int|float|double $val
	 * @param  array $minmax Propertie Configuration Range
	 * @return self
	 */
	protected function rebuildVal(&$val, $minmax) {
		$val = max(min($val, $minmax['max']), $minmax['min']);
		$this->readonly['val'] = $val;
		return $this;
	}	
	/**
	 * Reorder binary word string for any bytes configurated
	 *
	 * @param  string $cbin Binary word string
	 * @return string binary Odered value
	 */
	protected function orderWord($cbin) {
		$bytes = $this->bytes;
		$cbin = call_user_func_array(
			[$this, __FUNCTION__ . $bytes],
			[$cbin, $bytes,]
		);
		return $cbin;
	}	
	/**
	 * Reorder binary word string for 1 byte
	 *
	 * @param  string $cbin Binary word string
	 * @param  int $bytes Number of bytes of type
	 * @return string binary odered value
	 */
	protected function orderWord1($cbin, $bytes) {
		return $this->readonly['bitOrderNibble'] ? $cbin : pack('H*', unpack('h*', $cbin)[1]);
	}
	/**
	 * Reorder binary word string for 2 byte
	 *
	 * @param  string $cbin Binary word string
	 * @param  int $bytes Number of bytes of type
	 * @return string binary odered value
	 */
	protected function orderWord2($cbin, $bytes) {
		$cbin = $this->orderWord1($cbin, $bytes);
		return $this->readonly['bitOrderEndian'] ? $cbin : strrev($cbin);
	}
	/**
	 * Reorder binary word string for 3 byte
	 *
	 * @param  string $cbin Binary word string
	 * @param  int $bytes Number of bytes of type
	 * @return string binary odered value
	 */
	protected function orderWord3($cbin, $bytes) {
		return strlen($cbin) == 4 ? substr($cbin, -3) : "\x00$cbin";
	}
	/**
	 * Reorder binary word string for 4 byte
	 *
	 * @param  string $cbin Binary word string
	 * @param  int $bytes Number of bytes of type
	 * @return string binary odered value
	 */
	protected function orderWord4($cbin, $bytes) {
		$cbin = $this->orderWord1($cbin, $bytes);
		if ($this->readonly['bitOrderEndian']) {
			if ($this->readonly['bitOrderWord4'] <= 1) return $cbin;
			return substr($cbin, 2, 2) . substr($cbin, 0, 2);
		}
		return $this->readonly['bitOrderWord4'] == 1 ?
			$cbin[1] . $cbin[0] .
			$cbin[3] . $cbin[2] :

			$cbin[3] . $cbin[2] .
			$cbin[1] . $cbin[0];
	}
	/**
	 * Reorder binary word string for 8 byte
	 *
	 * @param  string $cbin Binary word string
	 * @param  int $bytes Number of bytes of type
	 * @return string binary odered value
	 */
	protected function orderWord8($cbin, $bytes) {
		$cbin = $this->orderWord1($cbin, $bytes);
		if ($this->readonly['bitOrderEndian']) {
			if ($this->readonly['bitOrderWord4'] <= 1) {
				if ($this->readonly['bitOrderWord8'] <= 1) return $cbin;
				return substr($cbin, 4, 4) . substr($cbin, 0, 4);
			}
			return $this->readonly['bitOrderWord8'] <= 1 ?
				$cbin[2] . $cbin[3] .
				$cbin[0] . $cbin[1] .
				$cbin[6] . $cbin[7] .
				$cbin[4] . $cbin[5] :

				$cbin[6] . $cbin[7] .
				$cbin[4] . $cbin[5] .
				$cbin[2] . $cbin[3] .
				$cbin[0] . $cbin[1];
		}
		if ($this->readonly['bitOrderWord4'] == 1) {
			return $this->readonly['bitOrderWord8'] <= 1 ?
				$cbin[1] . $cbin[0] .
				$cbin[3] . $cbin[2] .
				$cbin[5] . $cbin[4] .
				$cbin[7] . $cbin[6] :

				$cbin[5] . $cbin[4] .
				$cbin[7] . $cbin[6] .
				$cbin[1] . $cbin[0] .
				$cbin[3] . $cbin[2];
		}
		return
			$this->readonly['bitOrderWord8'] == 2 ||
			$this->readonly['bitOrderWord4'] +
			$this->readonly['bitOrderWord8'] == 0 ?

			$cbin[7] . $cbin[6] .
			$cbin[5] . $cbin[4] .
			$cbin[3] . $cbin[2] .
			$cbin[1] . $cbin[0] :

			$cbin[3] . $cbin[2] .
			$cbin[1] . $cbin[0] .
			$cbin[7] . $cbin[6] .
			$cbin[5] . $cbin[4];
	}
}
