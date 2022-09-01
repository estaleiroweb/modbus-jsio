#!/usr/bin/php
<?php
require __DIR__ . '/../vendor/autoload.php';

use EstaleiroWeb\Modbus\Modbus;

$m = new Modbus(getArgIP());
$m->readTimeout = .2;
$m->scan();
