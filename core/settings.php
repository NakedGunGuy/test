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