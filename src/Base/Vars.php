<?php

namespace EstaleiroWeb\Modbus\Base;

class Vars {
	static public $addressId = [
		1 => 'coils',
		2 => 'discret_inputs',
		3 => 'holding_register',
		4 => 'input_register',
	];
	static public $address = [
		'coils' => [
			'id' => 1, 'name' => 'coils',
			'description' => 'Coils Status',
			'address' => [
				'memory' => [1, 9999],
				'register_dec' => [0, 9998],
				'register_hex' => ['0000', '270E'],
			],
			'bits' => 1,
			'permition' => 'rw', 'type' => 'digital',
		],
		'discret_inputs' => [
			'id' => 2, 'name' => 'discret_inputs',
			'description' => 'Discret Inputs Status',
			'address' => [
				'memory' => [10001, 1999],
				'register_dec' => [0, 9998],
				'register_hex' => ['0000', '270E'],
			],
			'bits' => 1,
			'permition' => 'ro', 'type' => 'digital',
		],
		'holding_register' => [
			'id' => 3, 'name' => 'holding_register',
			'description' => 'Holding Register',
			'address' => [
				'memory' => [40001, 49999],
				'register_dec' => [0, 9998],
				'register_hex' => ['0000', '270E'],
			],
			'bits' => 16,
			'permition' => 'rw', 'type' => 'analogic',
		],
		'input_register' => [
			'id' => 4, 'name' => 'input_register',
			'description' => 'Input Register',
			'address' => [
				'memory' => [30001, 39999],
				'register_dec' => [0, 9998],
				'register_hex' => ['0000', '270E'],
			],
			'bits' => 16,
			'permition' => 'ro', 'type' => 'analogic',
		]
	];
	static public $message = [
		'resquest' => [
			'who' => 'master', 'direction' => 'push',
			'frame' => [
				['name' => 'node', 'bytes' => 1],
				['name' => 'fc', 'bytes' => 1],
				['name' => 'address', 'bytes' => 2, 'description' => 'address of register'],
				['name' => 'quantity', 'bytes' => 2, 'description' => 'quantity of register'],
				['name' => 'crc', 'bytes' => 2],
			],
		],
		'response' => [
			'who' => 'slave', 'direction' => 'pull',
			'frame' => [
				['name' => 'node', 'bytes' => 1],
				['name' => 'fc', 'bytes' => 1],
				['name' => 'bytes', 'bytes' => 1],
				['name' => 'regiter', 'bytes' => 'bytes'],
				['name' => 'crc', 'bytes' => 2],
			],
		],
	];
	static public $modes = [
		'TCP' => [
			'unit' => 'bytes',
			'default_port' => 502,
			'request' => [
				'data_encapsulated' => 'Like RTU',
				'data' => [[
					'len' => 7,
					'description' => [
						'en-us' => 'MBAP - Modbus Application Header',
					],
					'data' => [[
						'len' => 2,
						'description' => [
							'en-us' => 'Transaction identifier: usado para identificação da resposta para a transação'
						],
					], [
						'len' => 2,
						'description' => [
							'en-us' => 'Protocol identifier: 0 (zero) indica Modbus'
						],
					], [
						'len' => 2,
						'description' => [
							'en-us' => 'Length: contagem de todos os próximos bytes'
						],
					], [
						'len' => 1,
						'refer' => 'node',
						'description' => [
							'en-us' => 'Unit identifier: utilizado para identificar o escravo remoto em uma rede Modbus RTU'
						],
					]],
					'obs' => 'Modbus TCP não acrescenta ao quadro um campo de checagem de erros, entretanto o frame ethernet já utiliza CRC-32 tornando desnecessário outro campo de checagem. O cliente Modbus TCP deve iniciar uma conexão TCP com o servidor a fim de enviar as requisições. A porta TCP 502 é a porta padrão para conexão com servidores Modbus TCP.'
				], [
					'len' => 1,
					'refer' => 'fn',
					'description' => [
						'en-us' => 'Function code'
					],
				], [
					'len' => 'function (n) [ return n; ]',
					'refer' => 'data',
					'description' => [
						'en-us' => 'Data'
					],
				]]
			],
			'response' => []
		],
		'UDP' => [
			'extends' => 'TCP',
		],
		'RTU' => [
			'description' => [
				'en-us' => 'Remote Terminal Unit',
			],
			'interface' => [
				'type' => 'serial',
				'baudrate' => [300, 9600, 19200, 100000],
				'bit' => 8,
				'stop_bit' => 1,
				'check_bit' => false
			],
			'unit' => 'bit',
			'data' => [
				[
					'len' => 8,
					'refer' => 'node',
					'description' => [
						'en-us' => 'Address'
					],
				], [
					'len' => 8,
					'refer' => 'fn',
					'description' => [
						'en-us' => 'Function'
					],
				], [
					'len' => 'function (n) [ return n * 8; ]',
					'refer' => 'data',
					'description' => [
						'en-us' => 'Data'
					],
				],
				[
					'len' => 16,
					'description' => [
						'en-us' => 'CRC Check'
					],
				]
			]
		],
		'ASCII' => [
			'interval' => [
				'value' => 1,
				'unit' => 's'
			],
			'interface' => [
				'type' => 'serial',
				'baudrate' => [300, 9600, 19200, 100000],
				'bit' => 8,
				'stop_bit' => 1,
				'check_bit' => false
			],
			'code' => 'Hexa',
			'er_code' => '[0-9A-F]',
			'unit' => 'char',
			'data' => [
				[
					'len' => 1,
					'value' => '=>',
					'description' => [
						'en-us' => 'Start'
					],
				], [
					'len' => 2,
					'refer' => 'node',
					'description' => [
						'en-us' => 'Address'
					],
				], [
					'len' => 1,
					'refer' => 'fn',
					'description' => [
						'en-us' => 'Function'
					],
				], [
					'len' => 'function (n) [ return n * 8; ]',
					'refer' => 'data',
					'description' => [
						'en-us' => 'Data'
					],
				],
				[
					'len' => 2,
					'unit' => 'char',
					'description' => [
						'en-us' => 'LRC Check'
					],
				],
				[
					'len' => 2,
					'unit' => 'char',
					'value' => '\r\n',
					'description' => [
						'en-us' => 'End'
					],
				]
			],
			'bit_per_byte' => [[
				'len' => 1,
				'description' => [
					'en-us' => 'Begin'
				],
			], [
				'len' => 7,
				'description' => [
					'en-us' => 'Data. Least significant bit first',
					'pt-br' => 'Dados. bit menos significativo primeiro',
				],
			], [
				'len' => 1,
				'description' => [
					'en-us' => 'even/odd parity, or no parity bit',
					'pt-br' => 'paridade par / ímpar,  ou sem bit de paridade',
				],
			], [
				'len' => 1,
				'description' => [
					'en-us' => 'stop, if parity is used; 2 bits if no parity',
					'pt-br' => 'parada, se a paridade é usado; 2 bits se sem paridade',
				],
			], [
				'len' => 16,
				'description' => [
					'en-us' => 'Error check : Longitudinal Redundancy Check (LRC)',
					'pt-br' => 'Erro check : Longitudinal Redundancy Check (LRC)',
				],
			]]
		],
	];
	static public $returns = ['json', 'xml', 'text', 'table'];
	static public $errors=[
		'exception_bitmask' => 128,
	];
}
