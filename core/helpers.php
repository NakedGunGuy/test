<?php

function view($name, $data = []): void
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
