<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeDouble extends MbTypeFoat {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 8;
		return $this;
	}
}
