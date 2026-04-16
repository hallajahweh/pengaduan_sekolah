<?php

// kalau kamu mau pisahkan routing, bisa pakai ini
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