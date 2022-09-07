<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt2 extends MbTypeInt {
	public function __construct($endian = null, $lowWFirst = null) {
		parent::__construct($endian, $lowWFirst);
		$this->readonly['bytes'] = 2;
	}
	public function setBytes($val) {
		return $this;
	}

	public static function endian_c2v_signed_be($cbin) {
		return unpack('s', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin) {
		return unpack('s', self::cSwap16($cbin))[1];
	}
	public static function endian_c2v_signed_mb($cbin) {
		return unpack('s', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin) {
		return unpack('s', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin) { //ex: F312 => 3F21
		return unpack('s', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin) { //ex: F312 => 213F
		return unpack('s', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_c2v_unsigned_be($cbin) {
		return unpack('n', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin) {
		return unpack('v', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin) {
		return unpack('S', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin) {
		return unpack('S', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin) { //ex: F312 => 3F21
		return unpack('n', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin) { //ex: F312 => 213F
		return unpack('v', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_v2c_signed_be($val) {
		return pack('s', $val);
	}
	public static function endian_v2c_signed_le($val) {
		return self::cSwap16(pack('s', $val));
	}
	public static function endian_v2c_signed_mb($val) {
		return pack('s', $val);
	}
	public static function endian_v2c_signed_md($val) {
		return pack('s', $val);
	}
	public static function endian_v2c_signed_bi($val) { //ex: F312 => 3F21
		return self::cInvert(pack('s', $val));
	}
	public static function endian_v2c_signed_li($val) { //ex: F312 => 213F
		return self::cInvert(self::cSwap16(pack('s', $val)));
	}

	public static function endian_v2c_unsigned_be($val) {
		return pack('n', $val);
	}
	public static function endian_v2c_unsigned_le($val) {
		return pack('v', $val);
	}
	public static function endian_v2c_unsigned_mb($val) {
		return pack('S', $val);
	}
	public static function endian_v2c_unsigned_md($val) {
		return pack('S', $val);
	}
	public static function endian_v2c_unsigned_bi($val) {
		return self::cInvert(pack('n', $val));
	}
	public static function endian_v2c_unsigned_li($val) {
		return self::cSwap16(self::cInvert(pack('v', $val)));
	}
}
