<?php
/**
 * Abandoned Cart Cleanup
 *
 * This script returns inventory from abandoned shopping carts.
 * Carts that haven't been updated in 24+ hours are considered abandoned.
 *
 * Run this as a cronjob once per hour or once per day.
 *
 * Usage: php console/cleanup_abandoned_carts.php
 */

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/autoload.php';

echo "Starting abandoned cart cleanup...\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Clean up carts older than 24 hours (default)
    $hours_old = 24;
    echo "Cleaning up carts abandoned for {$hours_old}+ hours...\n";

    $items_returned = cleanup_abandoned_carts($hours_old);

    if ($items_returned > 0) {
        echo "[✓] Returned {$items_returned} items to inventory from abandoned carts\n";
    } else {
        echo "[·] No abandoned carts found\n";
    }

    // Get stats on current cart state
    $pdo = db();
    $stats_stmt = $pdo->query("
        SELECT
            COUNT(DISTINCT c.id) as active_carts,
            COUNT(ci.id) as total_items,
            COALESCE(SUM(ci.quantity), 0) as total_quantity
        FROM carts c
        LEFT JOIN cart_items ci ON c.id = ci.cart_id
        WHERE ci.added_at >= datetime('now', '-24 hours')
    ");
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    echo "\nCurrent cart stats (last 24h):\n";
    echo "- Active carts: {$stats['active_carts']}\n";
    echo "- Total cart items: {$stats['total_items']}\n";
    echo "- Total quantity reserved: {$stats['total_quantity']}\n";

    echo "\nCleanup complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
