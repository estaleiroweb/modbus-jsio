<?php

namespace EstaleiroWeb\Modbus\FC;

class WriteMultipleRegisters extends FC {	
	/**
	 * __construct
	 *
	 * @param  int $startAddress
	 * @param  array $registers
	 * @param  int $unitId
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($startAddress, array $registers, $unitId = 0, $transactionId = null) {
		call_user_func_array([$this, '__construct'], func_get_args());
	}
}
