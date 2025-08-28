<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']]);
});

get('/login', function () {
    view('login');
});

post('/login', function () {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!isset($_SESSION['login_attempts']) || !is_array($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = ['count' => 0, 'last_attempt' => time()];
    }

    if (time() - $_SESSION['login_attempts'][$ip]['last_attempt'] > 900) {
        $_SESSION['login_attempts'][$ip] = ['count' => 0, 'last_attempt' => time()];
    }

    if ($_SESSION['login_attempts'][$ip]['count'] >= 5) {
        http_response_code(429);
        exit('Too many login attempts. Try again later.');
    }

    if (authenticate_user($username, $password)) {
        $_SESSION['login_attempts'][$ip] = ['count' => 0, 'last_attempt' => time()];

        // HTMX redirect
        header('HX-Redirect: /profile');
        http_response_code(200);
        exit;
    } else {
        $_SESSION['login_attempts'][$ip]['count']++;
        $_SESSION['login_attempts'][$ip]['last_attempt'] = time();

        http_response_code(401);
        exit('Invalid credentials');
    }
});




get('/logout', function () {
    unset($_SESSION['user']);
    header("Location: /login");
    exit;
});
