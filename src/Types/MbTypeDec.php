<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeDec extends MbTypeInt {
	public function __construct($endian = null, $lowWFirst = null) {
		$this->__construct($endian, $lowWFirst);
		$this->readonly['precision'] = 2;
		$this->readonly['dec'] = true;
	}
	public function setDec($val) {
		$this->readonly['dec'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		return $this;
	}
	public function setPrecision($val) {
		$this->readonly['precision'] = min((int)$val, $this->getConfRange()['len']);
		return $this;
	}
}
