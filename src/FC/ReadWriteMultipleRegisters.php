<?php

namespace EstaleiroWeb\Modbus\FC;

class ReadWriteMultipleRegisters extends FC {	
	/**
	 * __construct
	 *
	 * @param  int $readStartAddress
	 * @param  int $readQuantity
	 * @param  int $writeStartAddress
	 * @param  array $writeRegisters
	 * @param  int $unitId
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($readStartAddress, $readQuantity, $writeStartAddress, array $writeRegisters, $unitId = 0, $transactionId = null) {
		call_user_func_array([$this, '__construct'], func_get_args());
	}
}