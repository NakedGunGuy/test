<?php

session_start();

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/autoload.php';
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/cms.php';

if (!route(null, null)) {
    http_response_code(404);
    echo "404 - Page Not Found";
}

register_shutdown_function(function () {
	unset($_SESSION['_flash']);
});
