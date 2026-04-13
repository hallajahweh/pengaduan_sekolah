<?php
session_start();

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch($controller){

    case 'auth':
        require_once 'controllers/authcontroller.php';
        $c = new AuthController();
        break;

    case 'aspirasi':
        require_once 'controllers/aspirasicontroller.php';
        $c = new AspirasiController();
        break;

    case 'siswa':
        require_once 'controllers/siswa_controller.php';
        $c = new SiswaController();
        break;

    case 'admin':
        require_once 'controllers/admincontroller.php';
        $c = new AdminController();
        break;

    default:
        die("Controller tidak ditemukan");
}

if(method_exists($c, $action)){
    $c->$action();
}else{
    die("Action tidak ditemukan");
}
