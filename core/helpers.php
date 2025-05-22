<?php

function view($name, $data = [])
{
	extract($data);
	include VIEW_PATH . "/{$name}.php";
}

function route($pattern, $callback = null)
{
	static $routes = [];
	if ($callback !== null) {
		// Convert "/user/{id}" to regex: "#^/user/(?P<id>[^/]+)$#"
		$regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
		$regex = "#^" . $regex . "$#";
		$routes[$regex] = $callback;
		return;
	}

	$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	foreach ($routes as $regex => $handler) {
		if (preg_match($regex, $uri, $matches)) {
			// Only keep named params
			$params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
			call_user_func($handler, $params);
			return true;
		}
	}

	return false;
}

function get($pattern, $callback)
{
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		route($pattern, $callback);
	}
}

function post($pattern, $callback)
{
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		route($pattern, $callback);
	}
}

function session($key = null, $value = null)
{
	if ($key === null) {
		return $_SESSION;
	}

	if ($value === null) {
		return $_SESSION[$key] ?? null;
	}

	$_SESSION[$key] = $value;
}

function session_has($key)
{
	return isset($_SESSION[$key]);
}

function session_remove($key)
{
	unset($_SESSION[$key]);
}

function session_flash($key, $value = null)
{
	static $flashed = [];

	if ($value !== null) {
		$_SESSION['_flash'][$key] = $value;
		return;
	}

	if (isset($_SESSION['_flash'][$key])) {
		$flashed[$key] = $_SESSION['_flash'][$key];
		unset($_SESSION['_flash'][$key]);
	}

	return $flashed[$key] ?? null;
}
