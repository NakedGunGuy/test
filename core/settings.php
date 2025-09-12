<?php

function get_setting($key, $default = null) {
    $stmt = db()->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->execute([':key' => $key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['value'] : $default;
}

function set_setting($key, $value) {
    $stmt = db()->prepare("
        INSERT INTO settings (key, value, updated_at) 
        VALUES (:key, :value, CURRENT_TIMESTAMP)
        ON CONFLICT(key) DO UPDATE SET 
            value = :value, 
            updated_at = CURRENT_TIMESTAMP
    ");
    return $stmt->execute([':key' => $key, ':value' => $value]);
}

function get_low_stock_threshold() {
    return (int) get_setting('low_stock_threshold', 5);
}

function check_store_status() {
    $store_status = get_setting('store_status', 'open');
    
    // If store is open, no restrictions
    if ($store_status === 'open') {
        return;
    }
    
    // Always allow admin routes
    if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0) {
        return;
    }
    
    // Always allow maintenance/closed status pages
    $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (in_array($current_path, ['/store-maintenance', '/store-closed'])) {
        return;
    }
    
    // Check if user is logged in as admin - if so, allow full access
    if (isset($_SESSION['admin'])) {
        return;
    }
    
    // Handle HTMX requests by returning appropriate response
    $is_htmx = !empty($_SERVER['HTTP_HX_REQUEST']);
    
    if ($store_status === 'maintenance') {
        if ($is_htmx) {
            header('HX-Redirect: /store-maintenance');
            exit;
        } else {
            header('Location: /store-maintenance');
            exit;
        }
    }
    
    if ($store_status === 'closed') {
        if ($is_htmx) {
            header('HX-Redirect: /store-closed');
            exit;
        } else {
            header('Location: /store-closed');
            exit;
        }
    }
}