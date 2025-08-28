<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']], 'default');
});

get('/login', function () {
    view('login');
});

post('/login', function () {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Initialize login attempts if not set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    // Reset attempts after 15 minutes
    if (time() - ($_SESSION['last_login_attempt'] ?? 0) > 900) { // 900s = 15min
        $_SESSION['login_attempts'] = 0;
    }

    // Check if too many attempts
    if ($_SESSION['login_attempts'] >= 5) {
        http_response_code(429);
        exit('Too many login attempts. Try again later.');
    }

    if (authenticate_user($username, $password)) {
        // Successful login → reset attempts
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();

        header("Location: /profile");
        exit;
    } else {
        // Failed login → increment attempts
        $_SESSION['login_attempts']++;
        $_SESSION['last_login_attempt'] = time();

        session_flash('error', 'Invalid credentials.');
        header("Location: /login");
        exit;
    }
});


get('/logout', function () {
    unset($_SESSION['user']);
    header("Location: /login");
    exit;
});
