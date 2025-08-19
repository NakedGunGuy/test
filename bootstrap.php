<?php

const ROOT_PATH = __DIR__;
const CONTENT_PATH = ROOT_PATH . '/content';
const VIEW_PATH = ROOT_PATH . '/views';
const CORE_PATH = ROOT_PATH . '/core';
const PUBLIC_PATH = ROOT_PATH . '/public';
const ROUTE_PATH = ROOT_PATH . '/routes';
const MAIL_PATH = ROOT_PATH . '/mail';

if (file_exists(ROOT_PATH . '/.env')) {
	foreach (parse_ini_file(__DIR__ . '/.env') as $key => $value) {
		$_ENV[$key] = $value;
	}
}

if ($_ENV['DEBUG']) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 0);
	error_reporting(0);
}

if (!headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
