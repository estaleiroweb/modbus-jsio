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

if (php_sapi_name() !== 'cli') {
	echo 'Should be used only in command line interface';
	return;
}
require __DIR__ . '/../vendor/autoload.php';

$m = new Modbus('192.168.1.115');
$r = $m->connect()->fc($argv[1] ?? 4, $argv[2] ?? 1, 2);
print_r(['result' => $r, 'log' => $m->log,]);
