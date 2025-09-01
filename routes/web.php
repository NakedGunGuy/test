<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']], 'default');
});

get('/discover', function () {
    $filters = [
        'name' => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ];
    $filters = array_filter($filters);

    $products = getProducts($filters, 'p.id DESC', 50);

    view('discover', ['products' => $products], 'default');
});
