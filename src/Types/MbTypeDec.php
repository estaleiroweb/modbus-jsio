<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeDec extends MbTypeInt {
	public function __construct($val = null) {
		$this->readonly['len'] = 2;
		$this->readonly['precision'] = 2;
		$this->readonly['unsigned'] = false;
		$this->readonly['dec'] = true;
		$this->raw = $val;
	}
}