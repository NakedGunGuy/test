<?php

function insert_product($edition_id, $name, $description, $price, $quantity) {
    $stmt = db()->prepare("
        INSERT INTO products (edition_id, name, description, price, quantity)
        VALUES (:edition_id, :name, :description, :price, :quantity)
    ");
    $stmt->execute([
        ':edition_id'  => $edition_id ?: null,
        ':name'        => $name,
        ':description' => $description,
        ':price'       => $price,
        ':quantity'    => $quantity,
    ]);
}

function update_product($id, $edition_id, $name, $description, $price, $quantity) {
    $stmt = db()->prepare("
        UPDATE products
        SET edition_id = :edition_id, name = :name, description = :description, price = :price, quantity = :quantity
        WHERE id = :id
    ");
    $stmt->execute([
        ':id'          => $id,
        ':edition_id'  => $edition_id ?: null,
        ':name'        => $name,
        ':description' => $description,
        ':price'       => $price,
        ':quantity'    => $quantity,
    ]);
}

function delete_product($id) {
    $stmt = db()->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
}
