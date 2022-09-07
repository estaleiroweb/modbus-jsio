<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeUInt3 extends MbTypeInt3 {
	public function setUnsigned($val) {
		return $this;
	}
}