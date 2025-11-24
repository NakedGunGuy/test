<?php

function get_user_cart_id(int $user_id): int {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = :uid LIMIT 1");
    $stmt->execute([':uid' => $user_id]);
    $cart_id = $stmt->fetchColumn();

    if (!$cart_id) {
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, created_at) VALUES (:uid, CURRENT_TIMESTAMP)");
        $stmt->execute([':uid' => $user_id]);
        $cart_id = $pdo->lastInsertId();
    }

    return (int)$cart_id;
}

function get_cart_items(int $cart_id): array {
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT ci.id, ci.product_id, ci.quantity, p.name, p.price, p.quantity as stock
        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.cart_id = :cid
    ");
    $stmt->execute([':cid' => $cart_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_to_cart(int $user_id, int $product_id, int $quantity = 1): bool {
    $pdo = db();
    $pdo->beginTransaction();

    try {
        // Get cart_id first before transaction to avoid nested issues
        $cart_id = get_user_cart_id($user_id);

        // Update product quantity with single atomic operation to check and reduce stock
        // This prevents race conditions in SQLite
        $stmt = $pdo->prepare("
            UPDATE products
            SET quantity = quantity - :qty
            WHERE id = :id AND quantity >= :qty
        ");
        $stmt->execute([
            ':qty' => $quantity,
            ':id' => $product_id
        ]);

        // Check if the update actually happened (affected_rows = 1 means success)
        if ($stmt->rowCount() === 0) {
            // No rows were updated, which means there wasn't enough stock
            $pdo->rollback();
            return false;
        }

        // insert or update cart_items
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity, added_at, updated_at)
            VALUES (:cid, :pid, :qty, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ON CONFLICT(cart_id, product_id) DO UPDATE
            SET quantity = quantity + excluded.quantity,
                updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            ':cid' => $cart_id,
            ':pid' => $product_id,
            ':qty' => $quantity
        ]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollback();
        return false;
    }
}

function update_cart_quantity(int $user_id, int $product_id, int $quantity_change): void {
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $cart_id = get_user_cart_id($user_id);

        // Get current quantity
        $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
        $stmt->execute([':cid' => $cart_id, ':pid' => $product_id]);
        $current_quantity = (int)$stmt->fetchColumn();

        $new_quantity = $current_quantity + $quantity_change;

        if ($new_quantity <= 0) {
            // Remove item completely if quantity becomes 0 or less
            $pdo->commit(); // Commit current transaction first
            // The remove_from_cart function will handle stock return, so don't do it here
            remove_from_cart($user_id, $product_id);
        } else {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :qty WHERE cart_id = :cid AND product_id = :pid");
            $stmt->execute([':qty' => $new_quantity, ':cid' => $cart_id, ':pid' => $product_id]);

            // Handle stock changes
            if ($quantity_change < 0) {
                // Return stock to product when decreasing cart quantity
                $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + :qty WHERE id = :id");
                $stmt->execute([':qty' => abs($quantity_change), ':id' => $product_id]);
            }

            $pdo->commit();
        }
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e; // Re-throw to handle appropriately
    }
}

function remove_from_cart(int $user_id, int $product_id): void {
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $cart_id = get_user_cart_id($user_id);

        // Get the quantity being removed to return to stock
        $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
        $stmt->execute([':cid' => $cart_id, ':pid' => $product_id]);
        $removed_quantity = (int)$stmt->fetchColumn();

        // Delete from cart
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
        $stmt->execute([':cid' => $cart_id, ':pid' => $product_id]);

        // Return stock to product
        if ($removed_quantity > 0) {
            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + :qty WHERE id = :id");
            $stmt->execute([':qty' => $removed_quantity, ':id' => $product_id]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e; // Re-throw to handle appropriately
    }
}

function create_order(int $user_id, array $cart_items): int {
    $pdo = db();

    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status)
        VALUES (:uid, :total, 'pending')
    ");
    $stmt->execute([
        ':uid'   => $user_id,
        ':total' => $total,
    ]);

    $order_id = (int)$pdo->lastInsertId();

    // Insert items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price, name)
        VALUES (:oid, :pid, :qty, :price, :name)
    ");
    foreach ($cart_items as $item) {
        $stmt->execute([
            ':oid'   => $order_id,
            ':pid'   => $item['product_id'],
            ':qty'   => $item['quantity'],
            ':price' => $item['price'],
            ':name'  => $item['name'],
        ]);
    }

    return $order_id;
}

function create_order_with_shipping(int $user_id, array $cart_items, array $shipping_address, int $weight_grams = 0, float $shipping_cost = 0, int $shipping_tier_id = null): int {
    $pdo = db();

    // Calculate subtotal
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    // Total includes shipping
    $total = $subtotal + $shipping_cost;

    // Create shipping address string
    $shipping_string = json_encode($shipping_address);

    // Insert order with shipping information
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, shipping_address, status, shipping_country, shipping_weight_grams, shipping_cost, shipping_tier_id)
        VALUES (:uid, :total, :shipping, 'pending', :country, :weight, :shipping_cost, :tier_id)
    ");
    $stmt->execute([
        ':uid'           => $user_id,
        ':total'         => $total,
        ':shipping'      => $shipping_string,
        ':country'       => $shipping_address['country'] ?? null,
        ':weight'        => $weight_grams,
        ':shipping_cost' => $shipping_cost,
        ':tier_id'       => $shipping_tier_id
    ]);

    $order_id = (int)$pdo->lastInsertId();

    // Insert items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price, name)
        VALUES (:oid, :pid, :qty, :price, :name)
    ");
    foreach ($cart_items as $item) {
        $stmt->execute([
            ':oid'   => $order_id,
            ':pid'   => $item['product_id'],
            ':qty'   => $item['quantity'],
            ':price' => $item['price'],
            ':name'  => $item['name'],
        ]);
    }

    return $order_id;
}

function update_order_status(int $order_id, string $status, string $tracking_number = ''): void {
    $pdo = db();

    // Get current status before updating
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = :id");
    $stmt->execute([':id' => $order_id]);
    $old_status = $stmt->fetchColumn();

    // Handle stock management when order is cancelled
    if ($status === 'cancelled' && $old_status !== 'cancelled') {
        // Return stock to products when order is cancelled
        $stmt = $pdo->prepare("
            SELECT oi.product_id, oi.quantity
            FROM order_items oi
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($order_items as $item) {
            // Return stock to product
            $update_stock = $pdo->prepare("
                UPDATE products SET quantity = quantity + :qty
                WHERE id = :product_id
            ");
            $update_stock->execute([
                ':qty' => $item['quantity'],
                ':product_id' => $item['product_id']
            ]);
        }
    }

    // Update the order status and tracking number if provided
    if ($tracking_number) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status, tracking_number = :tracking_number WHERE id = :id");
        $stmt->execute([':status' => $status, ':tracking_number' => $tracking_number, ':id' => $order_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $order_id]);
    }

    // Send shipping email if status changed to 'shipped'
    if ($old_status !== 'shipped' && $status === 'shipped') {
        send_order_shipped_email($order_id, $tracking_number);
    }
}

function generate_order_token(int $order_id): string {
    $secret = $_ENV['APP_SECRET'] ?? 'default_secret_change_me';
    return hash('sha256', $order_id . '|' . $secret . '|' . date('Y-m-d'));
}

function verify_order_token(int $order_id, string $token): bool {
    return hash_equals(generate_order_token($order_id), $token);
}

/**
 * Return stock for abandoned carts (carts not updated in the last 24 hours)
 */
function cleanup_abandoned_carts(int $hours_old = 24): int {
    $pdo = db();

    // Find cart items that have been in cart for more than specified hours
    $stmt = $pdo->prepare("
        SELECT ci.cart_id, ci.product_id, ci.quantity
        FROM cart_items ci
        JOIN carts c ON ci.cart_id = c.id
        WHERE ci.added_at < datetime('now', '-{$hours_old} hours')
    ");
    $stmt->execute();
    $abandoned_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items_returned = 0;

    if ($abandoned_items) {
        $pdo->beginTransaction();
        try {
            foreach ($abandoned_items as $item) {
                // Return stock to the product
                $update_stock = $pdo->prepare("
                    UPDATE products SET quantity = quantity + :qty
                    WHERE id = :product_id
                ");
                $update_stock->execute([
                    ':qty' => $item['quantity'],
                    ':product_id' => $item['product_id']
                ]);

                $items_returned++;
            }

            // Remove all abandoned cart items
            $stmt = $pdo->prepare("
                DELETE FROM cart_items
                WHERE added_at < datetime('now', '-{$hours_old} hours')
            ");
            $stmt->execute();

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }
    }

    return $items_returned;
}

function get_product_order_history(int $product_id): array {
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT 
            oi.id, oi.quantity, oi.price, 
            o.id as order_id, o.status, o.created_at,
            u.username
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN users u ON o.user_id = u.id
        WHERE oi.product_id = :product_id
        AND o.status IN ('paid', 'pending')
        ORDER BY o.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([':product_id' => $product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_card_variants(string $card_name, ?int $exclude_product_id = null): array {
    $pdo = db();
    $sql = "
        SELECT 
            p.*, 
            e.collector_number AS edition_number,
            e.slug AS edition_slug,
            e.rarity,
            s.name AS set_name,
            c.name AS card_name
        FROM products p
        JOIN editions e ON p.edition_id = e.id
        JOIN cards c ON e.card_id = c.id
        JOIN sets s ON e.set_id = s.id
        WHERE c.name = :card_name
          AND p.quantity > 0
    ";

    $params = [':card_name' => $card_name];

    if ($exclude_product_id) {
        $sql .= " AND p.id != :exclude_id";
        $params[':exclude_id'] = $exclude_product_id;
    }
    
    $sql .= " ORDER BY s.name, e.collector_number";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function send_order_confirmation_email(int $order_id): bool {
    require_once MAIL_PATH . '/mailer.php';
    
    $pdo = db();
    
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        return false;
    }
    
    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            CASE 
                WHEN p.edition_id IS NOT NULL THEN
                    c.name || ' - ' || s.name || ' #' || e.collector_number || 
                    CASE WHEN e.rarity THEN ' (' || e.rarity || ')' ELSE '' END ||
                    CASE WHEN p.is_foil = 1 THEN ' [Foil]' ELSE '' END
                ELSE 
                    oi.name
            END as card_details
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse shipping address
    $shipping_address = null;
    if ($order['shipping_address']) {
        $shipping_address = json_decode($order['shipping_address'], true);
    }
    
    // Prepare email data
    $email_data = [
        'order' => [
            'id' => $order['id'],
            'status' => $order['status'],
            'total_amount' => $order['total_amount'],
            'created_at' => $order['created_at'],
            'customer_name' => $shipping_address['full_name'] ?? $order['username']
        ],
        'items' => $items,
        'shipping_address' => $shipping_address
    ];
    
    // Queue the email
    return queue_email(
        $order['email'],
        'Order Confirmation #' . $order['id'],
        'order_confirmation',
        $email_data
    );
}

function send_order_shipped_email(int $order_id, string $tracking_number = ''): bool {
    require_once MAIL_PATH . '/mailer.php';
    
    $pdo = db();
    
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        return false;
    }
    
    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            CASE 
                WHEN p.edition_id IS NOT NULL THEN
                    c.name || ' - ' || s.name || ' #' || e.collector_number || 
                    CASE WHEN e.rarity THEN ' (' || e.rarity || ')' ELSE '' END ||
                    CASE WHEN p.is_foil = 1 THEN ' [Foil]' ELSE '' END
                ELSE 
                    oi.name
            END as card_details
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse shipping address
    $shipping_address = null;
    if ($order['shipping_address']) {
        $shipping_address = json_decode($order['shipping_address'], true);
    }
    
    // Prepare email data
    $email_data = [
        'order' => [
            'id' => $order['id'],
            'status' => $order['status'],
            'total_amount' => $order['total_amount'],
            'created_at' => $order['created_at'],
            'customer_name' => $shipping_address['full_name'] ?? $order['username']
        ],
        'items' => $items,
        'shipping_address' => $shipping_address,
        'tracking_number' => $tracking_number
    ];
    
    // Queue the email
    return queue_email(
        $order['email'],
        'Order Shipped #' . $order['id'] . ($tracking_number ? ' - Tracking: ' . $tracking_number : ''),
        'order_shipped',
        $email_data
    );
}

function send_order_refunded_email(int $order_id, string $reason = ''): bool {
    require_once MAIL_PATH . '/mailer.php';

    $pdo = db();

    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        return false;
    }

    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT
            oi.*,
            CASE
                WHEN p.edition_id IS NOT NULL THEN
                    c.name || ' - ' || s.name || ' #' || e.collector_number ||
                    CASE WHEN e.rarity THEN ' (' || e.rarity || ')' ELSE '' END ||
                    CASE WHEN p.is_foil = 1 THEN ' [Foil]' ELSE '' END
                ELSE
                    oi.name
            END as card_details
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Parse shipping address
    $shipping_address = null;
    if ($order['shipping_address']) {
        $shipping_address = json_decode($order['shipping_address'], true);
    }

    // Prepare email data
    $email_data = [
        'order' => [
            'id' => $order['id'],
            'status' => $order['status'],
            'total_amount' => $order['total_amount'],
            'created_at' => $order['created_at'],
            'customer_name' => $shipping_address['full_name'] ?? $order['username']
        ],
        'items' => $items,
        'shipping_address' => $shipping_address,
        'refund_reason' => $reason
    ];

    // Queue the email
    return queue_email(
        $order['email'],
        'Order Refunded #' . $order['id'],
        'order_refunded',
        $email_data
    );
}

/**
 * Deny an order and process refund via Stripe
 *
 * @param int $order_id The order ID to deny
 * @param string $reason Optional reason for denial/refund
 * @return array ['success' => bool, 'message' => string, 'refund_id' => string|null]
 */
function deny_and_refund_order(int $order_id, string $reason = ''): array {
    $pdo = db();

    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.email, u.username
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = :id
    ");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        return ['success' => false, 'message' => 'Order not found', 'refund_id' => null];
    }

    // Check if order is in a refundable status (paid, shipped)
    if (!in_array($order['status'], ['paid', 'shipped'])) {
        return ['success' => false, 'message' => 'Order cannot be refunded (status: ' . $order['status'] . ')', 'refund_id' => null];
    }

    // Check if Stripe is configured
    $stripe_key = $_ENV['STRIPE_SECRET_KEY'] ?? $_SERVER['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY') ?: null;

    if (empty($stripe_key)) {
        // Demo mode - just cancel the order without actual Stripe refund
        update_order_status($order_id, 'cancelled');
        send_order_refunded_email($order_id, $reason ?: 'Order denied by admin (Demo mode - no actual refund processed)');

        return [
            'success' => true,
            'message' => 'Order denied and cancelled (Demo mode - Stripe not configured)',
            'refund_id' => 'demo_mode'
        ];
    }

    // Real Stripe refund
    try {
        require_once ROOT_PATH . '/vendor/autoload.php';
        \Stripe\Stripe::setApiKey($stripe_key);

        // Search for the payment intent associated with this order
        // We'll search for checkout sessions with this order_id in metadata
        $sessions = \Stripe\Checkout\Session::all([
            'limit' => 10,
        ]);

        $payment_intent_id = null;
        foreach ($sessions->data as $session) {
            if (isset($session->metadata['order_id']) && $session->metadata['order_id'] == $order_id) {
                $payment_intent_id = $session->payment_intent;
                break;
            }
        }

        if (!$payment_intent_id) {
            return ['success' => false, 'message' => 'Payment intent not found for this order', 'refund_id' => null];
        }

        // Create the refund
        $refund = \Stripe\Refund::create([
            'payment_intent' => $payment_intent_id,
            'reason' => 'requested_by_customer', // Stripe requires specific reason values
            'metadata' => [
                'order_id' => $order_id,
                'admin_reason' => $reason
            ]
        ]);

        // Update order status to cancelled
        update_order_status($order_id, 'cancelled');

        // Store refund information in order notes
        $notes = $order['notes'] ?? '';
        $notes .= "\n\n[" . date('Y-m-d H:i:s') . "] Refund processed: " . $refund->id;
        if ($reason) {
            $notes .= "\nReason: " . $reason;
        }
        $notes .= "\nRefund amount: €" . number_format($refund->amount / 100, 2);

        $stmt = $pdo->prepare("UPDATE orders SET notes = :notes WHERE id = :id");
        $stmt->execute([':notes' => $notes, ':id' => $order_id]);

        // Send refund notification email
        send_order_refunded_email($order_id, $reason);

        return [
            'success' => true,
            'message' => 'Order refunded successfully (€' . number_format($refund->amount / 100, 2) . ')',
            'refund_id' => $refund->id
        ];

    } catch (\Stripe\Exception\ApiErrorException $e) {
        return ['success' => false, 'message' => 'Stripe error: ' . $e->getMessage(), 'refund_id' => null];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'refund_id' => null];
    }
}

