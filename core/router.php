<?php

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

function get($pattern, $callback): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        route($pattern, $callback);
    }
}

function post($pattern, $callback): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        route($pattern, $callback);
    }
}

/**
 * @throws Exception
 */
function build_page_tree(): array
{
    $pages = [];
    $files = recursive_glob(CONTENT_PATH . '/pages/*.yaml');

    foreach ($files as $file) {

        $data = parse_yaml_file($file);
        $slug = $data['slug'] ?? '';
        $parent = $data['parent'] ?? null;
        $id = basename($file, '.yaml');
        $pages[$id] = [
            'slug' => $slug,
            'parent' => $parent,
            'file' => $file,
            'data' => $data
        ];
    }

    foreach ($pages as &$page) {
        $path = $page['slug'];
        $current = $page;
        while ($current['parent'] && isset($pages[$current['parent']])) {
            $current = $pages[$current['parent']];
            $path = $current['slug'] . '/' . $path;
        }
        $page['full_path'] = '/' . $path;
    }

    return $pages;
}

function route_page_tree($pages): void
{
    foreach ($pages as $page) {
        route($page['full_path'], function () {
            view('home', ['appName' => $_ENV['APP_NAME']]);
        });
    }
}