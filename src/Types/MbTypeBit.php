<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeBit extends MbTypeAny {
	public function __construct($val = null) {
		$this->readonly['raw'] = 0;
		$this->readonly['aRaw'] = [0];
		$this->readonly['len'] = 1;
		$this->readonly['unit'] = 'bit';
		$this->readonly['unsigned'] = true;
		$this->raw = $val;
	}
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
	public function setLen($val) {
		return $this;
	}
}
