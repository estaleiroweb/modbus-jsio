#!/usr/bin/php
<?php
/*
	/home/helbert/code/modbus-jsio/test/main.php

	https://pt.linuxcapable.com/install-php-8-1-on-linux-mint-20/
	https://packagist.org/packages/aldas/modbus-tcp-client
	https://github.com/aldas/modbus-tcp-client



	Welcome to ScadaBR installer for Linux!

	64-bit machine detected
	Files present! Let's go to install!

	=== Tomcat configuration ===
	Define Tomcat port (default: 8080): 
	Define a username for tomcat-manager (default: tomcat): 
	Define a password for created user: 
	============================

	Tomcat port will be set to: 8080

	The following user will be created to access tomcat-manager:
	Username: "tomcat"
	Password: "!tc27896"

	Type n to change data or press ENTER to continue.


*/

namespace EstaleiroWeb\Modbus\Types;

use EstaleiroWeb\Modbus\Modbus;
use EstaleiroWeb\Traits\Args;
use EstaleiroWeb\Traits\GetSet;

require __DIR__ . '/../vendor/autoload.php';

cli();
define('MAX_VALUE_UINT16', 0xFFFF); // 65535 as dec
define('MIN_VALUE_UINT16', 0x0);
define('MAX_VALUE_INT16', 0x7FFF); // 32767 as dec
define('MIN_VALUE_INT16', -32768); // 0x8000 as hex
define('MAX_VALUE_UINT32', 0xFFFFFFFF); // 4294967295 as dec
define('MIN_VALUE_UINT32', 0x0); // 0 as dec
define('MAX_VALUE_INT32', 0x7FFFFFFF); // 2147483647 as dec
define('MIN_VALUE_INT32', -2147483648); // 0x80000000 as hex
define('MAX_VALUE_BYTE', 0xFF);
define('MIN_VALUE_BYTE', 0x0);
define('BIG_ENDIAN', 1);
define('LITTLE_ENDIAN', 2);
/**
 * Double words (32bit types) consist of two 16bit words. Different PLCs send double words differently over wire
 * So 0xDCBA can be sent low word (0xBA) first 0xBADC or high word (0xDC) first 0xDCBA. High word first on true big/little endian
 * and does not have separate flag
 */
define('LOW_WORD_FIRST', 4);
/**
 * Used by WAGO 750-XXX as endianness.
 *
 * When bytes for little endian are in 'ABCD' order then Big Endian Low Word First is in 'BADC' order
 * This mean that high word (BA) is first and low word (DC) for double word is last and bytes in words are in big endian order.
 */
define('BIG_ENDIAN_LOW_WORD_FIRST', BIG_ENDIAN | LOW_WORD_FIRST);

vwType();
$val = "\x12\xF3"; //\x00\x12";
$val = "\xF3\x12";
$H = strtoupper(unpack("H*Hex", $val)['Hex']);
$h = strtoupper(unpack("h*Hex", $val)['Hex']);
$v = substr($H, 2, 2) . substr($H, 0, 2);
print_r([
	'Hex' => $H,
	'be-u-n' => unpack('n', $val)[1], //12F0
	'le-u-v' => unpack('v', $val)[1], //F012
	'mb-u-S' => unpack('S', $val)[1], //v
	'mb-s-s' => unpack('s', $val)[1],
	'be-u-n-' . $H => hexdec($H),
	'le-u-v-' . $v => hexdec($v),
	'le-s-v-' . $v => unpack('s', pack('H*', $v))[1],
	'__-_-_-' . $h => hexdec($h),
	'__-_-_-' . ($k = substr($h, 2, 2) . substr($h, 0, 2)) => hexdec($k),
]);
//'endian' => [null, null, 's', 'i'],
//'endian' => ['n', 'v', 'S', 'I'],
$val = "\x01\x23\x45\x67\x89\xAB\xCD\xEF";
print strtoupper(unpack('H*', $val)[1]) . "\n";

//$format1 = $format2 = 'C';

$bytes = 1;
$val = str_split($val, 3);
$val = array_map(function ($v) use ($bytes) {
	return substr($v, 0, $bytes) . substr($v, $bytes, $bytes);
	return substr($v, $bytes, $bytes) . substr($v, 0, $bytes);
}, $val);
//print_r($val);
$val = implode('', $val);
print strtoupper(unpack('H*', $val)[1]) . "\n";
$formats = [
	'c' => 1,
	'C' => 1,

	'n' => 2,
	'v' => 2,
	's' => 2,
	'S' => 2,

	'N' => 4,
	'V' => 4,
	'l' => 4,
	'L' => 4,
	'i' => 4,
	'I' => 4,

	'J' => 8,
	'P' => 8,
	'q' => 8,
	'Q' => 8,
];
$val = "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF";
$val = "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF";
$val = "\x01\x23\x45\x67\x89\xAB\xCD\x8F";
//$ret = unpack('cs0/C*s', $val);
$ret = [];

