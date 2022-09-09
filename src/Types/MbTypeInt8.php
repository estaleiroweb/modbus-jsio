<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt8 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 8;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}
}
