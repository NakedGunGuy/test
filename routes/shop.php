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
    view('shop/partials/cart_badge', ['cart' => $items]);
    view('shop/partials/cart_list', ['cart' => $items]);
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

    $order_id = create_order($user['id'], $cart_items);

    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET']);

    $lineItems = [];
    foreach ($cart_items as $item) {
        $lineItems[] = [
            'price_data' => [
                'currency'     => 'usd',
                'product_data' => ['name' => $item['name']],
                'unit_amount'  => $item['price'] * 100,
            ],
            'quantity' => $item['quantity'],
        ];
    }

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items'           => $lineItems,
        'mode'                 => 'payment',
        'metadata'             => ['order_id' => $order_id],
        'success_url'          => $_ENV['APP_URL'] . '/checkout/success?order_id=' . $order_id,
        'cancel_url'           => $_ENV['APP_URL'] . '/checkout/cancel?order_id=' . $order_id,
    ]);

    header('Content-Type: application/json');
    echo json_encode(['id' => $session->id, 'url' => $session->url]);
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


