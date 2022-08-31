<?php
#declare(strict_types=1);
namespace EstaleiroWeb\Modbus\FC;

use EstaleiroWeb\Modbus\Base\NewObj;
use EstaleiroWeb\Modbus\Base\Vars;
use EstaleiroWeb\Modbus\Types\MbTypeAny;

class FC {
	public const FCS_NAMES = [
		'read_coils' => 1,
		'read_input_discretes' => 2,
		'read_holding_registers' => 3,
		'read_input_registers' => 4,
		'write_single_coil' => 5,
		'write_single_register' => 6,
		'write_multiple_coils' => 15,
		'write_multiple_registers' => 16,
		'mask_write_register' => 22,
		'read_write_multiple_registers' => 23,
	];
	public const FCS = [
		1 => [
			'name' => 'read_coils',
			'class' => 'ReadCoils',
			'decription' => [
				'en-us' => 'Read coil-type bit block(discrete output).',
				'pt-br' => 'Leitura de bloco de bits do tipo coil(saída discreta).'
			],
			'hex' => '01',
			'action' => 'read',
			'refer' => 'coils',
		],
		2 => [
			'name' => 'read_input_discretes',
			'class' => 'ReadInputDiscretes',
			'decription' => [
				'en-us' => 'Read block of discrete inputs type bits.',
				'pt-br' => 'Leitura de bloco de bits do tipo entradas discretas.'
			],
			'hex' => '02',
			'action' => 'read',
			'refer' => 'discret_inputs'
		],
		3 => [
			'name' => 'read_holding_registers',
			'class' => 'ReadHoldingRegisters',
			'decription' => [
				'en-us' => 'Reading block of holding type registers.',
				'pt-br' => 'Leitura de bloco de registradores do tipo holding.'
			],
			'hex' => '03',
			'action' => 'read',
			'refer' => 'holding_register'
		],
		4 => [
			'name' => 'read_input_registers',
			'class' => 'ReadInputRegisters',
			'decription' => [
				'en-us' => 'Input-type register block reading.',
				'pt-br' => 'Leitura de bloco de registradores do tipo input.'
			],
			'hex' => '04',
			'action' => 'read',
			'refer' => 'input_register'
		],
		5 => [
			'name' => 'write_single_coil',
			'class' => 'WriteSingleCoil',
			'decription' => [
				'en-us' => 'Write to a single coil bit (discrete output).',
				'pt-br' => 'Escrita em um único bit do tipo coil(saída discreta).'
			],
			'hex' => '05',
			'action' => 'write',
			'multiples' => false,
			'refer' => 'coils'
		],
		6 => [
			'name' => 'write_single_register',
			'class' => 'WriteSingleRegister',
			'decription' => [
				'en-us' => 'Writing to a single holding register.',
				'pt-br' => 'Escrita em um único registrador do tipo holding.'
			],
			'hex' => '06',
			'action' => 'write',
			'multiples' => false,
			'refer' => 'holding_register'
		],
		7 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Read the contents of 8 exception states.',
				'pt-br' => 'Ler o conteúdo de 8 estados de exceção.'
			],
			'hex' => '07'
		],
		8 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Provide a series of tests to verify communication and internal errors.',
				'pt-br' => 'Prover uma série de testes para verificação da comunicação e erro internos.'
			],
			'hex' => '08'
		],
		11 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Modbus: Get the event counter.',
				'pt-br' => 'Modbus: Obter o contador de eventos.'
			],
			'hex' => '0B'
		],
		12 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Modbus: Get an event report.',
				'pt-br' => 'Modbus: Obter um relatório de eventos.'
			],
			'hex' => '0C'
		],
		15 => [
			'name' => 'write_multiple_coils',
			'class' => 'WriteMultipleCoils',
			'decription' => [
				'en-us' => 'Write in bit block of coil type (discrete output).',
				'pt-br' => 'Escrita em bloco de bits do tipo coil(saída discreta).'
			],
			'hex' => '0F',
			'action' => 'write',
			'multiples' => true,
			'refer' => 'coils'
		],
		16 => [
			'name' => 'write_multiple_registers',
			'class' => 'WriteMultipleRegisters',
			'decription' => [
				'en-us' => 'Block writing of holding type registers.',
				'pt-br' => 'Escrita em bloco de registradores do tipo holding.'
			],
			'hex' => '10',
			'action' => 'write',
			'multiples' => true,
			'refer' => 'holding_register'
		],
		17 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Read some device information.',
				'pt-br' => 'Ler algumas informações do dispositivo.'
			],
			'hex' => '11'
		],
		20 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Read information from a file.',
				'pt-br' => 'Ler informações de um arquivo.'
			],
			'hex' => '14'
		],
		21 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Write information to a file.',
				'pt-br' => 'Escrever informações em um arquivo.'
			],
			'hex' => '15'
		],
		22 => [
			'name' => 'mask_write_register',
			'class' => 'MaskWriteRegister',
			'decription' => [
				'en-us' => 'Modify the contents of wait registers through logical operations.',
				'pt-br' => 'Modificar o conteúdo de registradores de espera através de operações   lógicas.'
			],
			'hex' => '16'
		],
		23 => [
			'name' => 'read_write_multiple_registers',
			'class' => 'ReadWriteMultipleRegisters',
			'decription' => [
				'en-us' => 'Combines reading and writing registers in a single transaction.',
				'pt-br' => 'Combina ler e escrever em registradores numa única transação.'
			],
			'hex' => '17'
		],
		24 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Read the contents of the FIFO queue of registers.',
				'pt-br' => 'Ler o conteúdo da fila FIFO de registradores.'
			],
			'hex' => '18'
		],
		43 => [
			//'name' => 'xxxxxx',
			//'class' => 'xxxxxx', 
			'decription' => [
				'en-us' => 'Device Model Identification.',
				'pt-br' => 'Identificação do modelo do dispositivo.'
			],
			'hex' => '2B'
		]
	];
	protected $node = 1;
	protected $transactionId = null;
	protected $class;
	protected $request;
	protected $response;

	/**
	 * __construct
	 *
	 * @param  int $startAddress
	 * @param  int $quantity
	 * @param  int $node
	 * @param  int|null $transactionId
	 * @return void
	 */
	public function __construct($startAddress, $quantity, $node = 1, $transactionId = null) {
		$this->node = $node;
		$this->transactionId = $transactionId;
		$this->class = '\\ModbusTcpClient\\Packet\\ModbusFunction\\' . get_class($this);

		$this->request  = NewObj::call_user_class_array($this->class . 'Request', func_get_args());
	}
	static private function init($key, $args) {
		$fc = array_shift($args);
		$fcs = self::FCS[$fc];
		if (!$fc) throw new \RuntimeException("Unknown function code '{$fc}' read from response packet");
		elseif (!key_exists($k = 'class', $fcs)) throw new \RuntimeException("function code '{$fc}' without class");
		else {
			$root = '\\ModbusTcpClient\\Packet\\ModbusFunction\\';
			return NewObj::call_user_class_array($root . $fcs[$k] . $key, $args);
		}
		exit;
	}
	static public function objRequest($args) {
		return self::init('Request', $args);
	}
	static public function objResponse($args) {
		return self::init('Response', $args);
	}
	/**
	 * @param string|null $binaryString
	 * @return ModbusResponse
	 */
	static public function parserResponse($binaryString) {
		if ($binaryString === null) throw new \RuntimeException('Response null');
		// 7 bytes for MBAP header and at least 2 bytes for PDU
		elseif (strlen($binaryString) < 7) throw new \RuntimeException('Response data length too short (<7)');
		else {
			$fc = ord($binaryString[7]);
			$transactionId = MbTypeAny::parseUInt16($binaryString[0] . $binaryString[1]);
			$length = MbTypeAny::parseUInt16($binaryString[4] . $binaryString[5]);
			$node = MbTypeAny::parseByte($binaryString[6]);
			$errorKey = "FC: $fc, Transaction: $transactionId, Len: $length, Node: $node";
			if (strlen($binaryString) < 9) throw new \RuntimeException("$errorKey, Response data length too short (<9)");
			elseif (($fc & Vars::ERRORS['exception_bitmask']) > 0) {
				//function code is in low bits of exception
				$fc -= Vars::ERRORS['exception_bitmask'];
				$exceptionCode = MbTypeAny::parseByte($binaryString[8]);
				throw new \RuntimeException("$errorKey, exceptionCode: $exceptionCode");
			} else {
				return [
					'fc' => $fc,
					'len' => MbTypeAny::parseByte($binaryString[8]),
					'data' => substr($binaryString, 9),
					'node' => $node,
					'transactionId' => $transactionId,
				];
				//return self::getResponse([$rawData,$node,$transactionId]);
			}
		}
		exit;
	}
}

