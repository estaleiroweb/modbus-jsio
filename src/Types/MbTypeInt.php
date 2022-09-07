<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt extends MbType {
	/**
	 * Detaild of Ranges
	 */
	public const INT_RANGES = [
		1 => [
			'signed' => ['len' => 3, 'min' => -128, 'max' => 127,],
			'unsigned' => ['len' => 3, 'min' => 0, 'max' => 255,],
		], // 0x80~0x7F ~ 0x00~0xFF
		2 => [
			'signed' => ['len' => 6, 'min' => -32768, 'max' => 32767,],
			'unsigned' => ['len' => 5, 'min' => 0, 'max' => 65535,],
		], // 0x8000~0x7FFF ~ 0x0000~0xFFFF
		3 => [
			'signed' => ['len' => 8, 'min' => -8388608, 'max' => 8388607,],
			'unsigned' => ['len' => 8, 'min' => 0, 'max' => 16777215,],
		], // 0x800000~0x7FFFFF ~ 0x000000~0xFFFFFF
		4 => [
			'signed' => ['len' => 11, 'min' => -2147483648, 'max' => 2147483647,],
			'unsigned' => ['len' => 10, 'min' => 0, 'max' => 4294967295,],
		], // 0x80000000~0x7FFFFFFF ~ 0x00000000~0xFFFFFFFF
		8 => [
			'signed' => ['len' => 20, 'min' => -9223372036854775808, 'max' => 9223372036854775807,],
			'unsigned' => ['len' => 20, 'min' => 0, 'max' => 18446744073709551615,],
		], // 0x8000000000000000~0x7FFFFFFFFFFFFFFF ~ 0x0000000000000000~0xFFFFFFFFFFFFFFFF
	];

	public function __construct() {
		$this->init();
		$this->readonly['bytes'] = 4;
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
		if ($val && key_exists($val, self::INT_RANGES)) {
			$this->readonly['bytes'] = $val;
		}
		return $this;
	}
	public function setLen($val) {
		$val = (int)$val;
		if ($val > 0) $this->readonly['len'] = min(
			$val,
			$this->getConfRange()['len']
		);
		return $this;
	}
	public function setVal($val) {
		$conf = $this->getConfRange();
		$this->readonly['val'] = max(min($val, $conf['max']), $conf['min']);
		return $this;
	}

	protected function getConfRange() {
		$s = $this->readonly['unsigned'] ? 'unsigned' : 'signed';
		$b = $this->readonly['bytes'];
		$conf = self::INT_RANGES[$b][$s];
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
		return $conf;
	}
}
