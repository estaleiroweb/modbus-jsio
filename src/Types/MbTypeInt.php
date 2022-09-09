<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt extends MbType {
	public const BIT_ORDER_WORD_NONE = 0;
	public const BIT_ORDER_WORD_HIGH = 1;
	public const BIT_ORDER_WORD_LOW = 2;
	public const BIT_ORDER = [
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
	 * Detaild of Ranges
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
		$this->readonly['bytes'] = 4;
		$this->readonly['bitOrderNibble'] = true;
		$this->readonly['bitOrderEndian'] = true;
		$this->readonly['bitOrderWord4'] = 1;
		$this->readonly['bitOrderWord8'] = 1;
		return $this;
	}
	public function __toString() {
		return $this->val;
	}
	public function __invoke(&$cbin = null) {
		if (is_null($cbin)) return $this->readonly['raw'];
		$conf = $this->getRange();
		$raw = substr($cbin, 0, $conf['bytes']);
		$this->readonly['raw'] = $raw;
		$cbin = substr($cbin, $conf['bytes']);

		$raw = $this->orderWord($raw);
		$raw = strrev($raw);
		$this->readonly['val'] = unpack($conf['endian'], $raw)[1];
		return $this;
	}
	public function setVal($val) {
		$conf = $this->getMinMax();
		$val = max(min($val, $conf['max']), $conf['min']);
		$this->readonly['val'] = $val;

		$raw = pack($conf['endian'], $val);
		$raw = strrev($raw);
		$this->readonly['raw'] = $this->orderWord($raw);
		return $this;
	}
	public function setUnsigned($val) {
		$this->readonly['unsigned'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		return $this;
	}
	public function setZerofill($val) {
		$this->readonly['zerofill'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
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
	public function setLen($val) {
		$val = (int)$val;
		if ($val > 0) $this->readonly['len'] = $val;
		return $this;
	}
	public function setBitOrderNibble($val) {
		$this->readonly['bitOrderNibble'] = (bool)$val;
		return $this;
	}
	public function setBitOrderEndian($val) {
		$this->readonly['bitOrderEndian'] = (bool)$val;
		return $this;
	}
	public function setBitOrderWord4($val) {
		$c = filter_var($val, FILTER_VALIDATE_INT, ['min_range' => 0, 'max_range' => 2]);
		if ($c !== false) $this->readonly['bitOrderWord4'] = (int)$val;
		return $this;
	}
	public function setBitOrderWord8($val) {
		$c = filter_var($val, FILTER_VALIDATE_INT, ['min_range' => 0, 'max_range' => 2]);
		if ($c !== false) $this->readonly['bitOrderWord8'] = (int)$val;
		return $this;
	}
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

	protected function orderWord($cbin) {
		$bytes = $this->bytes;
		$cbin = call_user_func_array(
			[$this, __FUNCTION__ . $bytes],
			[$cbin, $this->order, $bytes,]
		);
		return $cbin;
	}
	protected function orderWord1($cbin, $order, $bytes) {
		return $this->readonly['bitOrderNibble'] ? $cbin : pack('H*', unpack('h*', $cbin)[1]);
	}
	protected function orderWord2($cbin, $order, $bytes) {
		$cbin = $this->orderWord1($cbin, $order, $bytes);
		return $this->readonly['bitOrderEndian'] ? $cbin : strrev($cbin);
	}
	protected function orderWord3($cbin, $order, $bytes) {
		return strlen($cbin) == 4 ? substr($cbin, -3) : "\x00$cbin";
	}
	protected function orderWord4($cbin, $order, $bytes) {
		$cbin = $this->orderWord1($cbin, $order, $bytes);
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
	protected function orderWord8($cbin, $order, $bytes) {
		$cbin = $this->orderWord1($cbin, $order, $bytes);
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
