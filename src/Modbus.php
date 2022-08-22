<?php

namespace EstaleiroWeb\Modbus;

use Exception;
use SimpleXMLElement;
use EstaleiroWeb\Traits\GetSet;
use EstaleiroWeb\Traits\FuncArray;

use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersResponse;
use ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersRequest;
use ModbusTcpClient\Packet\ResponseFactory;
use ModbusTcpClient\Utils\Endian;

class Modbus {
	use GetSet, FuncArray;

	protected $readonly = [
		'modes' => ['TCP', 'UDP', 'RTU', 'ASCII'],
		'returns' => ['json', 'xml', 'text', 'table'],
		'fcs' => [
			1 =>  ['fn' => 'ReadCoils', 'bits' => 1, 'permition' => 'ro', 'descr' => 'Read Coils',],
			2 =>  ['fn' => 'ReadInputDiscretes', 'bits' => 1, 'permition' => 'ro', 'descr' => 'Read Input Discretes',],
			3 =>  ['fn' => 'ReadHoldingRegisters', 'bits' => 16, 'permition' => 'ro', 'descr' => 'Read Holding Registers',],
			4 =>  ['fn' => 'ReadInputRegisters', 'bits' => 16, 'permition' => 'ro', 'descr' => 'Read Input Registers',],
			5 =>  ['fn' => 'WriteSingleCoil', 'bits' => 1, 'permition' => 'rw', 'descr' => 'Write Single Coil',],
			6 =>  ['fn' => 'WriteSingleRegister', 'bits' => 16, 'permition' => 'rw', 'descr' => 'Write Single Register',],
			15 => ['fn' => 'WriteMultipleCoils', 'bits' => 1, 'permition' => 'rw', 'descr' => 'Write Multiple Coils',],
			16 => ['fn' => 'WriteMultipleRegisters', 'bits' => 16, 'permition' => 'rw', 'descr' => 'Write Multiple Registers',],
			22 => ['fn' => 'MaskWriteRegister', 'bits' => 16, 'permition' => 'rw', 'descr' => 'Mask Write Register',],
			23 => ['fn' => 'ReadWriteMultipleRegisters', 'bits' => 16, 'permition' => 'rw', 'descr' => 'Read / Write Multiple Registers',],
		],

		'mode' => null,
		'ip' => null,
		'port' => 502,
		'serial' => null,
		'return' => 'json',
		'doc' => [],
		'log' => [],
		'debug' => null,
		'elapsed' => 0,
	];
	protected $protect = [
		'connectTimeout' => 1.5, // seconds timeout when establishing connection to the server
		'writeTimeout' => 0.5, // seconds timeout when writing/sending packet to the server
		'readTimeout' => 1.0, // seconds timeout when waiting response from server
	];
	protected $result = [];
	protected $conn;

	/**
	 * __construct
	 * @param string|array Mode or array of arguments readonly with setters method
	 * @param mixed If parameter 1 is a mode, see int_<modes> methods
	 * @return void
	 */
	public function __construct() {
		$args = func_get_args();
		$item = array_shift($args);
		if (is_array($item)) $this->init($item);
		elseif (!is_object($item)) {
			$this->mode = $item;
			if ($this->mode) call_user_func_array([$this, 'init_' . $this->mode], $args);
			else {
				$arr = ['ip', 'serial'];
				foreach ($arr as $k) {
					$this->$k = $item;
					if ($this->$k) break;
				}
			}
		}
	}
	public function __destruct() {
		$this->disconnect();
	}
	public function __toString() {
		return "";
	}
	public function __invoke() {
		return $this->return_main();
	}

	private function _setByReadonlyArray($key, $val, $src) {
		if (in_array($val, $this->$src)) $this->readonly[$key] = $val;
		if (key_exists($val, $this->$src)) $this->readonly[$key] = $this->$src[$val];
	}

	public function setMode($val) {
		$this->_setByReadonlyArray('mode', strtoupper($val), 'modes');
		return $this;
	}
	public function setIP($val) {
		$port = null;
		if (preg_match('/^(\d+.\d+.\d+.\d+)(?::(\d+))?$/', $val, $ret)) {
			$val = $ret[1];
			$port = (int)@$ret[2];
			$port = $port > 0 && $port <= 65535 ? $port : null;
		}
		if (!filter_var($val, FILTER_VALIDATE_IP)) return;
		$this->readonly['ip'] = $val;
		$this->port = $port;
		if (is_null($this->mode)) $this->mode = 'TCP';
		return $this;
	}
	public function setPort($val) {
		$val = (int)$val;
		if ($val <= 0 || $val > 65535) return;
		$this->readonly['port'] = $val;
		return $this;
	}
	public function setSerial($val) {
		//print __FUNCTION__ . '[' . __LINE__ . ']:' . $val . "\n";
		$this->readonly['serial'] = $val;
		if (is_null($this->mode)) $this->mode = 'RTU';
		return $this;
	}
	public function setReturn($val) {
		$this->_setByReadonlyArray('return', strtolower($val), 'returns');
		return $this;
	}
	public function setLog($val) {
		$this->readonly['log'][] = $val;
		return $this;
	}

	protected function init($arr) {
		foreach ($arr as $k => $v) $this->$k = $v;
	}
	protected function init_TCP($ip = null, $port = null) {
		$this->ip = $ip;
		$this->port = $port;
	}
	protected function init_UDP($ip = null, $port = null) {
		$this->ip = $ip;
		$this->port = $port;
	}
	protected function init_RTU($serial = null) {
		$this->serial = $serial;
	}
	protected function init_ASCII($serial = null) {
		$this->serial = $serial;
	}

