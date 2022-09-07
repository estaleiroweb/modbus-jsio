<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt3 extends MbTypeInt {
	public function __construct($endian = null, $lowWFirst = null) {
		parent::__construct($endian, $lowWFirst);
		$this->readonly['bytes'] = 3;
	}
	public function setBytes($val) {
		return $this;
	}

	public static function endian_c2v_signed_be($cbin) {
	}
	public static function endian_c2v_signed_le($cbin) {
	}
	public static function endian_c2v_signed_mb($cbin) {
	}
	public static function endian_c2v_signed_md($cbin) {
	}
	public static function endian_c2v_signed_li($cbin) {
	}
	public static function endian_c2v_signed_bi($cbin) {
	}

	public static function endian_c2v_unsigned_be($cbin) {
	}
	public static function endian_c2v_unsigned_le($cbin) {
	}
	public static function endian_c2v_unsigned_mb($cbin) {
	}
	public static function endian_c2v_unsigned_md($cbin) {
	}
	public static function endian_c2v_unsigned_bi($cbin) {
	}
	public static function endian_c2v_unsigned_li($cbin) {
	}

	public static function endian_v2c_signed_be($cbin) {
	}
	public static function endian_v2c_signed_le($cbin) {
	}
	public static function endian_v2c_signed_mb($cbin) {
	}
	public static function endian_v2c_signed_md($cbin) {
	}
	public static function endian_v2c_signed_li($cbin) {
	}
	public static function endian_v2c_signed_bi($cbin) {
	}

	public static function endian_v2c_unsigned_be($cbin) {
	}
	public static function endian_v2c_unsigned_le($cbin) {
	}
	public static function endian_v2c_unsigned_mb($cbin) {
	}
	public static function endian_v2c_unsigned_md($cbin) {
	}
	public static function endian_v2c_unsigned_bi($cbin) {
	}
	public static function endian_v2c_unsigned_li($cbin) {
	}
}
