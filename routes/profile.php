<?php

$getUserAuth = function() {
    require_user_auth();
};

// Show profile page
get('/profile', function () {
    $user = get_current_user();
    view('profile/index', ['user' => $user], 'default');
}, [$getUserAuth]);

// Update profile info (username/email)
post('/profile/update', function () {
    $user = get_current_user();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];
    if ($username === '') $errors[] = 'Username cannot be empty';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';

    if ($errors) {
        http_response_code(422);
        echo implode(', ', $errors);
        return;
    }

    if (update_user_profile($user['id'], $username, $email)) {
        // Update session data
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email'] = $email;
        echo '✅ Profile updated successfully';
    } else {
        http_response_code(500);
        echo '❌ Failed to update profile';
    }
}, [$getUserAuth]);

// Change password
post('/profile/password', function () {
    $user = get_current_user();
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (!password_verify($current, db()->query("SELECT password FROM users WHERE id = {$user['id']}")->fetchColumn())) {
        $errors[] = 'Current password is incorrect';
    }
    if ($new === '') $errors[] = 'New password cannot be empty';
    if ($new !== $confirm) $errors[] = 'Passwords do not match';

    if ($errors) {
        http_response_code(422);
        echo implode(', ', $errors);
        return;
    }

    if (update_user_password($user['id'], $new)) {
        echo '✅ Password changed successfully';
    } else {
        http_response_code(500);
        echo '❌ Failed to change password';
    }
}, [$getUserAuth]);
