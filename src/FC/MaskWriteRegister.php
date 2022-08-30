<?php

namespace EstaleiroWeb\Modbus\FC;

class MaskWriteRegister extends FC {	
	/**
	 * __construct
	 *
	 * @param  int $address
	 * @param  int $andMask
	 * @param  int $orMask
	 * @param  int $unitId
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($address, $andMask, $orMask, $unitId = 1, $transactionId = null) {
		call_user_func_array([$this, '__construct'], func_get_args());
	}
}