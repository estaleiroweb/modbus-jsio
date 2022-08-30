<?php
function cli() {
	if (php_sapi_name() !== 'cli') {
		die('Should be used only in command line interface');
	}
}
