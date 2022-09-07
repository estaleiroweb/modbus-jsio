<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt3 extends MbTypeInt3 {
	protected function init() {
		parent::init();
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function setUnsigned($val) {
		return $this;
	}
}