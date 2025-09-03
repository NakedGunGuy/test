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

function remove_from_cart(int $user_id, int $product_id): void {
    $pdo = db();
    $cart_id = get_user_cart_id($user_id);

    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
    $stmt->execute([':cid' => $cart_id, ':pid' => $product_id]);
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
        INSERT INTO order_items (order_id, product_id, name, price, quantity)
        VALUES (:oid, :pid, :name, :price, :qty)
    ");
    foreach ($cart_items as $item) {
        $stmt->execute([
            ':oid'   => $order_id,
            ':pid'   => $item['id'],
            ':name'  => $item['name'],
            ':price' => $item['price'],
            ':qty'   => $item['quantity'],
        ]);
    }

    return $order_id;
}

function update_order_status(int $order_id, string $status): void {
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $order_id]);
}

