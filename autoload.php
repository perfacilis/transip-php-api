<?php

spl_autoload_register(function ($class) {
    $prefix = 'Transip\\';
    if (strpos($class, $prefix) === false) {
        return true;
    }

    $base_dir = __DIR__ . '/src/Transip/';

    $path = str_replace([$prefix, '\\'], [$base_dir, DIRECTORY_SEPARATOR], $class);
    $path .= '.php';

    if (!file_exists($path)) {
        return false;
    }

    require_once $path;
    return true;
});

