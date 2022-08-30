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

use EstaleiroWeb\Modbus\Modbus;

if (php_sapi_name() !== 'cli') die('Should be used only in command line interface');

require __DIR__ . '/../vendor/autoload.php';

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
	*/
];

$arr = [
	//"\x80\x00\x00\x00\x00\x00\x00\x00", "\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
	//"\x00\x00\x00\x00\x00\x00\x00\x00", "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
	//"\x20\x20\x48\x20\x20\x00","\x00\x00\x00\x48\x00\x00\x00",
	//"\x12\x34\x56\x78\x9A\xBC",
	"\xFF\xFF\xFF\x0F",
];

foreach ($types as $k => $descr) {
	print "#### [$k] $descr\n";
	print "---------------------------------------------\n";
	foreach ($arr as $v) {
		//$h = dechex($v);
		//$b = decbin($v);
		$js = json_encode(array_merge(
			unpack("H*Hex", $v),
			['quant' => strlen($v),],
			//unpack("{$k}2Dual", $v),
			//unpack("{$k}4Four", $v),
			unpack("{$k}*All", $v)
		), JSON_PRETTY_PRINT);
		print " Val: $js\n";
	}
}
