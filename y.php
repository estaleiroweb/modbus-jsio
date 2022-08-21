#!/usr/bin/php
<?php
require __DIR__ . '/vendor/autoload.php';

// Modbus master UDP
$modbus = new ModbusMaster("192.168.1.115", "TCP"); 
// Read multiple registers
try {
	$adddr=12288;
    //$recData = $modbus->readMultipleRegisters(1, 52, 1); 
    //$recData = $modbus->readMultipleInputRegisters(1, 2, 1); 
    $recData = $modbus->readMultipleRegisters(1, 1, 1); 
} catch (Exception $e) {
    // Print error information if any
    echo $modbus . "\n";
    echo $e;
    exit;
}
// Print data in string format
echo PhpType::bytes2string($recData); 