foreach ($formats as $f => $bytes) {
	$v = substr($val, -1 * $bytes);
	$h = strtoupper(unpack('H*', $v)[1]);
	$d = unpack($f, $v)[1];
	$len = $bytes * -2;
	$hTo = strtoupper(str_pad(dechex($d), 16, 0, STR_PAD_LEFT));
	$dTo = (int)hexdec($hTo);
	$hTo = substr($hTo, $len);
	$ret[$f . '_' . $bytes] = "$h => $hTo ($d => $dTo)";
}
print_r($ret);
$rVal = substr($val, -4);
$h = unpack('H*', $rVal)[1];
/*
$h = unpack('C*', $rVal);
$h = array_map(function ($v) {
	return str_pad(strtoupper(dechex($v)), 2, 0, STR_PAD_LEFT) . ":$v";
}, $h);
*/



function signed2hex($value, $reverseEndianness = true) {
	$packed = pack('s', $value);
	$hex = '';
	for ($i = 0; $i < 2; $i++) {
		$hex .= strtoupper(str_pad(dechex(ord($packed[$i])), 2, '0', STR_PAD_LEFT));
	}
	$tmp = str_split($hex, 2);
	$out = implode('', ($reverseEndianness ? array_reverse($tmp) : $tmp));
	return $out;
}

//print PHP_INT_SIZE . "\n";
/*print_r([
	'format'=>$f,
	'ret'=>$ret,
	'dec'=>$d,
	'hex'=>$h,
	'hRet'=>strtoupper(str_pad(dechex($d), 8, 0, STR_PAD_LEFT)),
	'dec2'=>hexdec($h),
]);
print_r([
	$f=>$h.'=>'.strtoupper(str_pad(dechex($d), 8, 0, STR_PAD_LEFT)),
	//'ret'=>$ret,
	'dec'=>$d,
	'dec2'=>hexdec($h),
]);*/

