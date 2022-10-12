<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeBit extends MbType {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 1;
		$this->readonly['len'] = 1;
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function __toString() {
		return ($this->unsigned ? 1 : -1) * $this->val;
	}
	public function setBytes($val) {
		return $this;
	}
	public function setLen($val) {
		return $this;
	}
}
