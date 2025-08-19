<?php

session_start();

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/autoload.php';
require_once ROUTE_PATH . '/autoload.php';

if (!route(null)) {
    http_response_code(404);
    echo "404 - Page Not Found";
}

register_shutdown_function(function () {
	unset($_SESSION['_flash']);
});
