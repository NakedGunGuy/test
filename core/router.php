<?php

function route($pattern, $callback = null, $middleware = [])
{
    static $routes = [];
    if ($callback !== null) {
        // Convert "/user/{id}" to regex
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = "#^" . rtrim($regex, '/') . "$#"; // normalize trailing slash
        $routes[$regex] = [
            'callback' => $callback,
            'middleware' => $middleware,
        ];
        return;
    }

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/'); // normalize trailing slash

    foreach ($routes as $regex => $route) {
        if (preg_match($regex, $uri, $matches)) {
            // Run middleware first
            foreach ($route['middleware'] as $mw) {
                if (is_callable($mw)) {
                    $mw();
                }
            }

            // Only keep named params
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            call_user_func($route['callback'], $params);
            return true;
        }
    }

    return false;
}

function get($pattern, $callback, $middleware = [])
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        route($pattern, $callback, $middleware);
    }
}

function post($pattern, $callback, $middleware = [])
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        route($pattern, $callback, $middleware);
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
        $title = $data['title'] ?? '';
        $view = $data['view'] ?? '';
        $layout = $data['layout'] ?? '';
        $pages[$id] = [
            'slug' => $slug,
            'parent' => $parent,
            'file' => $file,
            'title' => $title,
            'view' => $view,
            'layout' => $layout,
            'data' => $data,
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
        route($page['full_path'], function () use ($page) {
            view(
                (!empty($page['view']) ? $page['view'] : 'home'),
                ['appName' => $_ENV['APP_NAME']],
                (!empty($page['layout']) ? $page['layout'] : 'default')
            );
        });
    }
}
