<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt4 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 4;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}

	public static function endian_c2v_signed_be($cbin,$lowWFirst=null) {
		return unpack('l', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin,$lowWFirst=null) {
		return unpack('l', self::cSwap16($cbin))[1];
	}
	public static function endian_c2v_signed_mb($cbin,$lowWFirst=null) {
		return unpack('l', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin,$lowWFirst=null) {
		return unpack('i', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin,$lowWFirst=null) {
		return unpack('l', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin,$lowWFirst=null) {
		return unpack('l', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_c2v_unsigned_be($cbin,$lowWFirst=null) {
		return unpack('N', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin,$lowWFirst=null) {
		return unpack('V', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin,$lowWFirst=null) {
		return unpack('L', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin,$lowWFirst=null) {
		return unpack('I', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin,$lowWFirst=null) {
		return unpack('V', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin,$lowWFirst=null) {
		return unpack('N', self::cSwap16(self::cInvert($cbin)))[1];
	}

	public static function endian_v2c_signed_be($val,$lowWFirst=null) {
		return pack('l', $val);
	}
	public static function endian_v2c_signed_le($val,$lowWFirst=null) {
		return self::cSwap16(pack('l', $val));
	}
	public static function endian_v2c_signed_mb($val,$lowWFirst=null) {
		return pack('l', $val);
	}
	public static function endian_v2c_signed_md($val,$lowWFirst=null) {
		return pack('i', $val);
	}
	public static function endian_v2c_signed_bi($val,$lowWFirst=null) {
		return self::cInvert(pack('l', $val));
	}
	public static function endian_v2c_signed_li($val,$lowWFirst=null) {
		return self::cInvert(self::cSwap16(pack('l', $val)));
	}

	public static function endian_v2c_unsigned_be($val,$lowWFirst=null) {
		return pack('N', $val);
	}
	public static function endian_v2c_unsigned_le($val,$lowWFirst=null) {
		return pack('V', $val);
	}
	public static function endian_v2c_unsigned_mb($val,$lowWFirst=null) {
		return pack('L', $val);
	}
	public static function endian_v2c_unsigned_md($val,$lowWFirst=null) {
		return pack('I', $val);
	}
	public static function endian_v2c_unsigned_bi($val,$lowWFirst=null) {
		return self::cInvert(pack('V', $val));
	}
	public static function endian_v2c_unsigned_li($val,$lowWFirst=null) {
		return self::cInvert(self::cSwap16(pack('N', $val)));
	}
}
