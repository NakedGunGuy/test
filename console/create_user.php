<?php

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/database.php';
require_once CORE_PATH . '/user.php';

$username = $argv[1];
$password = $argv[2];

create_user($username, $password);

echo "User '{$username}' created successfully.\n";