function testArgs() {
	/**
	 * initArgs
	 * Initialize arguments of the object with backtrace of the back method like PHP8
	 * 	- function method(){$this->initArgs(['arg1']);}
	 * 	- $this->method(1,2,3);
	 * 	- $this->method(['arg1'=>1,'arg2'=>2','arg3'=>3]);
	 * 
	 * @param  mixed $args List of arguments of the object
	 * @return object self object
	 */
	class tst {
		use Args;
		function __construct() {
			$arr = ['arg1' => false, 'arg2' => null, 'arg3' => FILTER_VALIDATE_INT,];
			//$arr = ['arg1', 'arg2'];
			$this->initArgs($arr);
		}
	}
	$b = new tst(1, 2, 'a');
	print_r($b);
}
function typeList() {
	$ranges = [
		'bit' => [
			'bits' =>   1,
			'bytes' =>   1,
			'signed' =>   [0, -1], // 0x00~0x71
			'unsigned' => [0, 1],  // 0x00~0x01
		],
		'byte' => [
			'bits' =>   8,
			'bytes' =>   1,
			'signed' =>   [-128, 127], // 0x80~0x7F
			'unsigned' => [0, 255],    // 0x00~0xFF
		],
		'int16' => [
			'bits' =>   16,
			'bytes' =>   2,
			'signed' => [-32768, 32767], // 0x8000~0x7FFF
			'unsigned' => [0, 65535],   // 0x0000~0xFFFF
		],
		'int24' => [
			'bits' =>   24,
			'bytes' =>   3,
			'signed' => [-8388608, 8388607], // 0x800000~0x7FFFFF
			'unsigned' => [0, 16777215],     // 0x000000~0xFFFFFF
		],
		'int32' => [
			'bits' =>   32,
			'bytes' =>   4,
			'signed' =>   [-2147483648, 2147483647], // 0x80000000~0x7FFFFFFF
			'unsigned' => [0, 4294967295],           // 0x00000000~0xFFFFFFFF
		],
		'int64' => [
			'bits' =>   64,
			'bytes' =>   8,
			'signed' =>   [-9223372036854775808, 9223372036854775807], // 0x8000000000000000~0x7FFFFFFFFFFFFFFF
			'unsigned' => [0, 18446744073709551615],                   // 0x0000000000000000~0xFFFFFFFFFFFFFFFF
		],
		'float' => [
			'bits' =>   32,
			'bytes' =>   4,
			'signed' =>   [-3.402823466E+38, -1.175494351E-38],
			'unsigned' => [1.175494351E-38, 3.402823466E+38],
		],
		'double' => [
			'bits' =>   64,
			'bytes' =>   8,
			'signed' =>   [-1.7976931348623157E+308, -2.2250738585072014E-308],
			'unsigned' => [2.2250738585072014E-308, 1.7976931348623157E+308],
		],
		'timestamp' => [
			'bits' =>   16,
			'bytes' =>   4,
			'unsigned' => ['1970-01-01 00:00:01.000000 UTC', '2038-01-19 03:14:07.999999 UTC'],
		],
		'datetime' => [
			'bits' =>   32,
			'bytes' =>   4,
			'unsigned' => ['1000-01-01 00:00:00.000000', '9999-12-31 23:59:59.999999'],
		],
		'date' => [
			'bits' =>   16,
			'bytes' =>   4,
			'unsigned' => ['1000-01-01', '9999-12-31'],
		],
		'time' => [
			'bits' =>   16,
			'bytes' =>   4,
			'unsigned' => ['-838:59:59.000000', '838:59:59.999999'],
		],
		'year4' => [
			'bits' =>   8,
			'bytes' =>   1,
			'unsigned' => [1901, 2155],
		],
	];
	$types = [
		/*
		'a' => 'NUL-padded string',
		'Z'=>'NUL-padded string',
		'A' => 'SPACE-padded string',
		'x'=>'NUL byte',
		'X'=>'Back up one byte',
		'@'=>'NUL-fill to absolute position',

		'H'=>'Hex string, high nibble first',
		'h'=>'Hex string, low nibble first',

		'c' => 'signed char',
		'C' => 'unsigned char',
		's'=>'signed short (always 16 bit, machine byte order)',
		'S' => 'unsigned short (always 16 bit, machine byte order)',
		'n' => 'unsigned short (always 16 bit, big endian byte order)',
		'v' => 'unsigned short (always 16 bit, little endian byte order)',
		'I' => 'unsigned integer (machine dependent size and byte order)',
		'i'=>'signed integer (machine dependent size and byte order)',
		'l' => 'signed long (always 32 bit, machine byte order)',
		'L'=>'unsigned long (always 32 bit, machine byte order)',
		*/
		/*

		'N'=>'unsigned long (always 32 bit, big endian byte order)',
		'V'=>'unsigned long (always 32 bit, little endian byte order)',
		'q'=>'signed long long (always 64 bit, machine byte order)',
		'Q'=>'unsigned long long (always 64 bit, machine byte order)',
		'J'=>'unsigned long long (always 64 bit, big endian byte order)',
		'P'=>'unsigned long long (always 64 bit, little endian byte order)',
		'f'=>'float (machine dependent size and representation)',
		'g'=>'float (machine dependent size, little endian byte order)',
		'G'=>'float (machine dependent size, big endian byte order)',
		'd'=>'double (machine dependent size and representation)',
		'e'=>'double (machine dependent size, little endian byte order)',
		'E'=>'double (machine dependent size, big endian byte order)',
	*/];

	$arr = [
		//"\x80\x00\x00\x00\x00\x00\x00\x00", "\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
		//"\x00\x00\x00\x00\x00\x00\x00\x00", "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
		//"\x20\x20\x48\x20\x20\x00","\x00\x00\x00\x48\x00\x00\x00",
		//"\x12\x34\x56\x78\x9A\xBC",
		"\x89\xAB\xCD\xEF",
	];

	foreach ($types as $type => $descr) {
		print "#### [$type] $descr\n";
		print "---------------------------------------------\n";
		showArr($arr, $type);
	}
}
function showArr($arr, $type) {
	foreach ($arr as $v) {
		//$h = dechex($v);
		//$b = decbin($v);
		$js = json_encode(array_merge(
			unpack("H*Hex", $v),
			['quant' => strlen($v),],
			//unpack("{$k}2Dual", $v),
			//unpack("{$k}4Four", $v),
			unpack("{$type}*All", $v)
		), JSON_PRETTY_PRINT);
		print " Val: $js\n";
	}
}
function vwType($fn = 'getBytesForInt32Parse', $v = "\x01\x23\x45\x67\x89\xAB\xCD\xEF") {
	print json_encode([
		'Hex' => implode(' ', str_split(strtoupper(unpack("H*Hex", $v)['Hex']), 2)),
		'Qt' => strlen($v),
		'fn' => $fn,
		'be_hi_1' => $fn($v, 1),
		'le_hi_2' => $fn($v, 2),
		'be_lo_5' => $fn($v, 5),
		'le_lo_6' => $fn($v, 6),
	], JSON_PRETTY_PRINT) . "\n";
}
function getInt16Format(int $fromEndian = null): string {
	if ($fromEndian & BIG_ENDIAN) return 'n'; // unsigned short (always 16 bit, big endian byte order)
	if ($fromEndian & LITTLE_ENDIAN) return 'v'; // unsigned short (always 16 bit, little endian byte order)
	throw new \RuntimeException('Unsupported endianness given!');
}
/**
 * @param string $doubleWord
 * @param int|null $endianness
 * @return int[]
 */
