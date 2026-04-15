<?php

$url = $_GET['url'] ?? 'auth/login';
$url = explode('/', $url);

// Ambil controller & method
$controllerName = ucfirst($url[0]) . 'Controller';
$method = $url[1] ?? 'index';

// Path controller
$controllerFile = "controllers/" . $controllerName . ".php";

// Cek file controller
if (!file_exists($controllerFile)) {
    die("Controller tidak ditemukan: " . $controllerName);
}

require_once $controllerFile;

// Buat object controller
$controller = new $controllerName();

// Cek method
if (!method_exists($controller, $method)) {
    die("Method tidak ditemukan: " . $method);
}

// Jalankan method
$controller->$method();