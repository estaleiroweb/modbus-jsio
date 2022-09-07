<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt2 extends MbTypeInt2 {
	public function setUnsigned($val) {
		return $this;
	}
}