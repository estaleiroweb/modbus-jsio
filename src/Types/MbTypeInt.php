<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt extends MbTypeAny {
	public function __construct($val = null) {
		$this->readonly['len'] = 2;
		$this->readonly['unsigned'] = false;
		$this->raw = $val;
	}
	public function setLen($val) {
		$val = (int)$val;
		if (in_array($val, [2, 4, 8])) {
			$this->readonly['len'] = $val;
			if ($this->aRaw) $this->raw = $this->raw;
		}
		return $this;
	}
}
