<?php
#declare(strict_types=1);

namespace EstaleiroWeb\Modbus;

use Exception;
use EstaleiroWeb\Modbus\Base\Vars;
use EstaleiroWeb\Modbus\FC\FC;
use EstaleiroWeb\Modbus\Types\MbTypeAny;
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

	/**
	 * errorCodes
	 *
	 * - Example packet: \xda\x87\x00\x00\x00\x03\x01\x81\x03
	 * - \xda\x87 - transaction id
	 * - \x00\x00 - protocol id
	 * - \x00\x03 - number of bytes in the message (PDU = ProtocolDataUnit) to follow
	 * - \x01 - unit id
	 * - \x81 - function code + 128 (exception bitmask)
	 * - \x03 - error code
	 * 
	 * @var array
	 */
	protected $errorCodes = [
		1 => 'Illegal function',
		2 => 'Illegal data address',
		3 => 'Illegal data value',
		4 => 'Server failure',
		5 => 'Acknowledge',
		6 => 'Server busy',
		10 => 'Gateway path unavailable',
		11 => 'Gateway targeted device failed to respond',
		128 => 'BitMask',
	];
	protected $readonly = [
		'mode' => null,
		'ip' => null,
		'port' => 502,
		'serial' => null,
		'return' => 'json',
		'doc' => [],
		'log' => [],
		'debug' => null,
	];
	protected $protect = [
		'elapsed' => 0,
		'endianess' => 1,
		'connectTimeout' => 1.5, // seconds timeout when establishing connection to the server
		'writeTimeout' => 0.5, // seconds timeout when writing/sending packet to the server
		'readTimeout' => 0.5, // seconds timeout when waiting response from server
	];
	protected $result = [];
	protected $conn;

	/**
	 * __construct
	 * @param string|array $mode_conn Mode or array of arguments readonly with setters method
	 * @param mixed $param If parameter 1 is a mode, see int_<modes> methods
	 * @return void
	 */
	public function __construct($mode_conn = null, $param = null) {
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
		if (in_array($val, $src)) $this->readonly[$key] = $val;
		if (key_exists($val, $src)) $this->readonly[$key] = $src[$val];
	}

	public function setMode($val) {
		$this->_setByReadonlyArray('mode', strtoupper($val), Vars::MODES);
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
		$this->_setByReadonlyArray('return', strtolower($val), Vars::RETURNS);
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
		return $this->conn;
	}
	public function disconnect() {
		if (is_null($this->conn)) return;
		$this->conn->close();
		$this->conn = null;
	}
	public function fc($fc, $addr = 1, $quant = 1, $uId = 1) {
		if (is_null($this->conn)) return;
		$startTime = microtime(true) * 1000;

		$packet = FC::objRequest([$fc, $addr, $quant, $uId]);

		$endianess = (int)MbTypeAny::BIG_ENDIAN_LOW_WORD_FIRST;
		MbTypeAny::$defaultEndian = $endianess;

		$endianess = (int)Endian::BIG_ENDIAN_LOW_WORD_FIRST;
		Endian::$defaultEndian = $endianess;

		$this->log = "{$this->ip}:{$this->ip}/#$uId-FC$fc@{$addr}[$quant] endianess:{$endianess}";
		$this->log = 'Send(hex): ' . $packet->toHex();

		$this->result = [];
		$this->elapsed = 0;

		try {
			$binaryData = $this->conn->sendAndReceive($packet);
			$result = FC::parserResponse($binaryData);
			$this->log = 'Received(hex): ' . unpack('H*', $binaryData)[1];
		} catch (Exception $exception) {
			$result = null;
			$this->log = 'An exception occurred';
			$this->log = $exception->getMessage();
			$this->log = $exception->getTraceAsString();
		}

		$this->elapsed = (microtime(true) * 1000) - $startTime;
		return $result;
	}
	public function fc_old($fc, $addr = 1, $quant = 1, $uId = 1) {
		if (is_null($this->conn)) return $this;

		$packet = FC::objRequest([$fc, $addr, $quant, $uId]);

		$endianess = (int)MbTypeAny::BIG_ENDIAN_LOW_WORD_FIRST;
		MbTypeAny::$defaultEndian = $endianess;

		$endianess = (int)Endian::BIG_ENDIAN_LOW_WORD_FIRST;
		Endian::$defaultEndian = $endianess;

		$this->log = "{$this->ip}:{$this->ip}/#$uId-FC$fc@{$addr}[$quant] endianess:{$endianess}";
		$this->log = 'Send(hex): ' . $packet->toHex();

		$startTime = round(microtime(true) * 1000, 3);
		$this->result = [];
		$this->elapsed = 0;

		try {
			$binaryData = $this->conn->sendAndReceive($packet);
			$resp = FC::parserResponse($binaryData);
			$resp['hex'] = unpack('H*', $resp['data'])[1];
			print_r($resp);

			$this->log = 'Received(hex): ' . unpack('H*', $binaryData)[1];

			/** @var $response ReadHoldingRegistersResponse */
			$r = ResponseFactory::parseResponseOrThrow($binaryData);
			$response = $r->withStartAddress($addr);
			//print_r([__LINE__ => $response]);

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
	static public function scan_ips($v = 4, $port = 502) {
		$ipa = preg_grep(
			'/^(127\..*\/8|::[0-9a-z]\/128)$/i',
			explode(
				"\n",
				trim(`ip a | grep 'inet' | sed -r 's/^\\s*inet.? ([^ ]+).*/\\1/'`)
			),
			PREG_GREP_INVERT
		);
		if ($v == 4) $ipa = preg_grep('/\./', $ipa);
		elseif ($v == 4) $ipa = preg_grep('/:/', $ipa);
		$out = [];
		foreach ($ipa as $ip) {
			$nmap = `sudo nmap -sUT $ip -p $port`;
			$nmap = preg_grep('/ open /', explode('Nmap scan report', $nmap));
			foreach ($nmap as $k => $v) if (
				preg_match(
					'/^.*?(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|[0-9a-f:]+:[0-9a-f:]*)/i',
					$v,
					$ret
				)
			) {
				preg_match_all('/\b(tcp|udp) +open/', $v, $r);
				sort($r[1]);
				$out[$ret[1]] = implode(',', $r[1]);
			}
		}
		return $out;
	}
	public function scan($fcs = null, $addrs = null, $nodes = null) {
		static $head = true;

		if (!$fcs) $fcs = range(1, 4);
		else $fcs = (array)$fcs;
		if (!$addrs) $addrs = range(0, 9998);
		else $addrs = (array)$addrs;
		if (!$nodes) $nodes = [1];
		else $nodes = (array)$nodes;
		$lastAddr = end($addrs);
		if(!$this->connect()) die("Not Connected\n");
		$log = null;
		foreach ($nodes as $node) {
			foreach ($fcs as $fc) {
				foreach ($addrs as $addr) {
					print " #$node/FC$fc($addr/$lastAddr)\r";
					$r = $this->fc($fc, $addr, 1, $node);
					if ($r) {
						if ($head) {
							$head = false;
							print "Node FC Addr Len Data TId___ Elapse(ms)\n";
						}
						print '' .
							str_pad($r['node'], 4, ' ', STR_PAD_LEFT) . ' ' .
							str_pad($r['fc'], 2, ' ', STR_PAD_LEFT) . ' ' .
							str_pad($addr, 4, ' ', STR_PAD_LEFT) . ' ' .
							str_pad($r['len'], 3, ' ', STR_PAD_LEFT) . ' ' .
							str_pad(unpack('H*', $r['data'])[1], 4, ' ', STR_PAD_LEFT) . ' ' .
							str_pad($r['transactionId'], 6, ' ', STR_PAD_LEFT) . ' ' .
							$this->elapsed . "\n";
						$log = $this->log;
					}
				}
			}
		}
		$head = true;
		print "                   \r";
		print_r($log);

		return $this;
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
	public function toHex($val) {
		return unpack('H*', $val)[1];
	}
}
