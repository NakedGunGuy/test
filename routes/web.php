<?php

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']]);
});

route('/user/{id}', function ($params) {
	echo "User ID: " . htmlspecialchars($params['id']);
});

if (!route(null, null)) {
	http_response_code(404);
	echo "404 - Page Not Found";
}
