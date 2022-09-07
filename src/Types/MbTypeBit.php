<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeBit extends MbType {
	protected function init() {
		$this->readonly['bytes'] = 1;
		$this->readonly['len'] = 1;
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function __construct($val = null) {
		$this->init();
	}
	public function __toString() {
		$s = $this->unsigned ? 1 : -1;
		return $s * $this->raw;
	}
	public function setLen($val) {
		return $this;
	}
}
