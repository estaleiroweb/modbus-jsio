<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt4 extends MbTypeInt4 {
	protected function init() {
		parent::init();
		$this->readonly['unsigned'] = true;
		return $this;
	}
	public function setUnsigned($val) {
		return $this;
	}
}