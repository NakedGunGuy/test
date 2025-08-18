<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']], 'default');
});

route('/user/{id}', function ($params) {
	echo "User ID: " . htmlspecialchars($params['id']);
});

route('/search', function () {
	view('search_products', [], 'default');
});
