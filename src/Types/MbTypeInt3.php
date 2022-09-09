<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt3 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 3;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}
}
