<?php

/**
 * Shipping management functions
 */

define('CARD_WEIGHT_GRAMS', 2); // Average weight per card in grams

/**
 * Get all enabled shipping countries
 */
function get_shipping_countries() {
    $db = db();
    $stmt = $db->query("
        SELECT * FROM shipping_countries 
        WHERE is_enabled = 1 
        ORDER BY country_name ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all shipping weight tiers
 */
function get_shipping_weight_tiers() {
    $db = db();
    $stmt = $db->query("
        SELECT * FROM shipping_weight_tiers 
        WHERE is_enabled = 1 
        ORDER BY sort_order ASC, max_weight_kg ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Calculate shipping cost for a given weight and country
 */
function calculate_shipping_cost($weight_grams, $country_code) {
    $weight_kg = $weight_grams / 1000;
    
    $db = db();
    
    // Check if country is supported
    $stmt = $db->prepare("
        SELECT * FROM shipping_countries 
        WHERE country_code = :country_code AND is_enabled = 1
    ");
    $stmt->execute([':country_code' => $country_code]);
    $country = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$country) {
        return null; // Country not supported
    }
    
    // Find appropriate weight tier for this specific country
    $stmt = $db->prepare("
        SELECT swt.* FROM shipping_weight_tiers swt
        WHERE swt.country_id = :country_id 
        AND swt.is_enabled = 1 
        AND swt.max_weight_kg >= :weight_kg
        ORDER BY swt.max_weight_kg ASC
        LIMIT 1
    ");
    $stmt->execute([
        ':country_id' => $country['id'],
        ':weight_kg' => $weight_kg
    ]);
    $tier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tier) {
        return null; // Weight exceeds maximum tier for this country
    }
    
    return [
        'cost' => $tier['price'],
        'tier' => $tier,
        'country' => $country,
        'weight_kg' => $weight_kg
    ];
}

/**
 * Calculate total weight for cart items (cards are 2g each)
 */
function calculate_cart_weight($cart_items) {
    $total_weight_grams = 0;
    
    foreach ($cart_items as $item) {
        $quantity = $item['quantity'];
        $total_weight_grams += $quantity * CARD_WEIGHT_GRAMS;
    }
    
    return $total_weight_grams;
}

/**
 * Get shipping estimate for cart
 */
function get_shipping_estimate($cart_items, $country_code) {
    $weight_grams = calculate_cart_weight($cart_items);
    return calculate_shipping_cost($weight_grams, $country_code);
}

/**
 * Admin functions
 */

/**
 * Get all countries for admin (enabled and disabled)
 */
function get_all_shipping_countries() {
    $db = db();
    $stmt = $db->query("
        SELECT * FROM shipping_countries 
        ORDER BY country_name ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all weight tiers for admin (enabled and disabled)
 */
function get_all_shipping_weight_tiers() {
    $db = db();
    $stmt = $db->query("
        SELECT swt.*, sc.country_name, sc.country_code 
        FROM shipping_weight_tiers swt
        JOIN shipping_countries sc ON swt.country_id = sc.id
        ORDER BY sc.country_name ASC, swt.sort_order ASC, swt.max_weight_kg ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get weight tiers for a specific country
 */
function get_shipping_weight_tiers_by_country($country_id) {
    $db = db();
    $stmt = $db->prepare("
        SELECT swt.*, sc.country_name, sc.country_code 
        FROM shipping_weight_tiers swt
        JOIN shipping_countries sc ON swt.country_id = sc.id
        WHERE swt.country_id = :country_id AND swt.is_enabled = 1
        ORDER BY swt.sort_order ASC, swt.max_weight_kg ASC
    ");
    $stmt->execute([':country_id' => $country_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update shipping country
 */
function update_shipping_country($id, $country_name, $estimated_days_min, $estimated_days_max, $is_enabled) {
    $db = db();
    $stmt = $db->prepare("
        UPDATE shipping_countries 
        SET country_name = :country_name,
            estimated_days_min = :estimated_days_min,
            estimated_days_max = :estimated_days_max,
            is_enabled = :is_enabled,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    
    return $stmt->execute([
        ':id' => $id,
        ':country_name' => $country_name,
        ':estimated_days_min' => $estimated_days_min,
        ':estimated_days_max' => $estimated_days_max,
        ':is_enabled' => $is_enabled ? 1 : 0
    ]);
}

/**
 * Add new weight tier
 */
function add_shipping_weight_tier($country_id, $tier_name, $max_weight_kg, $price, $sort_order = 0) {
    $db = db();
    $stmt = $db->prepare("
        INSERT INTO shipping_weight_tiers (country_id, tier_name, max_weight_kg, price, sort_order)
        VALUES (:country_id, :tier_name, :max_weight_kg, :price, :sort_order)
    ");
    
    return $stmt->execute([
        ':country_id' => $country_id,
        ':tier_name' => $tier_name,
        ':max_weight_kg' => $max_weight_kg,
        ':price' => $price,
        ':sort_order' => $sort_order
    ]);
}

/**
 * Update weight tier
 */
function update_shipping_weight_tier($id, $country_id, $tier_name, $max_weight_kg, $price, $is_enabled, $sort_order = 0) {
    $db = db();
    $stmt = $db->prepare("
        UPDATE shipping_weight_tiers 
        SET country_id = :country_id,
            tier_name = :tier_name,
            max_weight_kg = :max_weight_kg,
            price = :price,
            is_enabled = :is_enabled,
            sort_order = :sort_order,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    
    return $stmt->execute([
        ':id' => $id,
        ':country_id' => $country_id,
        ':tier_name' => $tier_name,
        ':max_weight_kg' => $max_weight_kg,
        ':price' => $price,
        ':is_enabled' => $is_enabled ? 1 : 0,
        ':sort_order' => $sort_order
    ]);
}

/**
 * Delete weight tier
 */
function delete_shipping_weight_tier($id) {
    $db = db();
    $stmt = $db->prepare("DELETE FROM shipping_weight_tiers WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}