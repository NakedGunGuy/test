<?php

$getAdminAuth = function() {
    require_admin_auth();
};

get('/admin', function () {
    view('admin/dashboard', [], 'admin');
}, [$getAdminAuth]);

get('/admin/login', function () {
    view('admin/login', [], 'default');
});

post('/admin/login', function () {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (authenticate_admin($username, $password)) {
        header("Location: /admin");
        exit;
    } else {
        session_flash('error', 'Invalid credentials.');
        header("Location: /admin/login");
        exit;
    }
});

get('/admin/logout', function () {
    unset($_SESSION['admin']);
    header("Location: /admin/login");
    exit;
});

get('/admin/products', function () {
    $stmt = db()->query("
        SELECT 
            p.*,
            e.collector_number AS edition_number,
            e.slug AS edition_slug,
            c.name AS card_name,
            s.name AS set_name,
            p.edition_id IS NULL AS is_custom
        FROM products p
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        ORDER BY p.id DESC
    ");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    view('admin/products/index', ['products' => $products], 'admin');
}, [$getAdminAuth]);

get('/admin/products/search', function () {
    $q = $_GET['q'] ?? '';
    if (trim($q) === '') {
        return;
    }
    $stmt = db()->prepare("
        SELECT e.*, c.name AS card_name, s.name AS set_name
        FROM editions e
        JOIN cards c ON e.card_id = c.id
        JOIN sets s ON e.set_id = s.id
        WHERE c.name LIKE :q OR e.collector_number LIKE :q
        LIMIT 20
    ");
    $stmt->execute([':q' => "%$q%"]);
    $editions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    partial('admin/products/partials/edition_search_results', ['editions' => $editions]);
}, [$getAdminAuth]);

get('/admin/products/add', function () {
    $edition_id = $_GET['edition_id'] ?? null;
    $edition = null;

    if ($edition_id) {
        $stmt = db()->prepare("
            SELECT e.*, c.name AS card_name, s.name AS set_name
            FROM editions e
            JOIN cards c ON e.card_id = c.id
            JOIN sets s ON e.set_id = s.id
            WHERE e.id = :id
        ");
        $stmt->execute([':id' => $edition_id]);
        $edition = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    partial('admin/products/partials/product_form', ['edition' => $edition]);
}, [$getAdminAuth]);

post('/admin/products/create', function () {
    $edition_id  = $_POST['edition_id'] ?? null;
    $name        = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price       = $_POST['price'] ?? 0;
    $quantity    = $_POST['quantity'] ?? 0;

    insert_product($edition_id, $name, $description, $price, $quantity);

    $stmt = db()->query("
        SELECT 
            p.*,
            e.collector_number AS edition_number,
            e.slug AS edition_slug,
            c.name AS card_name,
            s.name AS set_name,
            p.edition_id IS NULL AS is_custom
        FROM products p
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        ORDER BY p.id DESC
        LIMIT 1
    ");
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    view('admin/products/partials/product_row', ['product' => $product]);
}, [$getAdminAuth]);

get('/admin/products/edition/{edition_id}', function ($edition_id) {
    $stmt = db()->prepare("
        SELECT 
            p.*,
            e.collector_number AS edition_number,
            e.slug AS edition_slug,
            c.name AS card_name,
            s.name AS set_name,
            p.edition_id IS NULL AS is_custom
        FROM products p
        JOIN editions e ON p.edition_id = e.id
        JOIN cards c ON e.card_id = c.id
        JOIN sets s ON e.set_id = s.id
        WHERE p.edition_id = :edition_id
    ");
    $stmt->execute([':edition_id' => $edition_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    view('admin/products/partials/product_variations', ['products' => $products]);
}, [$getAdminAuth]);