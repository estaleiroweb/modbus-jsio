<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt1 extends MbTypeInt1 {
	protected function init() {
		parent::init();
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function setUnsigned($val) {
		return $this;
	}
}