function getBytesForInt32Parse(string $doubleWord, int $endianness = null) {
	$left = 'high';
	$right = 'low';
	if ($endianness & 4) { //LOW_WORD_FIRST
		$left = 'low';
		$right = 'high';
	}
	if ($endianness & 1) $format = 'n'; // BIG_ENDIAN
	elseif ($endianness & 2) $format = 'v'; //LITTLE_ENDIAN
	else throw new \RuntimeException('Unsupported endianness given!');
	$out = unpack("{$format}{$left}/{$format}{$right}", $doubleWord);

	foreach ($out as $k => $v) {
		$h = strtoupper(str_pad(dechex($v), 4, 0, STR_PAD_LEFT));
		$v = str_pad($v, 5, ' ', STR_PAD_LEFT);
		$out[$k] = "$k:0x$h|$v";
	}
	ksort($out);
	return implode(',', $out);
}
/**
 * Data types with Double Word (4 bytes) length can have different byte order when sent over wire depending of PLC vendor
 * For some data is sent in true big endian format, Big-Endian with Low Word first. This class is to provide flags
 * to switch needed byte order when parsing data.
 *
 * Background info: http://www.digi.com/wiki/developer/index.php/Modbus_Floating_Points (about floats but 32bit int is also double word)
 *
 *
 * Example:
 * 32bit (4 byte) integer 67305985 is in hex 0x01020304 (little endian), most significant byte is 01 and the
 * lowest byte contain hex value 04.
 * Source: http://unixpapa.com/incnote/byteorder.html
 *
 * 32bit (dword) integer is in:
 *      Little Endian (ABCD) = 0x01020304  (0x04 + (0x03 << 8) + (0x02 << 16) + (0x01 << 24))
 *
 * May be sent over tcp/udp as:
 *      Big Endian (DCBA) = 0x04030201
 *      Big Endian Low Word First (BADC) = 0x02010403 <-- used by WAGO 750-XXX to send modbus packets over tcp/udp
 *
 */
function getCurrentEndianness(int $endianness = null, $defaultEndian = null) {
	return $endianness === null ? $defaultEndian : $endianness;
}
/**
 * Parse binary string (1 word) with given endianness byte order to 16bit unsigned integer (2 bytes to uint16)
 *
 * @param string $word binary string to be converted to unsigned 16 bit integer
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return int
 */
function parseUInt16(string $word, int $fromEndian = null): int {
	return unpack(getInt16Format($fromEndian), $word)[1];
}
/**
 * Parse binary string (1 word) with given endianness byte order to 16bit signed integer (2 bytes to int16)
 *
 * @param string $word binary string to be converted to signed 16 bit integer
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return int
 */
function parseInt16(string $word, int $fromEndian = null): int {
	$fromEndian = getCurrentEndianness($fromEndian);
	if ($fromEndian & BIG_ENDIAN) {
		$format = 'chigh/Clow';
	} elseif ($fromEndian & LITTLE_ENDIAN) {
		$format = 'Clow/chigh';
	} else {
		throw new \RuntimeException('Unsupported endianness given!');
	}
	$byteArray = unpack($format, $word);
	return ($byteArray['high'] << 8) + $byteArray['low'];
}
/**
 * Parse binary string (double word) with big endian byte order to 32bit unsigned integer (4 bytes to uint32)
 *
 * NB: On 32bit php and having highest bit set method will return float instead of int value. This is due 32bit php supports only 32bit signed integers
 *
 * @param string $doubleWord binary string to be converted to signed 16 bit integer
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return int|float
 */
function parseUInt32(string $doubleWord, int $fromEndian = null): int|float {
	$byteArray = getBytesForInt32Parse($doubleWord, $fromEndian);
	if (PHP_INT_SIZE === 4) {
		//can not bit shift safely (for unsigneds) already 16bit value by 16 bits on 32bit arch so shift 15 and multiply by 2
		$byteArray['high'] = ($byteArray['high'] << 15) * 2;
	} else {
		$byteArray['high'] <<= 16;
	}
	return $byteArray['high'] + $byteArray['low'];
}
/**
 * Parse binary string (double word) with big endian byte order to 32bit signed integer (4 bytes to int32)
 *
 * @param string $doubleWord binary string to be converted to signed 16 bit integer
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return int
 */
function parseInt32(string $doubleWord, int $fromEndian = null): int {
	$byteArray = getBytesForInt32Parse($doubleWord, $fromEndian);
	$byteArray['high'] = uintToSignedInt($byteArray['high']);
	return ($byteArray['high'] << 16) + $byteArray['low'];
}
/**
 * Convert 2/4/8 byte into a signed integer. This is needed to make code 32bit php and 64bit compatible as Pack function
 * does not have options to convert big endian signed integers
 * taken from http://stackoverflow.com/q/13322327/2514290
 * @param int $uint
 * @param int $bitSize
 * @return int
 */
