<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt8 extends MbTypeInt {
	public function __construct($endian = null, $lowWFirst = null) {
		parent::__construct($endian, $lowWFirst);
		$this->readonly['bytes'] = 8;
	}
	public function setBytes($val) {
		return $this;
	}
	
	public static function endian_c2v_signed_be($cbin) {
		return unpack('q', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin) {
		return unpack('q', self::cSwap64($cbin))[1];
	}
	public static function endian_c2v_signed_mb($cbin) {
		return unpack('q', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin) {
		return unpack('q', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin) {
		return unpack('q', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin) {
		return unpack('q', self::cSwap64(self::cInvert($cbin)))[1];
	}

	public static function endian_c2v_unsigned_be($cbin) {
		return unpack('J', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin) {
		return unpack('P', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin) {
		return unpack('Q', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin) {
		return unpack('Q', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin) {
		return unpack('J', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin) {
		return unpack('P', self::cInvert($cbin))[1];
	}

	public static function endian_v2c_signed_be($val) {
		return pack('q', $val);
	}
	public static function endian_v2c_signed_le($val) {
		return self::cSwap64(pack('q', $val));
	}
	public static function endian_v2c_signed_mb($val) {
		return pack('q', $val);
	}
	public static function endian_v2c_signed_md($val) {
		return pack('q', $val);
	}
	public static function endian_v2c_signed_bi($val) {
		return self::cInvert(pack('q', $val));
	}
	public static function endian_v2c_signed_li($val) {
		return self::cInvert(self::cSwap64(pack('q', $val)));
	}

	public static function endian_v2c_unsigned_be($val) {
		return pack('J', $val);
	}
	public static function endian_v2c_unsigned_le($val) {
		return pack('P', $val);
	}
	public static function endian_v2c_unsigned_mb($val) {
		return pack('Q', $val);
	}
	public static function endian_v2c_unsigned_md($val) {
		return pack('Q', $val);
	}
	public static function endian_v2c_unsigned_bi($val) {
		return self::cInvert(pack('J', $val));
	}
	public static function endian_v2c_unsigned_li($val) {
		return self::cInvert(pack('P', $val));
	}
}
