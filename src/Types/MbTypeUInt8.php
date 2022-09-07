<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt8 extends MbTypeInt8 {
	public function setUnsigned($val) {
		return $this;
	}
}