function uintToSignedInt(int $uint, int $bitSize = 16): int {
	if ($bitSize === 16 && ($uint & 0x8000) > 0) {
		// This is a negative number.  Invert the bits and add 1 and add negative sign
		$uint = - ((~$uint & 0xFFFF) + 1);
	} elseif ($bitSize === 32 && ($uint & 0x80000000) > 0) {
		// This is a negative number.  Invert the bits and add 1 and add negative sign
		$uint = - ((~$uint & 0xFFFFFFFF) + 1);
	} elseif ($bitSize === 64 && ($uint & 0x8000000000000000) > 0) {
		// This is a negative number.  Invert the bits and add 1 and add negative sign
		$uint = - ((~$uint & 0xFFFFFFFFFFFFFFFF) + 1);
	}
	return $uint;
}
/**
 * Parse binary string (1 char) to 8bit unsigned integer (1 bytes to uint8)
 *
 * @param string $char binary string to be converted to unsigned 8 bit unsigned integer
 * @return int
 */
function parseByte(string $char): int {
	return unpack('C', $char)[1];
}
/**
 * Parse binary string to array of unsigned integers (uint8)
 *
 * @param string $binaryData binary string to be converted to array of unsigned 8 bit unsigned integers
 * @return int[]
 */
function parseByteArray(string $binaryData): array {
	return array_values(unpack('C*', $binaryData));
}
/**
 * Convert array of PHP data to array of bytes. Each element of $data is converted to 1 byte (unsigned int8)
 *
 * @param int[] $data
 * @return string
 */
function byteArrayToByte(array $data): string {
	return pack('C*', ...$data);
}
/**
 * Converts array of booleans values to array of bytes (integers)
 *
 * @param bool[] $booleans
 * @return int[]
 */
function booleanArrayToByteArray(array $booleans): array {
	$result = [];
	$count = count($booleans);

	$currentByte = 0;
	for ($index = 0; $index < $count; $index++) {
		$bit = $index % 8;
		if ($index !== 0 && $bit === 0) {
			$result[] = $currentByte;
			$currentByte = 0;
		}

		$current = $booleans[$index];
		if ($current) {
			$currentByte |= 1 << $bit;
		}
	}
	$result[] = $currentByte;

	return $result;
}
/**
 * @param string $binary
 * @return bool[]
 */
function binaryStringToBooleanArray(string $binary): array {
	$result = [];
	$coilCount = 8 * strlen($binary);
	$byteAsInt = 0;
	for ($index = 0; $index < $coilCount; $index++) {
		$bit = $index % 8;
		if ($bit === 0) {
			$byteAsInt = ord($binary[(int)($index / 8)]);
		}
		$result[] = (($byteAsInt & (1 << $bit)) >> $bit) === 1;
	}
	return $result; //TODO refactor to generator?
}
/**
 * Check if N-th bit is set in data. NB: Bits are counted from 0 and right to left.
 *
 * @param int|string $data
 * @param int $bit to be checked
 * @return bool
 */
function isBitSet(int|string $data, int $bit): bool {
	if (is_string($data)) {
		$nthByte = (int)($bit / 8);
		$bit %= 8;
		$offset = (strlen($data) - 1) - $nthByte;
		$data = ord($data[$offset]);
	} elseif (is_int($data)) {
		/**
		 * From: http://php.net/manual/en/language.operators.bitwise.php
		 * Warning: Shifting integers by values greater than or equal to the system long integer width results
		 * in undefined behavior. In other words, don't shift more than 31 bits on a 32-bit system,
		 * and don't shift more than 63 bits on a 64-bit system.
		 */
		if (PHP_INT_SIZE === 4 && $bit > 31) {
			throw new \RuntimeException('On 32bit PHP bit shifting more than 31 bit is not possible as int size is 32 bytes');
		}

		if (PHP_INT_SIZE === 8 && $bit > 63) {
			throw new \RuntimeException('On 64bit PHP bit shifting more than 63 bit is not possible as int size is 64 bytes');
		}
	}

	return 1 === (($data >> $bit) & 1);
}
/**
 * Parse binary string representing real (32bits) in given endianness to float (double word/4 bytes to float)
 *
 * @param string $binaryData binary byte string to be parsed to float
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return float
 */
