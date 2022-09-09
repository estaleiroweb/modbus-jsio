<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt2 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 2;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}
}
