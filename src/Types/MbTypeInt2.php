<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt2 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 2;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}

	public static function endian_c2v_signed_be($cbin,$lowWFirst=null) {
		return unpack('s', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin,$lowWFirst=null) {
		return unpack('s', self::cSwap16($cbin))[1];
	}
	public static function endian_c2v_signed_mb($cbin,$lowWFirst=null) {
		return unpack('s', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin,$lowWFirst=null) {
		return unpack('s', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin,$lowWFirst=null) { //ex: F312 => 3F21
		return unpack('s', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin,$lowWFirst=null) { //ex: F312 => 213F
		return unpack('s', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_c2v_unsigned_be($cbin,$lowWFirst=null) {
		return unpack('n', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin,$lowWFirst=null) {
		return unpack('v', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin,$lowWFirst=null) {
		return unpack('S', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin,$lowWFirst=null) {
		return unpack('S', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin,$lowWFirst=null) { //ex: F312 => 3F21
		return unpack('n', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin,$lowWFirst=null) { //ex: F312 => 213F
		return unpack('v', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_v2c_signed_be($val,$lowWFirst=null) {
		return pack('s', $val);
	}
	public static function endian_v2c_signed_le($val,$lowWFirst=null) {
		return self::cSwap16(pack('s', $val));
	}
	public static function endian_v2c_signed_mb($val,$lowWFirst=null) {
		return pack('s', $val);
	}
	public static function endian_v2c_signed_md($val,$lowWFirst=null) {
		return pack('s', $val);
	}
	public static function endian_v2c_signed_bi($val,$lowWFirst=null) { //ex: F312 => 3F21
		return self::cInvert(pack('s', $val));
	}
	public static function endian_v2c_signed_li($val,$lowWFirst=null) { //ex: F312 => 213F
		return self::cInvert(self::cSwap16(pack('s', $val)));
	}

	public static function endian_v2c_unsigned_be($val,$lowWFirst=null) {
		return pack('n', $val);
	}
	public static function endian_v2c_unsigned_le($val,$lowWFirst=null) {
		return pack('v', $val);
	}
	public static function endian_v2c_unsigned_mb($val,$lowWFirst=null) {
		return pack('S', $val);
	}
	public static function endian_v2c_unsigned_md($val,$lowWFirst=null) {
		return pack('S', $val);
	}
	public static function endian_v2c_unsigned_bi($val,$lowWFirst=null) {
		return self::cInvert(pack('n', $val));
	}
	public static function endian_v2c_unsigned_li($val,$lowWFirst=null) {
		return self::cSwap16(self::cInvert(pack('v', $val)));
	}
}
