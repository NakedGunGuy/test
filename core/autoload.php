<?php

foreach (glob(__DIR__ . '/*.php') as $file) {
	if (basename($file) === 'autoload.php') {
		continue;
	}
	require_once $file;
}
