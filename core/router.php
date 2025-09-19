<?php

function route($pattern, $callback = null, $middleware = [])
{
    static $routes = [];
    if ($callback !== null) {
        // Support both language-prefixed and non-prefixed routes
        $patterns = [];

        if ($pattern === '/') {
            // Special case for root - add language prefix routes and redirect root to default language
            $patterns[] = '/';
            $patterns[] = '/{lang}';
        } elseif ($pattern === '/switch-language') {
            // Special case for language switching - only non-prefixed
            $patterns[] = $pattern;
        } else {
            // Add language prefix pattern
            $patterns[] = '/{lang}' . $pattern;
            // Also add non-prefixed pattern for backwards compatibility
            $patterns[] = $pattern;
        }

        foreach ($patterns as $p) {
            // Convert "/user/{id}" to regex
            $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $p);
            $regex = "#^" . rtrim($regex, '/') . "$#"; // normalize trailing slash
            $routes[$regex] = [
                'callback' => $callback,
                'middleware' => $middleware,
                'original_pattern' => $pattern
            ];
        }
        return;
    }

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/'); // normalize trailing slash

    // Handle root redirect to default language
    if ($uri === '' || $uri === '/') {
        $language = get_current_language();
        header("Location: /{$language}/");
        exit;
    }

    foreach ($routes as $regex => $route) {
        if (preg_match($regex, $uri, $matches)) {
            // Set language from URL if present
            if (isset($matches['lang']) && in_array($matches['lang'], AVAILABLE_LANGUAGES)) {
                set_language($matches['lang']);
            } else {
                // If no language in URL, redirect to language-prefixed version
                $current_lang = get_current_language();
                if ($route['original_pattern'] !== '/' && $route['original_pattern'] !== '/switch-language') {
                    $new_url = "/{$current_lang}{$uri}";
                    if (!empty($_SERVER['QUERY_STRING'])) {
                        $new_url .= '?' . $_SERVER['QUERY_STRING'];
                    }
                    header("Location: {$new_url}");
                    exit;
                }
            }

            // Run middleware first
            foreach ($route['middleware'] as $mw) {
                if (is_callable($mw)) {
                    $mw();
                }
            }

            // Only keep named params (excluding 'lang')
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            unset($params['lang']); // Remove language param from callback

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
        $id = basename($file, '.yaml');
        $pages[$id] = parse_yaml_file($file);
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
                (!empty($page['view']) ? $page['view'] : 'page'),
                ['page' => $page],
                (!empty($page['layout']) ? $page['layout'] : 'default')
            );
        });
    }
}
