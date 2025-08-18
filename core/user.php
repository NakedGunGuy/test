<?php

function create_user($username, $password, $email = null) {
    $pdo = db();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hash,
        ':email' => $email
    ]);
}

function authenticate_user($username, $password) {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, username, password, email FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ];
        return true;
    }

    return false;
}

function is_user_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_user_auth(): void {
    if (!is_user_logged_in()) {
        header("Location: /login");
        exit;
    }
}

function user_logout(): void {
    unset($_SESSION['user']);
    header("Location: /login");
    exit;
}