	public function connect() {
		try {
			$this->log = 'Connect: ' . $this->ip;
			$this->conn = BinaryStreamConnection::getBuilder()
				->setHost($this->ip)
				->setPort($this->port)
				->setConnectTimeoutSec($this->connectTimeout) // timeout when establishing connection to the server
				->setWriteTimeoutSec($this->writeTimeout) // timeout when writing/sending packet to the server
				->setReadTimeoutSec($this->readTimeout) // timeout when waiting response from server
				->build();
			$this->conn->connect();
		} catch (Exception $exception) {
			$this->conn = null;
			$this->log = 'An exception occurred';
			$this->log = $exception->getMessage();
			$this->log = $exception->getTraceAsString();
		}
		return $this;
	}
	public function disconnect() {
		if (is_null($this->conn)) return;
		$this->conn->close();
		$this->conn = null;
	}
	public function fc($fc, $addr = 256, $quant = 1, $uId = 1) {
		if (is_null($this->conn)) return $this;

		if ($fc == 4) {
			$packet = new ReadInputRegistersRequest($addr, $quant, $uId);
		} elseif ($fc == 3) {
			$fc = 3;
			$packet = new ReadHoldingRegistersRequest($addr, $quant, $uId);
		} else return $this;

		$endianess = (int)Endian::BIG_ENDIAN_LOW_WORD_FIRST;
		Endian::$defaultEndian = $endianess;
		$this->log = "{$this->ip}:{$this->ip}/#$uId-FC$fc@{$addr}[$quant] endianess:{$endianess}";
		$this->log = 'Send(hex): ' . $packet->toHex();

		$startTime = round(microtime(true) * 1000, 3);
		$this->result = [];
		$this->elapsed = 0;

		try {
			$binaryData = $this->conn->sendAndReceive($packet);

			$this->log = 'Received(hex): ' . unpack('H*', $binaryData)[1];

			/** @var $response ReadHoldingRegistersResponse */
			$response = ResponseFactory::parseResponseOrThrow($binaryData)->withStartAddress($addr);

			foreach ($response as $address => $word) {
				$doubleWord = isset($response[$address + 1]) ? $response->getDoubleWordAt($address) : null;
				$quadWord = null;
				if (isset($response[$address + 3])) {
					$quadWord = $response->getQuadWordAt($address);
					try {
						$UInt64 = $quadWord->getUInt64(); // some data can not be converted to unsigned 64bit int due PHP memory limitations
					} catch (Exception $e) {
						$UInt64 = '-';
					}
					try {
						$Int64 = $quadWord->getInt64();
					} catch (Exception $e) {
						$Int64 = '-';
					}
					try {
						$double = $quadWord->getDouble();
					} catch (Exception $e) {
						$double = '-';
					}
				}

				$highByteAsInt = $word->getHighByteAsInt();
				$lowByteAsInt = $word->getLowByteAsInt();
				$this->result[$address] = [
					'highByte' => '0x' . str_pad(dechex($highByteAsInt), 2, '0', STR_PAD_LEFT) . ' / ' . $highByteAsInt . ' / "&#' . $highByteAsInt . ';"',
					'lowByte' => '0x' . str_pad(dechex($lowByteAsInt), 2, '0', STR_PAD_LEFT) . ' / ' . $lowByteAsInt . ' / "&#' . $lowByteAsInt . ';"',
					'highByteBits' => sprintf('%08d', decbin($highByteAsInt)),
					'lowByteBits' => sprintf('%08d', decbin($lowByteAsInt)),
					'int16' => $word->getInt16(),
					'UInt16' => $word->getUInt16(),
					'int32' => $doubleWord ? $doubleWord->getInt32() : null,
					'UInt32' => $doubleWord ? $doubleWord->getUInt32() : null,
					'float' => $doubleWord ? $doubleWord->getFloat() : null,
					'double' => $quadWord ? $double : null,
					'Int64' => $quadWord ? $Int64 : null,
					'UInt64' => $quadWord ? $UInt64 : null,
				];
			}
		} catch (Exception $exception) {
			$result = null;
			$this->log = 'An exception occurred';
			$this->log = $exception->getMessage();
			$this->log = $exception->getTraceAsString();
		}

		$this->elapsed = round(microtime(true) * 1000) - $startTime;
		return $this->result;
	}

	protected function return_main($val = null, $print = false) {
		if (is_null($val)) $val = $this->result;
		return call_user_func([$this, 'return_' . $this->return], [
			'data' => $val,
			'debug' => $this->log,
			'time_ms' => $this->elapsed
		], $print);
	}
	protected function return_json($val, $print = false) {
		$out = json_encode(
			$val,
			JSON_PRETTY_PRINT
		);
		if ($print) {
			header('Access-Control-Allow-Origin: *');
			header('Content-Type: application/json');
			http_response_code($val !== null ? 200 : 500);
			print $out;
			exit(0);
		}
		return $out;
	}
	protected function return_xml($val, $print = false) {
		$out = $this->array2xml($val);
		if ($print) {
			header('Access-Control-Allow-Origin: *');
			header('Content-Type: application/xml');
			http_response_code($val !== null ? 200 : 500);
			print $out;
			exit(0);
		}
		return $out;
	}
	protected function return_text($val, $print = false) {
		$out = print_r($val, true);
		if ($print) {
			header('Access-Control-Allow-Origin: *');
			header('Content-Type: plain/text');
			http_response_code($val !== null ? 200 : 500);
			print $out;
			exit(0);
		}
		return $out;
	}
}
