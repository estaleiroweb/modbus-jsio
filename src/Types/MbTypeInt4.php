<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt4 extends MbTypeInt {
	public function __construct($endian = null, $lowWFirst = null) {
		parent::__construct($endian, $lowWFirst);
		$this->readonly['bytes'] = 4;
	}
	public function setBytes($val) {
		return $this;
	}

	public static function endian_c2v_signed_be($cbin) {
		return unpack('l', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin) {
		return unpack('l', self::cSwap16($cbin))[1];
	}
	public static function endian_c2v_signed_mb($cbin) {
		return unpack('l', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin) {
		return unpack('i', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin) {
		return unpack('l', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin) {
		return unpack('l', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_c2v_unsigned_be($cbin) {
		return unpack('N', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin) {
		return unpack('V', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin) {
		return unpack('L', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin) {
		return unpack('I', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin) {
		return unpack('V', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin) {
		return unpack('N', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_v2c_signed_be($val) {
		return pack('l', $val);
	}
	public static function endian_v2c_signed_le($val) {
		return self::cSwap16(pack('l', $val));
	}
	public static function endian_v2c_signed_mb($val) {
		return pack('l', $val);
	}
	public static function endian_v2c_signed_md($val) {
		return pack('i', $val);
	}
	public static function endian_v2c_signed_bi($val) {
		return self::cInvert(pack('l', $val));
	}
	public static function endian_v2c_signed_li($val) {
		return self::cInvert(self::cSwap16(pack('l', $val)));
	}

	public static function endian_v2c_unsigned_be($val) {
		return pack('N', $val);
	}
	public static function endian_v2c_unsigned_le($val) {
		return pack('V', $val);
	}
	public static function endian_v2c_unsigned_mb($val) {
		return pack('L', $val);
	}
	public static function endian_v2c_unsigned_md($val) {
		return pack('I', $val);
	}
	public static function endian_v2c_unsigned_bi($val) {
		return self::cInvert(pack('V', $val));
	}
	public static function endian_v2c_unsigned_li($val) {
		return self::cInvert(self::cSwap16(pack('N', $val)));
	}
}
