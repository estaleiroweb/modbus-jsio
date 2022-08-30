#!/usr/bin/php
<?php
require __DIR__ . '/../vendor/autoload.php';

use EstaleiroWeb\Modbus\Modbus;

cli();
print_r(Modbus::scan_ips());
