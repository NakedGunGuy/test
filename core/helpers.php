<?php

$__sections = []; // holds content sections

function view($name, $data = [], $layout = 'default')
{
    global $__sections;
    extract($data);

    ob_start(); // buffer content
    include VIEW_PATH . "/{$name}.php";
    $content = ob_get_clean();

    if ($layout) {
        // Layout can access $content
        include VIEW_PATH . "/layouts/{$layout}.php";
    } else {
        echo $content; // this will go through ob_gzhandler
    }
}


function start_section($name)
{
    global $__sections;
    ob_start();
}

function end_section($name)
{
    global $__sections;
    $__sections[$name] = ob_get_clean();
}

function section($name, $default = '')
{
    global $__sections;
    return $__sections[$name] ?? $default;
}

function partial(string $name, array $data = []): void
{
    extract($data);
    include VIEW_PATH . "/{$name}.php";
}

function recursive_glob($pattern, $flags = 0): false|array
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, recursive_glob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}

/**
 * @throws Exception
 */
function parse_yaml_file($path)
{
    if (function_exists('yaml_parse_file')) {
        return yaml_parse_file($path);
    }

    throw new Exception('YAML extension not installed. Install ext-yaml.');
}

/**
 * Get cached card image URL
 *
 * @param string $edition_slug The edition slug for the card
 * @return string The local URL path to the cached image
 */
function card_image($edition_slug)
{
    return get_cached_card_image($edition_slug);
}

// Polyfill for str_starts_with() for PHP < 8.0
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
