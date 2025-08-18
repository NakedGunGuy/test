<?php

$getAdminAuth = function() {
    require_admin_auth();
};

get('/admin', function () {
    view('admin/dashboard', [], 'admin');
}, [$getAdminAuth]);

get('/admin/login', function () {
    view('admin/login');
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

// Logout
get('/admin/logout', function () {
    unset($_SESSION['admin']);
    header("Location: /admin/login");
    exit;
});

