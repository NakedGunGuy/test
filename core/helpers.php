<?php

$__sections = []; // holds content sections

function view($name, $data = [], $layout = null)
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
