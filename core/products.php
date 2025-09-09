<?php

function insert_product($edition_id, $name, $description, $price, $quantity, $is_foil) {
    $stmt = db()->prepare("
        INSERT INTO products (edition_id, name, description, price, quantity, is_foil)
        VALUES (:edition_id, :name, :description, :price, :quantity, :is_foil)
    ");
    $stmt->execute([
        ':edition_id'  => $edition_id ?: null,
        ':name'        => $name,
        ':description' => $description,
        ':price'       => $price,
        ':quantity'    => $quantity,
        ':is_foil'     => $is_foil
    ]);
}

function update_product($id, $name, $description, $price, $quantity, $is_foil) {
    $stmt = db()->prepare("
        UPDATE products
        SET name = :name, description = :description, price = :price, quantity = :quantity, is_foil = :is_foil
        WHERE id = :id
    ");
    $stmt->execute([
        ':id'          => $id,
        ':name'        => $name,
        ':description' => $description,
        ':price'       => $price,
        ':quantity'    => $quantity,
        ':is_foil'     => $is_foil
    ]);
}

function delete_product($id) {
    $stmt = db()->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function getProducts($filters = [], $sort = 'p.id DESC', $limit = null, $offset = null) {
    $sql = "
        SELECT 
            p.*,
            e.collector_number AS edition_number,
            e.slug AS edition_slug,
            c.name AS card_name,
            s.name AS set_name,
            p.edition_id IS NULL AS is_custom,
            
            -- number of times this product exists in carts
            IFNULL(ci.cart_count, 0) AS in_carts,
            
            -- can be deleted if no carts have it
            CASE WHEN IFNULL(ci.cart_count,0) = 0 THEN 1 ELSE 0 END AS can_be_deleted,
            
            -- can reduce quantity below current quantity
            CASE WHEN IFNULL(ci.cart_count,0) <= p.quantity THEN 1 ELSE 0 END AS can_edit_quantity

        FROM products p
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        
        -- subquery to sum cart item quantities per product
        LEFT JOIN (
            SELECT product_id, SUM(quantity) AS cart_count
            FROM cart_items
            GROUP BY product_id
        ) ci ON ci.product_id = p.id
        
        WHERE 1=1
    ";

    $params = [];

    // filters
    if (isset($filters['id'])) {
        $sql .= " AND p.id = :id";
        $params[':id'] = $filters['id'];
    }


    if (isset($filters['edition_id'])) {
        $sql .= " AND p.edition_id = :edition_id";
        $params[':edition_id'] = $filters['edition_id'];
    }

    if (isset($filters['name'])) {
        $sql .= " AND p.name LIKE :name";
        $params[':name'] = "%" . $filters['name'] . "%";
    }

    if (isset($filters['min_price'])) {
        $sql .= " AND p.price >= :min_price";
        $params[':min_price'] = $filters['min_price'];
    }

    if (isset($filters['max_price'])) {
        $sql .= " AND p.price <= :max_price";
        $params[':max_price'] = $filters['max_price'];
    }

    // sorting
    if ($sort) {
        $sql .= " ORDER BY $sort";
    }

    // limits
    if ($limit !== null) {
        $sql .= " LIMIT " . intval($limit);
    }
    if ($offset !== null) {
        $sql .= " OFFSET " . intval($offset);
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

