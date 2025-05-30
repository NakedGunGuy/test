<?php

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        header("Location: /login");
        exit;
    }
}