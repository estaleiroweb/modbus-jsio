<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt1 extends MbTypeInt1 {
	public function setUnsigned($val) {
		return $this;
	}
}