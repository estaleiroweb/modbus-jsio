<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt2 extends MbTypeInt2 {
	protected function init() {
		parent::init();
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function setUnsigned($val) {
		return $this;
	}
}