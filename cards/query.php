<?php

function getEditions($filters = [], $sort = 'e.id DESC', $limit = 20, $offset = null) {
    $sql = "
        SELECT e.*, c.name AS card_name, s.name AS set_name
        FROM editions e
        JOIN cards c ON e.card_id = c.id
        JOIN sets s ON e.set_id = s.id
        WHERE 1=1
    ";

    $params = [];

    if (isset($filters['q'])) {
        $sql .= " AND (c.name LIKE :q OR e.collector_number LIKE :q)";
        $params[':q'] = "%" . $filters['q'] . "%";
    }

    if (isset($filters['set_id'])) {
        $sql .= " AND e.set_id = :set_id";
        $params[':set_id'] = $filters['set_id'];
    }

    if (isset($filters['card_id'])) {
        $sql .= " AND e.card_id = :card_id";
        $params[':card_id'] = $filters['card_id'];
    }

    if ($sort) {
        $sql .= " ORDER BY $sort";
    }

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