function parseFloat(string $binaryData, int $fromEndian = null): float {
	$fromEndian = getCurrentEndianness($fromEndian);
	if ($fromEndian & LOW_WORD_FIRST) {
		$binaryData = substr($binaryData, 2, 2) . substr($binaryData, 0, 2);
	}

	if ($fromEndian & BIG_ENDIAN) {
		$format = 'N';
	} elseif ($fromEndian & LITTLE_ENDIAN) {
		$format = 'V';
	} else {
		throw new \RuntimeException('Unsupported endianness given!');
	}
	// reverse words if needed
	// parse as uint32 to binary big/little endian,
	// pack to machine order int 32,
	// unpack to machine order float
	$pack = unpack($format, $binaryData)[1];
	return unpack('f', pack('L', $pack))[1];
}
/**
 * Parse binary string representing double (64bits) in given endianness to float (quad word/8 bytes to float)
 *
 * @param string $binaryData binary byte string to be parsed to float
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return float
 */
function parseDouble(string $binaryData, int $fromEndian = null): float {
	if (PHP_INT_SIZE !== 8) {
		throw new \RuntimeException('64-bit format codes are not available for 32-bit versions of PHP');
	}
	if (strlen($binaryData) !== 8) {
		throw new \RuntimeException('binaryData must be 8 bytes in length');
	}

	$fromEndian = getCurrentEndianness($fromEndian);
	if ($fromEndian & LOW_WORD_FIRST) {
		$binaryData = ($binaryData[6] . $binaryData[7]) .
			($binaryData[4] . $binaryData[5]) .
			($binaryData[2] . $binaryData[3]) .
			($binaryData[0] . $binaryData[1]);
	}

	if ($fromEndian & BIG_ENDIAN) {
		return unpack('E', $binaryData)[1];
	}
	return unpack('e', $binaryData)[1];
}
/**
 * Parse binary string representing 64 bit unsigned integer to 64bit unsigned integer in given endianness (quad word/8 bytes to 64bit int)
 *
 * @param string $binaryData binary string representing 64 bit unsigned integer in big endian order
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return int
 */
function parseUInt64(string $binaryData, int $fromEndian = null): int {
	if (strlen($binaryData) !== 8) {
		throw new \RuntimeException('binaryData must be 8 bytes in length');
	}
	if (PHP_INT_SIZE !== 8) {
		throw new \RuntimeException('64-bit format codes are not available for 32-bit versions of PHP');
	}

	$fromEndian = getCurrentEndianness($fromEndian);
	if ($fromEndian & LOW_WORD_FIRST) {
		$binaryData = ($binaryData[6] . $binaryData[7]) .
			($binaryData[4] . $binaryData[5]) .
			($binaryData[2] . $binaryData[3]) .
			($binaryData[0] . $binaryData[1]);
	}

	if ($fromEndian & BIG_ENDIAN) {
		$format = 'J';
	} elseif ($fromEndian & LITTLE_ENDIAN) {
		$format = 'P';
	} else {
		throw new \RuntimeException('Unsupported endianness given!');
	}

	$result = unpack($format, $binaryData)[1];

	if ($result < 0) {
		$value = unpack('H*', $binaryData)[1];
		throw new \RuntimeException('64-bit PHP supports only up to 63-bit signed integers. Current input has 64th bit set and overflows. Hex: ' . $value);
	}
	return $result;
}
/**
 * Parse binary string representing 64 bit signed integer to 64bit signed integer in given endianness  (quad word/8 bytes to 64bit int)
 *
 * @param string $binaryData binary string representing 64 bit signed integer in big endian order
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return int
 */
function parseInt64(string $binaryData, int $fromEndian = null): int {
	if (strlen($binaryData) !== 8) {
		throw new \RuntimeException('binaryData must be 8 bytes in length');
	}
	if (PHP_INT_SIZE !== 8) {
		throw new \RuntimeException('64-bit format codes are not available for 32-bit versions of PHP');
	}

	$fromEndian = getCurrentEndianness($fromEndian);
	if ($fromEndian & LOW_WORD_FIRST) {
		$binaryData = ($binaryData[6] . $binaryData[7]) .
			($binaryData[4] . $binaryData[5]) .
			($binaryData[2] . $binaryData[3]) .
			($binaryData[0] . $binaryData[1]);
	}

	if ($fromEndian & BIG_ENDIAN) {
		$format = 'J';
	} elseif ($fromEndian & LITTLE_ENDIAN) {
		$format = 'P';
	} else {
		throw new \RuntimeException('Unsupported endianness given!');
	}
	return uintToSignedInt(unpack($format, $binaryData)[1], 64);
}
/**
 * Parse ascii string from registers to utf-8 string. Supports extended ascii codes ala 'ø' (decimal 248)
 *
 * @param string $binaryData binary string representing register (words) contents
 * @param int $length number of characters to parse from data
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return string
 */
