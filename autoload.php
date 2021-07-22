<?php

spl_autoload_register(function ($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $path = __DIR__ . '/' . $className . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
    //require_once $_SERVER['document_root'] . '/classes/' . $className . '.php';
});

require_once __DIR__ . '/vendor/autoload.php';
