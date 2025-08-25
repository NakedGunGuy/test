<?php

function getProducts($filters = [], $sort = 'p.id DESC', $limit = null, $offset = null) {
    $sql = "
        SELECT 
            p.*,
            e.collector_number AS edition_number,
            e.slug AS edition_slug,
            c.name AS card_name,
            s.name AS set_name,
            p.edition_id IS NULL AS is_custom
            -- count how many cart items for this product
            --(SELECT COUNT(*) FROM cart_items ci WHERE ci.product_id = p.id) AS cart_count,
            -- can_be_deleted is true (1) if cart_count = 0
            --(SELECT COUNT(*) = 0 FROM cart_items ci WHERE ci.product_id = p.id) AS can_be_deleted
        FROM products p
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        WHERE 1=1
    ";

    $params = [];

    // filters
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

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $products;
}
