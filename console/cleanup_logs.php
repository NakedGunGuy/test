<?php
/**
 * Log Cleanup Script
 *
 * Removes old log files to prevent disk space issues.
 * Run this as a cronjob weekly or monthly.
 *
 * Usage: php console/cleanup_logs.php [days]
 * Example: php console/cleanup_logs.php 30  (removes logs older than 30 days)
 */

require_once __DIR__ . '/../bootstrap.php';

// Get days from command line argument, default to 30
$days = isset($argv[1]) ? (int)$argv[1] : 30;

if ($days < 1) {
    echo "Error: Days must be at least 1.\n";
    exit(1);
}

echo "Archiving log files older than {$days} days...\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $result = Logger::cleanup($days);

    echo "Results:\n";
    echo "- Archived: {$result['archived']} log files\n";
    echo "- Archive location: {$result['archive_dir']}\n";
    echo "- Cutoff date: " . date('Y-m-d', time() - ($days * 86400)) . "\n";

    if ($result['archived'] > 0) {
        echo "\nOld logs have been moved to the archive folder for safekeeping.\n";
        echo "You can manually delete archived logs if needed to free up space.\n";
    } else {
        echo "\nNo logs older than {$days} days found.\n";
    }

    echo "\nLog cleanup complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
