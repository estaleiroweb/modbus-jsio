#!/usr/bin/php
<?php

use ModbusTcpClient\Network\IOException;

$protocol = 'tcp';
$ip = '192.168.1.115';
$port = 502;
$slaveId = 1;
$fn = 4;
$addr = 52;
$timeoutSec = 5;
$connectTimeoutSec = 1;
$readTimeoutSec = 0.3;
$writeTimeoutSec = 1;
$errno = $errstr = null;
$send = '0f1b00000006010400340001';
$send='089300000006010300340001';

//Simple Blank error handler
set_error_handler('my_error_handler');
function my_error_handler($errno, $errstr, $errfile, $errline) {
}
function checkUDP($protocol, $host, $port = 502) {
	global $timeoutSec;
	//look no suppression
	$fp = fsockopen($protocol . '://' . $host, $port, $errno, $errstr, $timeoutSec);
	if (!$fp) {
		return false;
	} else {
		fclose($fp);
		return true;
	}
}
function receiveFrom(array $readStreams, float $timeout = null): array {
	if ($timeout === null) {
		$timeout = 0.3;
	}

	$responsesToWait = \count($readStreams);
	// map streams by their ID so we could reliably return data when we receive it in different order
	$streamMap = [];
	foreach ($readStreams as $indexOrKey => $stream) {
		$streamMap[(int)$stream] = $indexOrKey;
	}

	$result = [];
	$lastAccess = microtime(true);
	$timeoutUsec = (int)(($timeout - (int)$timeout) * 1e6);
	$write = [];
	$except = [];

	while ($responsesToWait > 0) {
		$read = $readStreams;

		/**
		 * On success stream_select returns the number of
		 * stream resources contained in the modified arrays, which may be zero if
		 * the timeout expires before anything interesting happens. On error false
		 * is returned and a warning raised (this can happen if the system call is
		 * interrupted by an incoming signal).
		 */

		$modifiedStreams = stream_select(
			$read,
			$write,
			$except,
			(int)$timeout,
			$timeoutUsec
		);

		if (false == $modifiedStreams) {
			throw new IOException('stream_select interrupted by an incoming signal');
		}

		print "Polling data\n";

		$dataReceived = false;
		foreach ($read as $stream) {
			$streamId = (int)$stream;

			$streamIndex = $streamMap[$streamId] ?? null;
			if ($streamIndex !== null) {
				$data = fread($stream, 256); // read max 256 bytes
				if ($data === false) {
					throw new IOException('fread error during receiveFrom');
				}
				if (!empty($data)) {
					print "Stream {$streamId} @ index: {$streamIndex} received data: ". unpack('H*', $data)."\n";
					$packetData = ($result[$streamIndex] ?? b'') . $data;
					$result[$streamIndex] = $packetData;

					// MODBUS SPECIFIC PART: if we received complete packet to at least one stream we were waiting
					// then it is good enough stream_select cycle
					/*
					if ($this->getIsCompleteCallback()($packetData, $streamIndex)) {
						// happy path, got exactly what we expect
						// or response is an modbus error packet. nothing to wait anymore
						$responsesToWait--;

						$dataReceived = true;
					}
					*/
				}
			}
		}

		if (!$dataReceived) {
			$timeSpentWaiting = microtime(true) - $lastAccess;
			if ($timeSpentWaiting >= $timeout) {
				throw new IOException('Read total timeout expired');
			}
		} else {
			$lastAccess = microtime(true);
		}
	}
	return $result;
}

$stream = fsockopen($protocol . '://' . $ip, $port, $errno, $errstr, $timeoutSec);

stream_set_blocking($stream, false); // use non-blocking stream

// set as stream timeout as we use 'stream_select' to read data and this method has its own timeout
// this call will only affect our fwrite parts (send data method)
stream_set_timeout(
	$stream,
	(int)$writeTimeoutSec,
	extractUsec($writeTimeoutSec)
);

if (!$fp) {
	echo "Unable to open\n";
} else {
	fwrite($stream, "GET / HTTP/1.0\r\n\r\n");
	stream_set_timeout($stream, 2);
	$res = fread($stream, 2000);
	$info = stream_get_meta_data($stream);
	fclose($stream);

	if ($info['timed_out']) {
		echo 'Connection timed out!';
	} else {
		echo $res;
	}
}


function extractUsec(float $seconds): int {
	return (int)(($seconds - (int)$seconds) * 1e6);
}



use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersResponse;
use ModbusTcpClient\Packet\ResponseFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/logger.php';

$connection = BinaryStreamConnection::getBuilder()
	->setPort(502)
	->setHost('192.168.1.115')
	->setLogger(new EchoLogger())
	->build();

$startAddress = 5;
$quantity = 1;
$unitID = 1;
$packet = new ReadHoldingRegistersRequest($startAddress, $quantity, $unitID); // NB: This is Modbus TCP packet not Modbus RTU over TCP!
echo 'Packet to be sent (in hex): ' . $packet->toHex() . PHP_EOL;

try {
	$connection->connect();
	$binaryData = $connection->sendAndReceive($packet);
	echo 'Binary received (in hex):   ' . unpack('H*', $binaryData)[1] . PHP_EOL;

	/**
	 * @var $response ReadInputRegistersResponse
	 */
	$response = ResponseFactory::parseResponseOrThrow($binaryData);
	echo 'Parsed packet (in hex):     ' . $response->toHex() . PHP_EOL;
	echo 'Data parsed from packet (bytes):' . PHP_EOL;
	print_r($response->getData());

	foreach ($response as $word) {
		print_r($word->getBytes());
	}
	foreach ($response->asDoubleWords() as $doubleWord) {
		print_r($doubleWord->getBytes());
	}

	// set internal index to match start address to simplify array access
	$responseWithStartAddress = $response->withStartAddress($startAddress);
	print_r($responseWithStartAddress[256]->getBytes()); // use array access to get word
	print_r($responseWithStartAddress->getDoubleWordAt(257)->getFloat());
} catch (Exception $exception) {
	echo 'An exception occurred' . PHP_EOL;
	echo $exception->getMessage() . PHP_EOL;
	echo $exception->getTraceAsString() . PHP_EOL;
} finally {
	$connection->close();
}
