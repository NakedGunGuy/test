<?php

function is_cms_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_cms_auth(): void
{
    if (!is_logged_in()) {
        header("Location: /admin/login");
        exit;
    }
}