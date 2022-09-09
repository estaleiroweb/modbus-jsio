<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt8 extends MbTypeInt {
	protected function init() {
		parent::init();
		$this->readonly['bytes'] = 8;
		return $this;
	}
	public function setBytes($val) {
		return $this;
	}
	
	public static function endian_c2v_signed_be($cbin,$lowWFirst=null) {
		return unpack('q', $cbin)[1];
	}
	public static function endian_c2v_signed_le($cbin,$lowWFirst=null) {
		return unpack('q', self::cSwap64($cbin))[1];
	}
	public static function endian_c2v_signed_mb($cbin,$lowWFirst=null) {
		return unpack('q', $cbin)[1];
	}
	public static function endian_c2v_signed_md($cbin,$lowWFirst=null) {
		return unpack('q', $cbin)[1];
	}
	public static function endian_c2v_signed_bi($cbin,$lowWFirst=null) {
		return unpack('q', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_signed_li($cbin,$lowWFirst=null) {
		return unpack('q', self::cSwap64(self::cInvert($cbin)))[1];
	}

	public static function endian_c2v_unsigned_be($cbin,$lowWFirst=null) {
		return unpack('J', $cbin)[1];
	}
	public static function endian_c2v_unsigned_le($cbin,$lowWFirst=null) {
		return unpack('P', $cbin)[1];
	}
	public static function endian_c2v_unsigned_mb($cbin,$lowWFirst=null) {
		return unpack('Q', $cbin)[1];
	}
	public static function endian_c2v_unsigned_md($cbin,$lowWFirst=null) {
		return unpack('Q', $cbin)[1];
	}
	public static function endian_c2v_unsigned_bi($cbin,$lowWFirst=null) {
		return unpack('J', self::cInvert($cbin))[1];
	}
	public static function endian_c2v_unsigned_li($cbin,$lowWFirst=null) {
		return unpack('P', self::cInvert($cbin))[1];
	}

	public static function endian_v2c_signed_be($val,$lowWFirst=null) {
		return pack('q', $val);
	}
	public static function endian_v2c_signed_le($val,$lowWFirst=null) {
		return self::cSwap64(pack('q', $val));
	}
	public static function endian_v2c_signed_mb($val,$lowWFirst=null) {
		return pack('q', $val);
	}
	public static function endian_v2c_signed_md($val,$lowWFirst=null) {
		return pack('q', $val);
	}
	public static function endian_v2c_signed_bi($val,$lowWFirst=null) {
		return self::cInvert(pack('q', $val));
	}
	public static function endian_v2c_signed_li($val,$lowWFirst=null) {
		return self::cInvert(self::cSwap64(pack('q', $val)));
	}

	public static function endian_v2c_unsigned_be($val,$lowWFirst=null) {
		return pack('J', $val);
	}
	public static function endian_v2c_unsigned_le($val,$lowWFirst=null) {
		return pack('P', $val);
	}
	public static function endian_v2c_unsigned_mb($val,$lowWFirst=null) {
		return pack('Q', $val);
	}
	public static function endian_v2c_unsigned_md($val,$lowWFirst=null) {
		return pack('Q', $val);
	}
	public static function endian_v2c_unsigned_bi($val,$lowWFirst=null) {
		return self::cInvert(pack('J', $val));
	}
	public static function endian_v2c_unsigned_li($val,$lowWFirst=null) {
		return self::cInvert(pack('P', $val));
	}
}
