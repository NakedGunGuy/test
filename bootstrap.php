<?php

define('ROOT_PATH', __DIR__);
define('VIEW_PATH', ROOT_PATH . '/views');
define('CORE_PATH', ROOT_PATH . '/core');
define('PUBLIC_PATH', ROOT_PATH . '/public');

if (file_exists(ROOT_PATH . '/.env')) {
	foreach (parse_ini_file(__DIR__ . '/.env') as $key => $value) {
		$_ENV[$key] = $value;
	}
}

if ($_ENV['DEBUG'] === 'true') {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 0);
	error_reporting(0);
}
