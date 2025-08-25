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
    $filters = [
        'name' => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ];
    $filters = array_filter($filters); // remove nulls

    $products = getProducts($filters, 'p.id DESC', 50);

    view('admin/products/index', ['products' => $products], 'admin');
}, [$getAdminAuth]);

get('/admin/products/search', function () {
    $q = $_GET['q'] ?? '';
    $editions = $q ? getEditions(['q' => $q]) : [];
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

    partial('admin/products/partials/product_add', ['edition' => $edition]);
}, [$getAdminAuth]);

post('/admin/products/create', function () {
    $edition_id  = $_POST['edition_id'] ?? null;
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price'] ?? null;
    $quantity    = $_POST['quantity'] ?? null;
    $is_foil     = $_POST['is_foil'] ?? 0;

    $errors = [];

    if ($name === '') {
        $errors[] = 'Name is required';
    }

    if ($price === null || !is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a positive number';
    }

    if ($quantity === null || !is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'Quantity must be a positive number';
    }

    if ($errors) {
        http_response_code(422);
        echo '❌ ' . implode(', ', $errors);
        return;
    }

    insert_product($edition_id, $name, $description, $price, $quantity, $is_foil);

    $filters = array_filter([
        'name'      => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ]);

    $order = $_GET['sort'] ?? 'p.id DESC';

    $products = getProducts($filters, $order, 50);

    partial('admin/products/partials/products_table_body', ['products' => $products]);

}, [$getAdminAuth]);


get('/admin/products/edition/{edition_id}', function ($data) {
    $edition_id = $data['edition_id'] ?? null;
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

    if ($products) {
        partial('admin/products/partials/product_variations', ['products' => $products]);
    } else {
        $stmt = db()->prepare("
            SELECT e.*, c.name AS card_name, s.name AS set_name
            FROM editions e
            JOIN cards c ON e.card_id = c.id
            JOIN sets s ON e.set_id = s.id
            WHERE e.id = :id
        ");
        $stmt->execute([':id' => $edition_id]);
        $edition = $stmt->fetch(PDO::FETCH_ASSOC);

        partial('admin/products/partials/product_form', ['edition' => $edition]);
    }
}, [$getAdminAuth]);

get('/admin/products', function () {
    $filters = [
        'name' => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ];
    $filters = array_filter($filters);

    $products = getProducts($filters, 'p.id DESC', 50);

    if (!empty($_SERVER['HTTP_HX_REQUEST'])) {
        // htmx request → only return table body
        partial('admin/products/partials/products_table_body', ['products' => $products]);
    } else {
        // normal request → full page
        view('admin/products/index', ['products' => $products], 'admin');
    }
}, [$getAdminAuth]);

post('/admin/products/update/{product_id}', function ($data) {
    $product_id  = $data['product_id'] ?? null;
    $edition_id  = $_POST['edition_id'] ?? null;
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price'] ?? null;
    $quantity    = $_POST['quantity'] ?? null;
    $is_foil     = $_POST['is_foil'] ?? 0;

    $errors = [];

    if ($product_id === null || !is_numeric($product_id)) {
        $errors[] = 'Invalid product ID';
    }

    if ($name === '') {
        $errors[] = 'Name is required';
    }

    if ($price === null || !is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a positive number';
    }

    if ($quantity === null || !is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'Quantity must be a positive number';
    }

    if ($errors) {
        http_response_code(422);
        echo '❌ ' . implode(', ', $errors);
        return;
    }

    update_product($product_id, $edition_id, $name, $description, $price, $quantity, $is_foil);

    $filters = array_filter([
        'name'      => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ]);

    $order = $_GET['sort'] ?? 'p.id DESC';

    $products = getProducts($filters, $order, 50);

    partial('admin/products/partials/products_table_body', ['products' => $products]);

}, [$getAdminAuth]);

get('/admin/products/confirm-delete/{product_id}', function ($data) {
    $product_id = $data['product_id'] ?? null;

    if ($product_id) {
        partial('admin/products/partials/product_delete_confirm', ['product_id' => $product_id]);
    }
});

post('/admin/products/delete/{product_id}', function ($data) {
    $product_id = $data['product_id'] ?? null;

    if ($product_id) {
        delete_product($product_id);

        $filters = array_filter([
            'name'      => $_GET['name'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
        ]);

        $order = $_GET['sort'] ?? 'p.id DESC';

        $products = getProducts($filters, $order, 50);

        partial('admin/products/partials/products_table_body', ['products' => $products, 'actions' => ['edit', 'delete']]);
    } else {
        http_response_code(400);
        echo '❌ Invalid product ID';
    }
}, [$getAdminAuth]);
