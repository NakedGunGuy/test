<?php
/**
 * DEBUG SCRIPT - DELETE AFTER USE
 * This loads the REAL bootstrap and checks environment
 */

echo "<h1>Real Environment Debug</h1>";
echo "<pre>";

echo "=== BEFORE BOOTSTRAP ===\n";
echo "Current __DIR__: " . __DIR__ . "\n";
echo "Bootstrap path: " . __DIR__ . '/../bootstrap.php' . "\n";
echo "Bootstrap exists: " . (file_exists(__DIR__ . '/../bootstrap.php') ? 'YES' : 'NO') . "\n\n";

// Load the real bootstrap
require_once __DIR__ . '/../bootstrap.php';

echo "=== AFTER BOOTSTRAP ===\n";
echo "ROOT_PATH constant: " . ROOT_PATH . "\n";
echo "CORE_PATH constant: " . CORE_PATH . "\n\n";

echo "=== ENV FILE CHECK ===\n";
$env_path = ROOT_PATH . '/.env';
echo ".env path: " . $env_path . "\n";
echo ".env exists: " . (file_exists($env_path) ? 'YES' : 'NO') . "\n";
echo ".env is_readable: " . (is_readable($env_path) ? 'YES' : 'NO') . "\n\n";

if (file_exists($env_path)) {
    echo "File permissions: " . substr(sprintf('%o', fileperms($env_path)), -4) . "\n";
    echo "File size: " . filesize($env_path) . " bytes\n\n";

    // Try to read
    $contents = @file_get_contents($env_path);
    if ($contents !== false) {
        echo "SUCCESS: Can read file\n";
        echo "First 100 chars: " . substr($contents, 0, 100) . "...\n\n";
    } else {
        echo "ERROR: Cannot read file\n";
        echo "Last error: " . print_r(error_get_last(), true) . "\n\n";
    }

    // Try parse
    $parsed = @parse_ini_file($env_path);
    if ($parsed !== false) {
        echo "SUCCESS: Parsed INI file\n";
        echo "Found " . count($parsed) . " variables\n";
        echo "Keys: " . implode(', ', array_keys($parsed)) . "\n\n";
    } else {
        echo "ERROR: Failed to parse INI\n";
        echo "Last error: " . print_r(error_get_last(), true) . "\n\n";
    }
}

echo "=== $_ENV CHECK ===\n";
echo "STRIPE_SECRET_KEY in \$_ENV: " . (isset($_ENV['STRIPE_SECRET_KEY']) ? 'YES' : 'NO') . "\n";
if (isset($_ENV['STRIPE_SECRET_KEY'])) {
    echo "Value (first 30 chars): " . substr($_ENV['STRIPE_SECRET_KEY'], 0, 30) . "...\n";
} else {
    echo "All \$_ENV keys: " . implode(', ', array_keys($_ENV)) . "\n";
}

echo "\n=== DIAGNOSIS ===\n";
if (!file_exists($env_path)) {
    echo "❌ PROBLEM: .env file not found at expected location\n";
    echo "   Expected: " . $env_path . "\n";
} elseif (!is_readable($env_path)) {
    echo "❌ PROBLEM: .env file exists but is not readable\n";
    echo "   Check permissions with: ls -la " . $env_path . "\n";
} elseif (!isset($_ENV['STRIPE_SECRET_KEY'])) {
    echo "❌ PROBLEM: .env file exists and readable, but not loaded into \$_ENV\n";
    echo "   Check bootstrap.php parsing logic\n";
} else {
    echo "✅ SUCCESS: Environment loaded correctly!\n";
}

echo "</pre>";
?>
