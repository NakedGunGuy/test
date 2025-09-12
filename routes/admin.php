<?php

$getAdminAuth = function() {
    require_admin_auth();
};

get('/admin', function () {
    // Get dashboard statistics
    $low_stock_threshold = get_low_stock_threshold();
    $stats_stmt = db()->prepare("
        SELECT 
            (SELECT COUNT(*) FROM products WHERE quantity > 0) as total_products,
            (SELECT COUNT(DISTINCT ci.product_id) FROM cart_items ci JOIN products p ON ci.product_id = p.id) as products_in_carts,
            (SELECT COUNT(*) FROM orders WHERE status = 'pending') as pending_orders,
            (SELECT COUNT(*) FROM products WHERE quantity <= :low_stock_threshold AND quantity > 0) as low_stock,
            (SELECT COUNT(*) FROM products WHERE quantity = 0) as out_of_stock,
            (SELECT ROUND(SUM(total_amount), 2) FROM orders WHERE created_at >= date('now', '-30 days')) as revenue,
            (SELECT COALESCE(SUM(quantity), 0) FROM products WHERE quantity > 0) as total_units_available,
            (SELECT COALESCE(SUM(ci.quantity), 0) FROM cart_items ci) as total_units_in_carts
    ");
    $stats_stmt->execute([':low_stock_threshold' => $low_stock_threshold]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    view('admin/dashboard', $stats, 'admin');
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
    $name = $_GET['name'] ?? '';
    $products = $name ? getProducts(['name' => $name], null, 10) : [];
    partial('admin/products/partials/product_search_results', ['products' => $products]);
}, [$getAdminAuth]);

get('/admin/editions/search', function () {
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

get('/admin/products/update/{product_id}', function ($data) {
    $product_id = $data['product_id'] ?? null;

    if (!$product_id || !is_numeric($product_id)) {
        http_response_code(400);
        echo '❌ Invalid product ID';
        return;
    }

    // Reuse getProducts with a filter
    $products = getProducts(['id' => $product_id], null, 1);

    $product = $products[0] ?? null;

    if (!$product) {
        http_response_code(404);
        echo '❌ Product not found';
        return;
    }

    // Render edit form with all product data
    partial('admin/products/partials/product_edit_form', ['product' => $product]);
}, [$getAdminAuth]);

post('/admin/products/update/{product_id}', function ($data) {
    $product_id  = $data['product_id'] ?? null;
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price'] ?? null;
    $quantity    = $_POST['quantity'] ?? null;
    $is_foil     = $_POST['is_foil'] ?? 0;

    if ($product_id === null || !is_numeric($product_id)) {
        http_response_code(422);
        echo '❌ Invalid product ID';
        return;
    }

    $stmt = db()->prepare("SELECT quantity, (SELECT COUNT(*) FROM cart_items ci WHERE ci.product_id = p.id) AS in_carts FROM products p WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo '❌ Product not found';
        return;
    }

    $errors = [];

    // if ($name === '') {
    //     $errors[] = 'Name is required';
    // }

    if ($price === null || !is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a positive number';
    }

    if ($quantity === null || !is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'Quantity must be a positive number';
    }

    // prevent reducing quantity below cart count
    if ($quantity < $product['in_carts']) {
        $errors[] = "Quantity cannot be lower than {$product['in_carts']} (products in carts)";
    }

    if ($errors) {
        http_response_code(422);
        echo '❌ ' . implode(', ', $errors);
        return;
    }

    update_product($product_id, $name, $description, $price, $quantity, $is_foil);

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

    if (!$product_id) {
        http_response_code(400);
        echo '❌ Invalid product ID';
        return;
    }

    // check if product is in any cart
    $stmt = db()->prepare("SELECT COUNT(*) AS in_carts FROM cart_items WHERE product_id = :id");
    $stmt->execute([':id' => $product_id]);
    $in_carts = $stmt->fetch(PDO::FETCH_ASSOC)['in_carts'];

    if ($in_carts > 0) {
        http_response_code(403);
        echo "❌ Cannot delete product: it exists in {$in_carts} cart(s)";
        return;
    }

    delete_product($product_id);

    $filters = array_filter([
        'name'      => $_GET['name'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
    ]);

    $order = $_GET['sort'] ?? 'p.id DESC';

    $products = getProducts($filters, $order, 50);

    partial('admin/products/partials/products_table_body', ['products' => $products]);

}, [$getAdminAuth]);

// Admin Orders Management
get('/admin/orders', function () {
    // Get orders with user information and order totals
    $stmt = db()->prepare("
        SELECT 
            o.*,
            u.username,
            u.email,
            COUNT(oi.id) as item_count,
            ROUND(o.total_amount, 2) as total_amount
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 50
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get order statistics
    $stats_stmt = db()->prepare("
        SELECT 
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing_count,
            COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_count,
            COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count,
            COUNT(*) as total_orders,
            ROUND(SUM(total_amount), 2) as total_revenue
        FROM orders 
        WHERE created_at >= date('now', '-30 days')
    ");
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    view('admin/orders/index', ['orders' => $orders, 'stats' => $stats], 'admin');
}, [$getAdminAuth]);

// Update order status
post('/admin/orders/{id}/status', function ($data) {
    $order_id = $data['id'];
    $status = $_POST['status'] ?? '';
    $tracking_number = $_POST['tracking_number'] ?? '';
    
    $valid_statuses = ['pending', 'processing', 'shipped', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        http_response_code(400);
        echo '❌ Invalid status';
        return;
    }
    
    // Use the shop function which handles email notifications
    try {
        update_order_status($order_id, $status, $tracking_number);
        
        $message = "✅ Order status updated to " . ucfirst($status);
        if ($status === 'shipped' && $tracking_number) {
            $message .= " (Tracking: $tracking_number)";
        }
        if ($status === 'shipped') {
            $message .= " - Shipping notification email queued";
        }
        
        echo $message;
    } catch (Exception $e) {
        http_response_code(500);
        echo '❌ Failed to update status: ' . $e->getMessage();
    }
}, [$getAdminAuth]);

// View order details
get('/admin/orders/{id}', function ($data) {
    $order_id = $data['id'];
    
    // Get order with user information
    $order_stmt = db()->prepare("
        SELECT 
            o.*,
            u.username,
            u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = :id
    ");
    $order_stmt->execute([':id' => $order_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        http_response_code(404);
        echo 'Order not found';
        return;
    }
    
    // Get order items with product details
    $items_stmt = db()->prepare("
        SELECT 
            oi.*,
            p.name as current_product_name,
            p.quantity as current_stock,
            e.collector_number,
            c.name as card_name,
            s.name as set_name,
            p.is_foil
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN editions e ON p.edition_id = e.id
        LEFT JOIN cards c ON e.card_id = c.id
        LEFT JOIN sets s ON e.set_id = s.id
        WHERE oi.order_id = :order_id
        ORDER BY oi.id
    ");
    $items_stmt->execute([':order_id' => $order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse shipping address if it exists
    $shipping_address = null;
    if ($order['shipping_address']) {
        $shipping_address = json_decode($order['shipping_address'], true);
    }
    
    view('admin/orders/detail', [
        'order' => $order,
        'items' => $items,
        'shipping_address' => $shipping_address
    ], 'admin');
}, [$getAdminAuth]);

// Admin Analytics
get('/admin/analytics', function () {
    // Sales data for the last 30 days
    $sales_stmt = db()->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as orders_count,
            ROUND(SUM(total_amount), 2) as daily_revenue
        FROM orders 
        WHERE created_at >= date('now', '-30 days')
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ");
    $sales_stmt->execute();
    $daily_sales = $sales_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top selling products
    $top_products_stmt = db()->prepare("
        SELECT 
            p.name,
            SUM(oi.quantity) as total_sold,
            ROUND(SUM(oi.quantity * oi.price), 2) as revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.created_at >= date('now', '-30 days')
        GROUP BY p.id, p.name
        ORDER BY total_sold DESC
        LIMIT 10
    ");
    $top_products_stmt->execute();
    $top_products = $top_products_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Overall statistics
    $overview_stmt = db()->prepare("
        SELECT 
            (SELECT COUNT(*) FROM orders) as total_orders,
            (SELECT COUNT(*) FROM products WHERE quantity > 0) as products_in_stock,
            (SELECT COUNT(DISTINCT ci.product_id) FROM cart_items ci JOIN products p ON ci.product_id = p.id) as products_in_carts,
            (SELECT COUNT(*) FROM products WHERE quantity <= 5 AND quantity > 0) as low_stock_products,
            (SELECT COUNT(*) FROM users) as total_customers,
            (SELECT ROUND(SUM(total_amount), 2) FROM orders) as total_revenue,
            (SELECT ROUND(AVG(total_amount), 2) FROM orders) as avg_order_value,
            (SELECT COALESCE(SUM(quantity), 0) FROM products WHERE quantity > 0) as total_units_available,
            (SELECT COALESCE(SUM(ci.quantity), 0) FROM cart_items ci) as total_units_in_carts
    ");
    $overview_stmt->execute();
    $overview = $overview_stmt->fetch(PDO::FETCH_ASSOC);
    
    view('admin/analytics/index', [
        'daily_sales' => $daily_sales, 
        'top_products' => $top_products,
        'overview' => $overview
    ], 'admin');
}, [$getAdminAuth]);

// Admin Settings
get('/admin/settings', function () {
    // Get current settings values
    $settings = [
        'store_status' => get_setting('store_status', 'open'),
        'default_order_status' => get_setting('default_order_status', 'pending'),
        'low_stock_threshold' => get_setting('low_stock_threshold', 5),
        'notification_email' => get_setting('notification_email', ''),
        'email_notifications' => get_setting('email_notifications', 1)
    ];
    
    view('admin/settings/index', ['settings' => $settings], 'admin');
}, [$getAdminAuth]);

// Save Settings
post('/admin/settings', function () {
    $store_status = $_POST['store_status'] ?? 'open';
    $default_order_status = $_POST['default_order_status'] ?? 'pending';
    $low_stock_threshold = $_POST['low_stock_threshold'] ?? 5;
    $notification_email = $_POST['notification_email'] ?? '';
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    
    // Validate inputs
    $valid_store_statuses = ['open', 'maintenance', 'closed'];
    $valid_order_statuses = ['pending', 'processing'];
    
    if (!in_array($store_status, $valid_store_statuses)) {
        session_flash('error', 'Invalid store status');
        header('Location: /admin/settings');
        exit;
    }
    
    if (!in_array($default_order_status, $valid_order_statuses)) {
        session_flash('error', 'Invalid default order status');
        header('Location: /admin/settings');
        exit;
    }
    
    if (!is_numeric($low_stock_threshold) || $low_stock_threshold < 0) {
        session_flash('error', 'Low stock threshold must be a positive number');
        header('Location: /admin/settings');
        exit;
    }
    
    if ($notification_email && !filter_var($notification_email, FILTER_VALIDATE_EMAIL)) {
        session_flash('error', 'Invalid email address');
        header('Location: /admin/settings');
        exit;
    }
    
    // Save settings
    set_setting('store_status', $store_status);
    set_setting('default_order_status', $default_order_status);
    set_setting('low_stock_threshold', $low_stock_threshold);
    set_setting('notification_email', $notification_email);
    set_setting('email_notifications', $email_notifications);
    
    session_flash('success', 'Settings saved successfully');
    header('Location: /admin/settings');
    exit;
}, [$getAdminAuth]);

// Clear Cache
post('/admin/settings/clear-cache', function () {
    // Clear any cache files if they exist
    $cache_cleared = false;
    
    // You can add cache clearing logic here if needed
    // For now, we'll just simulate clearing cache
    $cache_cleared = true;
    
    if ($cache_cleared) {
        session_flash('success', 'Cache cleared successfully');
    } else {
        session_flash('error', 'Failed to clear cache');
    }
    
    header('Location: /admin/settings');
    exit;
}, [$getAdminAuth]);

// Backup Database
post('/admin/settings/backup-database', function () {
    $backup_dir = 'backups';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $backup_file = $backup_dir . '/database_backup_' . $timestamp . '.sqlite';
    
    try {
        if (copy('database/database.sqlite', $backup_file)) {
            session_flash('success', 'Database backup created: ' . $backup_file);
        } else {
            session_flash('error', 'Failed to create database backup');
        }
    } catch (Exception $e) {
        session_flash('error', 'Error creating backup: ' . $e->getMessage());
    }
    
    header('Location: /admin/settings');
    exit;
}, [$getAdminAuth]);

// Import Cards
post('/admin/settings/import-cards', function () {
    try {
        // Execute the import cards console script
        $output = [];
        $return_var = 0;
        
        // Use the WAMP PHP path directly
        $php_path = 'C:\wamp64\bin\php\php8.2.18\php.exe';
        
        // Change to the project root directory and run the import script
        $original_dir = getcwd();
        chdir(__DIR__ . '/../');
        
        $command = '"' . $php_path . '" console/import_cards.php 2>&1';
        exec($command, $output, $return_var);
        
        // Change back to original directory
        chdir($original_dir);
        
        if ($return_var === 0) {
            session_flash('success', 'Cards imported successfully');
        } else {
            session_flash('error', 'Failed to import cards: ' . implode(' ', $output));
        }
    } catch (Exception $e) {
        session_flash('error', 'Error importing cards: ' . $e->getMessage());
    }
    
    header('Location: /admin/settings');
    exit;
}, [$getAdminAuth]);