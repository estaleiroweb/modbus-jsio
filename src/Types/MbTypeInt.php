<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt extends MbType {
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
		return $this;
	}
	public function __toString() {
		return $this->val;
	}
	public function __invoke(&$cbin = null) {
		$bytes = $this->bytes;
		$conf = $this->getConfRange();
		$format = $conf['endian'];
		if (is_null($cbin)) {
			$val = pack($format, $this->val);
			if ($bytes == 3) $val = substr($val, -3);
			$val = $this->orderWord($val);
			return $val;
		}
		$hex = substr($cbin, 0, $bytes);
		$cbin = substr($cbin, $bytes);

		$hex = $this->orderWord($hex);
		if ($bytes == 3) $hex = "\xFF$hex";
		$hex = unpack($format, $hex)[1];
		$this->val = $hex;
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
	public function setVal($val) {
		$conf = $this->getMinMax();
		$this->readonly['val'] = max(min($val, $conf['max']), $conf['min']);
		return $this;
	}

	protected function getMinMax() {
		$conf = $this->getConfRange();
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
	protected function getConfRange() {
		$unsigned = $this->unsigned;
		$signed = $unsigned ? 'unsigned' : 'signed';
		$bytes = $this->bytes;
		$conf = self::INT_RANGES[$bytes][$signed];
		$conf['bytes'] = $bytes;
		$conf['signed'] = $signed;
		$conf['unsigned'] = $unsigned;
		return $conf;
	}
}
