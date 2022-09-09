<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt4 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 4;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}
}
