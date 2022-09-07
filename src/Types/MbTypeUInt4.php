<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt4 extends MbTypeInt4 {
	public function setUnsigned($val) {
		return $this;
	}
}