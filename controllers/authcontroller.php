<?php

require_once "config/koneksi.php";

class AuthController {

    public function login() {
        require_once "views/auth/login.php";
    }

    public function register() {
        require_once "views/auth/register.php";
    }

    public function prosesLogin() {
        global $conn;

        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);

            if ($password == $data['password']) {
                session_start();
                $_SESSION['user'] = $data;

                header("Location: index.php?url=siswa/dashboard");
            } else {
                echo "Password salah";
            }
        } else {
            echo "User tidak ditemukan";
        }
    }

    public function prosesRegister() {
        global $conn;

        $username = $_POST['username'];
        $password = $_POST['password'];

        mysqli_query($conn, "INSERT INTO users(username,password) VALUES('$username','$password')");

        header("Location: index.php?url=auth/login");
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?url=auth/login");
    }
}