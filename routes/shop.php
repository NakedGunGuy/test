<?php

$getUserAuth = function() {
    require_user_auth();
};

get('/cart', function () {
    $user = get_logged_in_user();
    $cart_id = get_user_cart_id($user['id']);
    $cart_items = get_cart_items($cart_id);
    $error = $_GET['error'] ?? null;

    view('shop/cart', ['cart' => $cart_items, 'error' => $error]);
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
        header('Location: ' . url('cart'));
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

    // Calculate cart total
    $cart_total = 0;
    foreach ($cart_items as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }

    // We'll check Stripe minimum after calculating shipping

    // Get shipping information
    $country_code = $_POST['country'] ?? '';
    $shipping_address = [
        'full_name' => $_POST['full_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? '',
        'zip' => $_POST['zip'] ?? '',
        'country' => $country_code
    ];

    // Validate required shipping fields
    foreach ($shipping_address as $field => $value) {
        if (empty($value)) {
            header('Location: ' . url('cart?error=' . urlencode('Please fill in all shipping fields including country.')));
            exit;
        }
    }

    // Calculate shipping cost server-side (prevent manipulation)
    $weight_grams = calculate_cart_weight($cart_items);
    $shipping = calculate_shipping_cost($weight_grams, $country_code);
    
    if (!$shipping) {
        header('Location: ' . url('cart?error=' . urlencode('Shipping not available to selected country.')));
        exit;
    }

    // Verify shipping calculation matches session (if available)
    if (isset($_SESSION['shipping_estimate']) && 
        ($_SESSION['shipping_estimate']['cost'] !== $shipping['cost'] ||
         $_SESSION['shipping_estimate']['country']['country_code'] !== $country_code)) {
        // Shipping calculation mismatch - recalculate
        $_SESSION['shipping_estimate'] = $shipping;
    }

    // Update cart total with shipping
    $shipping_cost = $shipping['cost'];
    $total_with_shipping = $cart_total + $shipping_cost;

    // Check Stripe minimum amount with shipping (50 euro cents)
    if (!empty($_ENV['STRIPE_SECRET_KEY']) && $total_with_shipping < 0.50) {
        header('Location: ' . url('cart?error=' . urlencode('Order total with shipping must be at least €0.50 to process payment.')));
        exit;
    }

    $order_id = create_order_with_shipping($user['id'], $cart_items, $shipping_address, $weight_grams, $shipping_cost, $shipping['tier']['id']);
    
    // Track this order in the session for additional security
    if (!isset($_SESSION['checkout_orders'])) {
        $_SESSION['checkout_orders'] = [];
    }
    $_SESSION['checkout_orders'][$order_id] = time();

    // Generate secure token for this order
    $token = generate_order_token($order_id);
    
    // Check if Stripe is configured
    if (empty($_ENV['STRIPE_SECRET_KEY'])) {
        // Demo mode - simulate payment success
        update_order_status($order_id, 'paid');
        
        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
        $stmt->execute([':cid' => $cart_id]);
        
        header('Location: ' . url('checkout/success?order_id=' . $order_id . '&token=' . $token));
        exit;
    }
    
    // Debug: Check if APP_URL is set
    if (empty($_ENV['APP_URL'])) {
        update_order_status($order_id, 'cancelled');
        header('Location: ' . url('checkout/cancel?order_id=' . $order_id . '&error=' . urlencode('APP_URL not configured')));
        exit;
    }

    // Real Stripe integration
    require_once ROOT_PATH . '/vendor/autoload.php';
    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    $lineItems = [];
    foreach ($cart_items as $item) {
        $lineItems[] = [
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => ['name' => $item['name']],
                'unit_amount'  => $item['price'] * 100, // Stripe expects cents
            ],
            'quantity' => $item['quantity'],
        ];
    }
    
    // Add shipping as a separate line item
    if ($shipping_cost > 0) {
        $lineItems[] = [
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => [
                    'name' => 'Shipping to ' . $shipping['country']['country_name'] . ' (' . $shipping['tier']['tier_name'] . ')'
                ],
                'unit_amount'  => $shipping_cost * 100, // Stripe expects cents
            ],
            'quantity' => 1,
        ];
    }

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'metadata'             => ['order_id' => $order_id],
            'success_url'          => $_ENV['APP_URL'] . '/checkout/success?order_id=' . $order_id . '&token=' . $token,
            'cancel_url'           => $_ENV['APP_URL'] . '/checkout/cancel?order_id=' . $order_id . '&token=' . $token,
        ]);

        // Redirect to Stripe checkout
        header('Location: ' . $session->url);
        exit;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle Stripe errors
        update_order_status($order_id, 'cancelled');
        header('Location: ' . url('checkout/cancel?order_id=' . $order_id . '&token=' . $token . '&error=' . urlencode($e->getMessage())));
        exit;
    }
}, [$getUserAuth]);

