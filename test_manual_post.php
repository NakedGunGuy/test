<?php
// Manual test of the bulk create endpoint

// Simulate form data structure
$_POST['products'] = [
    0 => [
        0 => [
            'edition_id' => '33',  // Use a real edition ID
            'name' => 'Test Product Manual',
            'description' => 'Test description',
            'price' => '9.99',
            'quantity' => '5'
        ]
    ]
];

// Include the necessary files
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/core/autoload.php';

// Set up database
$db = db();

// Test the bulk create logic directly
$products = $_POST['products'] ?? [];

if (empty($products)) {
    echo '❌ No products to create' . PHP_EOL;
    exit;
}

$created_count = 0;
$errors = [];

foreach ($products as $edition_index => $variants) {
    // Each edition can have multiple product variants
    foreach ($variants as $variant_index => $product_data) {
        $edition_id = $product_data['edition_id'] ?? null;
        $name = trim($product_data['name'] ?? '');
        $description = trim($product_data['description'] ?? '');
        $price = $product_data['price'] ?? null;
        $quantity = $product_data['quantity'] ?? null;
        $is_foil = isset($product_data['is_foil']) ? 1 : 0;
        $is_used = isset($product_data['is_used']) ? 1 : 0;

        echo "Processing: edition_id=$edition_id, name=$name, price=$price, quantity=$quantity" . PHP_EOL;

        // Skip empty rows
        if (!$edition_id || !$name || $price === '' || $quantity === '' || $price === null || $quantity === null) {
            echo "Skipping empty row" . PHP_EOL;
            continue;
        }

        // Convert to numbers
        $price = floatval($price);
        $quantity = intval($quantity);

        // Validate each product
        if ($price < 0) {
            $errors[] = "Edition " . ($edition_index + 1) . ", Variant " . ($variant_index + 1) . ": Price must be positive";
            continue;
        }

        if ($quantity < 0) {
            $errors[] = "Edition " . ($edition_index + 1) . ", Variant " . ($variant_index + 1) . ": Quantity must be positive";
            continue;
        }

        try {
            require_once __DIR__ . '/core/products.php';
            insert_product($edition_id, $name, $description, $price, $quantity, $is_foil, $is_used);
            $created_count++;
            echo "✅ Created product successfully" . PHP_EOL;
        } catch (Exception $e) {
            $errors[] = "Edition " . ($edition_index + 1) . ", Variant " . ($variant_index + 1) . ": " . $e->getMessage();
            echo "❌ Error: " . $e->getMessage() . PHP_EOL;
        }
    }
}

if ($errors) {
    echo '⚠️ Created ' . $created_count . ' products. Errors: ' . implode(', ', $errors) . PHP_EOL;
} else {
    echo '✅ Successfully created ' . $created_count . ' products' . PHP_EOL;
}
?>