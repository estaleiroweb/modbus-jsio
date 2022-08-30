<?php

namespace EstaleiroWeb\Modbus\Base;

use ReflectionClass;

class NewObj {
	static public function call_user_class_array($class, array $args = []) {
		$reflect  = new ReflectionClass($class);
		return $reflect->newInstanceArgs($args);
	}
	static public function call_user_class($class) {
		$args = func_get_args();
		array_shift($args);
		return self::call_user_class_array($class, $args);
	}
}
