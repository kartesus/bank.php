<?php

spl_autoload_register(function ($filename) {
    $file = '..' . DIRECTORY_SEPARATOR . $filename . '.php';
    if (DIRECTORY_SEPARATOR === '/')
        $file = str_replace('\\', '/', $file);

    if (file_exists($file))
        require_once $file;
});

$REQUEST_URI = explode('/', substr($_SERVER['REQUEST_URI'], 1));

if (!empty($REQUEST_URI[0]) && isset($REQUEST_URI[0])) {
    if (!empty($REQUEST_URI[1]) && isset($REQUEST_URI[1])) {
        $queryIndex = strpos($REQUEST_URI[1], '?');
        if ($queryIndex !== false) {
            $REQUEST_URI[1] = ucfirst(substr($REQUEST_URI[1], 0, $queryIndex));
        } else {
            $REQUEST_URI[1] = ucfirst($REQUEST_URI[1]);
        }
        if (file_exists('../app/api/controllers/' . $REQUEST_URI[0] . '/' . $REQUEST_URI[1] . '.php')) {
            $handler_category = $REQUEST_URI[0];
            $handler_class = $REQUEST_URI[1];
        } else {
            $handler_category = 'errors';
            $handler_class = 'HandlerNotFound';
        }
    } else {
        $handler_category = 'errors';
        $handler_class = 'CategoryNotFound';
    }
} else {
    $handler_category = 'errors';
    $handler_class = 'BadURL';
}

header('Content-Type: application/json; charset=utf-8');
$handler_file = '../app/api/controllers/' . $handler_category . '/' . $handler_class . '.php';
require_once $handler_file;
$handler = new $handler_class();
$handler->run();