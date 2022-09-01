<?php

use EstaleiroWeb\Modbus\Modbus;

function cli() {
	if (php_sapi_name() !== 'cli') {
		die('Should be used only in command line interface');
	}
}
function getIpList() {
	return preg_replace(
		['/["{} ]/', '/^\s+/'],
		'',
		json_encode(
			Modbus::scan_ips(),
			JSON_PRETTY_PRINT
		)
	);
}
function getArgIP() {
	global $argv;
	cli();
	if (!key_exists(1, $argv)) {
		print "Sintaxe: {$argv[0]} <ip>\n";
		print_r(getIpList());
		exit;
	}
	$ip = $argv[1];
	print "IP: $ip\n";
	return $ip;
}
if (!function_exists('array_is_list')) {
	function array_is_list(array $arr) {
		$k = array_keys($arr);
		return $k === array_keys($k);
	}
}
