<?php

require_once "config/koneksi.php";

class User{

    static function login($username, $password){

        global $conn;

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if($data && $password === $data['password']){
            return $data;
        }

        return false;
    }

    static function register($username, $password, $role, $nis){

        global $conn;

        // Simpan password tanpa hash (plain text)
        $stmt = mysqli_prepare($conn, "INSERT INTO users(username, password, role, nis) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $role, $nis);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $success;
    }

    static function createSiswa($nis, $nama, $kelas){
        global $conn;
        
        $stmt = mysqli_prepare($conn, "INSERT INTO siswa (nis, nama, kelas) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nis, $nama, $kelas);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $success;
    }
}