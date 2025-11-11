<?php
/**
 * Master Cronjob Runner
 *
 * Single cronjob that runs every minute and executes tasks based on schedule.
 * This is much simpler for shared hosting - only need ONE cronjob!
 *
 * Usage in cron (cPanel):
 * * * * * * php /home/username/cardpoint/cron_master.php YOUR_SECRET_KEY
 *
 * Configuration:
 * Edit the $schedule array below to customize when tasks run.
 */

// Get secret key from command line
$secret = $argv[1] ?? null;

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line.\n");
}

// Load environment
require_once __DIR__ . '/bootstrap.php';

// Verify secret key
$expectedSecret = $_ENV['CRON_SECRET_KEY'] ?? null;
if (!$expectedSecret) {
    die("ERROR: CRON_SECRET_KEY not set in .env file.\n");
}

if ($secret !== $expectedSecret) {
    die("ERROR: Invalid secret key.\n");
}

// ====================================
// SCHEDULE CONFIGURATION
// ====================================
// Customize these schedules as needed
$schedule = [
    'emails' => [
        'frequency' => 'every_5_minutes',
        'script' => __DIR__ . '/console/send_emails.php',
        'description' => 'Process email queue'
    ],
    'sitemap' => [
        'frequency' => 'daily',
        'time' => '03:00',  // Run at 3:00 AM
        'script' => __DIR__ . '/console/generate_sitemap.php',
        'description' => 'Generate XML sitemap'
    ],
    'cache-images' => [
        'frequency' => 'weekly',
        'day' => 'sunday',  // Day of week
        'time' => '04:00',  // Run at 4:00 AM
        'script' => __DIR__ . '/console/cache_card_images.php',
        'description' => 'Cache card images'
    ],
    'import-cards' => [
        'frequency' => 'weekly',
        'day' => 'monday',  // Day of week
        'time' => '02:00',  // Run at 2:00 AM
        'script' => __DIR__ . '/console/import_cards.php',
        'description' => 'Import card data from API'
    ],
    'cleanup-logs' => [
        'frequency' => 'monthly',
        'day' => 1,  // Day of month
        'time' => '05:00',  // Run at 5:00 AM
        'script' => __DIR__ . '/console/cleanup_logs.php',
        'description' => 'Archive old log files'
    ]
];

// ====================================
// TASK SCHEDULER
// ====================================

$now = new DateTime();
$currentTime = $now->format('H:i');
$currentDay = strtolower($now->format('l'));
$currentDate = (int)$now->format('d');
$currentMinute = (int)$now->format('i');

$tasksRun = 0;
$tasksSkipped = 0;

echo "Cron Master - " . $now->format('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 50) . "\n";

foreach ($schedule as $taskName => $config) {
    $shouldRun = false;
    $reason = '';

    switch ($config['frequency']) {
        case 'every_5_minutes':
            // Run every 5 minutes (0, 5, 10, 15, ...)
            if ($currentMinute % 5 === 0) {
                $shouldRun = true;
                $reason = 'Every 5 minutes';
            }
            break;

        case 'every_10_minutes':
            if ($currentMinute % 10 === 0) {
                $shouldRun = true;
                $reason = 'Every 10 minutes';
            }
            break;

        case 'every_15_minutes':
            if ($currentMinute % 15 === 0) {
                $shouldRun = true;
                $reason = 'Every 15 minutes';
            }
            break;

        case 'hourly':
            if ($currentMinute === 0) {
                $shouldRun = true;
                $reason = 'Every hour';
            }
            break;

        case 'daily':
            $taskTime = $config['time'] ?? '00:00';
            if ($currentTime === $taskTime) {
                $shouldRun = true;
                $reason = "Daily at {$taskTime}";
            }
            break;

        case 'weekly':
            $taskDay = strtolower($config['day'] ?? 'sunday');
            $taskTime = $config['time'] ?? '00:00';
            if ($currentDay === $taskDay && $currentTime === $taskTime) {
                $shouldRun = true;
                $reason = "Weekly on {$taskDay} at {$taskTime}";
            }
            break;

        case 'monthly':
            $taskDate = (int)($config['day'] ?? 1);
            $taskTime = $config['time'] ?? '00:00';
            if ($currentDate === $taskDate && $currentTime === $taskTime) {
                $shouldRun = true;
                $reason = "Monthly on day {$taskDate} at {$taskTime}";
            }
            break;
    }

    if ($shouldRun) {
        echo "\n[RUN] {$taskName}: {$config['description']}\n";
        echo "      Schedule: {$reason}\n";
        echo "      " . str_repeat('-', 46) . "\n";

        if (file_exists($config['script'])) {
            ob_start();
            require $config['script'];
            $output = ob_get_clean();

            // Show last 5 lines of output
            $lines = array_filter(explode("\n", trim($output)));
            $lastLines = array_slice($lines, -5);
            foreach ($lastLines as $line) {
                echo "      " . $line . "\n";
            }

            $tasksRun++;
        } else {
            echo "      ERROR: Script not found: {$config['script']}\n";
        }
    } else {
        $tasksSkipped++;
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "Summary:\n";
echo "- Tasks executed: {$tasksRun}\n";
echo "- Tasks skipped: {$tasksSkipped}\n";
echo "- Total tasks: " . count($schedule) . "\n";

if ($tasksRun === 0) {
    echo "\nNo tasks scheduled for this time.\n";
}

echo "\nCron master complete.\n";
