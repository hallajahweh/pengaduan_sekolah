<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    $url = $_GET['url'] ?? 'auth/login';
    $url = explode('/', $url);

    $controllerName = ucfirst($url[0]) . 'Controller';
    $method = $url[1] ?? 'index';

    $controllerFile = "controllers/$controllerName.php";

    if (!file_exists($controllerFile)) {
        die("Controller tidak ditemukan: $controllerName");
    }

    require_once $controllerFile;

    $controller = new $controllerName();

    if (!method_exists($controller, $method)) {
        die("Method tidak ditemukan: $method");
    }

    $controller->$method();

} catch (Throwable $e) {
    die("APP ERROR: " . $e->getMessage());
}