get('/checkout/success', function () {
    $order_id = (int)($_GET['order_id'] ?? 0);
    $token = $_GET['token'] ?? '';
    $user = get_logged_in_user();
    $pdo = db();

    if ($order_id <= 0) {
        header('Location: ' . url('cart'));
        exit;
    }

    // Verify the security token
    if (!verify_order_token($order_id, $token)) {
        // Invalid token - possible manipulation attempt
        header('Location: ' . url('cart'));
        exit;
    }

    // Verify order ownership and status
    $stmt = $pdo->prepare("SELECT user_id, status FROM orders WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        // Order doesn't exist
        header('Location: ' . url('cart'));
        exit;
    }

    if ($order['user_id'] != $user['id']) {
        // User doesn't own this order
        header('Location: ' . url('cart'));
        exit;
    }

    // Additional security: Check if this order was created in this session
    if (!isset($_SESSION['checkout_orders'][$order_id])) {
        // Order was not created in this session - possible manipulation
        header('Location: ' . url('cart'));
        exit;
    }

    if ($order['status'] !== 'pending') {
        // Order is not pending (already processed or cancelled)
        view('shop/checkout_success');
        return;
    }

    // Only mark as paid if order is pending and belongs to current user
    update_order_status($order_id, 'paid');

    // Clear cart only on successful payment
    $cart_id = get_user_cart_id($user['id']);
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
    $stmt->execute([':cid' => $cart_id]);

    // Queue order confirmation email
    send_order_confirmation_email($order_id);

    // Remove from session tracking as it's now completed
    unset($_SESSION['checkout_orders'][$order_id]);

    // Check if store is in maintenance/closed mode
    $store_status = get_setting('store_status', 'open');
    if ($store_status !== 'open') {
        view('shop/checkout_success_maintenance', ['store_status' => $store_status]);
    } else {
        view('shop/checkout_success');
    }
}, [$getUserAuth]);

get('/checkout/cancel', function () {
    $order_id = (int)($_GET['order_id'] ?? 0);
    $token = $_GET['token'] ?? '';
    $error = $_GET['error'] ?? null;
    $user = get_logged_in_user();
    $pdo = db();

    if ($order_id > 0) {
        // Verify the security token first
        if (verify_order_token($order_id, $token)) {
            // Verify order ownership before cancelling
            $stmt = $pdo->prepare("SELECT user_id, status FROM orders WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order && $order['user_id'] == $user['id'] && $order['status'] == 'pending') {
                // Only cancel if user owns the order and it's still pending
                update_order_status($order_id, 'cancelled');
            }
        }
    }

    view('shop/checkout_cancel', ['error' => $error]);
}, [$getUserAuth]);

// Calculate shipping cost for checkout
post('/checkout/calculate-shipping', function () {
    $user = get_logged_in_user();
    $cart_id = get_user_cart_id($user['id']);
    $cart = get_cart_items($cart_id);
    $country_code = $_POST['country'] ?? '';
    
    if (empty($cart) || empty($country_code)) {
        echo 'Please select a country';
        return;
    }
    
    $shipping = get_shipping_estimate($cart, $country_code);
    
    if ($shipping) {
        // Store in session for order processing
        $_SESSION['shipping_estimate'] = $shipping;
        
        echo '€' . number_format($shipping['cost'], 2) . ' - Delivery: ' .
             $shipping['country']['estimated_days_min'] . '-' . 
             $shipping['country']['estimated_days_max'] . ' days';
    } else {
        unset($_SESSION['shipping_estimate']);
        echo 'Shipping not available';
    }
}, [$getUserAuth]);


