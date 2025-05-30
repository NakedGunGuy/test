<?php

route('/admin/login', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['username'] === 'admin' && $_POST['password'] === 'password') {
            $_SESSION['user'] = 'admin';
            header("Location: /admin");
            exit;
        } else {
            echo "Invalid credentials.";
        }
    }
    view('admin/login');
});

route('/admin/logout', function () {
    unset($_SESSION['user']);
    header("Location: /admin/login");
    exit;
});

route('/admin', function () {
    require_auth();
    view('admin/dashboard');
});