<?php

namespace EstaleiroWeb\Modbus\FC;

class WriteSingleRegister extends FC {	
	/**
	 * __construct
	 *
	 * @param  int $startAddress
	 * @param  int $value
	 * @param  int $unitId
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($startAddress, $value, $unitId = 0, $transactionId = null) {
		call_user_func_array([$this, '__construct'], func_get_args());
	}
}
