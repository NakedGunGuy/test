<?php

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/database.php';
require_once '../core/image_cache.php';

echo "Card Image Cache Utility\n";
echo "========================\n\n";

// Get all unique edition slugs from the database
try {
    $stmt = db()->query("
        SELECT DISTINCT slug 
        FROM editions 
        WHERE slug IS NOT NULL 
        AND slug != ''
        ORDER BY slug
    ");
    
    $edition_slugs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $total_slugs = count($edition_slugs);
    
    if ($total_slugs === 0) {
        echo "No edition slugs found in database.\n";
        exit(1);
    }
    
    echo "Found {$total_slugs} unique edition slugs to process.\n";
    echo "Starting batch download...\n\n";
    
    // Progress callback
    $start_time = time();
    $progress_callback = function($current, $total, $slug) use ($start_time) {
        $elapsed = time() - $start_time;
        $percent = round(($current / $total) * 100, 1);
        $eta = $elapsed > 0 ? round(($total - $current) * ($elapsed / $current)) : 0;
        
        echo "\r[{$percent}%] {$current}/{$total} - {$slug} (ETA: {$eta}s)";
        
        if ($current % 50 === 0 || $current === $total) {
            echo "\n";
        }
    };
    
    // Batch cache all images
    $results = batch_cache_card_images($edition_slugs, $progress_callback);
    
    echo "\n\nBatch processing complete!\n";
    echo "========================\n";
    echo "Successfully cached: {$results['success']}\n";
    echo "Already cached: {$results['already_cached']}\n";
    echo "Failed: {$results['failed']}\n";
    echo "Total processed: " . ($results['success'] + $results['already_cached'] + $results['failed']) . "\n";
    
    // Show cache stats
    $stats = get_card_image_cache_stats();
    echo "\nCache Statistics:\n";
    echo "Total files: {$stats['total_files']}\n";
    echo "Total size: {$stats['total_size_mb']} MB\n";
    echo "Oldest file: {$stats['oldest_file']}\n";
    echo "Newest file: {$stats['newest_file']}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}