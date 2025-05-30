<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']]);
});

route('/user/{id}', function ($params) {
	echo "User ID: " . htmlspecialchars($params['id']);
});
