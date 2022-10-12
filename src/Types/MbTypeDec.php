<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeDec extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 4;
		$this->readonly['precision'] = 2;
		return $this;
	}
	public function setPrecision($val) {
		$this->readonly['precision'] = min((int)$val, $this->getRange()['len']);
		return $this;
	}
}
