<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt1 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 1;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}
}
