<?php

session_start();

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../core/autoload.php';
require_once __DIR__ . '/../routes/web.php';

register_shutdown_function(function () {
	unset($_SESSION['_flash']);
});
