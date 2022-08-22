<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeFoat extends MbTypeInt {
	public function __construct($val = null) {
		$this->readonly['len'] = 2;
		$this->readonly['precision'] = 15;
		$this->readonly['unsigned'] = false;
		$this->readonly['dec'] = false;
		$this->raw = $val;
	}
}
