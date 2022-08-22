Modbus-JsIO
===========
<span class="bxLbl">PHP</span><span class="bxVer">5/7/8</span>
<span class="bxLbl">License</span><span class="bxVal">MIT</span>

## Description
Modbus TCP/UDP/RTU/ASCII framework and conect to hilevel json doc to manager every IO

It is a easy way to read and write every modbus registers using a documentation json.

## Installation

Use [Composer](https://getcomposer.org/) to install this library as dependency.
```bash
composer require estaleiroweb/modbus-jsio
```
## Supported Modbus Functions

* FC1 - Read Coils ([ReadCoils](src/Packet/ModbusFunction/ReadCoilsRequest.php))
* FC2 - Read Input Discretes ([ReadInputDiscretes](src/Packet/ModbusFunction/ReadInputDiscretesRequest.php))
* FC3 - Read Holding Registers ([ReadHoldingRegisters](src/Packet/ModbusFunction/ReadHoldingRegistersRequest.php))
* FC4 - Read Input Registers ([ReadInputRegisters](src/Packet/ModbusFunction/ReadInputRegistersRequest.php))
* FC5 - Write Single Coil ([WriteSingleCoil](src/Packet/ModbusFunction/WriteSingleCoilRequest.php))
* FC6 - Write Single Register ([WriteSingleRegister](src/Packet/ModbusFunction/WriteSingleRegisterRequest.php))
* FC15 - Write Multiple Coils ([WriteMultipleCoils](src/Packet/ModbusFunction/WriteMultipleCoilsRequest.php))
* FC16 - Write Multiple Registers ([WriteMultipleRegisters](src/Packet/ModbusFunction/WriteMultipleRegistersRequest.php))
* FC22 - Mask Write Register ([MaskWriteRegister](src/Packet/ModbusFunction/MaskWriteRegisterRequest.php))
* FC23 - Read / Write Multiple Registers ([ReadWriteMultipleRegisters](src/Packet/ModbusFunction/ReadWriteMultipleRegistersRequest.php))


## Intention
This library is influenced by [phpmodbus](https://github.com/adduc/phpmodbus)/[modbus-tcp-client](https://github.com/aldas/modbus-tcp-client) library and meant to be provide decoupled Modbus protocol (request/response packets) and networking related features so you could build modbus client with our own choice of networking code (ext_sockets/streams/Reactphp/Amp asynchronous streams) or use library provided networking classes (php Streams)
## Requirements
* writing....

## Modbus Referer
* Modbus TCP/IP specification: http://www.modbus.org/specs.php
* Modbus TCP/IP and RTU simpler description: http://www.simplymodbus.ca/TCP.htm
## Other Projetcs
* Modbus-TCP-Client <span class="bxLbl">PHP</span><span class="bxVer">8</span>: https://packagist.org/packages/aldas/modbus-tcp-client
* PHPModbus <span class="bxLbl">PHP</span><span class="bxVer">5/7</span>: https://packagist.org/packages/mightypork/phpmodbus
* Modbus <span class="bxLbl">PHP</span><span class="bxVer">7</span>: https://packagist.org/packages/fawno/modbus
* PHP Serial Modbus <span class="bxLbl">PHP</span><span class="bxVer">7</span>: https://github.com/toggio/PhpSerialModbus


<style>
	.bxLbl, .bxVal,.bxVer{
		padding: 2px 3px 2px 3px;
		text-shadow: 1px 1px 3px black;
		clear;
		font-size: small;
	}
	.bxLbl{
		background-color: #555;
		border-radius: 3px 0 0 3px;
	}
	.bxVer{
		background-color: #0060FF;
		border-radius: 0 3px 3px 0;
	}
	.bxVal{
		background-color: #00AA20;
		border-radius: 0 3px 3px 0;
	}
</style>
