<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeBit extends MbTypeAny {
	protected $readonly = [
		'raw' => 0,
		'aRaw' => [0],
		'len' => 1,
		'unit' => 'bit',
		'precision' => null,
		'unsigned' => true,
	];
	public function __toString() {
		$s = $this->unsigned ? 1 : -1;
		return $s * $this->raw;
	}
	public function setRaw(&$val) {
		$val = (int)filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		$this->readonly['raw'] = $val;
		$this->readonly['raw'] = [$val];
		return $this;
	}
}
