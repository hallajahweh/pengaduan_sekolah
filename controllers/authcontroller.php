<?php

require_once "models/user.php";

class AuthController{

    // halaman login
    function login(){
        include "views/auth/login.php";
    }

    // halaman register
    function register(){
        include "views/auth/register.php";
    }

    // proses login
    function doLogin(){

        session_start();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';

        $user = User::login($username, $password);

        if($user && $user['role'] === $role){
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['username'];
            if($user['role'] === "siswa"){
                $_SESSION['nis'] = $user['nis'];
            }

            if($role === "admin"){
                header("Location: router.php?controller=admin&action=dashboard");
            }else{
                header("Location: router.php?controller=siswa&action=dashboard");
            }
            exit;
        }else{
            echo "<script>alert('Login gagal! Periksa username, password, atau role Anda.'); window.location='router.php?controller=auth&action=login';</script>";
        }

    }

    // proses register
    function doRegister(){

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $nis = trim($_POST['nis'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');

        $success = User::register($username, $password, $role, $nis);

        if($success){
            // Jika siswa, create record di tabel siswa
            if($role === 'siswa'){
                User::createSiswa($nis, $nama, $kelas);
            }
            echo "<script>alert('Register berhasil! Silakan login'); window.location='router.php?controller=auth&action=login';</script>";
        } else {
            echo "<script>alert('Register gagal! Silakan coba lagi.'); window.location='router.php?controller=auth&action=register';</script>";
        }

    }

    // logout
    function logout(){

        session_start();
        session_destroy();

        header("Location:index.php");
    }

}