/*
	use ModbusTcpClient\Packet\ModbusFunction\ReadCoilsRequest;
	use ModbusTcpClient\Packet\ModbusFunction\ReadInputDiscretesRequest;
	use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
	use ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersRequest;
	use ModbusTcpClient\Packet\ModbusFunction\WriteSingleCoilRequest;
	use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterRequest;
	use ModbusTcpClient\Packet\ModbusFunction\WriteMultipleCoilsRequest;
	use ModbusTcpClient\Packet\ModbusFunction\WriteMultipleRegistersRequest;
	use ModbusTcpClient\Packet\ModbusFunction\MaskWriteRegisterRequest;
	use ModbusTcpClient\Packet\ModbusFunction\ReadWriteMultipleRegistersRequest;


	use ModbusTcpClient\Packet\ModbusFunction\ReadCoilsResponse;
	use ModbusTcpClient\Packet\ModbusFunction\ReadInputDiscretesResponse;
	use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersResponse;
	use ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersResponse;
	use ModbusTcpClient\Packet\ModbusFunction\WriteSingleCoilResponse;
	use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterResponse;
	use ModbusTcpClient\Packet\ModbusFunction\WriteMultipleCoilsResponse;
	use ModbusTcpClient\Packet\ModbusFunction\WriteMultipleRegistersResponse;
	use ModbusTcpClient\Packet\ModbusFunction\MaskWriteRegisterResponse;
	use ModbusTcpClient\Packet\ModbusFunction\ReadWriteMultipleRegistersResponse;
*/
