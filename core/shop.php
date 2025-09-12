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
    $cart_id = get_user_cart_id($user_id);

    // check product stock
    $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $stock = (int) $stmt->fetchColumn();

    if ($stock < $quantity) {
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

    // decrement stock
    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - :qty WHERE id = :id");
    $stmt->execute([':qty' => $quantity, ':id' => $product_id]);

    return true;
}

function update_cart_quantity(int $user_id, int $product_id, int $quantity_change): void {
    $pdo = db();
    $cart_id = get_user_cart_id($user_id);

    // Get current quantity
    $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
    $stmt->execute([':cid' => $cart_id, ':pid' => $product_id]);
    $current_quantity = (int)$stmt->fetchColumn();

    $new_quantity = $current_quantity + $quantity_change;

    if ($new_quantity <= 0) {
        // Remove item completely if quantity becomes 0 or less
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
    }
}

function remove_from_cart(int $user_id, int $product_id): void {
    $pdo = db();
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

function create_order_with_shipping(int $user_id, array $cart_items, array $shipping_address): int {
    $pdo = db();

    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Create shipping address string
    $shipping_string = json_encode($shipping_address);

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, shipping_address, status)
        VALUES (:uid, :total, :shipping, 'pending')
    ");
    $stmt->execute([
        ':uid'      => $user_id,
        ':total'    => $total,
        ':shipping' => $shipping_string,
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

function update_order_status(int $order_id, string $status): void {
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $order_id]);
}

function generate_order_token(int $order_id): string {
    $secret = $_ENV['APP_SECRET'] ?? 'default_secret_change_me';
    return hash('sha256', $order_id . '|' . $secret . '|' . date('Y-m-d'));
}

function verify_order_token(int $order_id, string $token): bool {
    return hash_equals(generate_order_token($order_id), $token);
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

