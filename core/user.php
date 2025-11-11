<?php

function create_user($username, $password, $email = null) {
    $pdo = db();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hash,
        ':email' => $email
    ]);
}

function authenticate_user($username, $password) {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, username, password, email FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ];
        return true;
    }

    return false;
}

function is_user_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_user_auth(): void {
    if (!is_user_logged_in()) {
        error_log('User not logged in: ' . print_r($_SESSION, true));

        if (isset($_SERVER['HTTP_HX_REQUEST'])) {
            header("HX-Redirect: " . url('login'));
            http_response_code(200);
        } else {
            header("Location: " . url('login'));
        }
        exit;
    }
}

function user_logout(): void {
    unset($_SESSION['user']);
    header("Location: " . url('login'));
    exit;
}

/**
 * Get the currently logged-in user.
 *
 * @return array|null
 */
function get_logged_in_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Update user profile info.
 *
 * @param int $user_id
 * @param string $name
 * @param string $email
 * @return bool
 */
function update_user_profile(int $user_id, string $name, string $email): bool {
    $pdo = db();
    $stmt = $pdo->prepare("
        UPDATE users
        SET username = :name, email = :email
        WHERE id = :id
    ");
    return $stmt->execute([
        ':id'    => $user_id,
        ':name'  => $name,
        ':email' => $email
    ]);
}

/**
 * Change user password.
 *
 * @param int $user_id
 * @param string $new_password
 * @return bool
 */
function update_user_password(int $user_id, string $new_password): bool {
    $pdo = db();
    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        UPDATE users
        SET password = :password
        WHERE id = :id
    ");
    return $stmt->execute([
        ':id'       => $user_id,
        ':password' => $hash
    ]);
}


/**
 * Get user's order history.
 *
 * @param int $user_id
 * @return array
 */
function get_user_orders(int $user_id): array {
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT o.*, 
               COUNT(oi.id) as item_count,
               SUM(oi.price * oi.quantity) as total_amount
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = :user_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get order details with items.
 *
 * @param int $order_id
 * @param int $user_id
 * @return array|null
 */
function get_user_order_details(int $order_id, int $user_id): ?array {
    $pdo = db();

    // Get order info
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $order_id, ':user_id' => $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) return null;

    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name,
               e.collector_number, c.name as card_name, s.name as set_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $order;
}

/**
 * Create a password reset token for a user
 *
 * @param string $email
 * @return string|null The reset token or null if user not found
 */
function create_password_reset_token(string $email): ?string {
    $pdo = db();

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return null;
    }

    // Generate a secure token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token in session (simple approach without database changes)
    if (!isset($_SESSION['password_reset_tokens'])) {
        $_SESSION['password_reset_tokens'] = [];
    }

    $_SESSION['password_reset_tokens'][$token] = [
        'user_id' => $user['id'],
        'email' => $email,
        'expires_at' => $expires_at
    ];

    return $token;
}

/**
 * Validate a password reset token
 *
 * @param string $token
 * @return array|null User data if valid, null otherwise
 */
function validate_password_reset_token(string $token): ?array {
    if (!isset($_SESSION['password_reset_tokens'][$token])) {
        return null;
    }

    $reset_data = $_SESSION['password_reset_tokens'][$token];

    // Check if token has expired
    if (strtotime($reset_data['expires_at']) < time()) {
        unset($_SESSION['password_reset_tokens'][$token]);
        return null;
    }

    return $reset_data;
}

/**
 * Reset user password using a token
 *
 * @param string $token
 * @param string $new_password
 * @return bool
 */
function reset_password_with_token(string $token, string $new_password): bool {
    $reset_data = validate_password_reset_token($token);

    if (!$reset_data) {
        return false;
    }

    // Update password
    $result = update_user_password($reset_data['user_id'], $new_password);

    if ($result) {
        // Invalidate the token
        unset($_SESSION['password_reset_tokens'][$token]);
    }

    return $result;
}
