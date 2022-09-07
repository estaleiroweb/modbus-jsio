<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt8 extends MbTypeInt8 {
	protected function init() {
		parent::init();
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function setUnsigned($val) {
		return $this;
	}
}