<?php

$getUserAuth = function() {
    require_user_auth();
};

// Profile overview page
get('/profile', function () {
    $session_user = get_logged_in_user();

    // Get full user data from database including created_at
    $db = db();
    $user_stmt = $db->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
    $user_stmt->execute([':id' => $session_user['id']]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    // Get user statistics using the same calculation as order details
    $stmt = $db->prepare("
        SELECT
            COUNT(DISTINCT o.id) as total_orders,
            COALESCE(SUM(oi.price * oi.quantity), 0) as total_spent,
            COUNT(DISTINCT CASE WHEN o.status = 'pending' THEN o.id END) as pending_orders,
            COUNT(DISTINCT CASE WHEN o.status = 'delivered' THEN o.id END) as delivered_orders
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = :user_id
    ");
    $stmt->execute([':user_id' => $user['id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Determine user status based on order history
    $status = 'New';
    if ($stats['total_orders'] > 0) {
        if ($stats['delivered_orders'] >= 10) {
            $status = 'VIP';
        } elseif ($stats['delivered_orders'] >= 5) {
            $status = 'Premium';
        } elseif ($stats['total_orders'] >= 1) {
            $status = 'Member';
        }
    }

    view('profile/overview', [
        'user' => $user,
        'stats' => $stats,
        'user_status' => $status
    ], 'default');
}, [$getUserAuth]);

// Account settings page
get('/profile/settings', function () {
    $user = get_logged_in_user();
    view('profile/settings', ['user' => $user], 'default');
}, [$getUserAuth]);

// Order history page
get('/profile/orders', function () {
    $user = get_logged_in_user();
    $orders = get_user_orders($user['id']);
    view('profile/orders', ['user' => $user, 'orders' => $orders], 'default');
}, [$getUserAuth]);

// Update profile info (username/email)
post('/profile/update', function () {
    $user = get_logged_in_user();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];
    if ($username === '') $errors[] = 'Username cannot be empty';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';

    if ($errors) {
        http_response_code(422);
        echo implode(', ', $errors);
        return;
    }

    if (update_user_profile($user['id'], $username, $email)) {
        // Update session data
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email'] = $email;
        echo '[✓] Profile updated successfully';
    } else {
        http_response_code(500);
        echo '[✗] Failed to update profile';
    }
}, [$getUserAuth]);

// Change password
post('/profile/password', function () {
    $user = get_logged_in_user();
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (!password_verify($current, db()->query("SELECT password FROM users WHERE id = {$user['id']}")->fetchColumn())) {
        $errors[] = 'Current password is incorrect';
    }
    if ($new === '') $errors[] = 'New password cannot be empty';
    if ($new !== $confirm) $errors[] = 'Passwords do not match';

    if ($errors) {
        http_response_code(422);
        echo implode(', ', $errors);
        return;
    }

    if (update_user_password($user['id'], $new)) {
        echo '[✓] Password changed successfully';
    } else {
        http_response_code(500);
        echo '[✗] Failed to change password';
    }
}, [$getUserAuth]);

// Get user orders (HTMX endpoint for loading order list)
get('/profile/orders/list', function () {
    $user = get_logged_in_user();
    $orders = get_user_orders($user['id']);
    partial('profile/partials/order_history', ['orders' => $orders]);
}, [$getUserAuth]);

// Get individual order details
get('/profile/order/{id}', function ($id) {
    $user = get_logged_in_user();
    $order = get_user_order_details((int)$id, $user['id']);
    
    if (!$order) {
        http_response_code(404);
        echo 'Order not found';
        return;
    }
    
    partial('profile/partials/order_details', ['order' => $order]);
}, [$getUserAuth]);