function parseAsciiStringFromRegister(string $binaryData, int $length = 0, int $fromEndian = null) {
	//return parseStringFromRegister($binaryData, $length, Charset::$defaultCharset, $fromEndian);
}
/**
 * Parse string from registers to utf-8 string.
 *
 * @param string $binaryData binary string representing register (words) contents
 * @param int $length number of characters to parse from data
 * @param string|null $fromEncoding
 * @param int|null $fromEndian byte and word order for modbus binary data
 * @return string
 */
function parseStringFromRegister(string $binaryData, int $length, string $fromEncoding = null, int $fromEndian = null): string {
	$data = $binaryData;

	$fromEndian = getCurrentEndianness($fromEndian);
	if ($fromEndian & BIG_ENDIAN) {

		$data = '';
		// big endian needs bytes in word reversed
		foreach (str_split($binaryData, 2) as $word) {
			if (isset($word[1])) {
				$data .= $word[1] . $word[0]; // low byte + high byte
			} else {
				$data .= $word[0]; // assume that last single byte is in correct place
			}
		}
	}

	$rawLen = strlen($data);
	if (!$length || $length > $rawLen) {
		$length = strlen($data);
	}

	$result = unpack("Z{$length}", $data)[1];

	if ($fromEncoding !== null) {
		$result = mb_convert_encoding($result, 'UTF-8', $fromEncoding);
	}

	return $result;
}
/**
 * Convert Php integer to modbus register (2 bytes of data) in big endian byte order
 *
 * @param int $data integer to be converted to register/word (binary string of 2 bytes)
 * @return string binary string with big endian byte order
 */
function toRegister(int $data): string {
	$data &= 0xFFFF;
	return pack('n', $data);
}
/**
 * Convert Php data as it would be 1 byte to binary string (1 char)
 *
 * @param int $data 1 bit integer to be converted to binary byte string
 * @return string binary string with length of 1 char
 */
function toByte(int $data): string {
	return pack('C', $data);
}
/**
 * Convert Php data as it would be 16 bit integer to binary string with big endian byte order
 *
 * @param int $data 16 bit integer to be converted to binary string (1 word)
 * @param int|null $toEndian byte and word order for modbus binary data
 * @param bool $doRangeCheck should min/max range check be done for data
 * @return string binary string with big endian byte order
 */
function toInt16(int $data, int $toEndian = null, bool $doRangeCheck = true): string {
	if ($doRangeCheck && ($data < MIN_VALUE_INT16 || $data > MAX_VALUE_INT16)) {
		throw new \RuntimeException('Data out of int16 range (-32768...32767)! Given: ' . $data);
	}

	return pack(getInt16Format($toEndian), $data);
}
/**
 * Convert Php data as it would be unsigned 16 bit integer to binary string in given endianess
 *
 * @param int $data 16 bit integer to be converted to binary string (1 word)
 * @param int|null $toEndian byte and word order for modbus binary data
 * @param bool $doRangeCheck should min/max range check be done for data
 * @return string binary string with big endian byte order
 */
function toUint16(int $data, int $toEndian = null, bool $doRangeCheck = true): string {
	if ($doRangeCheck && ($data < MIN_VALUE_UINT16 || $data > MAX_VALUE_UINT16)) {
		throw new \RuntimeException('Data out of uint16 range (0...65535)! Given: ' . $data);
	}

	return pack(getInt16Format($toEndian), $data);
}
/**
 * Convert Php data as it would be 32 bit integer to binary string with given endianness order
 *
 * @param int $data 32 bit integer to be converted to binary string (double word)
 * @param int|null $toEndian byte and word order for modbus binary data
 * @param bool $doRangeCheck should min/max range check be done for data
 * @return string binary string with big endian byte order
 */
function toInt32(int $data, int $toEndian = null, bool $doRangeCheck = true): string {
	if ($doRangeCheck && ($data < MIN_VALUE_INT32 || $data > MAX_VALUE_INT32)) {
		throw new \RuntimeException('Data out of int32 range (-2147483648...2147483647)! Given: ' . $data);
	}
	return toInt32Internal($data, $toEndian);
}
/**
 * Convert Php data as it would be unsigned 32 bit integer to binary string with given endianness order
 *
 * @param int $data 32 bit unsigned integer to be converted to binary string (double word)
 * @param int|null $toEndian byte and word order for modbus binary data
 * @param bool $doRangeCheck should min/max range check be done for data
 * @return string binary string with big endian byte order
 */
function toUint32(int $data, int $toEndian = null, bool $doRangeCheck = true): string {
	if ($doRangeCheck && ($data < MIN_VALUE_UINT32 || $data > MAX_VALUE_UINT32)) {
		throw new \RuntimeException('Data out of int32 range (0...4294967295)! Given: ' . $data);
	}
	return toInt32Internal($data, $toEndian);
}
/**
 * @param int $data
 * @param int|null $endianness
 * @return string
 */
