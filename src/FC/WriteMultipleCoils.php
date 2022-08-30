<?php

namespace EstaleiroWeb\Modbus\FC;

class WriteMultipleCoils extends FC {	
	/**
	 * __construct
	 *
	 * @param  int $startAddress
	 * @param  array $coils
	 * @param  int $unitId
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($startAddress, array $coils, $unitId = 0, $transactionId = null) {
		call_user_func_array([$this, '__construct'], func_get_args());
	}
}
