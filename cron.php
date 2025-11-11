<?php
/**
 * Cronjob Runner
 *
 * This file provides a secure entry point for running console scripts via cron jobs.
 * It should be placed in the root directory (NOT in public_html for security).
 *
 * Usage in cron (cPanel):
 * - Send Emails (every 5 minutes):
 *   (star)/5 * * * * php /home/username/cardpoint/cron.php emails YOUR_SECRET_KEY
 *
 * - Generate Sitemap (daily at 3am):
 *   0 3 * * * php /home/username/cardpoint/cron.php sitemap YOUR_SECRET_KEY
 *
 * - Cache Card Images (weekly on Sunday at 4am):
 *   0 4 * * 0 php /home/username/cardpoint/cron.php cache-images YOUR_SECRET_KEY
 *
 * Note: Replace (star) with an asterisk in the cron syntax above
 *
 * Security:
 * - Set CRON_SECRET_KEY in your .env file to a random string
 * - This file should NOT be web-accessible (keep it outside public_html)
 */

// Get task and secret key from command line arguments
$task = $argv[1] ?? null;
$secret = $argv[2] ?? null;

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

// Run the requested task
switch ($task) {
    case 'emails':
        echo "Starting email queue processing...\n";
        require __DIR__ . '/console/send_emails.php';
        break;

    case 'sitemap':
        echo "Starting sitemap generation...\n";
        require __DIR__ . '/console/generate_sitemap.php';
        break;

    case 'cache-images':
        echo "Starting card image caching...\n";
        require __DIR__ . '/console/cache_card_images.php';
        break;

    case 'import-cards':
        echo "Starting card data import...\n";
        require __DIR__ . '/console/import_cards.php';
        break;

    default:
        echo "ERROR: Unknown task '$task'.\n\n";
        echo "Available tasks:\n";
        echo "  emails        - Process email queue\n";
        echo "  sitemap       - Generate XML sitemap\n";
        echo "  cache-images  - Cache card images\n";
        echo "  import-cards  - Import card data from API\n";
        echo "\nUsage: php cron.php <task> <secret_key>\n";
        exit(1);
}

echo "Task completed successfully.\n";
