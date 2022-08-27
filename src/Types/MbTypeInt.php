<?php

namespace EstaleiroWeb\Modbus\Types;

class MbTypeInt extends MbTypeAny {
	protected $trUnpack = [
		'descr' => [
			'c' => 'signed char',
			's' => 'signed short (always 16 bit, machine byte order)',
			'i' => 'signed integer (machine dependent size and byte order)',
			'l' => 'signed long (always 32 bit, machine byte order)',
			'q' => 'signed long long (always 64 bit, machine byte order)',
			'C' => 'unsigned char',
			'S' => 'unsigned short (always 16 bit, machine byte order)',
			'I' => 'unsigned integer (machine dependent size and byte order)',
			'L' => 'unsigned long (always 32 bit, machine byte order)',
			'Q' => 'unsigned long long (always 64 bit, machine byte order)',
			'f' => 'float (machine dependent size and representation)',
			'd' => 'double (machine dependent size and representation)',

			'n' => 'unsigned short (always 16 bit, big endian byte order)',
			'N' => 'unsigned long (always 32 bit, big endian byte order)',
			'J' => 'unsigned long long (always 64 bit, big endian byte order)',
			'G' => 'float (machine dependent size, big endian byte order)',
			'E' => 'double (machine dependent size, big endian byte order)',

			'v' => 'unsigned short (always 16 bit, little endian byte order)',
			'V' => 'unsigned long (always 32 bit, little endian byte order)',
			'P' => 'unsigned long long (always 64 bit, little endian byte order)',
			'g' => 'float (machine dependent size, little endian byte order)',
			'e' => 'double (machine dependent size, little endian byte order)',

			'H' => 'Hex string, high nibble first',
			'h' => 'Hex string, low nibble first',

			'a' => 'NUL-padded string',
			'Z' => 'NUL-padded string',
			'A' => 'SPACE-padded string',
			'x' => 'NUL byte',
			'X' => 'Back up one byte',
			'@' => 'NUL-fill to absolute position',

			'mo' => 'machine byte order',
			'be' => 'big endian byte order',
			'le' => 'little endian byte order',
			'int' => 'integer by bits',
			'dec' => 'float/double',
		],
		'mo' => [
			'int' => [
				'signed' => [
					1 => 'c',
					2 => 's',
					//4 => 'i',
					4 => 'l',
					8 => 'q',
				],
				'unsigned' => [
					1 => 'C',
					2 => 'S',
					//4 => 'I',
					4 => 'L',
					8 => 'Q',
				],
			],
			'dec' => [
				'signed' => [
					1 => 'f',
					2 => 'f',
					3 => 'f',
					4 => 'f',
					8 => 'd',
				],
				'unsigned' => [
					1 => 'f',
					2 => 'f',
					3 => 'f',
					4 => 'f',
					8 => 'd',
				],
			],
		],
		'be' => [
			'int' => [
				'descr' => 'integer by bits',
				'signed' => [
					1 => 'c',
					2 => 'n',
					4 => 'N',
					8 => 'J',
				],
				'unsigned' => [
					1 => 'C',
					2 => 'n',
					4 => 'N',
					8 => 'J',
				],
			],
			'dec' => [
				'signed' => [
					1 => 'G',
					2 => 'G',
					3 => 'G',
					4 => 'G',
					8 => 'E',
				],
				'unsigned' => [
					1 => 'G',
					2 => 'G',
					3 => 'G',
					4 => 'G',
					8 => 'E',
				],
			],
		],
		'le' => [
			'int' => [
				'descr' => 'integer by bits',
				'signed' => [
					1 => 'C',
					2 => 'v',
					4 => 'V',
					8 => 'P',
				],
				'unsigned' => [
					1 => 'C',
					2 => 'v',
					4 => 'V',
					8 => 'P',
				],
			],
			'dec' => [
				'signed' => [
					1 => 'g',
					2 => 'g',
					3 => 'g',
					4 => 'g',
					8 => 'e',
				],
				'unsigned' => [
					1 => 'g',
					2 => 'g',
					3 => 'g',
					4 => 'g',
					8 => 'e',
				],
			],
		],
	];
	protected $types = [];
	public function __construct($val = null) {
		$this->readonly['len'] = 2;
		$this->readonly['unsigned'] = false;
		$this->raw = $val;
	}
	public function setLen($val) {
		$val = (int)$val;
		if (in_array($val, [2, 4, 8])) {
			$this->readonly['len'] = $val;
			if ($this->aRaw) $this->raw = $this->raw;
		}
		return $this;
	}
}
