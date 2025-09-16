<?php

try {
    route_page_tree(build_page_tree());
} catch (Exception $e) {}

route('/', function () {
    $seo_data = get_seo_meta('home');
    view('home', [
        'appName' => $_ENV['APP_NAME'],
        'seo_data' => $seo_data
    ]);
});

get('/discover', function () {
    $filters = [
        'name' => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ];
    $filters = array_filter($filters);

    // Handle per_page setting from session
    if (isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [10, 25, 50, 100])) {
        $_SESSION['discover_per_page'] = (int)$_GET['per_page'];
    }

    // Handle view preference from session (default to 'grid')
    if (!isset($_SESSION['view_preference'])) {
        $_SESSION['view_preference'] = 'grid';
    }
    
    // Pagination parameters
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = $_SESSION['discover_per_page'] ?? 10;
    $offset = ($page - 1) * $per_page;

    // Get total count and products
    $total_products = getProductsCount($filters);
    $products = getProducts($filters, 'p.id DESC', $per_page, $offset);

    // Calculate pagination info
    $total_pages = ceil($total_products / $per_page);

    // Get filters without per_page for pagination links
    $filter_params = array_diff_key($_GET, array_flip(['per_page', 'page']));

    $seo_data = get_seo_meta('discover');

    view('discover', [
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $per_page,
            'total_products' => $total_products,
            'total_pages' => $total_pages,
            'filters' => $filter_params // Pass filters excluding per_page and page
        ],
        'seo_data' => $seo_data
    ], 'default');
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

get('/product/{id}', function ($params) {
    $product_id = (int)$params['id'];
    
    if ($product_id <= 0) {
        http_response_code(404);
        echo "Product not found";
        return;
    }

    // Get product details
    $filters = ['id' => $product_id];
    $products = getProducts($filters);
    
    if (empty($products)) {
        http_response_code(404);
        echo "Product not found";
        return;
    }
    
    $product = $products[0];
    
    // Get order history for this product
    $order_history = get_product_order_history($product_id);
    
    // Get other products from same card (different editions/variants)
    $card_variants = [];
    if (!$product['is_custom'] && $product['card_name']) {
        $card_variants = get_card_variants($product['card_name'], $product_id);
    }

    // Prepare SEO data and schema markup
    $seo_data = get_seo_meta('product', ['product' => $product]);
    $breadcrumbs = [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Discover', 'url' => '/discover'],
        ['name' => $product['card_name'] ?? $product['name'], 'url' => '/product/' . $product_id]
    ];

    view('shop/product_detail', [
        'product' => $product,
        'order_history' => $order_history,
        'card_variants' => $card_variants,
        'seo_data' => $seo_data,
        'schemas' => [
            generate_schema_markup('organization'),
            generate_schema_markup('product', ['product' => $product]),
            generate_schema_markup('breadcrumblist', ['breadcrumbs' => $breadcrumbs])
        ]
    ]);
});

get('/cards/image/{slug}', function ($params) {
    $slug = $params['slug'];
    partial('page/products/partials/product_image_dialog', ['slug' => $slug]);
});

post('/set-view-preference', function () {
    $view = $_POST['view'] ?? '';

    if (in_array($view, ['grid', 'list', 'box'])) {
        $_SESSION['view_preference'] = $view;
        http_response_code(200);
        echo 'View preference updated';
    } else {
        http_response_code(400);
        echo 'Invalid view preference';
    }
});

get('/products/search', function () {
    $name = $_GET['name'] ?? '';
    $products = $name ? getProducts(['name' => $name], null, 10) : [];
    partial('admin/products/partials/product_search_results', ['products' => $products]);
});

get('/store-maintenance', function () {
    view('maintenance');
});

get('/store-closed', function () {
    view('closed');
});
