<?php

namespace EstaleiroWeb\Modbus\FC;

class WriteSingleCoil extends FC {	
	/**
	 * __construct
	 *
	 * @param  int $startAddress
	 * @param  bool $coil
	 * @param  int $unitId
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($startAddress, $coil, $unitId = 0, $transactionId = null) {
		call_user_func_array([$this, '__construct'], func_get_args());
	}
}
