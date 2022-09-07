<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt1 extends MbTypeInt {
	public function __construct($endian = null, $lowWFirst = null) {
		parent::__construct($endian, $lowWFirst);
		$this->readonly['bytes'] = 1;
	}
	public function setBytes($val) {
		return $this;
	}

	public static function endian_c2v_signed_be($cbin, $lowWFirst = null) {
		return unpack('c', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin, $lowWFirst = null) {
		return unpack('c', $cbin)[1];
	}
	public static function endian_c2v_signed_mb($cbin, $lowWFirst = null) {
		return unpack('c', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin, $lowWFirst = null) {
		return unpack('c', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin, $lowWFirst = null) {
		return unpack('c', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin, $lowWFirst = null) {
		return unpack('c', self::cInvert($cbin))[1];
	}

	public static function endian_c2v_unsigned_be($cbin, $lowWFirst = null) {
		return unpack('C', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin, $lowWFirst = null) {
		return unpack('C', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin, $lowWFirst = null) {
		return unpack('C', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin, $lowWFirst = null) {
		return unpack('C', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin, $lowWFirst = null) {
		return unpack('C', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin, $lowWFirst = null) {
		return unpack('C', self::cInvert($cbin))[1];
	}

	public static function endian_v2c_signed_be($val, $lowWFirst = null) {
		return pack('c', $val);
	}
	public static function endian_v2c_signed_le($val, $lowWFirst = null) {
		return pack('c', $val);
	}
	public static function endian_v2c_signed_mb($val, $lowWFirst = null) {
		return pack('c', $val);
	}
	public static function endian_v2c_signed_md($val, $lowWFirst = null) {
		return pack('c', $val);
	}
	public static function endian_v2c_signed_bi($val, $lowWFirst = null) {
		return self::cInvert(pack('c', $val));
	}
	public static function endian_v2c_signed_li($val, $lowWFirst = null) {
		return self::cInvert(pack('c', $val));
	}

	public static function endian_v2c_unsigned_be($val, $lowWFirst = null) {
		return pack('C', $val);
	}
	public static function endian_v2c_unsigned_le($val, $lowWFirst = null) {
		return pack('C', $val);
	}
	public static function endian_v2c_unsigned_mb($val, $lowWFirst = null) {
		return pack('C', $val);
	}
	public static function endian_v2c_unsigned_md($val, $lowWFirst = null) {
		return pack('C', $val);
	}
	public static function endian_v2c_unsigned_bi($val, $lowWFirst = null) {
		return self::cInvert(pack('C', $val));
	}
	public static function endian_v2c_unsigned_li($val, $lowWFirst = null) {
		return self::cInvert(pack('C', $val));
	}
}
