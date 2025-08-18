<?php

function create_admin($username, $password) {
    $pdo = db();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hash
    ]);
}

function authenticate_admin($username, $password) {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = [
            'id' => $admin['id'],
            'username' => $admin['username']
        ];
        return true;
    }

    return false;
}

function is_admin_logged_in(): bool {
    return isset($_SESSION['admin']);
}

function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        header("Location: /admin/login");
        exit;
    }
}

function admin_logout(): void {
    unset($_SESSION['admin']);
    header("Location: /admin/login");
    exit;
}
