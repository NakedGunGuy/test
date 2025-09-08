<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
	view('home', ['appName' => $_ENV['APP_NAME']]);
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

get('/register', function () {
    view('register');
});

post('/register', function () {
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
        http_response_code(422);
        exit('All fields are required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        exit('Invalid email address.');
    }

    if ($password !== $confirm_password) {
        http_response_code(422);
        exit('Passwords do not match.');
    }

    if (strlen($password) < 6) {
        http_response_code(422);
        exit('Password must be at least 6 characters long.');
    }

    $pdo = db();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
    $stmt->execute([
        ':username' => $username,
        ':email'    => $email
    ]);

    if ($stmt->fetchColumn() > 0) {
        http_response_code(422);
        exit('Username or email already exists.');
    }

    // Create user
    create_user($username, $password, $email);

    // Auto-login user
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['user'] = [
        'id'       => $user['id'],
        'username' => $user['username'],
        'email'    => $user['email']
    ];

    // Redirect to profile
    header('HX-Redirect: /profile');
    http_response_code(200);
    exit;
});

get('/cards/image/{slug}', function ($params) {
    $slug = $params['slug'];
    partial('page/products/partials/product_image_dialog', ['slug' => $slug]);
});
