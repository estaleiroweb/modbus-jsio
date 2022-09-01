#!/usr/bin/php
<?php

use EstaleiroWeb\Modbus\Modbus;

if (php_sapi_name() !== 'cli') die('Should be used only in command line interface');

require __DIR__ . '/../vendor/autoload.php';
$m = new Modbus('192.168.1.102');
$m->readTimeout = .2;

$fc = $argv[1] ?? 4;
$addr = $argv[2] ?? 1;
$quant = $argv[3] ?? 1;
print_r($m->fc($fc, $addr, $quant));



//print_r(['result' => $r, 'log' => $m->log,]);
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
