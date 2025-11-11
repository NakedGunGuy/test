<?php

const ROOT_PATH = __DIR__;
const CONTENT_PATH = ROOT_PATH . '/content';
const VIEW_PATH = ROOT_PATH . '/views';
const CORE_PATH = ROOT_PATH . '/core';
const PUBLIC_PATH = ROOT_PATH . '/public_html';
const ROUTE_PATH = ROOT_PATH . '/routes';
const MAIL_PATH = ROOT_PATH . '/mail';

if (file_exists(ROOT_PATH . '/.env')) {
	foreach (parse_ini_file(__DIR__ . '/.env') as $key => $value) {
		$_ENV[$key] = $value;
	}
}

// Load logger early
require_once CORE_PATH . '/logger.php';

if ($_ENV['DEBUG']) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 0);
	error_reporting(E_ALL); // Still report errors, just don't display them
	ini_set('log_errors', 1);
	ini_set('error_log', ROOT_PATH . '/logs/php-errors.log');
}

// Custom error handler for production
if (!($_ENV['DEBUG'] ?? false)) {
	set_error_handler(function($errno, $errstr, $errfile, $errline) {
		// Don't log suppressed errors (@)
		if (error_reporting() === 0) {
			return false;
		}

		$errorType = match($errno) {
			E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR => 'ERROR',
			E_WARNING, E_USER_WARNING => 'WARNING',
			E_NOTICE, E_USER_NOTICE => 'NOTICE',
			default => 'ERROR'
		};

		Logger::log(
			$errorType,
			"{$errstr} in {$errfile}:{$errline}",
			['errno' => $errno]
		);

		// Let PHP handle fatal errors
		if ($errno === E_ERROR || $errno === E_USER_ERROR || $errno === E_RECOVERABLE_ERROR) {
			return false;
		}

		return true;
	});

	// Custom exception handler
	set_exception_handler(function($exception) {
		Logger::critical(
			'Uncaught exception: ' . $exception->getMessage(),
			[
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
				'trace' => $exception->getTraceAsString()
			]
		);

		// Show generic error page
		http_response_code(500);
		if (file_exists(VIEW_PATH . '/errors/500.php')) {
			include VIEW_PATH . '/errors/500.php';
		} else {
			echo '500 Internal Server Error';
		}
		exit;
	});

	// Log fatal errors
	register_shutdown_function(function() {
		$error = error_get_last();
		if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
			Logger::critical(
				"Fatal error: {$error['message']}",
				[
					'file' => $error['file'],
					'line' => $error['line'],
					'type' => $error['type']
				]
			);
		}
	});
}

// Security headers
if (!headers_sent()) {
	// Prevent clickjacking
	header('X-Frame-Options: SAMEORIGIN');

	// XSS protection
	header('X-Content-Type-Options: nosniff');
	header('X-XSS-Protection: 1; mode=block');

	// Referrer policy
	header('Referrer-Policy: strict-origin-when-cross-origin');

	// Content Security Policy (basic - adjust as needed)
	if (!($_ENV['DEBUG'] ?? false)) {
		header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://*.stripe.com; img-src 'self' data: https:; font-src 'self' data:;");
	}

	// Enable gzip compression
	if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}
}