function toInt32Internal(int $data, int $endianness = null): string {
	$words = [
		($data >> 16) & 0xFFFF,
		$data & 0xFFFF
	];

	$endianness = getCurrentEndianness($endianness);
	if ($endianness & LOW_WORD_FIRST) {
		$words = [$words[1], $words[0]];
	}

	$format = getInt16Format($endianness);
	return pack("{$format}*", ...$words);
}
/**
 * Convert Php data as it would be 64 bit integer to binary string with given endianness order
 *
 * @param int $data 64 bit integer to be converted to binary string (quad word)
 * @param int|null $toEndian byte and word order for modbus binary data
 * @return string binary string with big endian byte order
 */
function toInt64(int $data, int $toEndian = null): string {
	$words = [
		($data >> 48) & 0xFFFF,
		($data >> 32) & 0xFFFF,
		($data >> 16) & 0xFFFF,
		$data & 0xFFFF
	];

	$toEndian = getCurrentEndianness($toEndian);
	if ($toEndian & LOW_WORD_FIRST) {
		$words = [$words[3], $words[2], $words[1], $words[0]];
	}

	$format = getInt16Format($toEndian);
	return pack("{$format}*", ...$words);
}
/**
 * Convert Php data as it would be 64 bit unsigned integer to binary string with given endianness order
 *
 * @param int $data 64 bit integer to be converted to binary string (quad word)
 * @param int|null $toEndian byte and word order for modbus binary data
 * @param bool $doRangeCheck
 * @return string binary string with big endian byte order
 */
function toUint64(int $data, int $toEndian = null, bool $doRangeCheck = true): string {
	if ($doRangeCheck && $data < 0) {
		throw new \RuntimeException('Data out of uint64 range (0...9223372036854775807)! Given: ' . $data);
	}
	// php has actually only signed integers so we can actually use 63bits of 64bit of unsigned int value
	return toInt64($data, $toEndian);
}
/**
 * Convert Php data as it would be float (32bit) to binary string with given endian order
 *
 * @param float $float float to be converted to binary byte string
 * @param int|null $toEndian byte and word order for modbus binary data
 * @return string binary string with big endian byte order
 */
function toReal(float $float, int $toEndian = null): string {
	$toEndian = getCurrentEndianness($toEndian);
	$format = 'G'; // double (machine dependent size, big endian byte order)
	if ($toEndian & LITTLE_ENDIAN) {
		$format = 'g'; // double (machine dependent size, little endian byte order)
	}
	$data = pack($format, $float);

	if ($toEndian & LOW_WORD_FIRST) {
		$data = ($data[2] . $data[3]) . ($data[0] . $data[1]);
	}
	return $data;
}
/**
 * Convert Php data as it would be double (64bit) to binary string with given endian order
 *
 * @param float $double float to be converted to binary byte string
 * @param int|null $toEndian byte and word order for modbus binary data
 * @return string binary string with big endian byte order
 */
function toDouble(float $double, int $toEndian = null): string {
	$toEndian = getCurrentEndianness($toEndian);

	$format = 'E'; // double (machine dependent size, big endian byte order)
	if ($toEndian & LITTLE_ENDIAN) {
		$format = 'e'; // double (machine dependent size, little endian byte order)
	}
	$data = pack($format, $double);

	if ($toEndian & LOW_WORD_FIRST) {
		$data = ($data[6] . $data[7]) .
			($data[4] . $data[5]) .
			($data[2] . $data[3]) .
			($data[0] . $data[1]);
	}

	return $data;
}
/**
 * Convert PHP string to binary string suitable for modbus packet
 *
 * @param string $string string to convert
 * @param int $registersCount number of registers to hold string bytes
 * @param string|null $toEncoding in which string encoding data is expected
 * @param int|null $toEndian in which endianess and word order resulting binary string should be
 * @return string
 */
function toString(string $string, int $registersCount, string $toEncoding = null, int $toEndian = null): string {
	if ($toEncoding !== null) {
		// use 'cp1252' as encoding if you just need extended ASCII chars i.e. chars like 'ø'
		$string = mb_convert_encoding($string, $toEncoding);
	}
	$byteCount = $registersCount * 2;

	$raw = '';
	if (!empty($string)) {
		$string = substr($string, 0, $byteCount - 1);
		$words = str_split($string, 2);

		$toEndian = getCurrentEndianness($toEndian);
		if ($toEndian & LOW_WORD_FIRST) {
			$words = array_reverse($words);
		}

		if ($toEndian & BIG_ENDIAN) {
			// big endian needs bytes in word reversed
			foreach ($words as &$word) {
				if (isset($word[1])) {
					$word = $word[1] . $word[0]; // low byte + high byte
				} else {
					$word = "\x00" . $word[0];
				}
			}
		}
		$raw = implode('', $words);
	}

	return pack("a{$byteCount}", $raw);
}
