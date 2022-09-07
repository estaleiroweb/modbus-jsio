<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeDec extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['dec'] = true;
		$this->readonly['bytes'] = 4;
		$this->readonly['precision'] = 2;
		return $this;
	}
	public function setDec($val) {
		$this->readonly['dec'] = filter_var(
			$val,
			FILTER_VALIDATE_BOOL
		);
		return $this;
	}
	public function setPrecision($val) {
		$this->readonly['precision'] = min((int)$val, $this->getConfRange()['len']);
		return $this;
	}
}
