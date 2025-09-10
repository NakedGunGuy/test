<?php

$getUserAuth = function() {
    require_user_auth();
};

get('/cart', function () {
    $user = get_logged_in_user();
    $cart_id = get_user_cart_id($user['id']);
    $cart_items = get_cart_items($cart_id);

    view('shop/cart', ['cart' => $cart_items]);
}, [$getUserAuth]);

post('/cart/add', function () {
    $user = get_logged_in_user();
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity'] ?? 1);

    if ($product_id <= 0 || $quantity <= 0) {
        http_response_code(422);
        exit("Invalid product or quantity.");
    }

    if (!add_to_cart($user['id'], $product_id, $quantity)) {
        http_response_code(422);
        exit("Not enough stock.");
    }

    $cart_id = get_user_cart_id($user['id']);
    $cart_items = get_cart_items($cart_id);

    $filters = array_filter([
        'id' => $product_id,
    ]);

    $product = getProducts($filters, null, 1);

    // Multi-target HTMX response
    partial('page/products/partials/product_row', ['product' => reset($product)]);
    partial('shop/partials/cart_badge', ['cart' => $cart_items]);
    partial('shop/partials/product_purchase_section', ['product' => reset($product)]);
}, [$getUserAuth]);

post('/cart/update-quantity', function () {
    $user = get_logged_in_user();
    $product_id = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($product_id <= 0) {
        http_response_code(422);
        exit("Invalid product.");
    }

    if (!in_array($action, ['increase', 'decrease'])) {
        http_response_code(422);
        exit("Invalid action.");
    }

    if ($action === 'increase') {
        // Check stock availability
        if (!add_to_cart($user['id'], $product_id, 1)) {
            http_response_code(422);
            exit("❌ Not enough stock.");
        }
    } else {
        // Decrease quantity by 1
        update_cart_quantity($user['id'], $product_id, -1);
    }

    $cart_id = get_user_cart_id($user['id']);
    $items = get_cart_items($cart_id);

    // Multi-target HTMX response
    partial('shop/partials/cart_badge', ['cart' => $items]);
    partial('shop/partials/cart_list', ['cart' => $items]);
}, [$getUserAuth]);

post('/cart/remove', function () {
    $user = get_logged_in_user();
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id <= 0) {
        http_response_code(422);
        exit("Invalid product.");
    }

    remove_from_cart($user['id'], $product_id);

    $cart_id = get_user_cart_id($user['id']);
    $items   = get_cart_items($cart_id);

    // Multi-target HTMX response
    partial('shop/partials/cart_badge', ['cart' => $items]);
    partial('shop/partials/cart_list', ['cart' => $items]);
}, [$getUserAuth]);

get('/checkout', function () {
    $user = get_logged_in_user();
    $cart_id = get_user_cart_id($user['id']);
    $cart_items = get_cart_items($cart_id);

    if (empty($cart_items)) {
        header('Location: /cart');
        exit;
    }

    // Calculate totals
    $cart_total = 0;
    foreach ($cart_items as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }

    view('shop/checkout', [
        'cart' => $cart_items,
        'total' => $cart_total,
        'user' => $user
    ]);
}, [$getUserAuth]);

post('/checkout', function () {
    $user = get_logged_in_user();
    $pdo = db();

    $cart_id = get_user_cart_id($user['id']);
    $cart_items = get_cart_items($cart_id);

    if (empty($cart_items)) {
        http_response_code(400);
        exit("Cart is empty.");
    }

    // Get shipping information
    $shipping_address = [
        'full_name' => $_POST['full_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? '',
        'zip' => $_POST['zip'] ?? ''
    ];

    $order_id = create_order_with_shipping($user['id'], $cart_items, $shipping_address);

    // For now, let's simulate payment success without Stripe
    // In a real implementation, you would integrate with Stripe here
    
    // Mark order as paid
    update_order_status($order_id, 'paid');
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
    $stmt->execute([':cid' => $cart_id]);
    
    // Redirect to success page
    header('Location: /checkout/success?order_id=' . $order_id);
    exit;
}, [$getUserAuth]);

get('/checkout/success', function () {
    $order_id = (int)($_GET['order_id'] ?? 0);

    if ($order_id > 0) {
        update_order_status($order_id, 'paid');
    }

    // Clear cart
    $user = get_logged_in_user();
    $pdo = db();
    $cart_id = get_user_cart_id($user['id']);
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
    $stmt->execute([':cid' => $cart_id]);

    view('shop/checkout_success');
}, [$getUserAuth]);

get('/checkout/cancel', function () {
    $order_id = (int)($_GET['order_id'] ?? 0);

    if ($order_id > 0) {
        update_order_status($order_id, 'cancelled');
    }

    view('shop/checkout_cancel');
}, [$getUserAuth]);


