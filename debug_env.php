<?php
/**
 * DEBUG SCRIPT - DELETE AFTER USE
 * This checks why .env isn't loading
 */

echo "<h1>Environment Debug</h1>";
echo "<pre>";

// 1. Check ROOT_PATH
define('ROOT_PATH', __DIR__);
echo "ROOT_PATH: " . ROOT_PATH . "\n";
echo "Current directory: " . getcwd() . "\n";
echo "Script location: " . __FILE__ . "\n\n";

// 2. Check if .env exists
$env_path = ROOT_PATH . '/.env';
echo ".env path: " . $env_path . "\n";
echo ".env exists: " . (file_exists($env_path) ? 'YES' : 'NO') . "\n";
echo ".env is readable: " . (is_readable($env_path) ? 'YES' : 'NO') . "\n";

if (file_exists($env_path)) {
    // 3. Check permissions
    $perms = fileperms($env_path);
    echo ".env permissions: " . substr(sprintf('%o', $perms), -4) . "\n";
    echo ".env owner: " . posix_getpwuid(fileowner($env_path))['name'] . "\n";
    echo ".env group: " . posix_getgrgid(filegroup($env_path))['name'] . "\n\n";

    // 4. Check current user
    echo "Current PHP user: " . get_current_user() . "\n";
    echo "Current process user: " . posix_getpwuid(posix_geteuid())['name'] . "\n\n";

    // 5. Try to read the file
    echo "Trying to read .env file...\n";
    $contents = @file_get_contents($env_path);
    if ($contents === false) {
        echo "ERROR: Cannot read .env file!\n";
        echo "Error: " . error_get_last()['message'] . "\n\n";
    } else {
        echo "File size: " . strlen($contents) . " bytes\n";
        echo "First 200 chars:\n" . substr($contents, 0, 200) . "\n\n";
    }

    // 6. Try parse_ini_file
    echo "Trying parse_ini_file...\n";
    $parsed = @parse_ini_file($env_path);
    if ($parsed === false) {
        echo "ERROR: parse_ini_file failed!\n";
        $error = error_get_last();
        echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n\n";
    } else {
        echo "SUCCESS: Parsed " . count($parsed) . " variables\n";
        echo "Keys found: " . implode(', ', array_keys($parsed)) . "\n";
        echo "STRIPE_SECRET_KEY set: " . (isset($parsed['STRIPE_SECRET_KEY']) ? 'YES' : 'NO') . "\n";
        if (isset($parsed['STRIPE_SECRET_KEY'])) {
            echo "STRIPE_SECRET_KEY value: " . substr($parsed['STRIPE_SECRET_KEY'], 0, 20) . "...\n";
        }
    }
} else {
    echo "ERROR: .env file not found at: " . $env_path . "\n";
    echo "Listing directory contents:\n";
    $files = scandir(ROOT_PATH);
    foreach ($files as $file) {
        if ($file[0] === '.') {
            echo "  - " . $file . "\n";
        }
    }
}

echo "\n\nPHP open_basedir: " . ini_get('open_basedir') . "\n";
echo "PHP safe_mode: " . (ini_get('safe_mode') ? 'ON' : 'OFF') . "\n";

echo "</pre>";
?>
