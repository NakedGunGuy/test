<?php

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/database.php';
require_once CORE_PATH . '/admin.php';

$username = $argv[1];
$password = $argv[2];

create_admin($username, $password);

echo "Admin user '{$username}' created successfully.\n";

