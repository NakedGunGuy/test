<?php

function session($key = null, $value = null)
{
    if ($key === null) {
        return $_SESSION;
    }

    if ($value === null) {
        return $_SESSION[$key] ?? null;
    }

    $_SESSION[$key] = $value;

    return $_SESSION;
}

function session_has($key): bool
{
    return isset($_SESSION[$key]);
}

function session_remove($key): void
{
    unset($_SESSION[$key]);
}

function session_flash($key, $value = null)
{
    static $flashed = [];

    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    if (isset($_SESSION['_flash'][$key])) {
        $flashed[$key] = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
    }

    return $flashed[$key] ?? null;
}