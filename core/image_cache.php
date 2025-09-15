<?php

/**
 * Image caching functions for card images
 */

define('CARD_CACHE_DIR', PUBLIC_PATH . '/images/cards/');
define('CARD_API_BASE_URL', 'https://api.gatcg.com/cards/images/');

/**
 * Get the local path for a cached image, downloading if needed
 * 
 * @param string $edition_slug The edition slug for the card
 * @return string The local URL path to the cached image
 */
function get_cached_card_image($edition_slug) {
    if (empty($edition_slug)) {
        return '/assets/placeholder-card.svg'; // Fallback image
    }
    
    $local_path = CARD_CACHE_DIR . $edition_slug . '.jpg';
    $local_url = '/images/cards/' . $edition_slug . '.jpg';
    
    // Check if image already exists locally
    if (file_exists($local_path)) {
        return $local_url;
    }
    
    // Download and cache the image
    if (download_and_cache_card_image($edition_slug)) {
        return $local_url;
    }
    
    // Return fallback if download failed
    return '/assets/placeholder-card.jpg';
}

/**
 * Download an image from the API and cache it locally
 * 
 * @param string $edition_slug The edition slug for the card
 * @return bool True if successful, false otherwise
 */
function download_and_cache_card_image($edition_slug) {
    try {
        $remote_url = CARD_API_BASE_URL . $edition_slug . '.jpg';
        $local_path = CARD_CACHE_DIR . $edition_slug . '.jpg';
        
        // Ensure cache directory exists
        if (!is_dir(CARD_CACHE_DIR)) {
            mkdir(CARD_CACHE_DIR, 0755, true);
        }
        
        // Use cURL for better error handling
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Cardpoint Image Cache Bot');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $image_data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200 && $image_data !== false && !empty($image_data)) {
            // Verify it's actually an image
            $image_info = getimagesizefromstring($image_data);
            if ($image_info !== false) {
                file_put_contents($local_path, $image_data);
                return true;
            }
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Image cache error for {$edition_slug}: " . $e->getMessage());
        return false;
    }
}

/**
 * Pre-cache multiple images (for batch processing)
 * 
 * @param array $edition_slugs Array of edition slugs to cache
 * @param callable $progress_callback Optional callback for progress updates
 * @return array Results with success/failure counts
 */
function batch_cache_card_images($edition_slugs, $progress_callback = null) {
    $results = [
        'success' => 0,
        'failed' => 0,
        'already_cached' => 0
    ];
    
    $total = count($edition_slugs);
    
    foreach ($edition_slugs as $index => $edition_slug) {
        $local_path = CARD_CACHE_DIR . $edition_slug . '.jpg';
        
        if (file_exists($local_path)) {
            $results['already_cached']++;
        } elseif (download_and_cache_card_image($edition_slug)) {
            $results['success']++;
        } else {
            $results['failed']++;
        }
        
        if ($progress_callback && is_callable($progress_callback)) {
            $progress_callback($index + 1, $total, $edition_slug);
        }
        
        // Small delay to be nice to the API
        usleep(100000); // 0.1 second delay
    }
    
    return $results;
}

/**
 * Clear the image cache
 * 
 * @param int $max_age_days Remove images older than this many days (0 = all)
 * @return int Number of files removed
 */
function clear_card_image_cache($max_age_days = 0) {
    if (!is_dir(CARD_CACHE_DIR)) {
        return 0;
    }
    
    $files_removed = 0;
    $cutoff_time = time() - ($max_age_days * 24 * 60 * 60);
    
    $files = glob(CARD_CACHE_DIR . '*.jpg');
    foreach ($files as $file) {
        if ($max_age_days === 0 || filemtime($file) < $cutoff_time) {
            if (unlink($file)) {
                $files_removed++;
            }
        }
    }
    
    return $files_removed;
}

/**
 * Get cache statistics
 * 
 * @return array Cache statistics
 */
function get_card_image_cache_stats() {
    if (!is_dir(CARD_CACHE_DIR)) {
        return [
            'total_files' => 0,
            'total_size_mb' => 0,
            'oldest_file' => null,
            'newest_file' => null
        ];
    }
    
    $files = glob(CARD_CACHE_DIR . '*.jpg');
    $total_size = 0;
    $oldest_time = null;
    $newest_time = null;
    
    foreach ($files as $file) {
        $size = filesize($file);
        $time = filemtime($file);
        
        $total_size += $size;
        
        if ($oldest_time === null || $time < $oldest_time) {
            $oldest_time = $time;
        }
        
        if ($newest_time === null || $time > $newest_time) {
            $newest_time = $time;
        }
    }
    
    return [
        'total_files' => count($files),
        'total_size_mb' => round($total_size / 1024 / 1024, 2),
        'oldest_file' => $oldest_time ? date('Y-m-d H:i:s', $oldest_time) : null,
        'newest_file' => $newest_time ? date('Y-m-d H:i:s', $newest_time) : null
    ];
}